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
 * @param array       $returnProperties      Which properties should be included in the returned group objects. (member_count should be last element.)
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
        if($count == 0) {
            $queryString .= " *";
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
    $queryString .= " FROM civicrm_group";
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
    $groups =array();
    while($crmDAO->fetch()) { 
        //$rows = array();
        
        $group =new CRM_Contact_DAO_Group();
        if($flag) {
            $group->id = $crmDAO->id;
            $count=count(crm_get_group_contacts(&$group));
        $crmDAO->member_count = $count;
        }
        $group = clone($crmDAO);
        $groups[] = $group;
        if (version_compare(phpversion(), '5.0') < 0) {
            eval('
                  function clone($object) {
                  return $object;
                  }
                 ');
        }

        /* CRM_Core_DAO::storeValues($crmDAO,$rows);
        if($flag) {
            $group =new CRM_Contact_DAO_Group();
            $group->id = $crmDAO->id;
            $count=count(crm_get_group_contacts(&$group));
            $rows['member_count']=$count;
        }
        $groupArray[] = $rows;*/
    }
    
    return $groups;


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


function crm_get_group_contacts(&$group, $returnProperties = null, $status = 'In', $sort = null, $offset = null, $row_count= null ) {
    
    $query = "SELECT * FROM civicrm_group WHERE id = '$group->id'";
    $groupDAO = new CRM_Contact_DAO_Group();
    $groupDAO->query($query);
    $groupDAO->fetch();
    if($groupDAO->saved_search_id !=NULL){
        $formValues = CRM_Contact_BAO_SavedSearch::getFormValues($groupDAO->saved_search_id);
        $result = CRM_Contact_BAO_Contact::searchQuery($formValues,0, 25, null,false,null,null,true);
        $contacts = explode(",",$result);
        
           
        if ($returnProperties == null) {
            $queryString = "SELECT * , civicrm_contact.id as civicrm_contact_id";
        } else {
            
            $queryString = "SELECT civicrm_contact.id as civicrm_contact_id ,";
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
        
            
        
        
        $queryString .= " FROM civicrm_contact 
                          LEFT OUTER JOIN civicrm_location ON (civicrm_contact.id = civicrm_location.contact_id)
                          LEFT OUTER JOIN civicrm_email    ON (civicrm_location.id = civicrm_email.location_id AND civicrm_email.is_primary = 1) WHERE ";
        $count =count($contacts);
        $counter = 1;
        foreach($contacts as $contactID) {           
            if($counter < $count){
                $queryString .=  "civicrm_contact.id = $contactID". " or ";
            } else {
                $queryString .=  "civicrm_contact.id = $contactID ";
            }
            $counter++;
        }
        
        
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
        if($offset !=null && $row_coun!= null) {
        $queryString.=" LIMIT $offset,$row_count";
        }
        $crmDAO =& new CRM_Core_DAO();
        $crmDAO->query($queryString);
    
    } else {
        
        if ( ! isset( $group->id )) {
            return _crm_error( 'Invalid group object passed in' );
        }
   

        if ($returnProperties == null) {
            $queryString = "SELECT * , civicrm_contact.id as civicrm_contact_id";
        } else {
            $queryString = "SELECT civicrm_contact.id as civicrm_contact_id, civicrm_group_contact.contact_id,";
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
        
        
        $queryString .= " FROM civicrm_contact LEFT JOIN  civicrm_group_contact ON (civicrm_contact.id =civicrm_group_contact.contact_id )";
        $queryString .= " LEFT JOIN  civicrm_location ON (civicrm_contact.id = civicrm_location.contact_id )";
        $queryString .= " LEFT JOIN  civicrm_email ON (civicrm_location.id = civicrm_email.location_id AND civicrm_email.is_primary = 1)";
        $queryString .= " WHERE civicrm_group_contact.status = '$status' AND civicrm_group_contact.group_id = '$group->id' ";
    
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
        if($offset !=null && $row_coun!= null) {
            $queryString.=" LIMIT $offset,$row_count";
        }
        $crmDAO =& new CRM_Contact_DAO_Contact();
        $crmDAO->query($queryString);
    }
    $contactArray = array();
    while($crmDAO->fetch()) { 
        
        if (version_compare(phpversion(), '5.0') < 0) {
            eval('
                  function clone($object) {
                  return $object;
                  }
                 ');
        }
        
        $contactDAO = new CRM_Contact_DAO_Contact();
        $contactDAO = clone($crmDAO);
        $contactArray[] = $contactDAO;
       
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

