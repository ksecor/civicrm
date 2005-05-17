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
 * This class contains functions for managing  contact groups.
 * 
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
     * @return array (reference)   the values that could be potentially assigned to smarty
     * @access public
     * @static
     */
    static function &getValues( &$params, &$values, &$ids ) {

        $groupIn = CRM_Contact_BAO_GroupContact::getContactGroup($params['contact_id'], 'In' , 3 );
        $values['group']['data']       =& CRM_Contact_BAO_GroupContact::getContactGroup($params['contact_id'], 'In' , 3 );
        // get the total count of relationships
        $values['group']['totalCount'] =  CRM_Contact_BAO_GroupContact::getContactGroup($params['contact_id'], 'In' , null, true );

        return $values;
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
                $groupContact->in_date   = $date;
                $groupContact->save( );
                $numContactsAdded++;
            } else {
                $numContactsNotAdded++;
            }
        }

        return array( count($contactIds), $numContactsAdded, $numContactsNotAdded );
    }


    /**
     * Given an array of contact ids, remove all the contacts from the group 
     *
     * @param array  $contactIds (reference ) the array of contact ids to be removed
     * @param int    $groupId    the id of the group
     *
     * @return array             (total, removed, notRemoved) count of contacts removed to group
     * @access public
     * @static
     */
    static function removeContactsFromGroup( &$contactIds, $groupId ) {
        $date = date('Ymd');

        $numContactsRemoved    = 0;
        $numContactsNotRemoved = 0;

        foreach ( $contactIds as $contactId ) {
            $groupContact = new CRM_Contact_DAO_GroupContact( );
            $groupContact->group_id   = $groupId;
            $groupContact->contact_id = $contactId;
            // check if the selected contact id already a member
            // if not a member remove to groupContact else keep the count of contacts that are not removed
            if ( $groupContact->find( ) ) {
                // remove the contact from the group
                $groupContact->status     = 'Out';
                $groupContact->out_method = 'Admin';
                $groupContact->out_date   = $date;
                $groupContact->save( );
                $numContactsRemoved++;
            } else {
                $numContactsNotRemoved++;
            }
        }

        return array( count($contactIds), $numContactsRemoved, $numContactsNotRemoved );
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

        $select = $from = $where = '';
        
        $select = 'SELECT crm_group.id, crm_group.title ';
        $from   = ' FROM crm_group, crm_group_contact ';
        $where  = " WHERE crm_group.group_type='static'";
        if ($contactId) {
            $where .= " AND crm_group.id = crm_group_contact.group_id AND crm_group_contact.contact_id = ".$contactId;
        }

        $orderby = " ORDER BY crm_group.name";
        $sql     = $select . $from . $where . $orderby;

        $group->query($sql);

        while($group->fetch()) {
            $values[$group->id] = $group->title;
        }
        
        return $values;
    }

   /**
     * function to get the list of groups for contact based on status of membership
     *
     * @param int     $contactId       contact id 
     * @param string  $status          state of membership
     * @param int     $numGroupContact number of groups for a contact that should be shown
     * @param boolean $count           true if we are interested only in the count
     *
     * @return array (reference )|int $values the relevant data object values for the contact or
                                      the total count when $count is true
     *
     * $access public
     */
    static function &getContactGroup( $contactId, $status = null, $numGroupContact = null, $count = false ) {
        $groupContact = new CRM_Contact_DAO_GroupContact( );

        if ( $count ) {
            $select = 'SELECT count(DISTINCT crm_group_contact.id)';
        } else {
            $select = 'SELECT crm_group_contact.id as crm_group_contact_id, crm_group.title as crm_group_title,
                             crm_group_contact.in_date as in_date, crm_group_contact.out_date as out_date,
                             crm_group_contact.pending_date as pending_date, crm_group_contact.status as status,
                             crm_group_contact.pending_method as pending_method, crm_group_contact.in_method as in_method,
                             crm_group_contact.out_method as out_method ';
        }

        $from   = ' FROM crm_group, crm_group_contact ';

        $where  = ' WHERE crm_group.id = crm_group_contact.group_id AND crm_group_contact.contact_id = ' . $contactId;
        
        if ( ! empty( $status ) ) {
            $where .= ' AND crm_group_contact.status = "' . $status . '"';
        }    

        $order = $limit = '';
        if (! $count ) {
            $order = ' ORDER BY crm_group.title ';

            if ( $numGroupContact ) {
                $limit = " LIMIT 0, $numGroupContact";
            }
        }

        $sql = $select . $from . $where . $order . $limit;

        $groupContact->query($sql);

        if ( $count ) {
            $row = $groupContact->getDatabaseResult()->fetchRow();
            return $row[0];
        } else {
            $values = array( );
            while ( $groupContact->fetch() ) {
                $id                            = $groupContact->crm_group_contact_id;
                $values[$id]['id']             = $id;
                $values[$id]['title']          = $groupContact->crm_group_title;
                $values[$id]['in_date']        = $groupContact->in_date;
                $values[$id]['out_date']       = $groupContact->out_date;
                $values[$id]['pending_method'] = $groupContact->pending_method;
                $values[$id]['in_method']      = $groupContact->in_method;
                $values[$id]['out_method']     = $groupContact->out_method;
            }
            return $values;
        }
    }


}

?>