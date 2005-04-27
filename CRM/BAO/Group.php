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

class CRM_BAO_Group extends CRM_Contact_DAO_Group {

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
        $group = new CRM_Contact_DAO_Group( );
        $group->copyValues( $params );
        if ( $group->find( true ) ) {
            $group->storeValues( $defaults );
            return $group;
        }
        return null;
    }

    /**
     * Function to delete the group 
     *
     * @param int $id group id
     *
     * @return null
     * @access public
     * @static
     *
     */
    static function del ( $id ) {
        /*
        // delete all crm_group_contact records with the selected group id
        $groupContact = new CRM_Contact_DAO_GroupContact( );
        $groupContact->group_id = $id;
        $groupContact->find();
        while ( $groupContact->fetch() ) {
            $groupContact->delete();
        }

        // delete from group table
        $group = new CRM_Contact_DAO_Group( );
        $group->id = $id;
        $group->delete();
        */
        
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
        $groupContact = new CRM_Contact_DAO_GroupContact( );
        
        $strSql = "SELECT crm_contact.id as contact_id, crm_contact.sort_name as name  
                   FROM crm_contact, crm_group_contact
                   WHERE crm_contact.id = crm_group_contact.contact_id 
                     AND crm_group_contact.group_id =".$lngGroupId;

        $groupContact->query($strSql);

        while ($groupContact->fetch()) {
            $aMembers[$groupContact->contact_id] = $groupContact->name;
        }

       return $aMembers;
    }
    
}

?>