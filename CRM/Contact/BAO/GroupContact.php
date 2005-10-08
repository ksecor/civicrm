<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.1                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Social Source Foundation                        |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the Affero General Public License Version 1,    |
 | March 2002.                                                        |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the Affero General Public License for more details.            |
 |                                                                    |
 | You should have received a copy of the Affero General Public       |
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 * This class contains functions for managing  contact groups.
 * 
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Social Source Foundation (c) 2005
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

        $values['group']['data']       =& CRM_Contact_BAO_GroupContact::getContactGroup($params['contact_id'], 'Added' , 3 );

        // get the total count of groups
        $values['group']['totalCount'] =  CRM_Contact_BAO_GroupContact::getContactGroup($params['contact_id'], 'Added' , null, true );

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
    static function addContactsToGroup( &$contactIds, $groupId, $method = 'Admin',$status = 'Added', $tracking = null)  {
        $date = date('YmdHis');
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
                    'tracking' => $tracking,
                );
                CRM_Contact_BAO_SubscriptionHistory::create($historyParams);
                $groupContact->status    = $status;
                $groupContact->save( );
                $numContactsAdded++;
            } else {
                $groupContact->fetch();
                if ($groupContact->status == 'Added') {
                    $numContactsNotAdded++;
                } else {
                    $historyParams = array(
                        'contact_id' => $contactId, 
                        'group_id' => $groupId, 
                        'method' => $method,
                        'status' => $status,
                        'date' => $date,
                        'tracking' => $tracking,
                    );
                    CRM_Contact_BAO_SubscriptionHistory::create($historyParams);
                    $groupContact->status    = $status;
                    $groupContact->save( );
                    $numContactsAdded++;
                }
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
    static function removeContactsFromGroup( &$contactIds, $groupId ,$method = 'Admin',$status = 'Removed',$tracking = null) {
        if ( ! is_array( $contactIds ) ) {
            return array( 0, 0, 0 );
        }

        $date = date('YmdHis');
        $numContactsRemoved    = 0;
        $numContactsNotRemoved = 0;

        $group =& new CRM_Contact_DAO_Group();
        $group->id = $groupId;
        $group->find(true);

        foreach ( $contactIds as $contactId ) {
            $groupContact =& new CRM_Contact_DAO_GroupContact( );
            $groupContact->group_id   = $groupId;
            $groupContact->contact_id = $contactId;
            // check if the selected contact id already a member, or if this is
            // an opt-out of a smart group.
            // if not a member remove to groupContact else keep the count of contacts that are not removed
            if ( $groupContact->find( true ) || $group->saved_search_id ) {
                $historyParams = array( 'group_id' => $groupId,
                                        'contact_id' => $contactId,
                                        'status' => $status,
                                        'method' => $method,
                                        'date' => $date,
                                        'tracking' => $tracking);
                CRM_Contact_BAO_SubscriptionHistory::create($historyParams);
                // remove the contact from the group
                $groupContact->status     = $status;
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
            $where .= " AND civicrm_group.id = civicrm_group_contact.group_id 
                        AND civicrm_group_contact.contact_id = " 
                    . CRM_Utils_Type::escape($contactId, 'Integer');
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
        if ( $count ) {
            $select = 'SELECT count(DISTINCT civicrm_group_contact.id)';
        } else {
            $select = 'SELECT 
                    civicrm_group_contact.id as civicrm_group_contact_id, 
                    civicrm_group.title as group_title,
                    civicrm_group.visibility as visibility,
                    civicrm_group_contact.status as status, 
                    civicrm_group.id as group_id,
                    civicrm_subscription_history.date as date,
                    civicrm_subscription_history.method as method';
        }

        $where  = ' WHERE civicrm_contact.id = ' 
                . CRM_Utils_Type::escape($contactId, 'Integer') 
                ." AND civicrm_group.is_active = '1' ";
        
        if ( ! empty( $status ) ) {
            $where .= ' AND civicrm_group_contact.status = "' 
                    . CRM_Utils_Type::escape($status, 'String') . '"';
        }
        $tables     = array( 'civicrm_group_contact'        => 1,
                             'civicrm_group'                => 1,
                             'civicrm_subscription_history' => 1 );
        $permission = CRM_Core_Permission::whereClause( CRM_Core_Permission::VIEW, $tables ); 
        $where .= " AND $permission ";
        
        $from = CRM_Contact_BAO_Query::fromClause( $tables );

        $order = $limit = '';
        if (! $count ) {
            $order = ' ORDER BY civicrm_group.title ';

            if ( $numGroupContact ) {
                $limit = " LIMIT 0, $numGroupContact";
            }
        }

        $sql = $select . $from . $where . $order . $limit;

        if ( $count ) {
            return CRM_Core_DAO::singleValueQuery( $sql ); 
        } else {
            $groupContact =& new CRM_Contact_DAO_GroupContact( );
            $groupContact->query($sql);
            $values = array( );
            while ( $groupContact->fetch() ) {
                $id                            = $groupContact->civicrm_group_contact_id;
                $values[$id]['id']             = $id;
                $values[$id]['group_id']       = $groupContact->group_id;
                $values[$id]['title']          = $groupContact->group_title;
                $values[$id]['visibility']     = $groupContact->visibility;
                switch($groupContact->status) {
                    case 'Added':
                        $prefix = 'in_';
                        break;
                    case 'Removed':
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
     * @param text        $status               A valid status value ('Added', 'Pending', 'Removed').
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
    static function getGroupContacts(&$group, $returnProperties = null, $status = 'Added', $sort = null, $offset = null, $row_count= null)
    {
        $query = "SELECT * FROM civicrm_group WHERE id = " . CRM_Utils_Type::escape($group->id, 'Integer');
       
        $groupDAO =& new CRM_Contact_DAO_Group();
        $groupDAO->id = $group->id;
        if ( ! $groupDAO->find( true ) ) {
            return CRM_Core_Error::fatal( "Could not locate group with id: $id" );
        }
        
        // make sure user has got permission to view this group
         if ( ! CRM_Contact_BAO_Group::checkPermission( $groupDAO->id, $groupDAO->title ) ) {
            return CRM_Core_Error::fatal( "You do not have permission to access group with id: $id" );
        }
        
        $query = '';
        if ( empty($returnProperties) ) {
            $query = "SELECT * , civicrm_contact.id as civicrm_contact_id,
                        civicrm_email.email as email";
        } else {
            $query  = "SELECT civicrm_contact.id as civicrm_contact_id ,";
            $query .= implode( ',', $returnProperties );
        }
        
        $fv = array(
            'cb_group'                  => array($group->id => true),
            'cb_group_contact_status'   => array($status => true)
        );
        
        $tables = array(
            self::getTableName() => true,
            CRM_Core_BAO_Email::getTableName() => true,
            CRM_Contact_BAO_Contact::getTableName() => true, 
            CRM_Contact_BAO_SubscriptionHistory::getTableName() => true,
        );
        
        $inner = array(
            CRM_Contact_BAO_Group::getTableName() => true
        );
        
        $where = CRM_Contact_BAO_Contact::whereClause($fv, false, $tables);
        $permission = CRM_Core_Permission::whereClause( CRM_Core_Permission::VIEW, $tables);
        $from = CRM_Contact_BAO_Query::fromClause($tables, $inner);
        $query .= " $from WHERE $permission AND $where ";
        
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
WHERE civicrm_group_contact.contact_id = ".CRM_Utils_Type::escape($contactId, 'Integer')." 
AND civicrm_group_contact.group_id = '".CRM_Utils_Type::escape($groupID, 'Integer')." 
AND civicrm_subscription_history.method ='Email' "  ;
        $dao =& new CRM_Contact_DAO_GroupContact();
        $dao->query($query);
        $dao->fetch();
        return $dao;

    }


    /**
     * Method to update the Status of Group member form 'Pending' to 'Added'
     *
     * @param  int  $contactId id of the contact
     *
     * @param  int  $groupID   Id of a perticuler group
     *
     * @param mixed $tracking   tracking information for history
     *
     * @return null If success
     * @access public
     * @static
     */

    function updateGroupMembershipStatus($contactId,$groupID,$method = 'Email',$tracking = null)
    {
        if(! isset($contactId) && ! isset($groupID)) {
            return CRM_Core_Error::fatal( "$contactId or $groupID should not empty" );
        } 

        $query = "UPDATE civicrm_group_contact 
SET civicrm_group_contact.status = 'Added'
WHERE civicrm_group_contact.contact_id = " . CRM_Utils_Type::escape($contactId, 'Integer') ." 
AND civicrm_group_contact.group_id = " . CRM_Utils_Type::escape($groupID, 'Integer');
      
        $dao =& new CRM_Contact_DAO_GroupContact();
        $dao->query($query);

//         $query = "UPDATE civicrm_subscription_history 
// SET civicrm_subscription_history.status = 'Added' 
// WHERE civicrm_subscription_history.contact_id = '$contactId' AND civicrm_subscription_history.group_id = '$groupID' AND civicrm_subscription_history.method = 'Email'";
//         
//         $dao =& new CRM_Contact_DAO_SubscriptionHistory();
//         $dao->query($query);
        $params = array('contact_id' => $contactId,
                        'group_id'  => $ggroupID,
                        'status'    => 'Added',
                        'method'    => $method,
                        'tracking'  => $tracking
        );
        CRM_Contact_BAO_SubscriptionHistory::create($params);
        return null;    
        
    }
    
    /**
     * Method to get Group Id 
     *
     * @param  int  $groupContactID   Id of a perticuler group
     *
     *
     * @return groupID
     * @access public
     * @static
     */
    public static function getGroupId($groupContactID){
        $dao =& new CRM_Contact_DAO_GroupContact();
        $dao->id = $groupContactID;
        $dao->find(true);
        return $dao->group_id; 
        
    }

    /**
     * takes an associative array and creates a contact tags 
     *
     *
     * @param array $params (reference ) an assoc array of name/value pairs
     * @param array $contactId    contact id
     *
     * @return none
     * @access public
     * @static
     */
    static function create( &$params, $contactId ) 
    {
        $contactIds = array();
        $contactIds[] = $contactId;

        //get groups for the contact id
        //$contactGroup =& CRM_Contact_BAO_GroupContact::getGroupList( $contactId );
        //get groups for the contact id
        if ($contactId) {
            $contactGroupList =& CRM_Contact_BAO_GroupContact::getContactGroup( $contactId, 'Added' );
            if (is_array($contactGroupList)) {
                foreach ($contactGroupList as $key) {
                    $groupId = $key['group_id'];
                    $contactGroup[$groupId] = $groupId;
                }
            }
         }

        // get the list of all the groups
        $allGroup =& CRM_Contact_BAO_GroupContact::getGroupList( );
        
        // this fix is done to prevent warning generated by array_key_exits incase of empty array is given as input
        if (!is_array($params)) {
            $params = array( );
        }
        
        // this fix is done to prevent warning generated by array_key_exits incase of empty array is given as input
        if (!is_array($contactGroup)) {
            $contactGroup = array( );
        }

        // check which values has to be add/remove contact from group
        foreach ($allGroup as $key => $varValue) {
            if (array_key_exists($key, $params) && !array_key_exists($key, $contactGroup) ) {
                // add contact to group
                CRM_Contact_BAO_GroupContact::addContactsToGroup($contactIds, $key);
            } else if (!array_key_exists($key, $params) && array_key_exists($key, $contactGroup) ) {
                // remove contact from group
                CRM_Contact_BAO_GroupContact::removeContactsFromGroup($contactIds, $key);
            }
        }
    }

}



?>
