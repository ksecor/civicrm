<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

function _civicrm_initialize( ) {
    require_once 'CRM/Core/Config.php';
    $config =& CRM_Core_Config::singleton( );
}

function civicrm_create_error( $msg ) {
    $values = array( );
    
    $values['is_error']      = 1;
    $values['error_message'] = $msg;
    return $values;
}

function civicrm_create_success( $result = 1 ) {
    $values = array( );
    
    $values['is_error'] = 0;
    $values['result']   = $result;
    return $values;
}

/**
 * Check if the given array is actually an error
 *
 * @param  array   $params           (reference ) input parameters
 *
 * @return boolean true if error, false otherwise
 * @static void
 * @access public
 */
function civicrm_error( $params ) {
    return ( array_key_exists( 'is_error', $params ) &&
             $params['is_error'] ) ? true : false;
}

function _civicrm_store_values( &$fields, &$params, &$values ) {
    $valueFound = false;
    
    foreach ($fields as $name => $field) {
        // ignore all ids for now
        if ( $name === 'id' || substr( $name, -1, 3 ) === '_id' ) {
            continue;
        }
        
        if ( array_key_exists( $name, $params ) ) {
            $values[$name] = $params[$name];
            $valueFound = true;
        }
    }
    return $valueFound;
}

/**
 * Converts an object to an array 
 *
 * @param  object   $dao           (reference )object to convert
 * @param  array    $dao           (reference )array
 * @return array
 * @static void
 * @access public
 */
function _civicrm_object_to_array( &$dao, &$values )
{
    $tmpFields = $dao->fields();
    $fields = array();
    //rebuild $fields array to fix unique name of the fields
    foreach( $tmpFields as $key => $val ) {
        $fields[$val["name"]]  = $val;
    }
    
    foreach( $fields as $key => $value ) {
        if (array_key_exists($key, $dao)) {
            $values[$key] = $dao->$key;
        }
    }
}

/**
 * This function adds the contact variable in $values to the
 * parameter list $params.  For most cases, $values should have length 1.  If
 * the variable being added is a child of Location, a location_type_id must
 * also be included.  If it is a child of phone, a phone_type must be included.
 *
 * @param array  $values    The variable(s) to be added
 * @param array  $params    The structured parameter list
 * 
 * @return bool|CRM_Utils_Error
 * @access public
 */
function _civicrm_add_formatted_param(&$values, &$params) 
{
    /* Crawl through the possible classes: 
     * Contact 
     *      Individual 
     *      Household
     *      Organization
     *          Location 
     *              Address 
     *              Email 
     *              Phone 
     *              IM 
     *      Note
     *      Custom 
     */

    /* Cache the various object fields */
    static $fields = null;

    if ($fields == null) {
        $fields = array();
    }
    
    //first add core contact values since for other Civi modules they are not added
    $contactFields =& CRM_Contact_DAO_Contact::fields( );
    _civicrm_store_values( $contactFields, $values, $params );
    
    if (isset($values['contact_type'])) {
        /* we're an individual/household/org property */
        
        $fields[$values['contact_type']] = CRM_Contact_DAO_Contact::fields();
        
        // if (!isset($fields[$values['contact_type']])) {
//             require_once(str_replace('_', DIRECTORY_SEPARATOR, 
//                                      'CRM_Contact_DAO_' .  $values['contact_type']) . '.php');
//             eval(
//                  '$fields['.$values['contact_type'].'] =& 
//                     CRM_Contact_DAO_'.$values['contact_type'].'::fields();'
//                  );
//         }
        
        _civicrm_store_values( $fields[$values['contact_type']], $values, $params );
        return true;
    }
    
    if ( isset($values['individual_prefix']) ) {
        if ( $params['prefix_id'] ) {
            $prefixes = array( );
            $prefixes = CRM_Core_PseudoConstant::individualPrefix( );
            $params['prefix'] = $prefixes[$params['prefix_id']];
        } else {
            $params['prefix'] = $values['individual_prefix'];
        }
        return true;
    }

    if (isset($values['individual_suffix'])) {
        if ( $params['suffix_id'] ) {
            $suffixes = array( );
            $suffixes = CRM_Core_PseudoConstant::individualSuffix( );
            $params['suffix'] = $suffixes[$params['suffix_id']];
        } else {
            $params['suffix'] = $values['individual_suffix'];
        }
        return true;
    }

    if ( isset($values['gender']) ) {
        if ( $params['gender_id'] ) {
            $genders = array( );
            $genders = CRM_Core_PseudoConstant::gender( );
            $params['gender'] = $genders[$params['gender_id']];
        } else {
            $params['gender'] = $values['gender'];
        }
        return true;
    }
    
    if ( isset($values['preferred_communication_method']) ) {
        $comm = array( );
        $preffComm = array( );
        $pcm = array( );
        $pcm = array_change_key_case( array_flip( CRM_Core_PseudoConstant::pcm() ), CASE_LOWER);
                                    
        $preffComm = explode(',' , $values['preferred_communication_method']);
        foreach ($preffComm as $v) {
            $v = strtolower(trim($v));
            if ( array_key_exists ( $v, $pcm) ) {
                $comm[$pcm[$v]] = 1;
            }
        }
        
        $params['preferred_communication_method'] = $comm;
        return true;
    }
    
    /**
     * FIXME : Need to fix below code (for location) for 2.0 schema changes 
     *
     */
    
    if (isset($values['location_type_id'])) {
        /* find and/or initialize the correct location block in $params */
        $locBlock = null;
        if (!isset($params['location'])) {
            /* if we don't have a location field yet, make one */
            $locBlock = 1;
            $params['location'][$locBlock] = array( 'location_type_id' => $values['location_type_id'],
                                                    'is_primary'       => true) ;
            
        } else {
            /* search through the location array for a matching loc. type */
            foreach ($params['location'] as $key => $loc) {
                if ($loc['location_type_id'] == $values['location_type_id']) {
                    $locBlock = $key;
                }
            }
            /* if no locBlock has the correct type, make a new one */
            if ($locBlock == null) {
                $locBlock = count($params['location']) + 1;
                $params['location'][$locBlock] = array('location_type_id' => $values['location_type_id']);
            }
        }
        //add location name (keep backward compatibility)
        if (isset($values['name'])) { 
            $params['location'][$locBlock]['name'] = $values['name'];
        }

        if ( isset($values['location_name']) ) { 
            $params['location'][$locBlock]['location_name'] = $values['location_name'];
        }

        /* if this is a phone value, find or create the correct block */
        if (isset($values['phone'])) {
            if (!isset($params['location'][$locBlock]['phone'])) {
                /* if we don't have a phone array yet, make one */
                $params['location'][$locBlock]['phone'] = array();
            } 
            
            /* add a new phone block to the array */
            $phoneBlock = count($params['location'][$locBlock]['phone']) + 1;
                        
            $params['location'][$locBlock]['phone'][$phoneBlock] = array();
            
            if (!isset($fields['Phone'])) {
                $fields['Phone'] = CRM_Core_DAO_Phone::fields();
            }
            
            _civicrm_store_values($fields['Phone'], $values,
                $params['location'][$locBlock]['phone'][$phoneBlock]);
                
            if ($phoneBlock == 1) {
                $params['location'][$locBlock]['phone'][$phoneBlock]['is_primary']
                = true;
            }
            return true;
        }
        
        /* If this is an email value, create a new block to store it */
        if (isset($values['email'])) {
            if (!isset($params['location'][$locBlock]['email'])) {
                $params['location'][$locBlock]['email'] = array();
            } 
            /* add a new email block */
            $emailBlock = count($params['location'][$locBlock]['email']) + 1;
            
            $params['location'][$locBlock]['email'][$emailBlock] = array();

            if (!isset($fields['Email'])) {
                $fields['Email'] = CRM_Core_DAO_Email::fields();
            }
            _civicrm_store_values($fields['Email'], $values,
                $params['location'][$locBlock]['email'][$emailBlock]);

            if ($emailBlock == 1) {
                $params['location'][$locBlock]['email'][$emailBlock]['is_primary']
                = true;
            }
            return true;
        }

        /* if this is an IM value, create a new block */
        if (isset($values['im'])) {
            if (!isset($params['location'][$locBlock]['im'])) {
                $params['location'][$locBlock]['im'] = array();
            }
            /* add a new IM block */
            $imBlock = count($params['location'][$locBlock]['im']) + 1;

            $params['location'][$locBlock]['im'][$imBlock] = array();
            $values['name'] = $values['im'];
            if (!isset($fields['IM'])) {
                $fields['IM'] = CRM_Core_DAO_IM::fields();
            }
            
            _civicrm_store_values($fields['IM'], $values,
                $params['location'][$locBlock]['im'][$imBlock]);

            if ($imBlock == 1) {
                $params['location'][$locBlock]['im'][$imBlock]['is_primary']
                = true;
            }
            return true;
        }

        /* Otherwise we must be an address */
        if (!isset($params['location'][$locBlock]['address'])) {
            $params['location'][$locBlock]['address'] = array();
        }
        
        if (!isset($fields['Address'])) {
            $fields['Address'] = CRM_Core_DAO_Address::fields();
        }
        
        _civicrm_store_values($fields['Address'], $values,
            $params['location'][$locBlock]['address']);

        $ids = array(   'county', 'country', 'state_province', 
                        'supplemental_address_1', 'supplemental_address_2', 
                        'StateProvince.name' );
        foreach ( $ids as $id ) {
            if ( array_key_exists( $id, $values ) ) {
                $params['location'][$locBlock]['address'][$id] = $values[$id];
            }
        }

        return true;
    }

    if (isset($values['note'])) {
        /* add a note field */
        if (!isset($params['note'])) {
            $params['note'] = array();
        }
        $noteBlock = count($params['note']) + 1;
        
        $params['note'][$noteBlock] = array();
        if (!isset($fields['Note'])) {
            $fields['Note'] = CRM_Core_DAO_Note::fields();
        }
        _civicrm_store_values($fields['Note'], $values, $params['note'][$noteBlock]);

        return true;
    }
    
        /* Check for custom field values */
    if ($fields['custom'] == null) {
        $fields['custom'] =& CRM_Core_BAO_CustomField::getFields( $values['contact_type'] );
    }
    
    foreach ($values as $key => $value) {
        if ($customFieldID = CRM_Core_BAO_CustomField::getKeyID($key)) {
            /* check if it's a valid custom field id */
            if (!array_key_exists($customFieldID, $fields['custom'])) {
                return civicrm_create_error('Invalid custom field ID');
            } else {
                //$customData = array( );
                //CRM_Core_BAO_CustomField::formatCustomField( $customFieldID, $customData,
                //                                             $value, 'Individual', null, null );
                $params[$key] = $value;
            }
        }
    }
    
    
}

/**
 * Check a formatted parameter list for required fields.  Note that this
 * function does no validation or dupe checking.
 *
 * @param array $params  Structured parameter list (as in crm_format_params)
 *
 * @return bool|CRM_core_Error  Parameter list has all required fields
 * @access public
 */
function _civicrm_required_formatted_contact(&$params) 
{
    
    if (! isset($params['contact_type'])) {
        return civicrm_create_error('No contact type specified');
    }
    
    switch ($params['contact_type']) {
        case 'Individual':
            if (isset($params['first_name']) && isset($params['last_name'])) {
                return civicrm_create_success(true);
            }
            if (is_array($params['location'])) {
                foreach ($params['location'] as $location) {
                    if (is_array($location['email']) 
                        && count($location['email']) >= 1) {
                        return civicrm_create_success(true);
                    }
                }
            }
            break;
        case 'Household':
            if (isset($params['household_name'])) {
                return civicrm_create_success(true);
            }
            break;
        case 'Organization':
            if (isset($params['organization_name'])) {
                return civicrm_create_success(true);
            }
            break;
        default:
            return 
            civicrm_create_error('Invalid Contact Type: ' . $params['contact_type'] );
    }

    return civicrm_create_error('Missing required fields');
}

function _civicrm_duplicate_formatted_contact(&$params) 
{
    if ( $params['contact_type'] == 'Individual' || (!isset($params['household_name']) && !isset($params['organization_name'])) ) {
        
        require_once 'CRM/Core/BAO/UFGroup.php';
        if ( ( $ids =& CRM_Core_BAO_UFGroup::findContact( $params, null, true ) ) != null ) {
            
            $error = CRM_Core_Error::createError( "Found matching contacts: $ids",
                                                  CRM_Core_Error::DUPLICATE_CONTACT, 
                                                  'Fatal', $ids );
            return civicrm_create_error( $error->pop( ) );
        }
        return civicrm_create_success( true );
    } else {  
        $contact = new CRM_Contact_DAO_Contact( );
        
        if ( $params['contact_type'] == 'Household' ) {
            $contact->household_name    = $params['household_name'];
        } else {
            $contact->organization_name = $params['organization_name'];
        }
        
        if ( $contact->find( true ) ) {
            $error = CRM_Core_Error::createError( "Found matching contacts: {$contact->contact_id}", 
                                                  CRM_Core_Error::DUPLICATE_CONTACT, 
                                                  'Fatal', $contact->contact_id );
            return civicrm_create_error( $error->pop( ) );
        }
        
        return civicrm_create_success( true );
    }
}

/**
 * Validate a formatted contact parameter list.
 *
 * @param array $params  Structured parameter list (as in crm_format_params)
 *
 * @return bool|CRM_Core_Error
 * @access public
 */
function _civicrm_validate_formatted_contact(&$params) 
{
    /* Look for offending email addresses */
    if (is_array($params['location'])) {
        $badEmail = true;
        $emails = 0;
        foreach ($params['location'] as $loc) {
            if (is_array($loc['email'])) {
                $emails++;
                foreach ($loc['email'] as $email) {
                    if (CRM_Utils_Rule::email( $email['email']) ) {
                        $badEmail = false;
                    }
                }
            }
        }
        if ($emails && $badEmail) {
            return civicrm_create_error( 'No valid email address');
        }
    }

    /* Validate custom data fields */
    if (is_array($params['custom'])) {
        foreach ($params['custom'] as $key => $custom) {
            if (is_array($custom)) {
                $valid = CRM_Core_BAO_CustomValue::typecheck(
                    $custom['type'], $custom['value']);
                if (! $valid) {
                    return civicrm_create_error('Invalid value for custom field \'' .
                                                $custom['name']. '\'');
                }
                if ( $custom['type'] == 'Date' ) {
                    $params['custom'][$key]['value'] = str_replace( '-', '', $params['custom'][$key]['value'] );
                }
            }
        }
    }

    return civicrm_create_success( true );
}

function _civicrm_custom_format_params( &$params, &$values, $extends )
{
    $values['custom'] = array();
    require_once 'CRM/Core/BAO/CustomField.php' ;    
    $customFields = CRM_Core_BAO_CustomField::getFields( $extends );
    
    foreach ($params as $key => $value) {
        if ($customFieldID = CRM_Core_BAO_CustomField::getKeyID($key)) {
            /* check if it's a valid custom field id */
            if ( !array_key_exists($customFieldID, $customFields)) {
                return civicrm_create_error('Invalid custom field ID');
            }
            
            /* validate the data against the CF type */
            $valid = CRM_Core_BAO_CustomValue::typecheck(
                                                         $customFields[$customFieldID][2], $value);
            
            if (! $valid) {
                return civicrm_create_error('Invalid value for custom field ' .
                                            $customFields[$customFieldID][1]);
            }
            
            // fix the date field if so
            if ( $customFields[$customFieldID][2] == 'Date' ) {
                $value = str_replace( '-', '', $value );
            }
            
            // fixed for checkbox and multiselect
            $newMulValues = array();
            if ( $customFields[$customFieldID][3] == 'CheckBox' || $customFields[$customFieldID][3] =='Multi-Select') {
                $value = str_replace("|",",",$value);
                $mulValues = explode( ',' , $value );
                $custuomOption = CRM_Core_BAO_CustomOption::getCustomOption($customFieldID, true);
                foreach( $mulValues as $v1 ) {
                    foreach( $custuomOption as $v2 ) {
                        if (( strtolower($v2['label']) == strtolower(trim($v1)) )||( strtolower($v2['value']) == strtolower(trim($v1)) )) {
                            $newMulValues[] = $v2['value'];
                        }
                    }
                }
                
                $value = CRM_Core_BAO_CustomOption::VALUE_SEPERATOR.implode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR,$newMulValues).CRM_Core_BAO_CustomOption::VALUE_SEPERATOR;
            } else if( $customFields[$customFieldID][3] == 'Select' || $customFields[$customFieldID][3] == 'Radio' ) {
                $custuomOption = CRM_Core_BAO_CustomOption::getCustomOption($customFieldID, true);
                foreach( $custuomOption as $v2 ) {
                    if( ( strtolower($v2['label']) == strtolower(trim($value)) )||( strtolower($v2['value']) == strtolower(trim($value)))) {
                        $value = $v2['value'];
                        break;
                    }
                }
            }
            
            $values['custom'][$customFieldID] = array(
                                                      'value'   => $value,
                                                      'extends' => $customFields[$customFieldID][3],
                                                      'type'    => $customFields[$customFieldID][2],
                                                      'custom_field_id' => $customFieldID,
                                                      );
        }
    }
}


/**
 * This function ensures that we have the right input parameters
 *
 * We also need to make sure we run all the form rules on the params list
 * to ensure that the params are valid
 *
 * @param array  $params       Associative array of property name/value
 *                             pairs to insert in new history.
 *                           
 *
 * @return bool true if success false otherwise
 * @access public
 */
function _civicrm_check_required_fields( &$params, $daoName)
{
    if ( isset($params['extends'] ) ) {
        if ( ( $params['extends'] == 'Activity' || 
            $params['extends'] == 'Phonecall'  || 
            $params['extends'] == 'Meeting'    || 
            $params['extends'] == 'Group'      || 
            $params['extends'] == 'Contribution' 
            ) && 
            ( $params['style'] == 'Tab' ) ) {
            return civicrm_create_error(ts("Can not create Custom Group in Tab for ". $params['extends']));
        }
    }

    require_once(str_replace('_', DIRECTORY_SEPARATOR, $daoName) . ".php");
    $dao =& new $daoName();
    $fields = $dao->fields();
 
    $missing = array();
    foreach ($fields as $k => $v) {
        if ($k == 'id') {
            continue;
        }

        if ( isset( $v['required'] ) ) {
            if ($v['required'] && !(isset($params[$k]))) {
                $missing[] = $k;
            }
        }
    }

    if (!empty($missing)) {
        return civicrm_create_error(ts("Required fields ". implode(',', $missing) . " for $daoName are not found"));
    }

    return true;
}





?>
