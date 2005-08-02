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

        $groupContact =& new CRM_Contact_BAO_GroupContact( );
        $groupContact->copyValues( $params );
        CRM_Contact_BAO_SubscriptionHistory::create($params);
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

        //$groupIn = CRM_Contact_BAO_GroupContact::getContactGroup($params['contact_id'], 'In' , 3 );
        $values['group']['data']       =& CRM_Contact_BAO_GroupContact::getContactGroup($params['contact_id'], 'In' , 3 );

        // get the total count of groups
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
    static function addContactsToGroup( &$contactIds, $groupId, $method = 'Admin',$status = 'In')  {
        $date = date('Ymd');
      
        $numContactsAdded    = 0;
        $numContactsNotAdded = 0;
        foreach ( $contactIds as $contactId ) {
            $groupContact =& new CRM_Contact_DAO_GroupContact( );
            $groupContact->group_id   = $groupId;
            $groupContact->contact_id = $contactId;
            // check if the selected contact id already a member
            // if not a member add to groupContact else keep the count of contacts that are not added
            if (  ! $groupContact->find( )) {
                // add the contact to group
                $historyParams = array(
                    'contact_id' => $contactId, 
                    'group_id' => $groupId, 
                    'method' => $method,
                    'status' => $status,
                    'date' => $date,
                );
                CRM_Contact_BAO_SubscriptionHistory::create($historyParams);
                $groupContact->status    = $status;
//                 $groupContact->in_method = 'Admin';
//                 $groupContact->in_date   = $date;
                
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
    static function removeContactsFromGroup( &$contactIds, $groupId ,$method = 'Admin',$status = 'Out') {
        $date = date('Ymd');

        $numContactsRemoved    = 0;
        $numContactsNotRemoved = 0;

        foreach ( $contactIds as $contactId ) {
            $groupContact =& new CRM_Contact_DAO_GroupContact( );
            $groupContact->group_id   = $groupId;
            $groupContact->contact_id = $contactId;
            // check if the selected contact id already a member
            // if not a member remove to groupContact else keep the count of contacts that are not removed
            if ( $groupContact->find( true ) ) {
                $historyParams = array( 'group_id' => $groupId,
                                        'contact_id' => $contactId,
                                        'status' => $status,
                                        'method' => $method,
                                        'date' => $date);
                CRM_Contact_BAO_SubscriptionHistory::create($historyParams);
                // remove the contact from the group
                $groupContact->status     = $status;
//                 $groupContact->out_method = 'Admin';
//                 $groupContact->out_date   = $date;
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
        $group =& new CRM_Contact_DAO_Group( );

        $select = $from = $where = '';
        
        $select = 'SELECT civicrm_group.id, civicrm_group.title ';
        $from   = ' FROM civicrm_group, civicrm_group_contact ';
        $where  = " WHERE civicrm_group.is_active = 1 ";
        if ( $contactId ) {
            $where .= " AND civicrm_group.id = civicrm_group_contact.group_id AND civicrm_group_contact.contact_id = " . $contactId;
        }

        $orderby = " ORDER BY civicrm_group.name";
        $sql     = $select . $from . $where . $orderby;

        $group->query($sql);

        $values = array( );
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
        $groupContact =& new CRM_Contact_DAO_GroupContact( );

        if ( $count ) {
            $select = 'SELECT count(DISTINCT civicrm_group_contact.id)';
        } else {
            $select = 'SELECT 
                    civicrm_group_contact.id as civicrm_group_contact_id, 
                    civicrm_group.title as group_title,
                    civicrm_group_contact.status as status, 
                    civicrm_group.id as group_id,
                    civicrm_subscription_history.date as date,
                    civicrm_subscription_history.method as method';
        }

        $where  = ' WHERE civicrm_contact.id = ' . $contactId ." AND civicrm_group.is_active = '1' ";
        
        if ( ! empty( $status ) ) {
            $where .= ' AND civicrm_group_contact.status = "' . $status . '"';
        }
        $tables     = array( 'civicrm_group_contact' => 1,
                             'civicrm_group'         => 1, );
        $permission = CRM_Core_Permission::whereClause( CRM_Core_Permission::VIEW, $tables ); 
        $where .= " AND $permission ";
        
//         $from = CRM_Contact_BAO_Contact::fromClause( $tables );
        $from = 
            ' FROM      civicrm_group_contact 
            INNER JOIN  civicrm_group
                ON      civicrm_group_contact.group_id = civicrm_group.id
            INNer JOIN  civicrm_contact
                ON      civicrm_group_contact.contact_id = civicrm_contact.id
            RIGHT JOIN  civicrm_subscription_history 
                ON 
                        civicrm_group_contact.contact_id = 
                            civicrm_subscription_history.contact_id
                AND
                        civicrm_group_contact.group_id =
                            civicrm_subscription_history.group_id';
                
        $order = $limit = '';
        if (! $count ) {
            $order = ' ORDER BY civicrm_group.title ';

            if ( $numGroupContact ) {
                $limit = " LIMIT 0, $numGroupContact";
            }
        }

        $sql = $select . $from . $where . $order . $limit;
//         CRM_Core_Error::debug( 'sql', $sql );

        $groupContact->query($sql);

        if ( $count ) {
            // does not work for php4
            //$row = $groupContact->getDatabaseResult()->fetchRow();
            $result = $groupContact->getDatabaseResult();
            $row    = $result->fetchRow();
            return $row[0];
        } else {
            $values = array( );
            while ( $groupContact->fetch() ) {
                $id                            = $groupContact->civicrm_group_contact_id;
                $values[$id]['id']             = $id;
                $values[$id]['group_id']       = $groupContact->group_id;
                $values[$id]['title']          = $groupContact->group_title;
                switch($groupContact->status) {
                    case 'In':
                        $prefix = 'in_';
                        break;
                    case 'Out':
                        $prefix = 'out_';
                        break;
                    default:
                        $prefix = 'pending_';
                }
                $values[$id][$prefix . 'date']      = $groupContact->date;
                $values[$id][$prefix . 'method']    = $groupContact->method;
            }
            return $values;
        }
    }

    /**
     * Delete the object records that are associated with this contact
     *
     * @param  int  $contactId id of the contact to delete
     *
     * @return void
     * @access public
     * @static
     */
    static function deleteContact( $contactId ) {
        $groupContact =& new CRM_Contact_DAO_GroupContact( );
        $groupContact->contact_id = $contactId;
        $groupContact->delete( );
    }

    /**
     * Returns array of contacts who are members of the specified group.
     *
     * @param CRM_Contact $group                A valid group object (passed by reference)
     * @param array       $returnProperties     Which properties
     *                    should be included in the returned Contact object(s). If NULL,
     *                    the default set of contact properties will be
     *                    included. group_contact properties (such as 'status',
     * '                  in_date', etc.) are included automatically.Note:Do not inclue
     *                    Id releted properties.  
     * @param text        $status               A valid status value ('In', 'Pending', 'Out').
     * @param text        $sort                 Associative array of
     *                    one or more "property_name"=>"sort direction"
     *                    pairs which will control order of Contact objects returned.
     * @param Int         $offset               Starting row index.
     * @param Int         $row_count            Maximum number of rows to returns.
     *
     *
     * @return            $contactArray         Array of contacts who are members of the specified group
     *
     * @access public
     */
    
    
    static function getGroupContacts(&$group, $returnProperties = null, $status = 'In', $sort = null, $offset = null, $row_count= null)
    {
        $query = "SELECT * FROM civicrm_group WHERE id = '$group->id'";
       
        $groupDAO = new CRM_Contact_DAO_Group();
        $groupDAO->id = $group->id;
        if ( ! $groupDAO->find( true ) ) {
            return CRM_Core_Error::fatal( "Could not locate group with id: $id" );
        }
        
        // make sure user has got permission to view this group
         if ( ! CRM_Contact_BAO_Group::checkPermission( $groupDAO->id, $groupDAO->title ) ) {
            return CRM_Core_Error::fatal( "You do not have permission to access group with id: $id" );
        }
        
        $query = '';
        if ( $returnProperties == null ) {
            $query = "SELECT * , civicrm_contact.id as civicrm_contact_id";
        } else {
            $query  = "SELECT civicrm_contact.id as civicrm_contact_id ,";
            $query .= implode( ',', $returnProperties );
        }
        
        if ( $groupDAO->saved_search_id != NULL ) {
            $formValues =& CRM_Contact_BAO_SavedSearch::getFormValues( $groupDAO->saved_search_id );
            $result     =  CRM_Contact_BAO_Contact::searchQuery($formValues, $offset, $row_count,
                                                                null, false, null, null,
                                                                true);
           

            $query .= " 
FROM civicrm_contact 
LEFT OUTER JOIN civicrm_location ON ( civicrm_location.entity_table = 'civicrm_contact' AND
                                      civicrm_contact.id = civicrm_location.entity_id )
LEFT OUTER JOIN civicrm_email    ON (civicrm_location.id = civicrm_email.location_id AND
                                      civicrm_email.is_primary = 1)
LEFT JOIN civicrm_group_contact ON (civicrm_contact.id =civicrm_group_contact.contact_id)
WHERE civicrm_group_contact.group_id = {$group->id} 
AND     (
            (civicrm_group_contact.status = '$status')
            OR 
            (civicrm_group_contact.status <> 'Out' AND civicrm_contact.id IN ( $result ))
        )
";

        } else {
            $query .= "
FROM       civicrm_contact
LEFT JOIN  civicrm_group_contact ON (civicrm_contact.id =civicrm_group_contact.contact_id )
LEFT JOIN  civicrm_subscription_history ON (civicrm_contact.id = civicrm_subscription_history.contact_id )
LEFT JOIN  civicrm_location ON ( civicrm_location.entity_table = 'civicrm_contact' AND
                                 civicrm_contact.id = civicrm_location.entity_id)
LEFT JOIN  civicrm_email ON (civicrm_location.id = civicrm_email.location_id AND civicrm_email.is_primary = 1)
WHERE civicrm_group_contact.status = '$status' AND civicrm_group_contact.group_id = '$group->id' ";
        }
        
        if ( $sort != null ) {
            $order = array( );
            foreach($sort as $key=> $direction) {
                $order[] = " $key $direction ";
            }
            $query .= " ORDER BY " . implode( ',', $order );
        }
        
        if ( $offset != null && $row_count != null ) {
            $query .= " LIMIT $offset, $row_count";
        }
        
        $dao =& new CRM_Contact_DAO_Contact();
        $dao->query($query);
        
        // this is quite inefficient, we need to change the return
        // values in docs
        $contactArray = array();
        while($dao->fetch()) { 
            $contactArray[] = clone($dao);
        }
        return $contactArray;
    }

    
    /**
     * Returns membership details of a contact for a group
     *
     * @param  int  $contactId id of the contact
     *
     * @param  int  $groupID   Id of a perticuler group
     *
     * @return object of group contact
     * @access public
     * @static
     */

    function getMembershipDetail($contactId,$groupID)
    {
        $query = "SELECT * 
FROM civicrm_group_contact 
LEFT JOIN civicrm_subscription_history ON (civicrm_group_contact.contact_id = civicrm_subscription_history.contact_id) 
WHERE civicrm_group_contact.contact_id = '".$contactId."' AND civicrm_group_contact.group_id = '".$groupID."' AND civicrm_subscription_history.method ='Email' "  ;
        $dao =& new CRM_Contact_DAO_GroupContact();
        $dao->query($query);
        $dao->fetch();
        return $dao;

    }


    /**
     * Method to update the Status of Group member form 'Pending' to 'In'
     *
     * @param  int  $contactId id of the contact
     *
     * @param  int  $groupID   Id of a perticuler group
     *
     * @return null If success
     * @access public
     * @static
     */

    function updateGroupMembershipStatus($contactId,$groupID)
    {
        if(! isset($contactId) && ! isset($groupID)) {
            return CRM_Core_Error::fatal( "$contactId or $groupID should not empty" );
        } 

        $query = "UPDATE civicrm_group_contact 
SET civicrm_group_contact.status = 'In' 
WHERE civicrm_group_contact.contact_id = '$contactId' AND civicrm_group_contact.group_id = '$groupID'" ;
      
        $dao =& new CRM_Contact_DAO_GroupContact();
        $dao->query($query);

        $query = "UPDATE civicrm_subscription_history 
SET civicrm_subscription_history.status = 'In' 
WHERE civicrm_subscription_history.contact_id = '$contactId' AND civicrm_subscription_history.group_id = '$groupID' AND civicrm_subscription_history.method = 'Email'";
        
        $dao =& new CRM_Contact_DAO_SubscriptionHistory();
        $dao->query($query);

        return null;    
        
    }
    
    
    
}



?>
