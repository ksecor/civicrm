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

require_once 'CRM/Error.php';
require_once 'CRM/Array.php';

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

    // return error if we do not get any params
    if ( empty( $params ) ) {
        $error = CRM_Error::singleton( );

        $error->push( 8000, "Fatal Error", array( ), "Input Parameters empty" );
        return $error;
    }

    $error = _crm_check_params( $params, $contact_type );
    if ( $error instanceof CRM_Error ) {
        return $error;
    }

    $values  = array( );
    $values['contact_type'] = $contact_type;
    _crm_format_params( $params, $values );

    $ids     = array( );

    $contact = CRM_Contact_BAO_Contact::create( $values, $ids, 1 );
    echo "<b>Info</b>: Contact ID: " . $contact->contact_id . "<br />\n";
    return $contact;
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
 * @return bool|CRM_Error
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
                                                   'nick_name',
                                                   ),
                             'Organization' => array(
                                                   'organization_name',
                                                   'nick_name',
                                                   ),
                             );

    $error = CRM_Error::singleton( );

    // cannot create a contact with empty params
    if ( empty( $params ) ) {
        $error->push( 8000, 'Fatal Error', array( ), 'Input Parameters empty' );
        return $error;
    }

    // contact_type has a limited number of valid values
    $fields = CRM_Array::value( $contact_type, $required );
    if ( $fields == null ) {
        $error->push( 8000, 'Fatal Error', array( ), "Invalid Contact Type: $contact_type" );
        return $error;
    }

    $valid = false;
    foreach ( $fields as $field ) {
        if ( is_array( $field ) ) {
            $valid = true;
            foreach ( $field as $element ) {
                if ( ! CRM_Array::value( $element, $params ) ) {
                    $valid = false;
                    break;
                }
            }
        } else {
            if ( CRM_Array::value( $field, $params ) ) {
                $valid = true;
            }
        }
        if ( $valid ) {
            break;
        }
    }
    
    if ( ! $valid ) {
        $error->push( 8000, 'Fatal Error', array( ), "Required fields not found for $contact_type" );
        return $error;
    }

    // make sure phone and email are valid strings
    if ( array_key_exists( 'email', $params ) &&
         ! CRM_Rule::email( $params['email'] ) ) {
        $error->push( 8000, 'Fatal Error', array( ), "Email not valid " . $params['email'] );
        return $error;
    }

    if ( array_key_exists( 'phone', $params ) &&
         ! CRM_Rule::phone( $params['phone'] ) ) {
        $error->push( 8000, 'Fatal Error', array( ), "Phone not valid " . $params['phone'] );
        return $error;
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

    eval( '$fields =& CRM_Contact_DAO_' . $values['contact_type'] . '::fields( );' );
    _crm_store_values( $fields, $params, $values );

    $values['location']               = array( );
    $values['location'][1]            = array( );
    $fields =& CRM_Contact_DAO_Location::fields( );
    _crm_store_values( $fields, $params, $values['location'][1] );
    _crm_resolve_value( $params, 'location_type',
                        $values['location'][1],
                        CRM_SelectValues::$locationType );

    $values['location'][1]['address'] = array( );
    $fields =& CRM_Contact_DAO_Address::fields( );
    _crm_store_values( $fields, $params, $values['location'][1]['address'] );
    _crm_resolve_value( $params, 'county',
                        $values['location'][1]['address'],
                        CRM_SelectValues::$county );
    _crm_resolve_value( $params, 'country',
                        $values['location'][1]['address'],
                        CRM_SelectValues::$country );
    _crm_resolve_value( $params, 'state_province',
                        $values['location'][1]['address'],
                        CRM_SelectValues::$stateProvince );

    $blocks = array( 'Email', 'Phone', 'IM' );
    foreach ( $blocks as $block ) {
        $name = strtolower($block);
        $values['location'][1][$name]    = array( );
        $values['location'][1][$name][1] = array( );
        eval( '$fields =& CRM_Contact_DAO_' . $block . '::fields( );' );
        _crm_store_values( $fields, $params, $values['location'][1][$name][1] );
    }

    _crm_resolve_value( $params, 'phone_type',
                        $values['location'][1]['phone'][1],
                        CRM_SelectValues::$phoneType );
    _crm_resolve_value( $params, 'im_provider',
                        $values['location'][1]['im'][1],
                        CRM_SelectValues::$imProvider );

    if ( array_key_exists( 'im_name', $params ) ) {
        $values['location'][1]['im'][1]['name'] = $params['im_name'];
    }
    if ( array_key_exists( 'im_provider_id', $values ) ) {
        $values['location'][1]['im'][1]['provider_id'] = $values['location'][1]['im'][1]['im_provider_id'];
    }
}

function _crm_store_values( &$fields, &$params, &$values ) {
    foreach ( $fields as $name => &$field ) {
        // ignore all ids for now
        if ( $name === 'id' || substr( $name, -1, 3 ) === '_id' ) {
            continue;
        }

        if ( array_key_exists( $name, $params ) ) {
            $values[$name] = $params[$name];
        }
    }
}

function _crm_resolve_value( &$params, $name, &$dest, &$values ) {
    if ( ! array_key_exists( $name, $params ) ) {
        return;
    }

    $flip = array_flip( $values );
    if ( ! array_key_exists( $params[$name], $flip ) ) {
        return;
    }

    $dest[ $name . '_id' ] = $flip[$params[$name]];
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
 * @return CRM_Contact|CRM_Error  Return the Contact Object if found, else
 *                                Error Object
 *
 * @access public
 *
 */
function &crm_get_contact( $params, $returnProperties = null ) {
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
 * @return CRM_Contact|CRM_Error  Return the updated Contact Object else
 *                                Error Object (if integrity violation)
 *
 * @access public
 *
 */
function &crm_update_contact( &$contact, $params ) {
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
 * @return void|CRM_Error  An error if 'contact' is invalid,
 *                         permissions are insufficient, etc.
 *
 * @access public
 *
 */
function crm_delete_contact( &$contact ) {
}

?>
