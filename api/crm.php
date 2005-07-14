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
require_once 'api/History.php';
require_once 'api/CustomGroup.php';



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
    CRM_Contact_BAO_Location::add($values, $ids, null);
    return $contact;
}

function crm_update_location(&$contact, $context_name, $params) {
}

function crm_delete_location(&$contact, $context_name) {
}

function crm_get_locations(&$contact) {
}

function crm_create_group($params) {
}


/**
 * Returns array of group object(s) matching a set of one or Group properties.
 *
 *
 * @param array       $param                 Array of one or more valid property_name=>value pairs. Limits the set of groups returned.
 * @param array       $returnProperties      Which properties should be included in the returned group objects. 
 *  
 * @return  An array of group objects.
 *
 * @access public
 */

function crm_get_groups($params = null, $returnProperties = null) {
    
    $queryString = "SELECT";
    if ($returnProperties == null) {
    $queryString .= " *";
    } else {
        $count = count($returnProperties);
        $counter = 1;
        foreach($returnProperties as $retProp) {
            if($counter < $count) {
                $queryString .=" ".$retProp.",";
            } else {
                $queryString .=" ".$retProp;
            }
            $counter++;
        }
    }
    $queryString .= " FROM crm_group";
    if ($params != null) {
        $total = count($params);
        $counter = 1;
        $queryString .= " WHERE";
        foreach($params as $key => $param) {
            if($counter < $total) {
                $queryString .=" $key". " LIKE". " '%$param%' ,";
            } else {
                $queryString .=" $key". " LIKE". " '%$param%' ";
            }
            $counter++;
        }
    }

    $crmDAO =& new CRM_Contact_DAO_Group();
    $crmDAO->query($queryString);
    $groupArray = array();
    
    while($crmDAO->fetch()) { 
        $rows = array();
        CRM_Core_DAO::storeValues($crmDAO,$rows);
        $groupArray[] = $rows;
    }
    
    return $groupArray;


}

function crm_update_group(&$group, $params) {
}

function crm_delete_group(&$group) {
}

/**
 * Add one or more contacts to an existing group.
 *
 * @param CRM_Contact $group       A valid group object (passed by reference)
 * @param array       $contact     An array of one or more valid Contact objects (passed by reference).
 * @param text        status       A valid status value ('In', 'Pending', 'Out').
 *  
 * @return null if success or CRM_Error (db error or contact was not valid)
 *
 * @access public
 */

function crm_add_group_contacts(&$group, $contacts, $status = 'In') {
    
    foreach($contacts as $contact){
        if ( ! isset( $contact->id )) {
            return _crm_error( 'Invalid contact object passed in' );
        }
        $contactID[] = $contact->id;
    } 
    
    CRM_Contact_BAO_GroupContact::addContactsToGroup( $contactID ,$group->id );
    return null;
}

/**
 * Returns array of contacts who are members of the specified group.
 *
 * @param CRM_Contact $group                A valid group object (passed by reference)
 * @param array       $returnProperties     Which properties should be included in the returned Contact object(s). If NULL, the default set of contact properties will be included. group_contact properties (such as 'status', 'in_date', etc.) are included automatically.
 * @param text        $status               A valid status value ('In', 'Pending', 'Out').
 * @param text        $sort                 Associative array of one or more "property_name"=>"sort direction" pairs which will control order of Contact objects returned.
 * @param Int         $offset               Starting row index.
 * @param Int         $row_count            Maximum number of rows to returns.
 *
 *
 * @return            $contactArray         Array of contacts who are members of the specified group
 *
 * @access public
 */


function crm_get_group_contacts(&$group, $returnProperties = null, $status = 'In', $sort = null, $offset = 0, $row_count = 25 ) {
    
    if ( ! isset( $group->id )) {
        return _crm_error( 'Invalid group object passed in' );
    }
   

    $queryString = "SELECT";
    if ($returnProperties == null) {
        $queryString .= "*";
    } else {
        $count = count($returnProperties);
        $counter = 1;
        foreach($returnProperties as $retProp) {
            if($counter < $count) {
                $queryString .=" ".$retProp.",";
            } else {
                $queryString .=" ".$retProp;
            }
            $counter++;
        }
    }

    $queryString .= " FROM crm_contact RIGHT JOIN  crm_group_contact ON (crm_contact.id =crm_group_contact.contact_id )";
    $queryString .= " WHERE crm_group_contact.status = '$status' AND crm_group_contact.group_id = '$group->id' ";
    
    if($sort != null) {
        $queryString .= " ORDER BY ";
        $count = count($sort);
        $counter = 1;
        foreach($sort as $key=> $direction) {
           if($counter < $count) {
                
                $queryString .= " ".$key." ".$direction. ",";
            }else{
                $queryString .= " ".$key." ".$direction;
            }
            $counter++; 
        }

    }
    $queryString.=" LIMIT $offset,$row_count";
    $crmDAO =& new CRM_Core_DAO();
    $crmDAO->query($queryString);
    $contactArray = array();
    while($crmDAO->fetch()) { 
        if($returnProperties != null) {
            foreach($returnProperties as $retProp) {
                $contactArray[$crmDAO->contact_id][$retProp]=$crmDAO->$retProp; 
                    }
        }else{
            $contactArray[$crmDAO->contact_id]['id'] = $crmDAO->id;
            $contactArray[$crmDAO->contact_id]['domain_id'] = $crmDAO->domain_id;
            $contactArray[$crmDAO->contact_id]['contact_type'] = $crmDAO->contact_type;
            $contactArray[$crmDAO->contact_id]['legal_identifier'] = $crmDAO->legal_identifier;
            $contactArray[$crmDAO->contact_id]['external_identifier'] = $crmDAO->external_identifier;
            $contactArray[$crmDAO->contact_id]['sort_name'] = $crmDAO->sort_name;
            $contactArray[$crmDAO->contact_id]['display_name'] = $crmDAO->display_name;
            $contactArray[$crmDAO->contact_id]['home_URL'] = $crmDAO->home_URL ;
            $contactArray[$crmDAO->contact_id]['image_URL'] = $crmDAO->image_URL ;
            $contactArray[$crmDAO->contact_id]['source'] = $crmDAO->source;
            $contactArray[$crmDAO->contact_id]['preferred_communication_method'] = $crmDAO->preferred_communication_method;
            $contactArray[$crmDAO->contact_id]['preferred_mail_format'] = $crmDAO->preferred_mail_format;
            $contactArray[$crmDAO->contact_id]['do_not_phone'] = $crmDAO->do_not_phone;
            $contactArray[$crmDAO->contact_id]['do_not_email'] = $crmDAO->do_not_email;
            $contactArray[$crmDAO->contact_id]['do_not_mail'] = $crmDAO->do_not_mail;
            $contactArray[$crmDAO->contact_id]['do_not_trade'] = $crmDAO->do_not_trade;
            $contactArray[$crmDAO->contact_id]['hash'] = $crmDAO->hash;
            $contactArray[$crmDAO->contact_id]['group_id'] = $crmDAO->group_id;
            $contactArray[$crmDAO->contact_id]['contact_id'] = $crmDAO->contact_id;
            $contactArray[$crmDAO->contact_id]['status'] = $crmDAO->status;
            $contactArray[$crmDAO->contact_id]['pending_date'] = $crmDAO->pending_date;
            $contactArray[$crmDAO->contact_id]['in_date'] = $crmDAO->in_date;
            $contactArray[$crmDAO->contact_id]['out_date'] = $crmDAO->out_date;
            $contactArray[$crmDAO->contact_id]['pending_method'] = $crmDAO->pending_method;
            $contactArray[$crmDAO->contact_id]['in_method'] = $crmDAO->in_method;
            $contactArray[$crmDAO->contact_id]['out_method'] = $crmDAO->out_method;
        }
    }
   
    return $contactArray;
    
}


/**
 * Remove one or more contacts from an existing 'static' group
 * 
 * @param CRM_Contact $group       A valid group object (passed by reference).
 * @param array       $contacts    An array of one or more valid Contact objects (passed by reference).
 *
 *  
 * @return null if success or CRM_Error (db error or contact was not valid)
 *
 * @access public
 */


function crm_delete_group_contacts(&$group, $contacts) {
     
    
    foreach($contacts as $contact){
        if ( ! isset( $contact->id )) {
            return _crm_error( 'Invalid contact object passed in' );
        }
        $contactID[] = $contact->id;
    } 
    CRM_Contact_BAO_GroupContact::removeContactsFromGroup($contactId, $group->id );
    return null;
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
    $property_object = array(); 
    $error = eval( '$fields = CRM_Contact_DAO_' .$class_name  . '::fields( );' );
    if($error) {
        
        return $error;
    }
    
    $id = -1;

    foreach($fields as $key => $values) {
       
        $property_object[] = array("id"=>$id,"name"=>$key,"data_type"=>CRM_Utils_Type::ConstToString($values['type']),"description"=>$values['title']);
    }
    
    if($class_name =='Individual' || $class_name =='Organization' || $class_name =='Household') {
        eval( '$fields = CRM_Contact_DAO_Contact::fields( );' );
        
        foreach($fields as $key => $values) {
            
            $property_object[] = array("id"=>$id,"name"=>$key,"data_type"=>CRM_Utils_Type::ConstToString($values['type']) ,"description"=>$values['title']);
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
