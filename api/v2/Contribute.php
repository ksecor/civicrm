<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 * File for the CiviCRM APIv2 Contribution functions
 *
 * @package CiviCRM_APIv2
 * @subpackage API_Contribute
 *
 * @copyright CiviCRM LLC (c) 2004-2009
 * @version $Id$
 *
 */

/**
 * Include utility functions
 */
require_once 'api/v2/utils.php';
require_once 'CRM/Utils/Rule.php';

/**
 * Add or update a contribution
 *
 * @param  array   $params           (reference ) input parameters
 *
 * @return array (reference )        contribution_id of created or updated record
 * @static void
 * @access public
 */
function &civicrm_contribution_add( &$params ) {
    _civicrm_initialize( );

    if ( empty( $params ) ) {
        return civicrm_create_error( ts( 'No input parameters present' ) );
    }

    if ( ! is_array( $params ) ) {
        return civicrm_create_error( ts( 'Input parameters is not an array' ) );
    }

    $error = _civicrm_contribute_check_params( $params );
    if ( civicrm_error( $error ) ) {
        return $error;
    }

    $values  = array( );
   
    require_once 'CRM/Contribute/BAO/Contribution.php';
    $error = _civicrm_contribute_format_params( $params, $values );
    if ( civicrm_error( $error ) ) {
        return $error;
    }

    $values["contact_id"] = $params["contact_id"];
    $values["source"]     = $params["source"];
    
    $ids     = array( );
    if ( CRM_Utils_Array::value( 'id', $params ) ) {
        $ids['contribution'] = $params['id'];
    }
    $contribution = CRM_Contribute_BAO_Contribution::create( $values, $ids );
    if ( is_a( $contribution, 'CRM_Core_Error' ) ) {
        return civicrm_create_error( ts( $contribution->_errors[0]['message'] ) );
    }

    _civicrm_object_to_array($contribution, $contributeArray);
    
    return $contributeArray;
}

/**
 * Retrieve a specific contribution, given a set of input params
 * If more than one contribution exists, return an error, unless
 * the client has requested to return the first found contact
 *
 * @param  array   $params           (reference ) input parameters
 *
 * @return array (reference )        array of properties, if error an array with an error id and error message
 * @static void
 * @access public
 */
function &civicrm_contribution_get( &$params ) {
    _civicrm_initialize( );

    $values = array( );
    if ( empty( $params ) ) {
        return civicrm_create_error( ts( 'No input parameters present' ) );
    }
    
    if ( ! is_array( $params ) ) {
        return civicrm_create_error( ts( 'Input parameters is not an array' ) );
    }

    $contributions =& civicrm_contribution_search( $params );
    if ( civicrm_error( $contributions ) ) {
        return $contributions;
    }

    if ( count( $contributions ) != 1 &&
         ! $params['returnFirst'] ) {
        return civicrm_create_error( ts( '%1 contributions matching input params', array( 1 => count( $contributions ) ) ),
                                     $contributions );
    }

    $contributions = array_values( $contributions );
    return $contributions[0];
}

/**
 * Delete a contribution
 *
 * @param  array   $params           (reference ) input parameters
 *
 * @return boolean        true if success, else false
 * @static void
 * @access public
 */
function civicrm_contribution_delete( &$params ) {

    $contributionID = CRM_Utils_Array::value( 'contribution_id', $params );
    if ( ! $contributionID ) {
        return civicrm_create_error( ts( 'Could not find contribution_id in input parameters' ) );
    }

    require_once 'CRM/Contribute/BAO/Contribution.php';
    if ( CRM_Contribute_BAO_Contribution::deleteContribution( $contributionID ) ) {
        return civicrm_create_success( );
    } else {
        return civicrm_create_error( ts( 'Could not delete contribution' ) );
    }
}

/**
 * Retrieve a set of contributions, given a set of input params
 *
 * @param  array   $params           (reference ) input parameters
 * @param array    $returnProperties Which properties should be included in the
 *                                   returned Contribution object. If NULL, the default
 *                                   set of properties will be included.
 *
 * @return array (reference )        array of contributions, if error an array with an error id and error message
 * @static void
 * @access public
 */
function &civicrm_contribution_search( &$params ) {
    _civicrm_initialize( );

    $inputParams      = array( );
    $returnProperties = array( );
    $otherVars = array( 'sort', 'offset', 'rowCount' );
    
    $sort     = null;
    $offset   = 0;
    $rowCount = 25;
    foreach ( $params as $n => $v ) {
        if ( substr( $n, 0, 7 ) == 'return.' ) {
            $returnProperties[ substr( $n, 7 ) ] = $v;
        } elseif ( in_array( $n, $otherVars ) ) {
            $$n = $v;
        } else {
            $inputParams[$n] = $v;
        }
    }
    
    // add is_test to the clause if not present
    if ( ! array_key_exists( 'contribution_test', $inputParams ) ) {
        $inputParams['contribution_test'] = 0;
    }

    require_once 'CRM/Contribute/BAO/Query.php';
    require_once 'CRM/Contact/BAO/Query.php';
    if ( empty( $returnProperties ) ) {
        $returnProperties = CRM_Contribute_BAO_Query::defaultReturnProperties( CRM_Contact_BAO_Query::MODE_CONTRIBUTE );
    }
    
    $newParams =& CRM_Contact_BAO_Query::convertFormValues( $inputParams );

    $query =& new CRM_Contact_BAO_Query( $newParams, $returnProperties, null );
    list( $select, $from, $where ) = $query->query( );
    
    $sql = "$select $from $where";  

    if ( ! empty( $sort ) ) {
        $sql .= " ORDER BY $sort ";
    }
    $sql .= " LIMIT $offset, $rowCount ";
    $dao =& CRM_Core_DAO::executeQuery( $sql, CRM_Core_DAO::$_nullArray );
    
    $contribution = array( );
    while ( $dao->fetch( ) ) {
        $contribution[$dao->contribution_id] = $query->store( $dao );
    }
    $dao->free( );
    
    return $contribution;
}

/**
 *
 * @param <type> $params
 * @return <type> 
 */
function &civicrm_contribution_format_create( &$params ) {
    _civicrm_initialize( );
   
    // return error if we have no params
    if ( empty( $params ) ) {
        return _civicrm_create_error( 'Input Parameters empty' );
    }
    
    $error = _civicrm_contribute_check_params($params);
    if ( civicrm_error( $error ) ) {
        return $error;
    }
    $values  = array( );
    $error = _civicrm_contribute_format_params($params, $values);
    if ( civicrm_error( $error ) ) {
        return $error;
    }
    
    $error = _civicrm_contribute_duplicate_check($params);
    if ( civicrm_error( $error ) ) {
        return $error;
    }
    $ids = array();
    
    CRM_Contribute_BAO_Contribution::resolveDefaults($params, true);

    $contribution = CRM_Contribute_BAO_Contribution::create( $params, $ids );
    _civicrm_object_to_array($contribution, $contributeArray);
    return $contributeArray;

}

/**
 * This function ensures that we have the right input contribution parameters
 *
 * We also need to make sure we run all the form rules on the params list
 * to ensure that the params are valid
 *
 * @param array  $params       Associative array of property name/value
 *                             pairs to insert in new contribution.
 *
 * @return bool|CRM_Utils_Error
 * @access private
 */
function _civicrm_contribute_check_params( &$params ) {
    static $required = array( 'contact_id', 'total_amount', 'contribution_type_id' );
    
    // cannot create a contribution with empty params
    if ( empty( $params ) ) {
        return civicrm_create_error( 'Input Parameters empty' );
    }

    $valid = true;
    $error = '';
    foreach ( $required as $field ) {
        if ( ! CRM_Utils_Array::value( $field, $params ) ) {
            $valid = false;
            $error .= $field;
            break;
        }
    }
    
    if ( ! $valid ) {
        return civicrm_create_error( "Required fields not found for contribution $error" );
    }
    
    return array();
}

/**
 * Check if there is a contribution with the same trxn_id or invoice_id
 *
 * @param array  $params       Associative array of property name/value
 *                             pairs to insert in new contribution.
 *
 * @return array|CRM_Error
 * @access private
 */
function _civicrm_contribute_duplicate_check( &$params ) {
    require_once 'CRM/Contribute/BAO/Contribution.php';
    $duplicates = array( );
    $result = CRM_Contribute_BAO_Contribution::checkDuplicate( $params,$duplicates ); 
    if ( $result ) {
        $d = implode( ', ', $duplicates );
        $error = CRM_Core_Error::createError( "Duplicate error - existing contribution record(s) have a matching Transaction ID or Invoice ID. Contribution record ID(s) are: $d", CRM_Core_Error::DUPLICATE_CONTRIBUTION, 'Fatal', $d);
        return civicrm_create_error( $error->pop( ),
                                     $d );
    } else {
        return array();
    }
}



/**
 * take the input parameter list as specified in the data model and 
 * convert it into the same format that we use in QF and BAO object
 *
 * @param array  $params       Associative array of property name/value
 *                             pairs to insert in new contact.
 * @param array  $values       The reformatted properties that we can use internally
 *                            '
 * @return array|CRM_Error
 * @access public
 */
function _civicrm_contribute_format_params( &$params, &$values, $create=false ) {
    // copy all the contribution fields as is
   
    $fields =& CRM_Contribute_DAO_Contribution::fields( );

    _civicrm_store_values( $fields, $params, $values );

    foreach ($params as $key => $value) {
        // ignore empty values or empty arrays etc
        if ( CRM_Utils_System::isNull( $value ) ) {
            continue;
        }

        switch ($key) {

        case 'contribution_contact_id':
            if (!CRM_Utils_Rule::integer($value)) {
                return civicrm_create_error("contact_id not valid: $value");
            }
            $dao =& new CRM_Core_DAO();
            $qParams = array();
            $svq = $dao->singleValueQuery("SELECT id FROM civicrm_contact WHERE id = $value",
                                          $qParams);
            if (!$svq) {
                return civicrm_create_error("Invalid Contact ID: There is no contact record with contact_id = $value.");
            }
            
            $values['contact_id'] = $values['contribution_contact_id'];
            unset ($values['contribution_contact_id']);
            break;

        case 'receive_date':
        case 'cancel_date':
        case 'receipt_date':
        case 'thankyou_date':
            if (!CRM_Utils_Rule::date($value)) {
                return civicrm_create_error("$key not a valid date: $value");
            }
            break;

        case 'non_deductible_amount':
        case 'total_amount':
        case 'fee_amount':
        case 'net_amount':
            if (!CRM_Utils_Rule::money($value)) {
                return civicrm_create_error("$key not a valid amount: $value");
            }
            break;
        case 'currency':
            if (!CRM_Utils_Rule::currencyCode($value)) {
                return civicrm_create_error("currency not a valid code: $value");
            }
            break;
        case 'contribution_type':            
            $values['contribution_type_id'] = CRM_Utils_Array::key( ucfirst( $value ),
                                                                    CRM_Contribute_PseudoConstant::contributionType( )
                                                                    );
            break;
        case 'payment_instrument': 
            require_once 'CRM/Core/OptionGroup.php';
            $values['payment_instrument_id'] = CRM_Core_OptionGroup::getValue( 'payment_instrument', $value );
            break;
        default:
            break;
        }
    }

    if ( array_key_exists( 'note', $params ) ) {
        $values['note'] = $params['note'];
    }

    _civicrm_custom_format_params( $params, $values, 'Contribution' );
    
    if ( $create ) {
        // CRM_Contribute_BAO_Contribution::add() handles contribution_source
        // So, if $values contains contribution_source, convert it to source
        $changes = array( 'contribution_source' => 'source' );
        
        foreach ($changes as $orgVal => $changeVal) {
            if ( isset($values[$orgVal]) ) {
                $values[$changeVal] = $values[$orgVal];
                unset($values[$orgVal]);
            }
        }
    }
    
    return array();
}

