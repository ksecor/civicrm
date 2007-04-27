<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the Affero General Public License Version 1,    |
 | March 2002.                                                        |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the Affero General Public License for more details.            |
 |                                                                    |
 | You should have received a copy of the Affero General Public       |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 * new version of civicrm apis. See blog post at
 * http://civicrm.org/node/131
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'api/v2/utils.php';

/**
 * Add or update a contribution
 *
 * @param  array   $params           (reference ) input parameters
 *
 * @return array (reference )        contribution_id of created or updated record
 * @static void
 * @access public
 */
function &civicrm_contribute_add( &$params ) {
    _civicrm_initialize( );
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
        return civicrm_create_error( ts( '%1 contributions matching input params', array( 1 => count( $contributions ) ) ) );
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
        } elseif ( array_key_exists( $n, $otherVars ) ) {
            $$n = $v;
        } else {
            $inputParams[$n] = $v;
        }
    }

    if ( empty( $returnProperties ) ) {
        $returnProperties = null;
    }

    require_once 'CRM/Contact/BAO/Query.php';
    $newParams =& CRM_Contact_BAO_Query::convertFormValues( $inputParams );
    list( $contacts, $options ) = CRM_Contact_BAO_Query::apiQuery( $newParams,
                                                                   $returnProperties,
                                                                   null,
                                                                   $sort,
                                                                   $offset,
                                                                   $rowCount );
    return $contacts;
}

?>