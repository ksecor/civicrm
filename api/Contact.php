<?php
/*
 +----------------------------------------------------------------------+
 | CiviCRM version 1.0                                                  |
 +----------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                    |
 +----------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                      |
 |                                                                      |
 | CiviCRM is free software; you can redistribute it and/or modify it   |
 | under the terms of the Affero General Public License Version 1,      |
 | March 2002.                                                          |
 |                                                                      |
 | CiviCRM is distributed in the hope that it will be useful, but       |
 | WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
 | See the Affero General Public License for more details at            |
 | http://www.affero.org/oagpl.html                                     |
 |                                                                      |
 | A copy of the Affero General Public License has been been            |
 | distributed along with this program (affero_gpl.txt)                 |
 +----------------------------------------------------------------------+
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
require_once 'PEAR.php';

require_once 'CRM/Core/Error.php';
require_once 'CRM/Utils/Array.php';

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

    //CRM_Core_Error::le_function();

    // return error if we do not get any params
    if (empty($params)) {
        //CRM_Core_Error::debug_log_message('breakpoint 10');
        //CRM_Core_Error::ll_function();
        return _crm_error( "Input Parameters empty" );
    }
    
    $error = _crm_check_params( $params, $contact_type );
    if (is_a($error, CRM_Core_Error)) {
        //CRM_Core_Error::debug_log_message('breakpoint 20');
        //CRM_Core_Error::debug_var('error', $error);
        //CRM_Core_Error::ll_function();
        return $error;
    }

    $values  = array( );
    $values['contact_type'] = $contact_type;

    $error = _crm_format_params( $params, $values );
    if (is_a($error, CRM_Core_Error) ) {
        //CRM_Core_Error::debug_log_message('breakpoint 30');
        //CRM_Core_Error::debug_var('error', $error);
        //CRM_Core_Error::ll_function();
        return $error;
    }

    $ids     = array( );
    $contact = CRM_Contact_BAO_Contact::create( $values, $ids, 1 );
    //CRM_Core_Error::debug_log_message('breakpoint 40');
    //CRM_Core_Error::ll_function();

    return $contact;
}


function &crm_create_contact_formatted( &$params ) {
    //CRM_Core_Error::le_function();
    // return error if we have no params
    if ( empty( $params ) ) {
        return _crm_error( 'Input Parameters empty' );
    }

    $error = _crm_required_formatted_contact($params);
    if (is_a( $error, CRM_Core_Error)) {
        return $error;
    }
    
    $error = _crm_validate_formatted_contact($params);
    if (is_a( $error, CRM_Core_Error)) {
        return $error;
    }
    
    $error = _crm_duplicate_formatted_contact($params);
    if (is_a( $error, CRM_Core_Error)) {
        return $error;
    }
    
    $ids = array();
    
    CRM_Contact_BAO_Contact::resolveDefaults($params, true);

    $contact = CRM_Contact_BAO_Contact::create( $params, $ids, 
        count($params['location']));
    return $contact;
}

function &crm_replace_contact_formatted($contactId, &$params) {
    //CRM_Core_Error::le_function();
    $contact = crm_get_contact(array('contact_id' => $contactId));
    crm_delete_contact($contact);
    return crm_create_contact_formatted($params);
}

function &crm_update_contact_formatted($contactId, &$params, $overwrite = true) {
    //CRM_Core_Error::le_function();
    $contact = crm_get_contact(array('contact_id' => $contactId));
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
    //CRM_Core_Error::le_function();

    // empty parameters ?
    if (empty($params)) {
        //CRM_Core_Error::debug_log_message('breakpoint 10');
        //CRM_Core_Error::ll_function();
        return _crm_error('$params is empty');
    }

    //CRM_Core_Error::debug_log_message('breakpoint 20');

    // correct parameter format ?
    if (!is_array($params)) {
        //CRM_Core_Error::debug_log_message('breakpoint 30');
        //CRM_Core_Error::ll_function();
        return _crm_error('$params is not an array');
    }

    // if id is not present, get contact id first
    if (!$params['contact_id']) {
        //CRM_Core_Error::debug_log_message('breakpoint 40');
        $contactId = _crm_get_contact_id($params);
        if (is_a($contactId, CRM_Core_Error)) {
            //CRM_Core_Error::debug_log_message('breakpoint 50');
            //CRM_Core_Error::ll_function();
            return $contactId;
        }

        //CRM_Core_Error::debug_log_message('breakpoint 60');
        $params['contact_id'] = $contactId;
    }

    //CRM_Core_Error::debug_log_message('breakpoint 70');
    $params['id'] = $params['contact_id'];
    $ids          = array( );

    //CRM_Core_Error::debug_var('params', $params);
    $contact = CRM_Contact_BAO_Contact::getValues( $params, $defaults, $ids );
    //CRM_Core_Error::debug_var('contact', $contact);

    if ( $contact == null || is_a($contact, CRM_Core_Error) || ! $contact->id ) {
        //CRM_Core_Error::debug_log_message('breakpoint 80');
        //CRM_Core_Error::ll_function();
        return _crm_error( 'Did not find contact object for ' . $params['contact_id'] );
    }

    //CRM_Core_Error::debug_log_message('breakpoint 90');
    unset($params['id']);
    
    require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Contact_BAO_" . $contact->contact_type) . ".php");
    $contact->contact_type_object =
        eval( 'return CRM_Contact_BAO_' . $contact->contact_type . '::getValues( $params, $defaults, $ids );' );

    $contact->location = CRM_Contact_BAO_Location::getValues( $params, $defaults, $ids, 1 );

    $contact->custom_values = CRM_Core_BAO_CustomValue::getContactValues($contact->id);

    //CRM_Core_Error::debug_var('contact', $contact);
    //CRM_Core_Error::debug_log_message('breakpoint 100');
    //CRM_Core_Error::ll_function();
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
    $values = array( );

    if ( ! isset( $contact->id ) || ! isset( $contact->contact_type ) ) {
        return _crm_error( 'Invalid contact object passed in' );
    }

    $values['contact_type'] = $contact->contact_type;
    $error = _crm_format_params( $params, $values );
    if ( is_a($error, CRM_Core_Error) ) {
        return $error;
    }

    $error = _crm_update_contact( $contact, $values );
    if ( is_a($error, CRM_Core_Error) ) {
        return $error;
    }

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
    
    if ( ! isset( $contact->id ) || ! isset( $contact->contact_type ) ) {
        return _crm_error( 'Invalid contact object passed in' );
    }
    
    CRM_Contact_BAO_Contact::deleteContact( $contact->id );
}


/**
 * Get unique contact id for input parameters.
 * Currently the parameters allowed are
 *
 * 1 - email
 * 2 - phone number
 * 3 - city
 *
 * @param array $param - array of input parameters
 *
 * @return $contactId|CRM_Error if unique id available
 *
 * @access public
 *
 */
function _crm_get_contact_id($params)
{
    //CRM_Core_Error::le_function();
    //CRM_Core_Error::debug_var('params', $params);

    if (!isset($params['email']) && !isset($params['phone']) && !isset($params['city'])) {
        //CRM_Core_Error::debug_log_message('$params must contain either email, phone or city to obtain contact id');
        //CRM_Core_Error::ll_function();
        return _crm_error( '$params must contain either email, phone or city to obtain contact id' );
    }


    $queryString = $select = $from = $where = '';

    $select = 'SELECT civicrm_contact.id';
    $from   = ' FROM civicrm_contact, civicrm_location';
    $andArray = array();

    $andArray[] = "civicrm_contact.id = civicrm_location.contact_id";

    if (isset($params['email'])) {// is email present ?
        $from .= ', civicrm_email';
        $andArray[] = "civicrm_location.id = civicrm_email.location_id";
        $andArray[] = "civicrm_email.email = '" . $params['email'] . "'";
    }

    if (isset($params['phone'])) { // is phone present ?
        $from .= ', civicrm_phone';
        $andArray[] = 'civicrm_location.id = civicrm_phone.location_id';
        $andArray[] = "civicrm_phone.phone = '" . $params['phone'] . "'";
    }

    if (isset($params['city'])) { // is city present ?
        $from .= ', civicrm_address';
        $andArray[] = 'civicrm_location.id = civicrm_address.location_id';
        $andArray[] = "civicrm_address.city = '" . $params['city'] . "'";
    }

    $where = " WHERE " . implode(" AND ", $andArray);

    $queryString = $select . $from . $where;
    //CRM_Core_Error::debug_var('queryString', $queryString);

    $dao = new CRM_Core_DAO();

    $dao->query($queryString);
    $count = 0;
    while($dao->fetch()) {
        $count++;
         if ($count > 1) {

        return _crm_error( 'more than one contact id matches $params' );
    }

    }
    //$result = $dao->getDatabaseResult();
    //$rows = $result->fetchRow();
    
    if ($count == 0) {
        //CRM_Core_Error::debug_log_message('more than one contact id matches $params  email, phone or city to obtain contact id');
        //CRM_Core_Error::ll_function();
        return _crm_error( 'No contact found for given $params ' );
    }

    //CRM_Core_Error::debug_var('contactId', $rows[0]);
    //CRM_Core_Error::ll_function();
    return $dao->id;
}

?>
