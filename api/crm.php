<?php

/**
 * Definition of the CRM API. For more detailed documentation, please check:
 * http://objectledge.org/confluence/display/CRM/CRM+v1.0+Public+APIs
 * @package CRM
 * @subpackage API
 * @version $id
 */

require_once 'PEAR.php';

require_once 'CRM/Error.php';

/**
 * Most API functions take in associative arrays ( name => value pairs as parameters)
 * Some of the most commonly used parameters are described below
 * $params - an associative array used in construction / retrieval of the object
 * $returnProperties - the limited set of object properties that need to be returned
 * to the caller
 */

/**
 * Create a contact for the given contact_type
 *
 * @param array $params       input properties
 * @param enum  $contact_type type of contact to be created. Valid values are
 *                            'Individual', 'Household', 'Organization'
 *
 * @return CRM_Contact or CRM_Error
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
