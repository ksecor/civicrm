<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * Definition of the Group part of the CRM API. 
 * More detailed documentation can be found 
 * {@link http://objectledge.org/confluence/display/CRM/CRM+v1.0+Public+APIs
 * here}
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

/**
 * Files required for this package
 */
require_once 'api/utils.php';

require_once 'CRM/Contact/BAO/Group.php';
require_once 'CRM/Contact/BAO/GroupContact.php';

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




/**
 * Defines a new group (static or query-based),
 *
 *
 * @param array       $param  Associative array of property name/value pairs to insert in new 'group'.             
 * @return     newly created 'group' object
 *
 * @access public
 */


function crm_create_group($params) {
    _crm_initialize( );
    if ($params != null && !is_array($params)) {
        return _crm_error('$params is not an array');
    }

    $group = CRM_Contact_BAO_Group::createGroup($params);
    
    return($group);

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
    _crm_initialize( );

    if ($returnProperties != null && !is_array($returnProperties)) {
        return _crm_error('$returnProperties is not an array');
    }
    
    if ($params != null && !is_array($params)) {
        return _crm_error('$params is not an array');
    }
      
    $groups = array();
    $groups =  CRM_Contact_BAO_Group::getGroups($params, $returnProperties);
  
    return $groups;


}


/**
 * Update the existing group
 *
 *
 * @param $group         A valid group object (passed by reference)
 * @param array $params  Associative array of property name/value pairs to be updated
 * 
 * @return Group object with updated property values. if success or CRM_Error (db error or contact was not valid)
 *
 * @access public
 */

function crm_update_group(&$group, $params) {
    _crm_initialize( );

    if( ! is_a( $group,'CRM_Contact_BAO_Group') && ! is_a( $group,'CRM_Contact_DAO_Group')) {
        return _crm_error( 'Invalid group object passed in' );
    }
    
    if (!is_array($params)) {
        return _crm_error('$params is not an array');
    }
    
    if ($group->id == null) {
        return _crm_error('Could not locate group with id: $id');
    }

    $group->copyValues($params);
    $updatedGroup = $group->save( );

    return $updatedGroup;
}

/**
 *
 * Delete an existing group.
 *
 * @param $group         A valid group object (passed by reference)
 *
 * @return  null, if successful.or  CRM error object, if permissions are insufficient, etc.
 *
 *
 * @access public
 */

function crm_delete_group(&$group) {
    _crm_initialize( );
    
    if( ! is_a( $group,'CRM_Contact_BAO_Group') && ! is_a( $group,'CRM_Contact_DAO_Group')) {
        return _crm_error( 'Invalid group object passed in' );
    }

    if ($group->id == null) {
        return _crm_error('Could not locate group with id: $id');
    }

    if ( ! CRM_Contact_BAO_Group::checkPermission( $group->id, $group->title ) ) {
            return CRM_Core_Error::fatal( "You do not have permission to access group with id: $id" );
     }
    
    CRM_Contact_BAO_Group::discard($group->id);    
    return null;
}

/**
 * Add one or more contacts to an existing group.
 *
 * @param CRM_Contact $group       A valid group object (passed by reference)
 * @param array       $contact     An array of one or more valid Contact objects (passed by reference).
 * @param text        status       A valid status value ('Added', 'Pending', 'Removed').
 * @param text        $method      A valid method to enter the contact to a group ('Admin','Email','Web','API').
 *
 *
 * @return null if success or CRM_Error (db error or contact was not valid)
 *
 * @access public
 */

function crm_add_group_contacts(&$group, &$contacts, $status = 'Added', $method = 'Admin' ) {
    _crm_initialize( );
    
    if(! isset( $group->id )) {
        return _crm_error( 'Invalid group object passed in' );
    }
    if( ! is_a( $group,'CRM_Contact_BAO_Group') && ! is_a( $group,'CRM_Contact_DAO_Group')) {
        return _crm_error( 'Invalid group object passed in' );
    }
    
    foreach($contacts as $contact){
        if ( ! isset( $contact->contact_id )) {
            return _crm_error( 'Invalid contact object passed in' );
        }
        
        $contactID[] = $contact->contact_id;
    } 
    
    CRM_Contact_BAO_GroupContact::addContactsToGroup( $contactID, $group->id, $method, $status);
    return null;
}


/**
 * Returns array of contacts who are members of the specified group. NOTE: If supplying row_count, offset must be set to 0, not null
 *
 * @param CRM_Contact $group                A valid group object (passed by reference)
 * @param array       $returnProperties     Which properties should be included in the returned Contact object(s). If NULL, the default set of contact properties will be included. group_contact properties (such as 'status', 'in_date', etc.) are included automatically.Note:Do not inclue Id releted properties. 
 * @param text        $status               A valid status value ('Added', 'Pending', 'Removed').
 * @param text        $sort                 Associative array of one or more "property_name"=>"sort direction" pairs which will control order of Contact objects returned.
 * @param Int         $offset               Starting row index. Use integer, not null.
 * @param Int         $row_count            Maximum number of rows to returns.
 *
 *
 * @return            $contactArray         Array of contacts who are members of the specified group
 *
 * @access public
 */


function crm_get_group_contacts(&$group, $returnProperties = null, $status = 'Added', $sort = null, $offset = null, $row_count= null ) {
    _crm_initialize( );
    
    if( ! is_a( $group,'CRM_Contact_BAO_Group') && ! is_a( $group,'CRM_Contact_DAO_Group')) {
        return _crm_error( 'Invalid group object passed in' );
    }

    if ( ! isset( $group->id )) {
        return _crm_error( 'Invalid group object passed in' );
    }
                   
    if ($returnProperties != null && !is_array($returnProperties)) {
        return _crm_error('$returnProperties is not an array');
    }
    
    if ($sort != null && !is_array($sort)) {
        return _crm_error('$sort is not an array');
    }

    $contacts = array();
    $contacts = CRM_Contact_BAO_GroupContact::getGroupContacts($group, $returnProperties, $status, $sort, $offset, $row_count);
    return $contacts;    
}


/**
 * Remove one or more contacts from an existing group
 * 
 * @param CRM_Contact $group       A valid group object (passed by reference).
 * @param array       $contacts    An array of one or more valid Contact objects (passed by reference).
 *
 *  
 * @return null if success or CRM_Error (db error or contact was not valid)
 *
 * @access public
 */
function crm_delete_group_contacts(&$group, $contacts, $method = 'Admin') {
    _crm_initialize( );

    if( ! is_a( $group,'CRM_Contact_BAO_Group') && ! is_a( $group,'CRM_Contact_DAO_Group')) {
        return _crm_error( 'Invalid group object passed in' );
    }

    $contactID = array( );
    foreach ( $contacts as $contact ) {
         if ( ! isset($contact->contact_id)) {
             return _crm_error( 'Invalid contact object passed in' );
         }
         
         $contactID[] = $contact->contact_id;
    }

    if ( ! empty( $contactID ) ) {
        CRM_Contact_BAO_GroupContact::removeContactsFromGroup($contactID, $group->id, $method);
    }
    return null;
}

/**
 * subscribe contacts to a group 
 * 
 * @param CRM_Contact $group       A valid group object (passed by reference).
 * @param array       $contacts    An array of one or more valid Contact objects (passed by reference).
 *
 *  
 * @return null if success or CRM_Error (db error or contacts were not valid)
 *
 * @access public
 */

function crm_subscribe_group_contacts(&$group, $contacts)
{
    _crm_initialize( );

    if(!is_array($contacts)) {
        return _crm_error( '$contacts is not  Array ' );
    }
   
    if( ! is_a( $group,'CRM_Contact_BAO_Group') && ! is_a( $group,'CRM_Contact_DAO_Group')) {
        return _crm_error( 'Invalid group object passed in' );
    }

    foreach($contacts as $contact){
        if ( ! isset( $contact->id )) {
            return _crm_error( 'Invalid contact object passed in' );
        }
        $contactID[] = $contact->id;
    }

    $status = 'Pending';
    $method = 'Email';

    CRM_Contact_BAO_GroupContact::addContactsToGroup( $contactID, $group->id, $method, $status);
    return null;

}

/**
 * confirm membership to a group  
 *
 * @param CRM_Contact $group       A valid group object (passed by reference).
 * @param array       $contacts    An array of one or more valid Contact objects (passed by reference).
 *
 *  
 * @return null if success or CRM_Error (db error or contact was not valid)
 *
 * @access public
 */
function crm_confirm_group_contacts(&$group, $contacts)
{
    _crm_initialize( );

    if( ! is_a( $group,'CRM_Contact_BAO_Group') && ! is_a( $group,'CRM_Contact_DAO_Group')) {
        return _crm_error( 'Invalid group object passed in' );
    }

    if(!is_array($contacts)) {
        return _crm_error( '$contacts is not  Array ' );
    }
    
    foreach($contacts as $contact){
        if ( ! isset( $contact->id )) {
            return _crm_error( 'Invalid contact object passed in' );
        }
        $member =& CRM_Contact_BAO_GroupContact::getMembershipDetail($contact->id,$group->id);
        if ( ! $member ) {
            continue;
        }
        
        if($member->status != 'Pending') {
            return _crm_error( 'Can not confirm subscription. Current group status is NOT Pending.' );
        }
        CRM_Contact_BAO_GroupContact::updateGroupMembershipStatus($contact->id,$group->id);
    }

    return null;    
}

?>
