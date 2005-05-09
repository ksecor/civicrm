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

require_once 'CRM/Contact/DAO/GroupContact.php';

require_once 'CRM/Contact/BAO/Block.php';

class CRM_Contact_BAO_GroupContact extends CRM_Contact_DAO_GroupContact {
    
    /**
     * class constructor
     */
    function __construct( ) 
    {
        parent::__construct( );
    }

    /**
     * takes an associative array and creates a groupContact object
     *
     * the function extract all the params it needs to initialize the create a
     * group object. the params array could contain additional unused name/value
     * pairs
     *
     * @param array  $params         (reference ) an assoc array of name/value pairs
     *
     * @return object CRM_Contact_BAO_Group object
     * @access public
     * @static
     */
    static function add( &$params ) 
    {
       
        $dataExists = self::dataExists( $params );
        if ( ! $dataExists ) {
            return null;
        }

        $groupContact = new CRM_Contact_BAO_GroupContact( );
        $groupContact->copyValues( $params );
        $groupContact->save( );
        return $groupContact;
    }

    /**
     * Check if there is data to create the object
     *
     * @param array  $params         (reference ) an assoc array of name/value pairs
     *
     * @return boolean
     * @access public
     * @static
     */
    static function dataExists( &$params ) 
    {
        // return if no data present
        if ($params['group_id'] == 0) {
            return false;
        }

        return true;
     }

    /**
     * Given the list of params in the params array, fetch the object
     * and store the values in the values array
     *
     * @param array $params        input parameters to find object
     * @param array $values        output values of the object
     * @param array $ids           the array that holds all the db ids
     *
     * @return void
     * @access public
     * @static
     */
    static function getValues( &$params, &$values, &$ids ) {
        $groupContact = new CRM_Contact_BAO_GroupContact( );

        $groupContact->contact_id   = $params['contact_id'] ;        

        // get the total count of groups for the contact 
        $values['groupCount'] = $groupContact->count( );

        return $groupContact;
    }

    /**
     * Given an array of contact ids, add all the contacts to the group 
     *
     * @param array  $contactIds (reference ) the array of contact ids to be added
     * @param int    $groupId    the id of the group
     *
     * @return array             (total, added, notAdded) count of contacts added to group
     * @access public
     * @static
     */
    static function addContactsToGroup( &$contactIds, $groupId ) {
        $date = date('Ymd');

        $numContactsAdded    = 0;
        $numContactsNotAdded = 0;

        foreach ( $contactIds as $contactId ) {
            $groupContact = new CRM_Contact_DAO_GroupContact( );
            $groupContact->group_id   = $groupId;
            $groupContact->contact_id = $contactId;
            // check if the selected contact id already a member
            // if not a member add to groupContact else keep the count of contacts that are not added
            if ( ! $groupContact->find( ) ) {
                // add the contact to group
                $groupContact->status    = 'In';
                $groupContact->in_method = 'Admin';
                $groupContact->in_date   = $in_date;
                $groupContact->save( );
                $numContactsAdded++;
            } else {
                $numContactsNotAdded++;
            }
        }

        return array( count($contactIds), $numContactsAdded, $numContactsNotAdded );
    }


    /**
     * Function to get list of all the groups and groups for a contact
     *
     * @param  int $contactId contact id
     *
     * @access public
     * @return array $values this array has key-> group id and value group title
     * @static
     */
    static function getGroupList($contactId = 0) {
        
        $group = new CRM_Contact_DAO_Group( );

        $str_select = $str_from = $str_where = '';
        
        $str_select = 'SELECT crm_group.id, crm_group.title ';
        $str_from = ' FROM crm_group, crm_group_contact ';
        $str_where = " WHERE crm_group.group_type='static'";
        if ($contactId) {
            $str_where .= " AND crm_group.id = crm_group_contact.group_id 
                       AND crm_group_contact.contact_id = ".$contactId;
        }

        $str_orderby = " ORDER BY crm_group.name";
        $str_sql = $str_select.$str_from.$str_where.$str_orderby;

        $group->query($str_sql);

        while($group->fetch()) {
            $values[$group->id] = $group->title;
        }
        
        return $values;
    }

}

?>