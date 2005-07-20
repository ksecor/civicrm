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
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

class CRM_Contact_BAO_Group extends CRM_Contact_DAO_Group {

    /**
     * class constructor
     */
    function __construct( ) {
        parent::__construct( );
    }

    /**
     * Takes a bunch of params that are needed to match certain criteria and
     * retrieves the relevant objects. Typically the valid params are only
     * contact_id. We'll tweak this function to be more full featured over a period
     * of time. This is the inverse function of create. It also stores all the retrieved
     * values in the default array
     *
     * @param array $params   (reference ) an assoc array of name/value pairs
     * @param array $defaults (reference ) an assoc array to hold the flattened values
     *
     * @return object CRM_Contact_BAO_Group object
     * @access public
     * @static
     */
    static function retrieve( &$params, &$defaults ) {
        $group =& new CRM_Contact_DAO_Group( );
        $group->copyValues( $params );
        if ( $group->find( true ) ) {
            CRM_Core_DAO::storeValues( $group, $defaults );
            return $group;
        }
        return null;
    }

    /**
     * Function to delete the group and all the object that connect to
     * this group. Incredibly destructive
     *
     * @param int $id group id
     *
     * @return null
     * @access public
     * @static
     *
     */
    static function discard ( $id ) {
        // delete all crm_group_contact records with the selected group id
        $groupContact =& new CRM_Contact_DAO_GroupContact( );
        $groupContact->group_id = $id;
        $groupContact->delete();

        // delete from group table
        $group =& new CRM_Contact_DAO_Group( );
        $group->id = $id;
        $group->delete();
    }

    /**
     * Get the count of a members in a group with the specific status
     *
     * @param int $id      group id
     * @param enum $status status of members in group
     *
     * @return int count of members in the group with above status
     * @access public
     */
    static function memberCount( $id, $status = 'In' ) {
        $groupContact =& new CRM_Contact_DAO_GroupContact( );
        $groupContact->group_id = $id;
        if ( isset( $status ) ) {
            $groupContact->status   = $status;
        }
        return $groupContact->count( );
    }

    /**
     * Get the list of member for a group id
     *
     * @param int $lngGroupId this is group id
     *
     * @return array $aMembers this arrray contains the list of members for this group id
     * @access public
     * @static
     */
    static function getMember ($lngGroupId) {
        $groupContact =& new CRM_Contact_DAO_GroupContact( );
        
        $strSql = "SELECT civicrm_contact.id as contact_id, civicrm_contact.sort_name as name  
                   FROM civicrm_contact, civicrm_group_contact
                   WHERE civicrm_contact.id = civicrm_group_contact.contact_id 
                     AND civicrm_group_contact.group_id =" . $lngGroupId;

        $groupContact->query($strSql);

        while ($groupContact->fetch()) {
            $aMembers[$groupContact->contact_id] = $groupContact->name;
        }

       return $aMembers;
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

    static function getGroups($params = null, $returnProperties = null) 
    {
        $queryString = "SELECT";
        if ($returnProperties == null) {
            $queryString .= " *";
        } else {
            
            if (!is_array($returnProperties)) {
                return _crm_error('$returnProperties is not an array');
            }
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
            if (!is_array($params)) {
                return _crm_error('$params is not an array');
            }
            
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
            $group =new CRM_Contact_DAO_Group();
            if($flag) {
                $count=CRM_Contact_BAO_Group::memberCount($crmDAO->id);
                $crmDAO->member_count = $count;
            }
            $group = clone($crmDAO);
            $groups[] = $group;
                        
        }
        return $groups;
    
    }
    
}

?>