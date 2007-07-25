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
     * Adds a new child group identified by $childGroupId to the group
     * identified by $groupId
     *
     * @param            $groupId               The id of the group to add the child to
     * @param            $childGroupId          The id of the new child group
     *
     * @return           void
     *
     * @access public
     */
    
    static function addChildGroup( $groupId, $childGroupId ) {
        $dao = new CRM_Contact_DAO_GroupNesting( );
        $query = "REPLACE INTO civicrm_group_nesting (child_group_id, parent_group_id) VALUES ($childGroupId,$groupId)";
        $dao->query( $query );
    }
    
    /**
     * Removes a child group identified by $childGroupId from the group
     * identified by $groupId; does not delete child group, just the
     * association between the two
     *
     * @param            $groupId               The id of the group to remove the child from
     * @param            $childGroupId          The id of the child group being removed
     *
     * @return           void
     *
     * @access public
     */
    
    static function removeChildGroup( $groupId, $childGroupId ) {
        $dao = new CRM_Contact_DAO_GroupNesting( );
        $query = "DELETE FROM civicrm_group_nesting WHERE child_group_id = $childGroupId AND parent_group_id = $groupId";
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
	//print $query . "\n<br><br>";
	$dao->query( $query );
        if ( $dao->fetch( ) ) {
            return true;
        }
        return false;
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
	//print $query . "\n<br><br>";
	$dao->query( $query );
        if ( $dao->fetch( ) ) {
            return true;
        }
        return false;
    }
    

/**
     * Returns true if checkGroupId is a parent of one of the groups in
     * groupIds, false otherwise.
     *
     * @param            $groupIds              Array of group ids (or one group id) to serve as the starting point
     * @param            $checkGroupId         The group id to check if it is a parent of the $groupIds group(s)
     *
     * @return           boolean                True if $checkGroupId points to a group that is a parent of one of the $groupIds groups, false otherwise.
     *
     * @access public
     */

    static function isParentGroup($groupIds, $checkGroupId){
        if ( ! is_array( $groupIds ) ) {
            $groupIds = array( $groupIds );
        }
        $dao = new CRM_Contact_DAO_GroupNesting( );
        $query = "SELECT parent_group_id FROM civicrm_group_nesting WHERE child_group_id IN (". implode( ',', $groupIds ) . ")";
        $dao->query( $query );
        while ( $dao->fetch( ) ) {
            $parentGroupId = $dao->parent_group_id;
            if ( $parentGroupId == $checkGroupId ) {
                /* print "One of these: <pre>";
                print_r($groupIds);
                print "</pre> has groupId $checkGroupId as an ancestor.<br/>"; */
                return true;
            }
        }
	return false;


    }


    /**
     * Returns true if checkGroupId is a child of one of the groups in
     * groupIds, false otherwise.
     *
     * @param            $groupIds              Array of group ids (or one group id) to serve as the starting point
     * @param            $checkGroupId         The group id to check if it is a child of the $groupIds group(s)
     *
     * @return           boolean                True if $checkGroupId points to a group that is a child of one of the $groupIds groups, false otherwise.
     *
     * @access public
     */
    static function isChildGroup($groupIds, $checkGroupId){

      if ( ! is_array( $groupIds ) ) {
	$groupIds = array( $groupIds );
        }
        $dao = new CRM_Contact_DAO_GroupNesting( );
        $query = "SELECT child_group_id FROM civicrm_group_nesting WHERE parent_group_id IN (". implode( ',', $groupIds ) . ")";
	//print $query;
	$dao->query( $query );
        while ( $dao->fetch( ) ) {
            $childGroupId = $dao->child_group_id;
            if ( $childGroupId == $checkGroupId ) {
                /* print "One of these: <pre>";
                print_r($groupIds);
                print "</pre> has groupId $checkGroupId as a descendent.<br/><br/>"; */
                return true;
            }
        }
	return false;
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
        $dao = new CRM_Contact_DAO_GroupNesting( );
        $query = "SELECT parent_group_id, child_group_id FROM civicrm_group_nesting WHERE child_group_id IN (" . implode( ',', $groupIds ) . ")";
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
    
    static function getChildGroupIds( $groupIds ) {
        if ( ! is_array( $groupIds ) ) {
            $groupIds = array( $groupIds );
        }
        $dao = new CRM_Contact_DAO_GroupNesting( );
        $query = "SELECT child_group_id FROM civicrm_group_nesting WHERE parent_group_id IN (" . implode( ',', $groupIds ) . ")";
        $dao->query( $query );
        $childGroupIds = array( );
        while ( $dao->fetch( ) ) {
            $childGroupIds[] = $dao->child_group_id;
        }
        return $childGroupIds;
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


  
    static function getParentGroupIds( $groupIds ) {
        if ( ! is_array( $groupIds ) ) {
            $groupIds = array( $groupIds );
        }
        $dao = new CRM_Contact_DAO_GroupNesting( );
        $query = "SELECT parent_group_id FROM civicrm_group_nesting WHERE child_group_id IN (" . implode( ',', $groupIds ) . ")";
        $dao->query( $query );
        $parentGroupIds = array( );
        while ( $dao->fetch( ) ) {
            $parentGroupIds[] = $dao->parent_group_id;
        }
        return $parentGroupIds;
    }

    /**
     * Returns array of group ids of parent groups of the specified group.
     *
     * @param             $groupIds               An array of valid group ids (passed by reference)
     *
     * @return            $groupIdArray         List of groupIds that represent the requested group and its parents
     *
     * @access public
     */


    static function getDescendentGroupIds( $groupIds, $includeSelf = true ) {
        if ( ! is_array( $groupIds ) ) {
            $groupIds = array( $groupIds );
        }
        $dao = new CRM_Contact_DAO_GroupNesting( );
        $query = "SELECT child_group_id, parent_group_id FROM civicrm_group_nesting WHERE parent_group_id IN (" . implode( ',', $groupIds ) . ")";
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

    
    static function getContainingGroups($contactId, $parentGroupId){
      $groups = CRM_Contact_BAO_Group::getGroups( );
      $containingGroups = array( );
      foreach ($groups as $group){
	if (self::isDescendentGroup($parentGroupId, $group->id)){
	  $members = CRM_Contact_BAO_Group::getMember($group->id);
	  if ($members[$contactId]){
	    $containingGroups[] = $group->title;
	  }
	}
      }
      //      print "\n<br>ContGroups is: ";
      //print_r ($containingGroups);
      //print "\n<br>";
      return $containingGroups;
    }
        
}

?>
