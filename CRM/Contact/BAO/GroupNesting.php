<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.8                                                |
 +--------------------------------------------------------------------+
 | Copyright U.S. PIRG (c) 2007                                       |
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
 * @package CRM
 * @copyright U.S. PIRG 2007
 * $Id$
 *
 */
 
require_once 'CRM/Contact/DAO/GroupNesting.php';

class CRM_Contact_BAO_GroupNesting extends CRM_Contact_DAO_GroupNesting {
    
    /**
     * Returns true if if the given groupId has 1 or more child groups,
     * false otherwise.
     *
     * @param            $groupId               The id of the group to check for child groups
     *
     * @return           boolean                True if 1 or more child groups are found, false otherwise.
     *
     * @access public
     */
    
    static function hasChildGroups( $groupId ) {
        $dao = new CRM_Contact_DAO_GroupNesting( );
        $query = "SELECT child_group_id FROM civicrm_group_nesting WHERE parent_group_id = $groupId LIMIT 1";
        $dao->query( $query );
        return $dao->fetch( );
    }
    
    /**
     * Returns true if if the given groupId has 1 or more parent groups,
     * false otherwise.
     *
     * @param            $groupId               The id of the group to check for parent groups
     *
     * @return           boolean                True if 1 or more parent groups are found, false otherwise.
     *
     * @access public
     */
    
    static function hasParentGroups( $groupId ) {
        $dao = new CRM_Contact_DAO_GroupNesting( );
        $query = "SELECT parent_group_id FROM civicrm_group_nesting WHERE child_group_id = $groupId LIMIT 1";
        $dao->query( $query );
        return $dao->fetch( );
    }

    /**
     * Returns array of contacts who are members of the specified group.
     *
     * @param             $groupIds               An array of valid group ids (passed by reference)
     *
     * @return            $groupIdArray         List of groupIds that represent the requested group and its children
     *
     * @access public
     */
     
    static function getChildGroupIds( $groupIds ) {
        $numGroupIds = count( $groupIds );
        $dao = new CRM_Contact_DAO_GroupNesting( );
        $query = "SELECT child_group_id FROM civicrm_group_nesting WHERE parent_group_id IN (" . implode( ',', $groupIds ) . ")";
        $dao->query( $query );
        $tmpGroupIds = array( );
        while ( $dao->fetch( ) ) {
            $tmpGroupIds[] = $dao->child_group_id;
        }
        $childGroupIds = $groupIds;
        if ( ! empty( $tmpGroupIds ) ) {
            $newChildGroupIds = self::getChildGroupIds( $tmpGroupIds );
            $childGroupIds = array_merge($childGroupIds,$newChildGroupIds);
        }
        return $childGroupIds;
    }
}