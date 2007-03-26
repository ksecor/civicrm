<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | copyright CiviCRM LLC (c) 2004-2007                                  |
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                 |
 +--------------------------------------------------------------------+
*/

/**
 * Definition of the Contact part of the CRM API. 
 * More detailed documentation can be found 
 * {@link http://objectledge.org/confluence/display/CRM/CRM+v1.0+Public+APIs
 * here}
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

/**
 * Files required for this package
 */
require_once 'api/utils.php';

require_once 'CRM/Contact/BAO/Contact.php';

/**
 * Most API functions take in associative arrays ( name => value pairs
 * as parameters. Some of the most commonly used parameters are
 * described below
 *
 * @param array $params           an associative array used in construction
                                  / retrieval of the object
 * @param array $returnProperties the limited set of object properties that
 *                                need to be returned to the caller
 *
 */

/**
 * Create a new contact.
 *
 * Creates a new contact record and returns the newly created
 * Contact object (including the contact_id property). Minimum
 * required data values for the various contact_type are:
 *
 * Individual:
 *         o 'email' OR
 *         o 'first_name' and 'last_name'
 * Household:
 *         o 'household_name'
 * Organization:
 *         o 'organization_name'
 *
 * Available properties for each type of Contact are listed in the
 * {@link http://objectledge.org/confluence/display/CRM/Data+Model#DataModel-ContactRef
 * CRM Data Model.}
 *
 * A 'duplicate contact' error is returned if an existing contact has
 * the same 'email' as its primary email address. A 'possible duplicate'
 * warning is returned if an exact match is found for all passed input
 * values.
 *
 * Tips - Creating Contacts
 * 
 * The Contact data objects may include both identifying
 * information (e.g. last_name, organization name, etc.) and primary
 * communications data (e.g. primary email, primary phone, primary
 * postal address...). 
 *
 * Properties which have administratively assigned sets of values
 * (ENUM types) are passed as strings (e.g. mobile_service_provider',
 * 'im_service', etc). If an unrecognized value is passed, an error
 * will be returned. 
 *
 * Modules may invoke crm_get_property_values($property_name) to
 * retrieve a list of currently available values for a given
 * property.
 *
 * EXAMPLE: If the available values for mobile_service_provider are
 * 'Working Assets', 'Sprint', 'Verizon' - and a value of 'Foobar' is passed,
 * an error is returned.
 *
 * @param array  $params       Associative array of property name/value
 *                             pairs to insert in new contact.
 * @param string $contact_type Which class of contact is being created.
 *            Valid values = 'Individual', 'Household', 'Organization'.
 *                            '
 *
 * @return CRM_Contact|CRM_Error Newly created Contact object
 *
 * @access public
 */
function &crm_create_contact( &$params, $contact_type = 'Individual' ) {
    _crm_initialize( );

    // return error if we do not get any params
    if (empty($params)) {
        return _crm_error( "Input Parameters empty" );
    }

    $error = _crm_check_params( $params, $contact_type );
    if (is_a($error, 'CRM_Core_Error')) {
        return $error;
    }

    $values  = array( );
    $values['contact_type'] = $contact_type;

    $error = _crm_format_params( $params, $values );
    if (is_a($error, 'CRM_Core_Error') ) {
        return $error;
    }

    $ids     = array( );
    $contact = CRM_Contact_BAO_Contact::create( $values, $ids, 1 );
    $contact->contact_id = $contact->id;
    
    return $contact;
}


function &crm_create_contact_formatted( &$params , $onDuplicate, $fixAddress = true ) {
    _crm_initialize( );

    CRM_Core_DAO::freeResult( );

    // return error if we have no params
    if ( empty( $params ) ) {
        return _crm_error( 'Input Parameters empty' );
    }

    $error = _crm_required_formatted_contact($params);
    if (is_a( $error, 'CRM_Core_Error')) {
        return $error;
    }
    
    $error = _crm_validate_formatted_contact($params);
    if (is_a( $error, 'CRM_Core_Error')) {
        return $error;
    }

    //get the prefix id etc if exists
    CRM_Contact_BAO_Contact::resolveDefaults($params, true);

    require_once 'CRM/Import/Parser.php';
    if ( $onDuplicate != CRM_Import_Parser::DUPLICATE_NOCHECK) {
        CRM_Core_Error::reset( );
        $error = _crm_duplicate_formatted_contact($params);
        if (is_a( $error, 'CRM_Core_Error')) {
            return $error;
        }
    }

    $ids = array();
    $contact = CRM_Contact_BAO_Contact::create( $params, $ids, 
                                                count($params['location']), $fixAddress);

    return $contact;
}

function &crm_replace_contact_formatted($contactId, &$params) {
    $contact = crm_get_contact(array('contact_id' => $contactId));
    if ( $contact ) {
        crm_delete_contact($contact);
    }
    return crm_create_contact_formatted($params);
}

function &crm_update_contact_formatted($contactId, &$params, $overwrite = true) {
    //$contact = crm_get_contact(array('contact_id' => $contactId));
    $contact =& CRM_Contact_BAO_Contact::check_contact_exists($contactId); 
    if ( !is_a( $contact, 'CRM_Contact_DAO_Contact' ) ) {
        return _crm_error("Could not find valid contact for: $contactId");
    }
    return _crm_update_contact($contact, $params, $overwrite);
}


/**
 * Get an existing contact.
 *
 * Returns a single existing Contact object which matches ALL property
 * values passed in $params. An error object is returned if there is
 * no match, or more than one match. This API can be used to retrieve
 * the CRM internal identifier (contact_id) based on a unique property
 * (e.g. email address). It can also be used to retrieve any desired
 * contact properties based on a known contact_id. Available
 * properties for each type of Contact are listed in the {@link
 * http://objectledge.org/confluence/display/CRM/Data+Model#DataModel-ContactRef
 * CRM Data Model.} Modules may also invoke crm_get_class_properties()
 * to retrieve all available property names, including extended
 * (i.e. custom) properties.contact of the specific type that matches
 * the input params  
 *
 * <b>Primary Location and Communication Info</b>
 *
 * <ul>
 * <li>Primary location properties (email address, phone, postal address,
 * etc.) are available in the Contact data objects. Primary email and
 * phone number are returned by default. Postal address fields and
 * primary instant messenger identifier are returned when specified in
 * $return_properties. For contacts with multiple locations, use
 * crm_get_locations() to retrieve additional location data.</li> 
 * </ul>
 *
 * @see crm_get_class_properties()
 * @see crm_get_locations()
 *
 * @example api/Contact.php
 *
 * @param array $params           Associative array of property name/value
 *                                pairs to attempt to match on.
 * @param array $returnProperties Which properties should be included in the
 *                                returned Contact object. If NULL, the default
 *                                set of properties will be included.
 *
 * @return CRM_Contact|CRM_Core_Error  Return the Contact Object if found, else
 *                                Error Object
 *
 * @access public
 *
 */
function &crm_get_contact( $params, $returnProperties = null ) {
    _crm_initialize( );

    // empty parameters ?
    if (empty($params)) {
        return _crm_error('$params is empty');
    }

    // correct parameter format ?
    if (!is_array($params)) {
        return _crm_error('$params is not an array');
    }

    if ( ! CRM_Utils_Array::value( 'contact_id', $params ) ) {
        $returnProperties = array( 'display_name' );
        list( $contacts, $options ) = crm_contact_search( $params, $returnProperties );
        if ( count( $contacts ) != 1 ) {
            return _crm_error( count( $contacts ) . " contacts matching input params." );
        }
        $contactIds = array_keys( $contacts );
        $params['contact_id'] = $contactIds[0];
    }

    $params['id'] = $params['contact_id']; 
    $ids          = array( ); 
 
    $contact =& CRM_Contact_BAO_Contact::getValues( $params, $defaults, $ids ); 
 
    if ( $contact == null || is_a($contact, 'CRM_Core_Error') || ! $contact->id ) { 
        return _crm_error( 'Did not find contact object for ' . $params['contact_id'] ); 
    } 
 
    unset($params['id']); 
     
    require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Contact_BAO_" . $contact->contact_type) . ".php"); 
    $contact->contact_type_object = 
        eval( 'return CRM_Contact_BAO_' . $contact->contact_type . '::getValues( $params, $defaults, $ids );' ); 
 
    $contact->location =& CRM_Core_BAO_Location::getValues( $params, $defaults, $ids, 2 ); //changed the location no
 
    $contact->custom_values =& CRM_Core_BAO_CustomValue::getContactValues($contact->id); 
 
    return $contact; 
}


/**
 * Fetch an existing contacts based on given search criteria
 *
 *
 * @see crm_contact_search()
 *
 * @example api/Contact.php
 *
 * @param array $params           Associative array of property name/value
 *                                pairs to attempt to match on.
 * @param array $returnProperties Which properties should be included in the
 *                                returned Contact object. If NULL, the default
 *                                set of properties will be included.
 *
 * @return CRM_Contact|CRM_Core_Error  Returns the array of contact if found, else
 *                                Error Object
 * @access public
 *
 */
function &crm_fetch_contact( $params, $returnProperties = null ) {
    _crm_initialize( );

    // empty parameters ?
    if (empty($params)) {
        return _crm_error('$params is empty');
    }

    // correct parameter format ?
    if (!is_array($params)) {
        return _crm_error('$params is not an array');
    }
    require_once 'api/crm.php';
    list( $contacts, $options ) = crm_contact_search( $params, $returnProperties );
    if ( count( $contacts ) != 1 ) {
        return _crm_error( count( $contacts ) . " contacts matching input params." );
    }
    
    $contacts = array_values( $contacts );
    return $contacts[0];
}

/**
 * Update a specified contact.
 *
 * Updates a contact with the values passed in the 'params' array. An
 * error is returned if an invalid contact is passed, or an invalid
 * property name or property value is included in 'params'. An error
 * is also returned if the processing the update would violate data
 * integrity rules, e.g. if a primary 'email' value is passed which is
 * the same as the primary email of another contact.
 *
 * <b>Clearing Property Values with Update APIs</b>
 * 
 * <ul>
 * <li>For any CRM 'update' API...to clear the value of an existing
 * property (i.e. set it to empty) - pass the property name in the
 * $params array with a NULL value.</li>
 * </ul>
 *
 * @param CRM_Contact $contact A valid Contact object
 * @param array       $params  Associative array of property
 *                             name/value pairs to be updated. 
 *  
 * @return CRM_Contact|CRM_Core_Error  Return the updated Contact Object else
 *                                Error Object (if integrity violation)
 *
 * @access public
 *
 */
function &crm_update_contact( &$contact, $params ) {
    _crm_initialize( );

    $values = array( );

    if ( ! isset( $contact->id ) || ! isset( $contact->contact_type ) ) {
        return _crm_error( 'Invalid contact object passed in' );
    }

    $values['contact_type'] = $contact->contact_type;
    $error = _crm_format_params( $params, $values );
    if ( is_a($error, 'CRM_Core_Error') ) {
        return $error;
    }

    $contact = _crm_update_contact( $contact, $values );
    

    return $contact;
}

/**
 * Delete a specified contact.
 *
 * <b>Versioning and Un-delete</b>
 *
 * <ul>
 * <li>CRM will implement a 'Versioning' utility which will include
 * structural support for 'un-delete' operations. The API and UI
 * interfaces for 'un-delete' will probably be available in v1.x.</li>
 * </ul>
 *
 * @param CRM_Contact $contact Contact object to be deleted
 *
 * @userID int $userId of the Logged-in id of the Civicrm.
 *
 * @return void|CRM_Core_Error  An error if 'contact' is invalid,
 *                         permissions are insufficient, etc.
 *
 * @access public
 *
 */
function crm_delete_contact( &$contact, $userId = null ) {
    _crm_initialize( );

    if ( ! isset( $contact->id ) || ! isset( $contact->contact_type ) ) {
        return _crm_error( 'Invalid contact object passed in' );
    }
    
    CRM_Contact_BAO_Contact::deleteContact( $contact->id, $userId );
}

/** 
 * Get all the groups that a contact is a member of with the given status
 * 
 * @param CRM_Contact $contact Contact object whose groups we are interested in
 *  
 * @return void|CRM_Core_Error  An error if 'contact' is invalid, 
 *  
 * @access public 
 * 
 */ 
 function crm_contact_groups( &$contact, $status = null ) {  
    _crm_initialize( ); 

    if ( ! isset( $contact->id ) ) {
        return _crm_error( 'Invalid contact object passed in' ); 
    }

    require_once 'CRM/Contact/BAO/GroupContact.php';
    $values =& CRM_Contact_BAO_GroupContact::getContactGroup( $contact->id, $status, null, false );
    
    $groups = array( );
    foreach ( $values as $value ) {
        $group =& new CRM_Contact_DAO_Group( );
        foreach ( $value as $k => $v ) {
            if ( ! empty( $v ) ) {
                $group->$k = $v;
            }
        }
        $groups[$group->id] = $group;
    }

    return $groups;
}


/** 
 * Get all the contact_ids 
 * 
 * @return $contacts array of contact ids 
 *  
 * @access public 
 * 
 */ 
function crm_get_contacts( $contactType = null ) {
   $config =& CRM_Core_Config::singleton();
   $domainID = $config->domainID();
   if ( $contactType ) {
       $query = "SELECT id FROM civicrm_contact WHERE domain_id = $domainID and  contact_type ='".$contactType."'";
   } else {
       $query = "SELECT * FROM civicrm_contact WHERE domain_id = $domainID";
   }
   $dao =& new CRM_Core_DAO( );
   $dao->query( $query );
   $contacts = array();
   while ( $dao->fetch( ) ) {
       $contacts[$dao->id]= $dao->id;
   }
   return $contacts;
}

/**
 * Get all the groups that this contact is a member of with the given status. This is used by
 * the Drupal module / organic group integration. Not sure why they needed a differnt twist
 * on the crm_contact_groups api, so here for backward compatibility
 * 
 * @param int     $contactId       contact id  
 * @param string  $status          state of membership 
 * @param int     $numGroupContact number of groups for a contact that should be shown 
 * @param boolean $count           true if we are interested only in the count 
 * 
 * @return array (reference )|int $values the relevant data object values for the contact or 
                                  the total count when $count is true 
 * 
 * $access public 
 */ 
function crm_contact_get_groups( $contactId, $status = null, $numGroupContact = null, $count = false ) {
    require_once 'CRM/Contact/BAO/GroupContact.php';
    return CRM_Contact_BAO_GroupContact::getContactGroup( $contactId, $status, $numGroupContact, $count );
}

/**
 * function to retrieve and update geocoding (lat/long) values for a specified 'address object' and 'contact object'  using the configured geo-coding method.
 * 
 * @param object  $object     valid address/contact object  
 *
 * @return null 
 * 
 * $access public 
 */ 
function crm_fix_address($object) {

    require_once 'CRM/Utils/Geocode/Yahoo.php';
    
    if ( is_a($object, 'CRM_Core_BAO_Address') ) {
        
        $temp = array( );
        foreach ( $object as $name => $value ) {
            $temp[$name] = $value;
        }
        
        $found = CRM_Utils_Geocode_Yahoo::format( $temp );
        $object->copyValues($temp);
        
        // code for saving the changes in database
        $params = array( );
        $ids    = array( );
        
        foreach ($object  as $name => $value ) {
            $params['location'][1]['address'][$name] = $value;
        }
        
        $ids['location'][1]['id'] = $object->location_id;
        $ids['location'][1]['address'] = $object->id;
        
        CRM_Core_BAO_Address::add($params, $ids, 1);
        
    } else if ( is_a($object, 'CRM_Contact_BAO_Contact') ) {
        
        $params = $ids = $temp = array();
        $locations =& crm_get_locations($object);
        $locNo = 1;
        
        foreach ($locations as $loc => $value) {
            $addObject =& $locations[$locNo]->address;
            foreach ( $addObject as $name => $value ) {
                $temp[$name] = $value;
            }
            if (CRM_Utils_Geocode_Yahoo::format( $temp )) {
                $params['location'][$locNo]['address']['geo_code_1'] = $temp['geo_code_1']; 
                $params['location'][$locNo]['address']['geo_code_2'] = $temp['geo_code_2']; 
                
                $ids['location'][$locNo]['id']      = $object->location[$locNo]->id;
                $ids['location'][$locNo]['address'] = $object->location[$locNo]->address->id;
                $locationId = $locNo;
                CRM_Core_BAO_Address::add($params, $ids, $locationId);
            }
            $locNo++;
        }
    } else { 
        return _crm_error( 'Please pass valid contact / address object.' ); 
    }
}

function &crm_get_property_values( $name ) {
    static $nameLookup = null;

    if ( ! $nameLookup ) {
        $nameLookup =& _crm_get_pseudo_constant_names( );
    }
    
    if ( ! CRM_Utils_Array::value( $name, $nameLookup ) ) {
        return null;
    }
    
    return eval( 'return CRM_Core_PseudoConstant::' . $nameLookup[$name] . '( );' );
}

/**
 * function to add/edit/register contacts through profile.
 *
 * @params  array  $params       Array of profile fields to be edited/added.
 * @params  array  $fields       array of fields from UFGroup
 * @params  int    $ufGroupId    uf group id
 * @params  int    $contactID    contact_idof the contact to be edited/added.
 * @params  int    $addToGroupID specifies the default group to which contact is added.
 *
 * @return null
 * @access public
 */

function crm_create_profile_contact( $params, $fields, $ufGroupId, $contactID = null, $addToGroupID = null ) {
    require_once 'CRM/Contact/BAO/Contact.php';
    CRM_Contact_BAO_Contact::createProfileContact($params, $fields, $contactID, $addToGroupID, $ufGroupId );

}

?>
