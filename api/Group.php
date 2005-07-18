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
 * Definition of the Group part of the CRM API. 
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
            if($retProp == 'member_count') {
                $count--;
                break;
            }
        }
        foreach($returnProperties as $retProp) {
            if($counter < $count) {
                if($retProp != 'member_count') {
                $queryString .=" ".$retProp.",";
                }
            } else {
                if($retProp != 'member_count') {
                $queryString .=" ".$retProp.',id';
                }
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
    $error = $crmDAO->query($queryString);
    if($error) {
        return _crm_error($error);
    }
    $groupArray = array();
    $flag = 0;
    if($returnProperties != null) {
        foreach($returnProperties as $ret) {
            if($ret == 'member_count'){
                $flag = 1;
            }
        }

    }
    while($crmDAO->fetch()) { 
        $rows = array();
        
        CRM_Core_DAO::storeValues($crmDAO,$rows);
        if($flag) {
            $group =new CRM_Contact_DAO_Group();
            $group->id = $crmDAO->id;
            $count=count(crm_get_group_contacts(&$group));
            $rows['member_count']=$count;
        }
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
 * @param array       $returnProperties     Which properties should be included in the returned Contact object(s). If NULL, the default set of contact properties will be included. group_contact properties (such as 'status', 'in_date', etc.) are included automatically.Note:Do not inclue Id releted properties. 
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
   

    if ($returnProperties == null) {
        $queryString = "SELECT * , crm_contact.id as crm_contact_id";
    } else {
        $queryString = "SELECT crm_contact.id as crm_contact_id, crm_group_contact.contact_id,";
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


    $queryString .= " FROM crm_contact LEFT JOIN  crm_group_contact ON (crm_contact.id =crm_group_contact.contact_id )";
    $queryString .= " LEFT JOIN  crm_location ON (crm_contact.id = crm_location.contact_id )";
    $queryString .= " LEFT JOIN  crm_email ON (crm_location.id = crm_email.location_id AND crm_email.is_primary = 1)";
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
            $contactArray[$crmDAO->crm_contact_id]['id'] = $crmDAO->id;
            $contactArray[$crmDAO->crm_contact_id]['domain_id'] = $crmDAO->domain_id;
            $contactArray[$crmDAO->crm_contact_id]['contact_type'] = $crmDAO->contact_type;
            $contactArray[$crmDAO->crm_contact_id]['legal_identifier'] = $crmDAO->legal_identifier;
            $contactArray[$crmDAO->crm_contact_id]['external_identifier'] = $crmDAO->external_identifier;
            $contactArray[$crmDAO->crm_contact_id]['sort_name'] = $crmDAO->sort_name;
            $contactArray[$crmDAO->crm_contact_id]['display_name'] = $crmDAO->display_name;
            $contactArray[$crmDAO->crm_contact_id]['home_URL'] = $crmDAO->home_URL ;
            $contactArray[$crmDAO->crm_contact_id]['image_URL'] = $crmDAO->image_URL ;
            $contactArray[$crmDAO->crm_contact_id]['source'] = $crmDAO->source;
            $contactArray[$crmDAO->crm_contact_id]['preferred_communication_method'] = $crmDAO->preferred_communication_method;
            $contactArray[$crmDAO->crm_contact_id]['preferred_mail_format'] = $crmDAO->preferred_mail_format;
            $contactArray[$crmDAO->crm_contact_id]['do_not_phone'] = $crmDAO->do_not_phone;
            $contactArray[$crmDAO->crm_contact_id]['do_not_email'] = $crmDAO->do_not_email;
            $contactArray[$crmDAO->crm_contact_id]['do_not_mail'] = $crmDAO->do_not_mail;
            $contactArray[$crmDAO->crm_contact_id]['do_not_trade'] = $crmDAO->do_not_trade;
            $contactArray[$crmDAO->crm_contact_id]['hash'] = $crmDAO->hash;
            $contactArray[$crmDAO->crm_contact_id]['group_id'] = $crmDAO->group_id;
            $contactArray[$crmDAO->crm_contact_id]['contact_id'] = $crmDAO->contact_id;
            $contactArray[$crmDAO->crm_contact_id]['status'] = $crmDAO->status;
            $contactArray[$crmDAO->crm_contact_id]['pending_date'] = $crmDAO->pending_date;
            $contactArray[$crmDAO->crm_contact_id]['in_date'] = $crmDAO->in_date;
            $contactArray[$crmDAO->crm_contact_id]['out_date'] = $crmDAO->out_date;
            $contactArray[$crmDAO->crm_contact_id]['pending_method'] = $crmDAO->pending_method;
            $contactArray[$crmDAO->crm_contact_id]['in_method'] = $crmDAO->in_method;
            $contactArray[$crmDAO->crm_contact_id]['out_method'] = $crmDAO->out_method;
            $contactArray[$crmDAO->crm_contact_id]['email'] = $crmDAO->email;
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
        if ( ! isset($contact->id)) {
            return _crm_error( 'Invalid contact object passed in' );
        }
        $contactID[] = $contact->id;
    } 
  
    CRM_Contact_BAO_GroupContact::removeContactsFromGroup($contactID, $group->id );
    return null;
}

