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
    
    static function addChildGroup( $groupId, $childGroupId ) {
        $dao = new CRM_Contact_DAO_GroupNesting( );
        $query = "REPLACE INTO civicrm_group_nesting (child_group_id, parent_group_id) VALUES ($childGroupId,$groupId)";
        $dao->query( $query );
    }
    
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
     * Returns true if the given groupId has 1 or more parent groups,
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
     * Returns true if checkGroupId is an ancestor of one of the groups in
     * groupIds, false otherwise.
     *
     * @param            $groupIds              Array of group ids (or one group id) to serve as the starting point
     * @param            $checkGroupId         The group id to check if it is an ancestor of the $groupIds group(s)
     *
     * @return           boolean                True if $checkGroupId points to a group that is an ancestor of one of the $groupIds groups, false otherwise.
     *
     * @access public
     */
    
    static function isAncestorGroup( $groupIds, $checkGroupId ) {
        if ( ! is_array( $groupIds ) ) {
            $groupIds = array( $groupIds );
        }
        $dao = new CRM_Contact_DAO_GroupNesting( );
        $query = "SELECT parent_group_id FROM civicrm_group_nesting WHERE child_group_id IN (". implode( ',', $groupIds ) . ")";
        $dao->query( $query );
        $nextGroupIds = array( );
        $gotAtLeastOneResult = false;
        while ( $dao->fetch( ) ) {
            $gotAtLeastOneResult = true;
            $parentGroupId = $dao->parent_group_id;
            if ( $parentGroupId == $checkGroupId ) {
                /* print "One of these: <pre>";
                print_r($groupIds);
                print "</pre> has groupId $checkGroupId as an ancestor.<br/>"; */
                return true;
            }
            $nextGroupIds[] = $parentGroupId;
        }
        if ( $gotAtLeastOneResult ) {
            return self::isAncestorGroup( $nextGroupIds, $checkGroupId );
        } else {
            return false;
        }
    }
    
    /**
     * Returns true if checkGroupId is a descendent of one of the groups in
     * groupIds, false otherwise.
     *
     * @param            $groupIds              Array of group ids (or one group id) to serve as the starting point
     * @param            $checkGroupId         The group id to check if it is a descendent of the $groupIds group(s)
     *
     * @return           boolean                True if $checkGroupId points to a group that is a descendent of one of the $groupIds groups, false otherwise.
     *
     * @access public
     */
    
    static function isDescendentGroup( $groupIds, $checkGroupId ) {
        if ( ! is_array( $groupIds ) ) {
            $groupIds = array( $groupIds );
        }
        $dao = new CRM_Contact_DAO_GroupNesting( );
        $query = "SELECT child_group_id FROM civicrm_group_nesting WHERE parent_group_id IN (". implode( ',', $groupIds ) . ")";
        $dao->query( $query );
        $nextGroupIds = array( );
        $gotAtLeastOneResult = false;
        while ( $dao->fetch( ) ) {
            $gotAtLeastOneResult = true;
            $childGroupId = $dao->child_group_id;
            if ( $childGroupId == $checkGroupId ) {
                /* print "One of these: <pre>";
                print_r($groupIds);
                print "</pre> has groupId $checkGroupId as a descendent.<br/><br/>"; */
                return true;
            }
            $nextGroupIds[] = $childGroupId;
        }
        if ( $gotAtLeastOneResult ) {
            return self::isDescendentGroup( $nextGroupIds, $checkGroupId );
        } else {
            return false;
        }
    }
    
    /**
     * Returns array of group ids of ancestor groups of the specified group.
     *
     * @param             $groupIds             An array of valid group ids (passed by reference)
     *
     * @return            $groupIdArray         List of groupIds that represent the requested group and its ancestors
     *
     * @access public
     */

    static function getAncestorGroupIds( $groupIds, $includeSelf = true ) {
        if ( ! is_array( $groupIds ) ) {
            $groupIds = array( $groupIds );
        }
        $numGroupIds = count( $groupIds );
        $dao = new CRM_Contact_DAO_GroupNesting( );
        $query = "SELECT parent_group_id FROM civicrm_group_nesting WHERE child_group_id IN (" . implode( ',', $groupIds ) . ")";
        $dao->query( $query );
        $tmpGroupIds = array( );
        $parentGroupIds = array( );
        if ( $includeSelf ) {
            $parentGroupIds = $groupIds;
        }
        while ( $dao->fetch( ) ) {
            // make sure we're not following any cyclical references
            if ( ! array_key_exists( $dao->child_group_id, $parentGroupIds ) && $dao->parent_group_id != $groupIds[0] ) {
                $tmpGroupIds[] = $dao->parent_group_id;
            }
        }
        if ( ! empty( $tmpGroupIds ) ) {
            $newParentGroupIds = self::getAncestorGroupIds( $tmpGroupIds );
            $parentGroupIds = array_merge( $parentGroupIds, $newParentGroupIds );
        }
        return $parentGroupIds;
    }

    /**
     * Returns array of group ids of child groups of the specified group.
     *
     * @param             $groupIds               An array of valid group ids (passed by reference)
     *
     * @return            $groupIdArray         List of groupIds that represent the requested group and its children
     *
     * @access public
     */

    static function getDescendentGroupIds( $groupIds, $includeSelf = true ) {
        if ( ! is_array( $groupIds ) ) {
            $groupIds = array( $groupIds );
        }
        $numGroupIds = count( $groupIds );
        $dao = new CRM_Contact_DAO_GroupNesting( );
        $query = "SELECT child_group_id FROM civicrm_group_nesting WHERE parent_group_id IN (" . implode( ',', $groupIds ) . ")";
        $dao->query( $query );
        $tmpGroupIds = array( );
        $childGroupIds = array( );
        if ( $includeSelf ) {
            $childGroupIds = $groupIds;
        }
        while ( $dao->fetch( ) ) {
            // make sure we're not following any cyclical references
            if ( ! array_key_exists( $dao->parent_group_id, $childGroupIds ) && $dao->child_group_id != $groupIds[0] )  {
                $tmpGroupIds[] = $dao->child_group_id;
            }
        }
        if ( ! empty( $tmpGroupIds ) ) {
            $newChildGroupIds = self::getDescendentGroupIds( $tmpGroupIds );
            $childGroupIds = array_merge($childGroupIds,$newChildGroupIds);
        }
        return $childGroupIds;
    }
    
    /**
     * Returns array of group ids of valid potential child groups of the specified group.
     *
     * @param             $groupId              The group id to get valid potential children for
     *
     * @return            $groupIdArray         List of groupIds that represent the valid potential children of the group
     *
     * @access public
     */

    static function getPotentialChildGroupIds( $groupId ) {
        require_once 'CRM/Contact/BAO/Group.php';
        $groups = CRM_Contact_BAO_Group::getGroups( );
        $potentialChildGroupIds = array( );
        foreach ( $groups as $group ) {
            $potentialChildGroupId = $group->id;
            // print "Checking if $potentialChildGroupId is a descendent/ancestor of $groupId<br/><br/>";
            if ( ! self::isDescendentGroup( $groupId, $potentialChildGroupId ) &&
                 ! self::isAncestorGroup( $groupId, $potentialChildGroupId ) &&
                 $potentialChildGroupId != $groupId ) {
                $potentialChildGroupIds[] = $potentialChildGroupId;
            }
        }
        return $potentialChildGroupIds;
    }
    
}

?>