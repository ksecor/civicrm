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
 * Definition of the CRM API. For more detailed documentation, please check:
 * http://objectledge.org/confluence/display/CRM/CRM+v1.0+Public+APIs
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'PEAR.php';
require_once 'CRM/Error.php';

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
 * <li>The Contact data objects may include both identifying information
 * (e.g. last_name, organization name, etc.) and primary communications
 * data (e.g. primary email, primary phone, primary postal address...).
 *
 * <li>Properties which have administratively assigned sets of values (ENUM types)
 * are passed as strings (e.g. mobile_service_provider', 'im_service', etc).
 * If an unrecognized value is passed, an error will be returned.
 *
 * <li>Modules may invoke crm_get_option_values($property_name) to retrieve a list
 * of currently available values for a given property.
 *
 * <li>Invoke crm_create_option_value($property_name) to add new option values for a property.
 * </ul>
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
function &crm_create_contact( $params, $contact_type = 'Individual' ) {
}

/**
 * Returns a contact of the specific type that matches the input params
 *
 * @param array $params           input properties
 * @param array $returnProperties properties to be included in the return Contact object
 * @param enum  $contact_type     type of contact to be created. Valid values are
 *                                'Individual', 'Household', 'Organization'
 *
 * @return CRM_Contact or CRM_Error (if no contact or more than one contact exists)
 *
 * @access public
 */
function &crm_get_contact( $params, $returnProperties = null, $contact_type = 'Individual' ) {
}

/**
 * Updates the contact with the input params
 *
 * @param CRM_Contact $contact    Contact object to be updated
 * @param array $params           input properties
 *  
 * @return CRM_Contact or CRM_Error (db error or contact was not valid)
 *
 * @access public
 */
function &crm_update_contact( &$contact, $params ) {
}


/**
 * Deletes the specified contact
 *
 * @param CRM_Contact $contact    Contact object to be deleted
 *
 * @return null or CRM_Error (db error or contact was not valid)
 *
 * @access public
 */
function crm_delete_contact( &$contact ) {
}

/**
 * Create an additional location for an existing contact
 *
 * @param CRM_Contact $contact     Contact object to be deleted
 * @param array       $params      input properties
 * @param enum        context_name Name of a valid Context
 *  
 * @return CRM_Contact or CRM_Error (db error or contact was not valid)
 *
 * @access public
 */
function crm_create_location(&$contact, $params) {
}

function crm_update_location(&$contact, $context_name, $params) {
}

function crm_delete_location(&$contact, $context_name) {
}

function crm_create_group($params) {
}

function crm_get_groups($params = null, $returnProperties = null) {
}

function crm_update_group(&$group, $params) {
}

function crm_delete_group(&$group) {
}

function crm_add_group_contacts(&$group, $contacts, $status = 'In') {
}

function crm_get_group_contacts(&$group, $returnProperties = null, $status = 'In', $sort = null, $offset = 0, $row_count = 25 ) {
}

function crm_delete_group_contacts(&$group, $contacts) {
}

function crm_create_relationship(&$contact, &$target_contact, $relationship_type_name) {
}

function crm_get_relationships(&$contact, $relationship_type_name = null, $returnProperties = null, $sort = null, $offset = 0, $row_count = 25 ) {
}

function crm_delete_relationship(&$contact, &$target_contact, $relationship_type_name) {
}

function crm_create_relationship_type($params) {
}

function crm_contact_search_count($params) {
}

function crm_contact_search($params, $returnProperties, $sort = null, $offset = 0, $row_count = 25) {
}

function crm_create_action(&$contact, $params) {
}

function crm_get_actions(&$contact, $params, $sort = null, $offset = 0, $row_count = 25) {
}

function crm_add_option_value($property, $filter = null, $option_values) {
}

function crm_get_option_values($property, $filter = null) {
}

function crm_get_class_properties($class_name = 'Individual', $filter = 'all') {
}

function crm_create_extended_property_group($class_name, $params) {
}

function crm_create_extended_property(&$property_group, $params) {
}

?>
