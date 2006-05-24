<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.4                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                  |
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
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
*/

require_once 'CRM/Core/I18n.php';
require_once 'CRM/Core/Config.php';
require_once 'CRM/Core/Error.php';
require_once 'CRM/Utils/Array.php';

function _crm_error( $message, $code = 8000, $level = 'Fatal', $params = null)
{
    return CRM_Core_Error::createError( $message, $code, $level, $params );
}

function _crm_store_values( &$fields, &$params, &$values ) {
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

function _crm_update_object(&$object, &$values)
{
    // abort early if the object is an error object: CRM-500 & CRM-559
    // we should trap and return somehow, or not get into this state
    if ( is_a( $object, 'CRM_Core_Error' ) || ! $object ) {
        return;
    }

    $fields =& $object->fields( );
    $valueFound = false;

    foreach ($fields as $name => $field) {
        // ignore all ids for now
        if ($name === 'id') {
            continue;
        }

        if (array_key_exists( $name, $values)) {
            $object->$name = $values[$name];
            //if ( substr( $name, -1, 3 ) !== '_id' ) {
            /* only say we've found a value if at least one is not null */
            // why do we check for non-id-ness and not null-ness?
            // we do want to update FKs and be able to null fields, don't we?
#           if (substr($name, -3, 3) !== '_id' && $values[$name] !== null) {
                $valueFound = true;
#           }
        }
    }
    //    print_r($object);
    if ($valueFound) {
        $object->save();
    }
}


function _crm_update_from_object(&$object, &$values, $empty = false, $zeroMoney = false)
{
    $fields =& $object->fields();

    require_once 'CRM/Utils/Type.php';
    foreach ($fields as $name => $field) {

        if (($name == 'id') or ($empty and empty($object->$name)) or
            ($zeroMoney and $field['type'] == CRM_Utils_Type::T_MONEY and $object->$name == '0.00')) {
            continue;
        }

        $values[$name] = $object->$name;

        // FIXME? change the dates from YYYY-MM-DD hh:mm:ss format back to YYYYMMDDhhmmss
        // so the $values array is actually importable
        if ($field['type'] & CRM_Utils_Type::T_DATE) {
            $dropArray = array('-' => '', ':' => '', ' ' => '');
            $values[$name] = strtr($values[$name], $dropArray);
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
 *                             pairs to insert in new contact.
 * @param string $contact_type Which class of contact is being created.
 *            Valid values = 'Individual', 'Household', 'Organization'.
 *                            '
 * @return bool|CRM_Utils_Error
 * @access public
 */
function _crm_check_params( &$params, $contact_type = 'Individual' ) {
    static $required = array(
                             'Individual'   => array(
                                                   array( 'first_name', 'last_name' ),
                                                   'email',
                                                   ),
                             'Household'    => array(
                                                     'household_name',
                                                     ),
                             'Organization' => array(
                                                     'organization_name',
                                                     ),
                             );

    // cannot create a contact with empty params
    if ( empty( $params ) ) {
        return _crm_error( 'Input Parameters empty' );
    }

    // contact_type has a limited number of valid values
    $fields = CRM_Utils_Array::value( $contact_type, $required );
    if ( $fields == null ) {
        return _crm_error( "Invalid Contact Type: $contact_type" );
    }

    $valid = false;
    $error = '';
    foreach ( $fields as $field ) {
        if ( is_array( $field ) ) {
            $valid = true;
            foreach ( $field as $element ) {
                if ( ! CRM_Utils_Array::value( $element, $params ) ) {
                    $valid = false;
                    $error .= $element; 
                    break;
                }
            }
        } else {
            if ( CRM_Utils_Array::value( $field, $params ) ) {
                $valid = true;
            }
        }
        if ( $valid ) {
            break;
        }
    }
    
    if ( ! $valid ) {
        return _crm_error( "Required fields not found for $contact_type $error" );
    }

    // check for record already existing
    require_once 'CRM/Core/BAO/UFGroup.php';
    if ( ( $ids = CRM_Core_BAO_UFGroup::findContact( $params ) ) != null ) {
        return _crm_error( "Found matching contacts: $ids", 8000, 'Fatal',
                           $ids );
    }

    return true;
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
 * @access public
 */
function _crm_check_contrib_params( &$params ) {
    static $required = array( 'contact_id', 'total_amount', 'contribution_type' );

    // cannot create a contribution with empty params
    if ( empty( $params ) ) {
        return _crm_error( 'Input Parameters empty' );
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
        return _crm_error( "Required fields not found for contribution $error" );
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
 *                            '
 * @return array|CRM_Error
 * @access public
 */
function _crm_format_params( &$params, &$values ) {
    // copy all the contact and contact_type fields as is
   
    $fields =& CRM_Contact_DAO_Contact::fields( );
    
    _crm_store_values( $fields, $params, $values );

    require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Contact_DAO_" . $values['contact_type']) . ".php");
    eval( '$fields =& CRM_Contact_DAO_' . $values['contact_type'] . '::fields( );' );
    _crm_store_values( $fields, $params, $values );
    
    $ids = array("prefix","suffix","gender"); 
    foreach ( $ids as $id ) {
        if ( array_key_exists( $id, $params ) ) {
            $values[$id] = $params[$id];
        }
    }
    

    $locationTypeNeeded = false;

    $values['location']               = array( );
    $values['location'][1]            = array( );
    $fields =& CRM_Core_DAO_Location::fields( );
    if ( _crm_store_values( $fields, $params, $values['location'][1] ) ) {
        $locationTypeNeeded = true;
    }
    if ( array_key_exists( 'location_type', $params ) ) {
        $locationTypes = CRM_Core_PseudoConstant::locationType( );

        $locationType = $locationTypeId = '';
        //fix for CRM-707
        if (!is_numeric($params['location_type'])) {
            $locationTypeName = $params['location_type'];
            $locationTypeId   = CRM_Utils_Array::value($params['location_type'], $locationTypes);
        } else {
            $locationTypeName = CRM_Utils_Array::value($params['location_type'], $locationTypes);
            $locationTypeId   = $params['location_type'];
        }
        
        $values['location'][1]['location_type']    = $locationTypeName;
        $values['location'][1]['location_type_id'] = $locationTypeId;
    }

    $values['location'][1]['address'] = array( );
    $fields =& CRM_Core_DAO_Address::fields( );
    
    // ignore the note field in address for now
    unset( $fields['note'] );

    if ( _crm_store_values( $fields, $params, $values['location'][1]['address'] ) ) {
        $locationTypeNeeded = true;
    }
    $ids = array( 'county', 'country', 'state_province', 'supplemental_address_1', 'supplemental_address_2', 'StateProvince.name' );
    foreach ( $ids as $id ) {
        if ( array_key_exists( $id, $params ) ) {
            $values['location'][1]['address'][$id] = $params[$id];
            $locationTypeNeeded = true;
        }
    }
   
   
    $blocks = array( 'Email', 'Phone', 'IM' );
    foreach ( $blocks as $block ) {
        $name = strtolower($block);
        $values['location'][1][$name]    = array( );
        $values['location'][1][$name][1] = array( );
        require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Core_DAO_" . $block) . ".php");
        eval( '$fields =& CRM_Core_DAO_' . $block . '::fields( );' );
        if ( _crm_store_values( $fields, $params, $values['location'][1][$name][1] ) ) {
            $locationTypeNeeded = true;
            $values['location'][1][$name][1]['is_primary'] = 1;
        }
    }
    
    if (! array_key_exists('first_name', $params) || 
            ! array_key_exists('last_name', $params) ) {
        // make sure phone and email are valid strings
        if ( array_key_exists( 'email', $params ) &&
            ! CRM_Utils_Rule::email( $params['email'] ) ) {
            return _crm_error( "Email not valid " . $params['email'] );
        }
    }

    if ( array_key_exists( 'im', $params ) ) {
        $values['location'][1]['im'][1]['name'] = $params['im'];
        $locationTypeNeeded = true;
    }

    if ( array_key_exists( 'im_provider', $params ) ) {
        $values['location'][1]['im'][1]['provider'] = $params['im_provider'];
        $locationTypeNeeded = true;
    }
   
    if ( $locationTypeNeeded ) {
        if ( ! array_key_exists( 'location_type_id', $values['location'][1] ) ) 
        {
            require_once 'CRM/Core/BAO/LocationType.php';
            $locationType =& CRM_Core_BAO_LocationType::getDefault( );
            $values['location'][1]['location_type_id'] = $locationType->id;
            $values['location'][1]['location_type']    = $locationType->name;
        }

        $values['location'][1]['is_primary'] = true;
    } else {
        unset( $values['location'] );
    }
  
   
    if ( array_key_exists( 'note', $params ) ) {
        $values['note'] = $params['note'];
    }

    $values['custom'] = array();

    $customFields = CRM_Core_BAO_CustomField::getFields( $values['contact_type'] );

    foreach ($params as $key => $value) {
        if ($customFieldID = CRM_Core_BAO_CustomField::getKeyID($key)) {
            /* check if it's a valid custom field id */
            if ( !array_key_exists($customFieldID, $customFields)) {
                return _crm_error('Invalid custom field ID');
            }

            /* validate the data against the CF type */
            //CRM_Core_Error::debug( $value, $customFields[$customFieldID] );
            $valid = CRM_Core_BAO_CustomValue::typecheck(
                            $customFields[$customFieldID][2], $value);

            if (! $valid) {
                return _crm_error('Invalid value for custom field ' .
                    $customFields[$customFieldID][0]);
            }
            
            // fix the date field if so
            if ( $customFields[$customFieldID][2] == 'Date' ) {
                $value = str_replace( '-', '', $value );
            }

            $newMulValues = array();
            if ( $customFields[$customFieldID][3] == 'CheckBox' || $customFields[$customFieldID][3] =='Multi-Select') {
                $value = str_replace("|",",",$value);
                $mulValues = explode( ',' , $value );
                $custuomOption = CRM_Core_BAO_CustomOption::getCustomOption($customFieldID, true);
                foreach( $mulValues as $v1 ) {
                    foreach( $custuomOption as $v2 ) {
                        if ( strtolower($v2['label']) == strtolower(trim($v1)) ) {
                            $newMulValues[] = $v2['value'];
                        }
                    }
                }
                $value = implode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR,$newMulValues);

            } else if( $customFields[$customFieldID][3] == 'Select' || $customFields[$customFieldID][3] == 'Radio' ) {
                $custuomOption = CRM_Core_BAO_CustomOption::getCustomOption($customFieldID, true);
                foreach( $custuomOption as $v2 ) {
                    if ( strtolower($v2['label']) == strtolower(trim($value)) ) {
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
   
    CRM_Contact_BAO_Contact::resolveDefaults( $values, true );
   
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
function _crm_format_contrib_params( &$params, &$values ) {
    // copy all the contribution fields as is
   
    $fields =& CRM_Contribute_DAO_Contribution::fields( );

    static $domainID = null;
    if (!$domainID) {
        $config =& CRM_Core_Config::singleton();
        $domainID = $config->domainID();
    }
    
    _crm_store_values( $fields, $params, $values );

    foreach ($params as $key => $value) {

        // ignore empty values or empty arrays etc
        if ( CRM_Utils_System::isNull( $value ) ) {
            continue;
        }

        switch ($key) {
        case 'contact_id':
            if (!CRM_Utils_Rule::integer($value)) {
                return _crm_error("contact_id not valid: $value");
            }
            $dao =& new CRM_Core_DAO();
            $svq = $dao->singleValueQuery("SELECT id FROM civicrm_contact WHERE domain_id = $domainID AND id = $value");
            if (!$svq) {
                return _crm_error("Invalid Contact ID: There is no contact record with contact_id = $value.");
            }
            break;
        case 'receive_date':
        case 'cancel_date':
        case 'receipt_date':
        case 'thankyou_date':
            if (!CRM_Utils_Rule::date($value)) {
                return _crm_error("$key not a valid date: $value");
            }
            break;
        case 'non_deductible_amount':
        case 'total_amount':
        case 'fee_amount':
        case 'net_amount':
            if (!CRM_Utils_Rule::money($value)) {
                return _crm_error("$key not a valid amount: $value");
            }
            break;
        case 'currency':
            if (!CRM_Utils_Rule::currencyCode($value)) {
                return _crm_error("currency not a valid code: $value");
            }
            break;
        default:
            break;
        }
    }

    $values['custom'] = array();

    $customFields = CRM_Core_BAO_CustomField::getFields('Contribution');

    foreach ($params as $key => $value) {
        if ($customFieldID = CRM_Core_BAO_CustomField::getKeyID($key)) {
            /* check if it's a valid custom field id */
            if ( !array_key_exists($customFieldID, $customFields)) {
                return _crm_error('Invalid custom field ID');
            }

            /* validate the data against the CF type */
            $valid = CRM_Core_BAO_CustomValue::typecheck(
                            $customFields[$customFieldID][2], $value);

            if (! $valid) {
                return _crm_error('Invalid value for custom field ' .
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
                        if ( strtolower($v2['label']) == strtolower(trim($v1)) ) {
                            $newMulValues[] = $v2['value'];
                        }
                    }
                }
                $value = implode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR,$newMulValues);
            } else if( $customFields[$customFieldID][3] == 'Select' || $customFields[$customFieldID][3] == 'Radio' ) {
                $custuomOption = CRM_Core_BAO_CustomOption::getCustomOption($customFieldID, true);
                foreach( $custuomOption as $v2 ) {
                    if ( strtolower($v2['label']) == strtolower(trim($value)) ) {
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
   
    return null;
   

}

function _crm_update_contact( $contact, $values, $overwrite = true ) 
{
    // first check to make sure the location arrays sync up
    $param = array("contact_id" =>$contact->id );
    $contact = crm_get_contact($param);
    
    $locMatch = _crm_location_match($contact, $values);
    
    if (! $locMatch) {
        return _crm_error('Cannot update contact location');
    }

    // it is possible that an contact type object record does not exist
    // if the contact_type_object is null etc, if so we create one
    if ( $contact->contact_type_object == null ) {
        require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Contact_BAO_" . $contact->contact_type) . ".php");
        eval('$contact->contact_type_object =& new CRM_Contact_BAO_' . $contact->contact_type . '( );' );
        $contact->contact_type_object->contact_id = $contact->id;
    }

    $sortNameArray = array();
    // fix sort_name and display_name
    if ( $contact->contact_type == 'Individual' ) {
        if ($overwrite || ! isset($contact->contact_type_object->first_name)) {
            $firstName = CRM_Utils_Array::value( 'first_name', $values );
        } else {
            $firstName = null;
        }
        if ( ! $firstName ) {
            $firstName = isset( $contact->contact_type_object->first_name ) ? $contact->contact_type_object->first_name : '';
        }
        
        if ($overwrite || ! isset($contact->contact_type_object->middle_name)) {
            $middleName = CRM_Utils_Array::value( 'middle_name', $values );
        } else {
            $middleName = null;
        }
        if ( ! $middleName ) {
            $middleName = isset( $contact->contact_type_object->middle_name ) ? $contact->contact_type_object->middle_name : '';
        }
        
        if ($overwrite || ! isset($contact->contact_type_object->last_name)) {
            $lastName = CRM_Utils_Array::value( 'last_name', $values );
        } else {
            $lastName = null;
        }
        if ( ! $lastName ) {
            $lastName = isset( $contact->contact_type_object->last_name ) ? $contact->contact_type_object->last_name : '';
        }
        
        if ($overwrite || ! isset($contact->contact_type_object->prefix_id)) {
            $prefix = CRM_Utils_Array::value( 'prefix', $values );
        } else {
            $prefix = null;
        }
        if ( ! $prefix ) {
            if (isset( $contact->contact_type_object->prefix_id )) {
                $prefix = & new CRM_Core_DAO_IndividualPrefix();
                $prefix->id = $contact->contact_type_object->prefix_id;
                $prefix->find();
                $prefix->fetch();
                $prefix = $prefix->name; 
            } else {
                $prefix = "";
            }
        }
        if ($overwrite || ! isset($contact->contact_type_object->suffix_id)) {
            $suffix = CRM_Utils_Array::value( 'suffix', $values );
        } else {
            $suffix = null;
        }
        if ( ! $suffix ) {
            if (isset( $contact->contact_type_object->suffix_id )) {
                $suffix = & new CRM_Core_DAO_IndividualSuffix();
                $suffix->id = $contact->contact_type_object->suffix_id;
                $suffix->find();
                $suffix->fetch();
                $suffix = $suffix->name; 
            } else {
                $suffix = "";
            }
        }

        if ( $overwrite ) {
            $gender = CRM_Utils_Array::value( 'gender', $values );
        } else {
            $gender = null;
        }
        
        if ( $gender ) {
            $genderDao = & new CRM_Core_DAO_Gender();
            $genderDao->name = $gender; 
            $genderDao->find(true);
            $values['gender_id'] = $genderDao->id;
        } 

        if ($lastName != "" && $firstName != "") {
            $values['sort_name'] = "$lastName, $firstName";
        } else if ( $lastName != "" ){
            $values['sort_name'] = "$lastName";
        } else if ( $firstName != "" ) {
            $values['sort_name'] = "$firstName";
        }
        $values['display_name'] = "$prefix $firstName $middleName $lastName $suffix ";
    } else if ( $contact->contact_type == 'Household' ) {
        if ($overwrite || ! isset($contact->contact_type_object->household_name)) {
            $householdName = CRM_Utils_Array::value( 'household_name', $values );
        } else {
            $householdName = null;
        }
        if ( ! $householdName ) {
            $householdName = isset( $contact->contact_type_object->household_name ) ? $contact->contact_type_object->household_name : '';
        }
        $values['sort_name'] = $householdName;
    } else {
        if ($overwrite || ! isset($contact->contact_type_object->organization_name)) {
            $organizationName = CRM_Utils_Array::value( 'organization_name', $values );
        } else {
            $organizationName = null;
        }
        if ( ! $organizationName ) {
            $organizationName = isset( $contact->contact_type_object->organization_name ) ? $contact->contact_type_object->organization_name : '';
        }
        $values['sort_name'] = $organizationName;
    }
    _crm_update_object( $contact, $values );

    _crm_update_object( $contact->contact_type_object, $values );


    if ( ! isset( $contact->location ) ) {
        $contact->location    = array( );
    }

    if ( ! array_key_exists( 1, $contact->location ) || empty( $contact->location[1] ) ) {
        $contact->location[1] =& new CRM_Core_BAO_Location( );
    }
    
    $primary_location = null;
    foreach ($contact->location as $key => $loc) {
        if ($loc->is_primary) {
            $primary_location = $key;
            break;
        }
    }
  
    if (is_array($values['location'])) {
    
    foreach ($values['location'] as $updateLocation) {
        $emptyBlock = $contactLocationBlock = null;
        
        /* Scan the location array for the correct block to update */
        foreach ($contact->location as $key => $loc) {
            if ($loc->location_type_id == $updateLocation['location_type_id']) {
                $contactLocationBlock = $key;
                break;
            } else if (! isset($loc->location_type_id)) {
                $emptyBlock = $key;
            }
        }
        
        if ($contactLocationBlock == null) {
            if ($emptyBlock != null) {
                $contactLocationBlock = $emptyBlock;
            } else {
                /* no matching blocks and no empty blocks, make a new one */
                $contact->location[] =& new CRM_Core_BAO_Location();
                $contactLocationBlock = count($contact->location);
            }
        }
        
        $updateLocation['entity_id']    = $contact->id;
        $updateLocation['entity_table'] = CRM_Contact_BAO_Contact::getTableName();
        
        /* If we're not overwriting, copy old data back before updating */
        if (! $overwrite) {
            _crm_update_from_object($contact->location[$contactLocationBlock], $updateLocation, true);
        }
        
        /* Make sure we only have one primary location */
        if ($primary_location == null && $updateLocation['is_primary']) {
            $primary_location = $contactLocationBlock;
        } else if ($primary_location != $contactLocationBlock) {
            $updateLocation['is_primary'] = false;
        }

        _crm_update_object( $contact->location[$contactLocationBlock], $updateLocation );
    
        if ( ! isset( $contact->location[$contactLocationBlock]->address ) ) {
            $contact->location[$contactLocationBlock]->address =& new CRM_Core_BAO_Address( );
        }
        $updateLocation['address']['location_id'] = $contact->location[$contactLocationBlock]->id;

        if ($updateLocation['address']['state_province']) {
            $state_province       = & new CRM_Core_DAO_StateProvince();
            $state_province->name = $updateLocation['address']['state_province'];
            if ( ! $state_province->find(true) ) {
                $state_province->name = null;
                $state_province->abbreviation = $updateLocation['address']['state_province'];
                $state_province->find(true);
            }
            $updateLocation['address']['state_province_id'] = $state_province->id;
        }

        if ($updateLocation['address']['country']) {
            $country       = & new CRM_Core_DAO_Country();
            $country->name = $updateLocation['address']['country'];
            if ( ! $country->find(true) ) {
                $country->name = null;
                $country->iso_code = $updateLocation['address']['country'];
                $country->find(true);
            }
            $updateLocation['address']['country_id'] = $country->id;
        }
        
        if (! $overwrite) {
            _crm_update_from_object($contact->location[$contactLocationBlock]->address, $updateLocation['address'], true);
        }
        _crm_update_object( $contact->location[$contactLocationBlock]->address, $updateLocation['address'] );
        
        $blocks = array( 'Email', 'IM' );
        foreach ( $blocks as $block ) {
            $name = strtolower($block);
            if ( ! is_array($updateLocation[$name]) ) {
                continue;
            }
          
            if ( ! isset( $contact->location[$contactLocationBlock]->$name ) ) {
                $contact->location[$contactLocationBlock]->$name = array( );
            }
            
            $primary = null;
            foreach ($contact->location[$contactLocationBlock]->$name as $key => $value) {
                if ($value->is_primary) {
                    $primary = $key;
                    break;
                }
            }

            $propertyBlock = 1;
            foreach ($updateLocation[$name] as $property) {
               
                if (! isset($contact->location[$contactLocationBlock]->{$name}[$propertyBlock])) {
                    require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Core_BAO_" . $block) . ".php");
                    eval( '$contact->location[$contactLocationBlock]->{$name}[$propertyBlock] =& new CRM_Core_BAO_' . $block . '( );' );
                }
                
                $property['location_id'] = $contact->location[$contactLocationBlock]->id;
        
                if (! $overwrite) {
                    _crm_update_from_object($contact->location[$contactLocationBlock]->{$name}[$propertyBlock], $property, true);
                }

                if ($primary == null && $property['is_primary']) {
                    $primary = $propertyBlock;
                } else if ($primary != $propertyBlock) {
                    $property['is_primary'] = false;
                }
                
                _crm_update_object( $contact->location[$contactLocationBlock]->{$name}[$propertyBlock], $property );
                $propertyBlock++;
            }
        }

        /* handle multiple phones */
        if (is_array($updateLocation['phone'])) {
        
            if (! isset($contact->location[$contactLocationBlock]->phone)) {
                $contact->location[$contactLocationBlock]->phone = array();
            }

            $primary_phone = null;
            foreach ($contact->location[$contactLocationBlock]->phone as $key => $value) {
                if ($value->is_primary) {
                    $primary_phone = $key;
                    break;
                }
            }
            
            foreach ($updateLocation['phone'] as $phone) {
                /* scan through the contact record for matching phone type at this location */
                $contactPhoneBlock = null;
                foreach ($contact->location[$contactLocationBlock]->phone as $key => $contactPhoneBlock) {
                    if ($contactPhoneBlock->phone_type_id == $phone['phone_type_id']) {
                        $contactPhoneBlock = $key;
                        break;
                    }
                }
                if ($contactPhoneBlock == null) {
                    if (empty($contact->location[$contactLocationBlock]->phone)) {
                        $contactPhoneBlock = 1;
                    } else {
                        $contactPhoneBlock = count($contact->location[$contactLocationBlock]->phone) + 1;
                    }
                    $contact->location[$contactLocationBlock]->phone[$contactPhoneBlock] =& new CRM_Core_BAO_Phone();
                }
    
                $phone['location_id'] = $contact->location[$contactLocationBlock]->id;
                if (! $overwrite) {
                    _crm_update_from_object($contact->location[$contactLocationBlock]->phone[$contactPhoneBlock], $phone, true);
                }
                
                if ($primary_phone == null && $phone['is_primary']) {
                    $primary_phone = $contactPhoneBlock;
                } else if ($primary_phone != $contactPhoneBlock) {
                    $phone['is_primary'] = false;
                }
                
                _crm_update_object($contact->location[$contactLocationBlock]->phone[$contactPhoneBlock], $phone);
            }
        }
        
    }

    }

    
    /* Custom data */
    if (is_array($values['custom'])) {

    foreach ($values['custom'] as $customValue) {
        /* get the field for the data type */
        $field = CRM_Core_BAO_CustomValue::typeToField($customValue['type']);
        if (! $field) {
            /* FIXME failure! */
            continue;
        }
        
        /* adjust the value if it's boolean */
        if ($customValue['type'] == 'Boolean') {
            $value = CRM_Utils_String::strtobool($customValue['value']);
        } else {
            $value = $customValue['value'];
        }

        
        /* look for a matching existing custom value */
        $match = false;

        

        
        foreach ($contact->custom_values as $cv) {
            if ($cv->custom_field_id == $customValue['custom_field_id']) {
                /* match */
                $match = true;
                if ($overwrite) {
                    $cv->$field = $value;
                    $cv->save();
                    break;
                }
            }
        }

        if (! $match) {
            /* no match, so create a new CustomValue */
            $cvParams = array(
                    'entity_table' => 'civicrm_contact',
                    'entity_id' => $contact->id,
                    'value' => $value,
                    'type' => $customValue['type'],
                    'custom_field_id' => $customValue['custom_field_id'],
                );
            CRM_Core_BAO_CustomValue::create($cvParams);
        }
    }

    }

    return $contact;
}

function _crm_update_contribution($contribution, $values, $overwrite = true)
{
    CRM_Contribute_BAO_Contribution::resolveDefaults($values, true);

    if (!$overwrite) {
        _crm_update_from_object($contribution, $values, true, true);
    }
    _crm_update_object($contribution, $values);

    return $contribution;
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
 * @return bool|CRM_Utils_Error
 * @access public
 */
function _crm_check_history_params(&$params, $type='Activity')
{
    static $required = array('entity_id');
    
    // cannot create a contact with empty params
    if (empty($params)) {
        return _crm_error(ts('Input Parameters empty'));
    }

    $valid = true;
    foreach ($required as $requiredField) {
        if (!CRM_Utils_Array::value($requiredField, $params)) {
            $valid = false;
            $error .= "$requiredField "; 
        }
    }

    if (!$valid) {
        return _crm_error(ts("Required fields $error not found for history"));
    }
    return true;
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
 * @return bool|CRM_Utils_Error
 * @access public
 */
function _crm_check_required_fields(&$params, $daoName)
{
    if ( ( $params['extends'] == 'Activity' || 
           $params['extends'] == 'Phonecall'  || 
           $params['extends'] == 'Meeting'    || 
           $params['extends'] == 'Group'      || 
           $params['extends'] == 'Contribution' 
           ) && 
         ( $params['style'] == 'Tab' ) ) {
        return _crm_error(ts("Can not create Custom Group in Tab for ". $params['extends']));
    }
       
    require_once(str_replace('_', DIRECTORY_SEPARATOR, $daoName) . ".php");
    $dao =& new $daoName();
    $fields = $dao->fields();
    
    //eval('$dao =& new CRM_Core_DAO_' . $type . 'History();');
    
    
    $missing = array();
    foreach ($fields as $k => $v) {
        if ($k == 'id') {
            continue;
        }
        //CRM_Core_Error::debug('Check Field Params', $params);
        
        if ($v['required'] && !(isset($params[$k]))) {
            $missing[] = $k;
        }
    }

    if (!empty($missing)) {
        return _crm_error(ts("Required fields ". implode(',', $missing) . " for $daoName are not found"));
    }

    return true;
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
function _crm_add_formatted_param(&$values, &$params) {
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
 
    if (isset($values['contact_type'])) {
        /* we're an individual/household/org property */
        if (!isset($fields[$values['contact_type']])) {
            require_once(str_replace('_', DIRECTORY_SEPARATOR, 
                'CRM_Contact_DAO_' .  $values['contact_type']) . '.php');
            eval(
                '$fields['.$values['contact_type'].'] =& 
                    CRM_Contact_DAO_'.$values['contact_type'].'::fields();'
            );
        }
        
        _crm_store_values( $fields[$values['contact_type']], $values, $params );
        return true;
    }
    
    if (isset($values['individual_prefix'])) {
        $params['prefix'] = $values['individual_prefix'];
        return true;
    }

    if (isset($values['individual_suffix'])) {
        $params['suffix'] = $values['individual_suffix'];
        return true;
    }

    if (isset($values['gender'])) {
        $params['gender'] = $values['gender'];
        return true;
    }
    
    if (isset($values['preferred_communication_method'])) {
        require_once 'CRM/Core/OptionGroup.php';
        $preffComm = $comm = array();
        $preffComm    = explode(',' , $values['preferred_communication_method']);
        $optionValues = CRM_Core_OptionGroup::values('preferred_communication_method',true);
        foreach( $preffComm as $v ) {
            if(array_key_exists( trim($v) ,$optionValues )) {
                $comm[trim($v)] = $optionValues[trim($v)];
            }
        }
        $params['preferred_communication_method'] = implode( CRM_Core_BAO_CustomOption::VALUE_SEPERATOR , $comm );
        return true;
    }
    
    
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
        //add location name
        if (isset($values['name'])) { 
            $params['location'][$locBlock]['name'] = $values['name'];
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
            
            _crm_store_values($fields['Phone'], $values,
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
            _crm_store_values($fields['Email'], $values,
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
            
            if (!isset($fields['IM'])) {
                $fields['IM'] = CRM_Core_DAO_IM::fields();
            }
            
            _crm_store_values($fields['IM'], $values,
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
        
        _crm_store_values($fields['Address'], $values,
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
        _crm_store_values($fields['Note'], $values, $params['note'][$noteBlock]);

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
                return _crm_error('Invalid custom field ID');
            }
            
            if (!isset($params['custom'])) {
                $params['custom'] = array();
            }
            // fixed for Import
            
            $newMulValues = array();
            if ( $fields['custom'][$customFieldID][3] == 'CheckBox' || $fields['custom'][$customFieldID][3] =='Multi-Select') {
                $value = str_replace("|",",",$value);
                $mulValues = explode( ',' , $value );
                $custuomOption = CRM_Core_BAO_CustomOption::getCustomOption($customFieldID, true);
                foreach( $mulValues as $v1 ) {
                    foreach( $custuomOption as $v2 ) {
                        if ( strtolower($v2['label']) == strtolower(trim($v1)) ) {
                            $newMulValues[] = $v2['value'];
                        }
                    }
                }
                $value = implode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR,$newMulValues);
            } else if( $fields['custom'][$customFieldID][3] == 'Select' || $fields['custom'][$customFieldID][3] == 'Radio' ) {
                $custuomOption = CRM_Core_BAO_CustomOption::getCustomOption($customFieldID, true);
                foreach( $custuomOption as $v2 ) {
                    if ( strtolower($v2['label']) == strtolower(trim($value)) ) {
                        $value = $v2['value'];
                        break;
                    }
                }

            }
            
            $customBlock = count($params['custom']) + 1;
            $params['custom'][$customBlock] = array(
                'custom_field_id'    => $customFieldID,
                'value' => $value,
                'type' => $fields['custom'][$customFieldID][2],
                'name' => $fields['custom'][$customFieldID][0]
            );
        }
    }
    
    /* Finally, check for contact fields */
    if (!isset($fields['Contact'])) {
        $fields['Contact'] =& CRM_Contact_DAO_Contact::fields( );
    }
    _crm_store_values( $fields['Contact'], $values, $params );
}

/**
 * This function adds the contribution variable in $values to the
 * parameter list $params.  For most cases, $values should have length 1.
 *
 * @param array  $values    The variable(s) to be added
 * @param array  $params    The structured parameter list
 * 
 * @return bool|CRM_Utils_Error
 * @access public
 */
function _crm_add_formatted_contrib_param(&$values, &$params) {

    /* Cache the various object fields */
    static $fields = null;

    if ($fields == null) {
        $fields = array();
    }
    //print_r($values); 
    //print_r($params);
    
    if (isset($values['contribution_type'])) {
        $params['contribution_type'] = $values['contribution_type'];
        return true;
    }

    if (isset($values['payment_instrument'])) {
        $params['payment_instrument'] = $values['payment_instrument'];
        return true;
    }

    /* Check for custom field values */
    if ($fields['custom'] == null) {
        $fields['custom'] =& CRM_Core_BAO_CustomField::getFields('Contribution');
    }
    
    foreach ($values as $key => $value) {
        if ($customFieldID = CRM_Core_BAO_CustomField::getKeyID($key)) {
            /* check if it's a valid custom field id */
            if (!array_key_exists($customFieldID, $fields['custom'])) {
                return _crm_error('Invalid custom field ID');
            }
            
            if (!isset($params['custom'])) {
                $params['custom'] = array();
            }
            
            // fixed for Import
            $newMulValues = array();
            if ( $fields['custom'][$customFieldID][3] == 'CheckBox' || $fields['custom'][$customFieldID][3] =='Multi-Select') {
                $value = str_replace("|",",",$value);
                $mulValues = explode( ',' , $value );
                $custuomOption = CRM_Core_BAO_CustomOption::getCustomOption($customFieldID, true);
                foreach( $mulValues as $v1 ) {
                    foreach( $custuomOption as $v2 ) {
                        if ( strtolower($v2['label']) == strtolower(trim($v1)) ) {
                            $newMulValues[] = $v2['value'];
                        }
                    }
                }
                $value = implode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR,$newMulValues);
            } else if( $fields['custom'][$customFieldID][3] == 'Select' || $fields['custom'][$customFieldID][3] == 'Radio' ) {
                $custuomOption = CRM_Core_BAO_CustomOption::getCustomOption($customFieldID, true);
                foreach( $custuomOption as $v2 ) {
                    if ( strtolower($v2['label']) == strtolower(trim($value)) ) {
                        $value = $v2['value'];
                        break;
                    }
                }

            }
            
            $customBlock = count($params['custom']) + 1;
            $params['custom'][$customBlock] = array(
                'custom_field_id'    => $customFieldID,
                'value' => $value,
                'type' => $fields['custom'][$customFieldID][2],
                'name' => $fields['custom'][$customFieldID][0]
            );
        }
    }
    
    /* Finally, check for contribution fields */
    if (!isset($fields['Contribution'])) {
        $fields['Contribution'] =& CRM_Contribute_DAO_Contribution::fields( );
    }
    _crm_store_values( $fields['Contribution'], $values, $params );
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
function _crm_required_formatted_contact(&$params) {
    
    if (! isset($params['contact_type'])) {
        return _crm_error('No contact type specified');
    }
    
    switch ($params['contact_type']) {
        case 'Individual':
            if (isset($params['first_name']) && isset($params['last_name'])) {
                return true;
            }
            if (is_array($params['location'])) {
                foreach ($params['location'] as $location) {
                    if (is_array($location['email']) 
                        && count($location['email']) >= 1) {
                        return true;
                    }
                }
            }
            break;
        case 'Household':
            if (isset($params['household_name'])) {
                return true;
            }
            break;
        case 'Organization':
            if (isset($params['organization_name'])) {
                return true;
            }
            break;
        default:
            return 
            _crm_error('Invalid Contact Type: ' . $params['contact_type'] );
    }

    return _crm_error('Missing required fields');
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
function _crm_required_formatted_contribution(&$params) {
    if (isset($params['contact_id'])) {
        return true;
    }
    return _crm_error('Missing required field: contact_id');
}

/**
 * Validate a formatted contact parameter list.
 *
 * @param array $params  Structured parameter list (as in crm_format_params)
 *
 * @return bool|CRM_Core_Error
 * @access public
 */
function _crm_validate_formatted_contact(&$params) {

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
            return _crm_error( 'No valid email address');
        }
    }

    /* Validate custom data fields */
    if (is_array($params['custom'])) {
        foreach ($params['custom'] as $key => $custom) {
            if (is_array($custom)) {
                $valid = CRM_Core_BAO_CustomValue::typecheck(
                    $custom['type'], $custom['value']);
                if (! $valid) {
                    return _crm_error('Invalid value for custom field \'' .
                        $custom['name']. '\'');
                }
                if ( $custom['type'] == 'Date' ) {
                    $params['custom'][$key]['value'] = str_replace( '-', '', $params['custom'][$key]['value'] );
                }
            }
        }
    }

    return true;
}

/**
 * Validate a formatted contribution parameter list.
 *
 * @param array $params  Structured parameter list (as in crm_format_params)
 *
 * @return bool|CRM_Core_Error
 * @access public
 */
function _crm_validate_formatted_contribution(&$params) {

    static $domainID = null;
    if (!$domainID) {
        $config =& CRM_Core_Config::singleton();
        $domainID = $config->domainID();
    }
    
    foreach ($params as $key => $value) {
        switch ($key) {
        case 'contact_id':
            if (!CRM_Utils_Rule::integer($value)) {
                return _crm_error("contact_id not valid: $value");
            }
            $dao =& new CRM_Core_DAO();
            $svq = $dao->singleValueQuery("SELECT id FROM civicrm_contact WHERE domain_id = $domainID AND id = $value");
            if (!$svq) {
                return _crm_error("there's no contact with contact_id of $value");
            }
            break;
        case 'receive_date':
        case 'cancel_date':
        case 'receipt_date':
        case 'thankyou_date':
            if (!CRM_Utils_Rule::date($value)) {
                return _crm_error("$key not a valid date: $value");
            }
            break;
        case 'non_deductible_amount':
        case 'total_amount':
        case 'fee_amount':
        case 'net_amount':
            if (!CRM_Utils_Rule::money($value)) {
                return _crm_error("$key not a valid amount: $value");
            }
            break;
        case 'currency':
            if (!CRM_Utils_Rule::currencyCode($value)) {
                return _crm_error("currency not a valid code: $value");
            }
            break;
        default:
            break;
        }
    }

    /* Validate custom data fields */
    if (is_array($params['custom'])) {
        foreach ($params['custom'] as $key => $custom) {
            if (is_array($custom)) {
                $valid = CRM_Core_BAO_CustomValue::typecheck(
                    $custom['type'], $custom['value']);
                if (! $valid) {
                    return _crm_error('Invalid value for custom field \'' .
                        $custom['name']. '\'');
                }
                if ( $custom['type'] == 'Date' ) {
                    $params['custom'][$key]['value'] = str_replace( '-', '', $params['custom'][$key]['value'] );
                }
            }
        }
    }

    return true;
}
function &_crm_duplicate_formatted_contact(&$params) {
    if ( $params['contact_type'] == 'Individual') {
        require_once 'CRM/Core/BAO/UFGroup.php';
        if ( ( $ids =& CRM_Core_BAO_UFGroup::findContact( $params, null, true ) ) != null ) {
            $error =& _crm_error( "Found matching contacts: $ids",
                                  CRM_Core_Error::DUPLICATE_CONTACT, 
                                  'Fatal', $ids );
            return $error;
        }
        return true;
    } else {

        require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Contact_DAO_" . $params['contact_type']) . ".php");
        eval('$contact =& new CRM_Contact_DAO_'.$params['contact_type'].'();');

        if ( $params['contact_type'] == 'Household' ) {
            $contact->household_name = $params['household_name'];
        } else {
            $contact->organization_name = $params['organization_name'];
        }

        if ( $contact->find( true ) ) {
            $error =& _crm_error( "Found matching contacts: {$contact->contact_id}", CRM_Core_Error::DUPLICATE_CONTACT, 'Fatal', $contact->contact_id );
            return $error;
        }
        return true;
    }    
}


function &_crm_duplicate_formatted_contribution(&$params) {
    require_once 'CRM/Contribute/BAO/Contribution.php';
    return CRM_Contribute_BAO_Contribution::checkDuplicate( $params,$duplicate );
}


function _crm_location_match(&$contact, &$values) {
    if (! is_array($values['location']) || empty($contact->location)) {
        return true;
    }

    foreach ($values['location'] as $loc) {
        /* count the number of locations in the contact with matching type */
        $count = 0;
        foreach ($contact->location as $contactLocation) {
            if ($contactLocation->location_type_id == $loc['location_type_id'])
            {
                $count++;
            }
        }
        if ($count > 1) {
            return false;
        }
    }
    return true;
}

/**
 * This function adds the activity history variable in $values to the
 * parameter list $params.  For most cases, $values should have length 1.
 *
 * @param array  $values    The variable(s) to be added
 * @param array  $params    The structured parameter list
 * 
 * @return bool|CRM_Utils_Error
 * @access public
 */
function _crm_add_formatted_history_param(&$values, &$params) {

    /* Cache the various object fields */
    static $fields = null;

    if ($fields == null) {
        $fields = array();
    }
    //print_r($values); 
    //print_r($params);
    
    if (isset($values['activity_type'])) {
        $params['activity_type'] = $values['activity_type'];
        return true;
    }

    if (isset($values['activity_date'])) {
        $params['activity_date'] = $values['activity_date'];
        return true;
    }

     if (isset($values['activity_id'])) {
        $params['activity_id'] = $values['activity_id'];
        return true;
    }

    /* Check for custom field values */
    if ($fields['custom'] == null) {
        $fields['custom'] =& CRM_Core_BAO_CustomField::getFields('Contribution');
    }
    
    foreach ($values as $key => $value) {
        if ($customFieldID = CRM_Core_BAO_CustomField::getKeyID($key)) {
            /* check if it's a valid custom field id */
            if (!array_key_exists($customFieldID, $fields['custom'])) {
                return _crm_error('Invalid custom field ID');
            }
            
            if (!isset($params['custom'])) {
                $params['custom'] = array();
            }
            
            // fixed for Import
            $newMulValues = array();
            if ( $fields['custom'][$customFieldID][3] == 'CheckBox' || $fields['custom'][$customFieldID][3] =='Multi-Select') {
                $value = str_replace("|",",",$value);
                $mulValues = explode( ',' , $value );
                $custuomOption = CRM_Core_BAO_CustomOption::getCustomOption($customFieldID, true);
                foreach( $mulValues as $v1 ) {
                    foreach( $custuomOption as $v2 ) {
                        if ( strtolower($v2['label']) == strtolower(trim($v1)) ) {
                            $newMulValues[] = $v2['value'];
                        }
                    }
                }
                $value = implode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR,$newMulValues);
            } else if( $fields['custom'][$customFieldID][3] == 'Select' || $fields['custom'][$customFieldID][3] == 'Radio' ) {
                $custuomOption = CRM_Core_BAO_CustomOption::getCustomOption($customFieldID, true);
                foreach( $custuomOption as $v2 ) {
                    if ( strtolower($v2['label']) == strtolower(trim($value)) ) {
                        $value = $v2['value'];
                        break;
                    }
                }

            }
            
            $customBlock = count($params['custom']) + 1;
            $params['custom'][$customBlock] = array(
                'custom_field_id'    => $customFieldID,
                'value' => $value,
                'type' => $fields['custom'][$customFieldID][2],
                'name' => $fields['custom'][$customFieldID][0]
            );
        }
    }
    
    /* Finally, check for contribution fields */
    if (!isset($fields['History'])) {
        $fields['History'] =& CRM_Core_DAO_ActivityHistory::fields( );
    }
    _crm_store_values( $fields['History'], $values, $params );
}

function _crm_initialize( ) {
    $config =& CRM_Core_Config::singleton( );
}

function &_crm_get_pseudo_constant_names( ) {
    $table = array(
                   'location_type'       => 'locationType',
                   'activity_type'       => 'activityType',
                   'individual_prefix'   => 'individualPrefix',
                   'individual_suffix'   => 'individualSuffix',
                   'gender'              => 'gender',
                   'im_provider'         => 'IMProvider',
                   'state_province'      => 'stateProvince',
                   'state_province_abbr' => 'stateProvinceAbbreviation',
                   'country'             => 'country',
                   'country_iso'         => 'countryIsoCode',
                   'tag'                 => 'tag',
                   'all_group'           => 'allGroup',
                   'group'               => 'group',
                   'uf_group'            => 'ufGroup',
                   'relationship_type'   => 'relationshipType',
                   );
    return $table;
}

?>
