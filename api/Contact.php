<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.3                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Social Source Foundation                        |
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
 | at http://www.openngo.org/faqs/licensing.html                      |
 +--------------------------------------------------------------------+
*/

/**
 * Definition of the Contact part of the CRM API. 
 * More detailed documentation can be found 
 * {@link http://objectledge.org/confluence/display/CRM/CRM+v1.0+Public+APIs
 * here}
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
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
 * 
 * <b>Tips - Creating Contacts</b>
 * <ul>
 * <li>The Contact data objects may include both identifying
 * information (e.g. last_name, organization name, etc.) and primary
 * communications data (e.g. primary email, primary phone, primary
 * postal address...).</li> 
 *
 * <li>Properties which have administratively assigned sets of values
 * (ENUM types) are passed as strings (e.g. mobile_service_provider',
 * 'im_service', etc). If an unrecognized value is passed, an error
 * will be returned.</li> 
 *
 * <li>Modules may invoke crm_get_option_values($property_name) to
 * retrieve a list of currently available values for a given
 * property.</li> 
 *
 * <li>Invoke crm_create_option_value($property_name) to add new
 * option values for a property.</li>
 * </ul>
 *
 * <i>EXAMPLE: If the available values for mobile_service_provider are
 * 'Working Assets', 'Sprint', 'Verizon' - and a value of 'Foobar' is passed,
 * an error is returned.</i>
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

    return $contact;
}


function &crm_create_contact_formatted( &$params , $onDuplicate) {
    _crm_initialize( );

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

    require_once 'CRM/Import/Parser.php';
    if ( $onDuplicate != CRM_Import_Parser::DUPLICATE_NOCHECK) {
        $error = _crm_duplicate_formatted_contact($params);
        if (is_a( $error, 'CRM_Core_Error')) {
            return $error;
        }
    }
    $ids = array();
    
    CRM_Contact_BAO_Contact::resolveDefaults($params, true);

    $contact = CRM_Contact_BAO_Contact::create( $params, $ids, 
                                                count($params['location']));
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
    $contact = crm_get_contact(array('contact_id' => $contactId));
    if ( ! $contact || is_a( $contact, 'CRM_Core_Error' ) ) {
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
 
    $contact->location =& CRM_Core_BAO_Location::getValues( $params, $defaults, $ids, 1 ); 
 
    $contact->custom_values =& CRM_Core_BAO_CustomValue::getContactValues($contact->id); 
 
    return $contact; 
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
 * @return void|CRM_Core_Error  An error if 'contact' is invalid,
 *                         permissions are insufficient, etc.
 *
 * @access public
 *
 */
function crm_delete_contact( &$contact ) {
    _crm_initialize( );

    if ( ! isset( $contact->id ) || ! isset( $contact->contact_type ) ) {
        return _crm_error( 'Invalid contact object passed in' );
    }
    
    CRM_Contact_BAO_Contact::deleteContact( $contact->id );
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
function crm_get_contacts() {
   $config =& CRM_Core_Config::singleton();
   $domainID = $config->domainID();
   $query = "SELECT * FROM civicrm_contact WHERE domain_id = $domainID";
   
   $dao =& new CRM_Core_DAO( );
   $dao->query( $query );
   $contacts = array();
   while ( $dao->fetch( ) ) {
       $contacts[$dao->id]= $dao->id;
   }
   return $contacts;
}

/**
 * Get all the groups that this contact is a member of with the given status
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
    CRM_Contact_BAO_GroupContact::getContactGroup( $contactId, $status, $numGroupContact, $count );
}

?>
