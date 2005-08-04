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
require_once 'api/utils.php';

require_once 'api/Contact.php';
require_once 'api/Group.php';
require_once 'api/History.php';
require_once 'api/CustomGroup.php';
require_once 'CRM/Contact/BAO/Group.php';



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
    _crm_initialize( );

    $values = array(
        'contact_id'    => $contact->id,
        'location'      => array(1 => array()),
    );

    $loc =& $values['location'][1];

    $loc['address'] = array( );
    $fields =& CRM_Contact_DAO_Address::fields( );
    _crm_store_values($fields, $params, $loc['address']);
    $ids = array( 'county', 'country', 'state_province', 'supplemental_address_1', 'supplemental_address_2', 'StateProvince.name' );
    foreach ( $ids as $id ) {
        if ( array_key_exists( $id, $params ) ) {
            $loc['address'][$id] = $params[$id];
        }
    }

    $blocks = array( 'Email', 'Phone', 'IM' );
    foreach ( $blocks as $block ) {
        $name = strtolower($block);
        $loc[$name]    = array( );
        $loc[$name][1] = array( );
        require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Contact_DAO_" . $block) . ".php");
        eval( '$fields =& CRM_Contact_DAO_' . $block . '::fields( );' );
        _crm_store_values( $fields, $params, $loc[$name][1] );
    }
    $loc['location_type_id'] = $params['location_type_id'];
 
    $ids = array();
    CRM_Core_BAO_Location::add($values, $ids, null);
    return $contact;
}

function crm_update_location(&$contact, $context_name, $params) {
}

function crm_delete_location(&$contact, $context_name) {
}

function crm_get_locations(&$contact) {
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


/**
 * Returns an array of property objects for the requested class.
 *
 * @param String      $class_name      'class_name' (string) A valid class name.
 * @param Striing     $filter           filter' (string) Limits properties returned ("core", "custom", "default", "all).
 *  
 * @return $property_object  Array of property objects containing the properties like id ,name ,data_type, description;
 *
 * @access public
 */

function crm_get_class_properties($class_name = 'Individual', $filter = 'all') {
    _crm_initialize( );

    $property_object = array(); 
    $error = eval( '$fields = CRM_Contact_DAO_' .$class_name  . '::fields( );' );
    if($error) {
        return _crm_error($error);
    }
    $id = -1;
    
    foreach($fields as $key => $values) {
        $property_object[] = array("id"=>$id,"name"=>$key,"data_type"=>CRM_Utils_Type::typeToString($values['type']),"description"=>$values['title']);
    }
    
    if($class_name =='Individual' || $class_name =='Organization' || $class_name =='Household') {
        eval( '$fields = CRM_Contact_DAO_Contact::fields( );' );
        
        foreach($fields as $key => $values) {
            
            $property_object[] = array("id"=>$id,"name"=>$key,"data_type"=>CRM_Utils_Type::typeToString($values['type']) ,"description"=>$values['title']);
        }
        $fields="";
    }
    
    if($filter == 'custom' || $filter == 'all' ) {
        $groupTree = CRM_Core_BAO_CustomGroup::getTree($class_name, null, -1);
        foreach($groupTree as $node) {
            $fields = $node["fields"];
            
            foreach($fields as $key => $values) {
       
                $property_object[] = array("id"=>$values['id'],"name"=>$values['name'],"data_type"=>$values['data_type'] ,"description"=>$values['help_post']);
            }
            
        }
        
    }
    
    return $property_object;

}

function crm_create_extended_property_group($class_name, $params) {
}

function crm_create_extended_property(&$property_group, $params) {
}

?>