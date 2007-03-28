<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                  |
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Contact/DAO/Group.php';

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
     * group_id. We'll tweak this function to be more full featured over a period
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
        require_once 'CRM/Utils/Hook.php';
        require_once 'CRM/Contact/DAO/SubscriptionHistory.php';
        CRM_Utils_Hook::pre( 'delete', 'Group', $id, CRM_Core_DAO::$_nullArray );

        CRM_Core_DAO::transaction('BEGIN');
	
	// added for CRM-1631
 	// delete all subscribed mails with the selected group id
	require_once 'CRM/Mailing/Event/DAO/Subscribe.php';
        $subscribe = & new CRM_Mailing_Event_DAO_Subscribe( );
        $subscribe->group_id = $id;
        $subscribe->delete();

        // delete all Subscription  records with the selected group id
        $subHistory = & new CRM_Contact_DAO_SubscriptionHistory( );
        $subHistory ->group_id = $id;
        $subHistory->delete();

        // delete all crm_group_contact records with the selected group id
        $groupContact =& new CRM_Contact_DAO_GroupContact( );
        $groupContact->group_id = $id;
        $groupContact->delete();

        // make all the 'add_to_group_id' field of 'civicrm_uf_group table', pointing to this group, as null
        $params = array( 1 => array( $id, 'Integer' ) );
        $query = "update civicrm_uf_group SET `add_to_group_id`= NULL where `add_to_group_id` = %1";
        CRM_Core_DAO::executeQuery( $query, $params );

        $query = "update civicrm_uf_group SET `limit_listings_group_id`= NULL where `limit_listings_group_id` = %1";
        CRM_Core_DAO::executeQuery( $query, $params );

        // delete from group table
        $group =& new CRM_Contact_DAO_Group( );
        $group->id = $id;
        $group->delete( );

        CRM_Core_DAO::transaction('COMMIT');

        CRM_Utils_Hook::post( 'delete', 'Group', $id, $group );
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
    static function memberCount( $id, $status = 'Added' ) {
        require_once 'CRM/Contact/DAO/GroupContact.php';
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
                     AND civicrm_group_contact.group_id =" 
                . CRM_Utils_Type::escape($lngGroupId, 'Integer');

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
        $dao =& new CRM_Contact_DAO_Group();
        $dao->is_active = 1;
        if ( $params ) {
            foreach ( $params as $k => $v ) {
                if ( $k == 'name' || $k == 'title' ) {
                    $dao->whereAdd( $k . ' LIKE "' . addslashes( $v ) . '"' );
                } else {
                    $dao->$k = $v;
                }
            }
        }
        $dao->find( );

        $flag = $returnProperties && in_array( 'member_count', $returnProperties ) ? 1 : 0;

        $groups =array();
        while ( $dao->fetch( ) ) { 
            $group =& new CRM_Contact_DAO_Group();
            if ( $flag ) {
                $dao->member_count = CRM_Contact_BAO_Group::memberCount( $dao->id );
            }
            $groups[] = clone( $dao );
        }
        return $groups;
    }

    /**
     * make sure that the user has permission to access this group
     *
     * @param int $id   the id of the object
     * @param int $name the name or title of the object
     *
     * @return string   the permission that the user has (or null)
     * @access public
     * @static
     */
    static function checkPermission( $id, $title ) {
        require_once 'CRM/ACL/API.php';
        require_once 'CRM/Core/Permission.php';

        if ( CRM_Core_Permission::check( 'edit all contacts' ) ||
             CRM_ACL_API::groupPermission( CRM_ACL_API::EDIT, $id ) ) {
            return CRM_Core_Permission::EDIT;
        }

        if ( CRM_Core_Permission::check( 'view all contacts' ) ||
             CRM_ACL_API::groupPermission( CRM_ACL_API::VIEW, $id ) ) {
            return CRM_Core_Permission::VIEW;
        }

        return null;
    }

    /**
     * Create a new group
     *
     * @param array $params     Associative array of parameters
     * @return object|null      The new group BAO (if created)
     * @access public
     * @static
     */
    public static function &create(&$params) {
        require_once 'CRM/Utils/Hook.php';
       
        // add custom field values
        if (CRM_Utils_Array::value('custom', $params)) {
            foreach ($params['custom'] as $customValue) {
                $cvParams = array(
                                  'entity_table'    => 'civicrm_group',
                                  'entity_id'       => $params['id'],
                                  'value'           => $customValue['value'],
                                  'type'            => $customValue['type'],
                                  'custom_field_id' => $customValue['custom_field_id'],
                                  'file_id'         => $customValue['file_id'],
                                  );
                
                if ($customValue['id']) {
                    $cvParams['id'] = $customValue['id'];
                }
                require_once 'CRM/Core/BAO/CustomValue.php';
                CRM_Core_BAO_CustomValue::create($cvParams);
            }
        }

        if ( CRM_Utils_Array::value( 'id', $params ) ) {
            CRM_Utils_Hook::pre( 'edit', 'Group', $params['id'], $params );
        } else {
            CRM_Utils_Hook::pre( 'create', 'Group', null, $params ); 
        }

        // form the name only if missing: CRM-627
        if( ! CRM_Utils_Array::value( 'name', $params ) ) {
            $params['name'] = CRM_Utils_String::titleToVar( $params['title'] );
        }

        $group =& new CRM_Contact_BAO_Group();
        $group->copyValues($params);
        $group->domain_id = CRM_Core_Config::domainID( ); 
        $group->save( );

        if ( ! $group->id ) {
            return null;
        }

        $group->buildClause( );
        $group->save( );

        return $group;
    }

    /**
     * given a saved search compute the clause and the tables
     * and store it for future use
     */
    function buildClause( ) {
        $params = array( array( 'group', '=', array( $this->id => 1 ), 0, 0 ) );
    
        if ( ! empty( $params ) ) {
            $tables = $whereTables = array( );
            require_once 'CRM/Contact/BAO/Query.php';
            $this->where_clause = CRM_Contact_BAO_Query::getWhereClause( $params, null, $tables, $whereTables );
            if ( ! empty( $tables ) ) {
                $this->select_tables = serialize( $tables );
            }
            if ( ! empty( $whereTables ) ) {
                $this->where_tables = serialize( $whereTables );
            }
        }

        return;
    }

    /**
     * Defines a new group (static or query-based)
     *
     * @param array $params     Associative array of parameters
     * @return object|null      The new group BAO (if created)
     * @access public
     * @static
     */

    public static function createGroup(&$params) {
         
        if ( CRM_Utils_Array::value( 'saved_search_id', $params ) ) {
            $savedSearch =& new CRM_Contact_DAO_SavedSearch();
            $savedSearch->domain_id   = CRM_Core_Config::domainID( );
            $savedSearch->form_values = CRM_Utils_Array::value( 'formValues', $params );
            $savedSearch->is_active = 1;
            $savedSearch->id = $params['saved_search_id'];
            $savedSearch->save();
        } 

        return self::create( $params );
    }
    
     /**
     * update the is_active flag in the db
     *
     * @param int      $id        id of the database record
     * @param boolean  $is_active value we want to set the is_active field
     *
     * @return Object             DAO object on sucess, null otherwise
     * @static
     */
    static function setIsActive( $id, $is_active ) {
        return CRM_Core_DAO::setFieldValue( 'CRM_Contact_DAO_Group', $id, 'is_active', $is_active );
    }
    
}

?>
