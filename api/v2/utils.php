<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
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

function _civicrm_initialize( ) 
{
    require_once 'CRM/Core/Config.php';
    $config =& CRM_Core_Config::singleton( );
}

function &civicrm_create_error( $msg, $data = null ) 
{
    return CRM_Core_Error::createAPIError( $msg, $data );
}

function civicrm_create_success( $result = 1 ) 
{
    return CRM_Core_Error::createAPISuccess( $result );
}

/**
 *  function to check if an error is actually a duplicate contact error
 *  
 *  @param array $error (array of) valid Error values
 *  
 *  @return true if error is duplicate contact error, false otherwise 
 *  
 *  @access public 
 */
function civicrm_duplicate($error)
{  
    if ( is_array( $error )  && civicrm_error( $error ) ) {
        $code = $error['error_message']['code'];
        if ($code == CRM_Core_Error::DUPLICATE_CONTACT ) {
            return true ;
        }
    }
    return false;
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
function civicrm_error( $params ) 
{
    if ( is_array( $params ) ) {
        return ( array_key_exists( 'is_error', $params ) &&
                 $params['is_error'] ) ? true : false;
    }
    return false;
}

function _civicrm_store_values( &$fields, &$params, &$values ) 
{
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
    require_once 'CRM/Contact/BAO/Contact.php';
    $contactFields =& CRM_Contact_DAO_Contact::fields( );
    _civicrm_store_values( $contactFields, $values, $params );
    
    if (isset($values['contact_type'])) {
        /* we're an individual/household/org property */
        
        $fields[$values['contact_type']] = CRM_Contact_DAO_Contact::fields();
        
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
    
    if ( isset($values['greeting_type']) ) {
        if ( $params['greeting_type_id'] ) {
            $greetings = array( );
            $greetings = CRM_Core_PseudoConstant::greeting( );
            $params['greeting'] = $greetings[$params['greeting_type_id']];
        } else {
            $params['greeting'] = $values['greeting_type'];
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
        
        // get the current logged in civicrm user
        $session          =& CRM_Core_Session::singleton( );
        $userID           =  $session->get( 'userID' );

        if ( $userID ) {
            $values['contact_id'] = $userID;
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
    $id = CRM_Utils_Array::value( 'id', $params );
    $externalId = CRM_Utils_Array::value( 'external_identifier', $params );
    if ( $id || $externalId ) {
        $contact = new CRM_Contact_DAO_Contact( );
        
        $contact->id = $id;
        $contact->external_identifier = $externalId;
        
        if ( $contact->find( true ) ) {
            if ( $params['contact_type'] != $contact->contact_type ) {
                return civicrm_create_error( "Mismatched contact IDs OR Mismatched contact Types" );
            }
            
            $error = CRM_Core_Error::createError( "Found matching contacts: $contact->id",
                                                  CRM_Core_Error::DUPLICATE_CONTACT, 
                                                  'Fatal', $contact->id );
            return civicrm_create_error( $error->pop( ) );
        }
    } else {
        require_once 'CRM/Dedupe/Finder.php';
        $dedupeParams = CRM_Dedupe_Finder::formatParams($params, $params['contact_type']);
        $ids = CRM_Dedupe_Finder::dupesByParams($dedupeParams, $params['contact_type'], 'Strict');
        if ( !empty($ids) ) {
            $ids = implode( ',', $ids );
            $error = CRM_Core_Error::createError( "Found matching contacts: $ids",
                                                  CRM_Core_Error::DUPLICATE_CONTACT, 
                                                  'Fatal', $ids );
            return civicrm_create_error( $error->pop( ) );
        } 
    }
    return civicrm_create_success( true );
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

function _civicrm_custom_format_params( &$params, &$values, $extends, $entityId = null )
{
    $values['custom'] = array();
    require_once 'CRM/Core/BAO/CustomField.php' ;    
    $customFields = CRM_Core_BAO_CustomField::getFields( $extends );
        
    require_once 'CRM/Core/BAO/CustomField.php';
    foreach ($params as $key => $value) {
        if ($customFieldID = CRM_Core_BAO_CustomField::getKeyID($key)) {
            CRM_Core_BAO_CustomField::formatCustomField( $customFieldID, $values['custom'], 
                                                         $value, $extends, null, $entityId );
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

/**
 * take the input parameter list as specified in the data model and 
 * convert it into the same format that we use in QF and BAO object
 *
 * @param array  $params       Associative array of property name/value
 *                             pairs to insert in new contact.
 * @param array  $values       The reformatted properties that we can use internally
 *
 * @param array  $create       Is the formatted Values array going to
 *                             be used for CRM_Event_BAO_Participant:create()
 *
 * @return array|CRM_Error
 * @access public
 */
function _civicrm_participant_formatted_param( &$params, &$values, $create=false) 
{
    $fields =& CRM_Event_DAO_Participant::fields( );
    _civicrm_store_values( $fields, $params, $values );
    
    require_once 'CRM/Core/OptionGroup.php';
    $customFields = CRM_Core_BAO_CustomField::getFields( 'Participant' );

    foreach ($params as $key => $value) {
        // ignore empty values or empty arrays etc
        if ( CRM_Utils_System::isNull( $value ) ) {
            continue;
        }

        //Handling Custom Data
        if ($customFieldID = CRM_Core_BAO_CustomField::getKeyID($key)) {
            $values[$key] = $value;
            $type = $customFields[$customFieldID]['html_type'];
            if( $type == 'CheckBox' || $type == 'Multi-Select' ) {
                $mulValues = explode( ',' , $value );
                $customOption = CRM_Core_BAO_CustomOption::getCustomOption($customFieldID, true);
                $values[$key] = array();
                foreach( $mulValues as $v1 ) {
                    foreach( $customOption as $v2 ) {
                        if (( strtolower($v2['label']) == strtolower(trim($v1)) ) ||
                            ( strtolower($v2['value']) == strtolower(trim($v1)) )) { 
                            if ( $type == 'CheckBox' ) {
                                $values[$key][$v2['value']] = 1;
                            } else {
                                $values[$key][] = $v2['value'];
                            }
                        }
                    }
                }
            } else if ( $type == 'Select' || $type == 'Radio' ) {
                $customOption = CRM_Core_BAO_CustomOption::getCustomOption($customFieldID, true);
                foreach( $customOption as $v2 ) {
                    if (( strtolower($v2['label']) == strtolower(trim($value)) )||
                        ( strtolower($v2['value']) == strtolower(trim($value)) )) {
                        $values[$key] = $v2['value'];
                    }
                }
            }
        }
        
        switch ($key) {
        case 'participant_contact_id':
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
            $values['contact_id'] = $values['participant_contact_id'];
            unset ($values['participant_contact_id']);
            break;
        case 'participant_register_date':
            if (!CRM_Utils_Rule::date($value)) {
                return civicrm_create_error("$key not a valid date: $value");
            }
            break;
        case 'event_title':
            $id = CRM_Core_DAO::getFieldValue( "CRM_Event_DAO_Event", $value, 'id', 'title' );
            $values['event_id'] = $id;
            break;
        case 'event_id':
            if (!CRM_Utils_Rule::integer($value)) {
                return civicrm_create_error("Event ID is not valid: $value");
            }
            $dao =& new CRM_Core_DAO();
            $qParams = array();
            $svq = $dao->singleValueQuery("SELECT id FROM civicrm_event WHERE id = $value",
                                          $qParams);
            if (!$svq) {
                return civicrm_create_error("Invalid Event ID: There is no event record with event_id = $value.");
            } 
            break;
        case 'participant_status_id':
            $id = CRM_Core_OptionGroup::getValue('participant_status', $value);
            $values[$key] = $id;
            break;
        case 'participant_role_id':
            $id = CRM_Core_OptionGroup::getValue('participant_role', $value);
            $values['role_id'] = $id;
            unset($values[$key]);
            break;
        default:
            break;
        }    
    }
    
    if ( array_key_exists( 'participant_note', $params ) ) {
        $values['participant_note'] = $params['participant_note'];
    }
      
    if ( $create ) {
        // CRM_Event_BAO_Participant::create() handles register_date,
        // status_id and source. So, if $values contains
        // participant_register_date, participant_status_id or participant_source,
        // convert it to register_date, status_id or source
        $changes = array('participant_register_date' => 'register_date',
                         'participant_source'        => 'source',
                         'participant_status_id'     => 'status_id',
                         'participant_role_id'       => 'role_id',
                         'participant_fee_level'     => 'fee_level',
                         'participant_fee_amount'    => 'fee_amount',
                         'participant_id'            => 'id'
                         );
        
        foreach ($changes as $orgVal => $changeVal) {
            if ( isset($values[$orgVal]) ) {
                $values[$changeVal] = $values[$orgVal];
                unset($values[$orgVal]);
            }
        }
    }
    
    return null;
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
function _civicrm_contribute_formatted_param( &$params, &$values, $create=false ) 
{
    // copy all the contribution fields as is
   
    $fields =& CRM_Contribute_DAO_Contribution::fields( );
      
    _civicrm_store_values( $fields, $params, $values );

    require_once 'CRM/Core/OptionGroup.php';
    $customFields = CRM_Core_BAO_CustomField::getFields( 'Contribution' );
    
    foreach ($params as $key => $value) {
        // ignore empty values or empty arrays etc
        if ( CRM_Utils_System::isNull( $value ) ) {
            continue;
        }

        //Handling Custom Data
        if ($customFieldID = CRM_Core_BAO_CustomField::getKeyID($key)) {
            $values[$key] = $value;
            $type = $customFields[$customFieldID]['html_type'];
            if( $type == 'CheckBox' || $type == 'Multi-Select' ) {
                $mulValues = explode( ',' , $value );
                $customOption = CRM_Core_BAO_CustomOption::getCustomOption($customFieldID, true);
                $values[$key] = array();
                foreach( $mulValues as $v1 ) {
                    foreach( $customOption as $v2 ) {
                        if (( strtolower($v2['label']) == strtolower(trim($v1)) ) ||
                            ( strtolower($v2['value']) == strtolower(trim($v1)) )) { 
                            if ( $type == 'CheckBox' ) {
                                $values[$key][$v2['value']] = 1;
                            } else {
                                $values[$key][] = $v2['value'];
                            }
                        }
                    }
                }
            } else if ( $type == 'Select' || $type == 'Radio' ) {
                $customOption = CRM_Core_BAO_CustomOption::getCustomOption($customFieldID, true);
                foreach( $customOption as $v2 ) {
                    if (( strtolower($v2['label']) == strtolower(trim($value)) )||
                        ( strtolower($v2['value']) == strtolower(trim($value)) )) {
                        $values[$key] = $v2['value'];
                    }
                }
            }
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

        case 'contact_type':
            //import contribution record according to select contact type
            require_once 'CRM/Contact/DAO/Contact.php';
            $contactType =& new CRM_Contact_DAO_Contact();
            //when insert mode check contact id or external identifire
            if ( $params['contribution_contact_id'] || $params['external_identifier'] ) {
                if ( $params['contribution_contact_id'] ) {
                    $contactType->id = $params['contribution_contact_id'];
                } else if( $params['external_identifier'] ) {
                    $contactType->external_identifier = $params['external_identifier'];
                }
                if ( $contactType->find(true) ) {
                    if ( $params['contact_type'] != $contactType->contact_type ) {
                        return civicrm_create_error("Contact Type is wrong: $contactType->contact_type");
                    }
                }
            } else if ( $params['contribution_id'] || $params['trxn_id'] ||$params['invoice_id'] ) {
                //when update mode check contribution id or trxn id or
                //invoice id
                $contactId =& new  CRM_Contribute_DAO_Contribution();
                if ( $params['contribution_id'] ) {
                    $contactId->id = $params['contribution_id'];
                } else if ( $params['trxn_id'] ) {
                    $contactId->trxn_id = $params['trxn_id'];
                } else if ( $params['invoice_id'] ) {
                    $contactId->invoice_id = $params['invoice_id'];  
                }
                if ( $contactId->find(true) ) {
                    $contactType->id = $contactId->contact_id;
                    if ( $contactType->find(true) ) {
                        if ( $params['contact_type'] != $contactType->contact_type ) {
                            return civicrm_create_error("Contact Type is wrong: $contactType->contact_type");
                        }
                    }
                }
            }
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
            require_once 'CRM/Contribute/PseudoConstant.php';
            $values['contribution_type_id'] = CRM_Utils_Array::key( ucfirst( $value ),
                                                                    CRM_Contribute_PseudoConstant::contributionType( )
                                                                    );
            if ( !CRM_Utils_Array::value( 'contribution_type_id', $values ) ) {
                return civicrm_create_error("Contribution Type is not valid: $value");
            }
            break;
        case 'payment_instrument': 
            require_once 'CRM/Core/OptionGroup.php';
            $values['payment_instrument_id'] = CRM_Core_OptionGroup::getValue( 'payment_instrument', $value );
            break;
        case 'contribution_status_id':  
            require_once 'CRM/Core/OptionGroup.php';
            if (!$values['contribution_status_id'] = CRM_Core_OptionGroup::getValue( 'contribution_status', $value )) {
                return civicrm_create_error("Contribution Status is not valid: $value");
            }
            break;
        case 'honor_type_id': 
            require_once 'CRM/Core/OptionGroup.php';
            $values['honor_type_id'] = CRM_Core_OptionGroup::getValue( 'honor_type', $value );
            break;
        default:
            break;
        }
    }
    if ( array_key_exists( 'note', $params ) ) {
        $values['note'] = $params['note'];
    }
       
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
    
    return null;
}

/**
 * take the input parameter list as specified in the data model and 
 * convert it into the same format that we use in QF and BAO object
 *
 * @param array  $params       Associative array of property name/value
 *                             pairs to insert in new contact.
 * @param array  $values       The reformatted properties that we can use internally
 *
 * @param array  $create       Is the formatted Values array going to
 *                             be used for CRM_Member_BAO_Membership:create()
 *
 * @return array|CRM_Error
 * @access public
 */
function _civicrm_membership_formatted_param( &$params, &$values, $create=false) 
{
    require_once "CRM/Member/DAO/Membership.php";
    $fields =& CRM_Member_DAO_Membership::fields( );

    _civicrm_store_values( $fields, $params, $values );
    
    require_once 'CRM/Core/OptionGroup.php';
    $customFields = CRM_Core_BAO_CustomField::getFields( 'Membership' );

    foreach ($params as $key => $value) {
        // ignore empty values or empty arrays etc
        if ( CRM_Utils_System::isNull( $value ) ) {
            continue;
        }
        
        //Handling Custom Data
        if ($customFieldID = CRM_Core_BAO_CustomField::getKeyID($key)) {
            $values[$key] = $value;
            $type = $customFields[$customFieldID]['html_type'];
            if( $type == 'CheckBox' || $type == 'Multi-Select' ) {
                $mulValues = explode( ',' , $value );
                $customOption = CRM_Core_BAO_CustomOption::getCustomOption($customFieldID, true);
                $values[$key] = array();
                foreach( $mulValues as $v1 ) {
                    foreach( $customOption as $v2 ) {
                        if (( strtolower($v2['label']) == strtolower(trim($v1)) ) ||
                            ( strtolower($v2['value']) == strtolower(trim($v1)) )) { 
                            if ( $type == 'CheckBox' ) {
                                $values[$key][$v2['value']] = 1;
                            } else {
                                $values[$key][] = $v2['value'];
                            }
                        }
                    }
                }
            } else if ( $type == 'Select' || $type == 'Radio' ) {
                $customOption = CRM_Core_BAO_CustomOption::getCustomOption($customFieldID, true);
                foreach( $customOption as $v2 ) {
                    if (( strtolower($v2['label']) == strtolower(trim($value)) )||
                        ( strtolower($v2['value']) == strtolower(trim($value)) )) {
                        $values[$key] = $v2['value'];
                    }
                }
            }
        }

        switch ($key) {
        case 'membership_contact_id':
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
            $values['contact_id'] = $values['membership_contact_id'];
            unset($values['membership_contact_id']);
            break;
        case 'join_date':
        case 'membership_start_date':
        case 'membership_end_date':
            if (!CRM_Utils_Rule::date($value)) {
                return civicrm_create_error("$key not a valid date: $value");
            }
            break;
        case 'membership_type_id':
            $id = CRM_Core_DAO::getFieldValue( "CRM_Member_DAO_MembershipType", $value, 'id', 'name' );
            $values[$key] = $id;
            break;
        case 'status_id':
            $id = CRM_Core_DAO::getFieldValue( "CRM_Member_DAO_MembershipStatus", $value, 'id', 'name' );
            $values[$key] = $id;
            break;
        case 'member_is_test':
            $values['is_test'] = CRM_Utils_Array::value( $key, $params, false );
            unset($values['member_is_test']);
            break;
        default:
            break;
        }
    }
    
    if ( $create ) {
        // CRM_Member_BAO_Membership::create() handles membership_start_date,
        // membership_end_date and membership_source. So, if $values contains
        // membership_start_date, membership_end_date  or membership_source,
        // convert it to start_date, end_date or source
        $changes = array('membership_start_date' => 'start_date',
                         'membership_end_date'   => 'end_date',
                         'membership_source'     => 'source',
                         );
        
        foreach ($changes as $orgVal => $changeVal) {
            if ( isset($values[$orgVal]) ) {
                $values[$changeVal] = $values[$orgVal];
                unset($values[$orgVal]);
            }
        }
    }
    
    return null;
}

/**
 * take the input parameter list as specified in the data model and 
 * convert it into the same format that we use in QF and BAO object
 *
 * @param array  $params       Associative array of property name/value
 *                             pairs to insert in new contact.
 * @param array  $values       The reformatted properties that we can use internally
 *
 * @param array  $create       Is the formatted Values array going to
 *                             be used for CRM_Activity_BAO_Activity::create()
 *
 * @return array|CRM_Error
 * @access public
 */
function _civicrm_activity_formatted_param( &$params, &$values, $create=false) 
{
    $fields =& CRM_Activity_DAO_Activity::fields( );
    _civicrm_store_values( $fields, $params, $values );
    
    require_once 'CRM/Core/OptionGroup.php';
    $customFields = CRM_Core_BAO_CustomField::getFields( 'Activity' );

    foreach ($params as $key => $value) {
        // ignore empty values or empty arrays etc
        if ( CRM_Utils_System::isNull( $value ) ) {
            continue;
        }

        //Handling Custom Data
        if ($customFieldID = CRM_Core_BAO_CustomField::getKeyID($key)) {
            $values[$key] = $value;
            $type = $customFields[$customFieldID]['html_type'];
            if( $type == 'CheckBox' || $type == 'Multi-Select' ) {
                $mulValues = explode( ',' , $value );
                $customOption = CRM_Core_BAO_CustomOption::getCustomOption($customFieldID, true);
                $values[$key] = array();
                foreach( $mulValues as $v1 ) {
                    foreach( $customOption as $v2 ) {
                        if (( strtolower($v2['label']) == strtolower(trim($v1)) ) ||
                            ( strtolower($v2['value']) == strtolower(trim($v1)) )) { 
                            if ( $type == 'CheckBox' ) {
                                $values[$key][$v2['value']] = 1;
                            } else {
                                $values[$key][] = $v2['value'];
                            }
                        }
                    }
                }
            } else if ( $type == 'Select' || $type == 'Radio' ) {
                $customOption = CRM_Core_BAO_CustomOption::getCustomOption($customFieldID, true);
                foreach( $customOption as $v2 ) {
                    if (( strtolower($v2['label']) == strtolower(trim($value)) )||
                        ( strtolower($v2['value']) == strtolower(trim($value)) )) {
                        $values[$key] = $v2['value'];
                    }
                }
            }
        }

    }
    return null;
}

