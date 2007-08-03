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
 
require_once 'CRM/Contact/DAO/GroupOrg.php';

class CRM_Contact_BAO_GroupOrg extends CRM_Contact_DAO_GroupOrg {
    
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
    
  static function addOrg( $groupId, $groupName = null ) {
      require_once('CRM/Contact/BAO/Organization.php');
      $countObj =& new CRM_Contact_BAO_Contact();
      $count = $countObj->count();
      $params = array('organization_name' => $groupName, 'contact_type' => 'Organization', 'contact_id' => $count + 1);
      $ids = array('contact' => $count + 1);
      include_once ( 'CRM/Contact/BAO/Organization.php' ) ;
      include_once ( 'CRM/Contact/BAO/Contact.php' );
      CRM_Contact_BAO_Contact::add($params, $id);
      CRM_Contact_BAO_Organization::add( $params , $id);
      $orgCount =& new  CRM_Contact_BAO_Organization();
      $count = $orgCount->count();
      $dao = new CRM_Contact_DAO_GroupOrg( );
      $query = "REPLACE INTO civicrm_group_org SET group_id = $groupId, org_id = $count";
      $dao->query($query);
    	
    }

    static function getOrgId( $groupId ) {
      $dao = new CRM_Contact_DAO_GroupOrg( );
      $query = "SELECT org_id FROM civicrm_group_org WHERE group_id = $groupId";
      $dao->query($query);
      if ($dao->fetch()){
	$orgId = $dao->org_id;
      }
      else{
	$orgId = null;
      }
      return $orgId;


    }


    static function getOrgContactId( $groupId ) {
      $orgId = self::getOrgId($groupId);
      if (empty ($orgId) ) {
	return null;
      }
      $dao = new CRM_Contact_DAO_Organization( );
      $query = "SELECT contact_id FROM civicrm_organization WHERE id = $orgId";
      $dao->query($query);
      
      if ($dao->fetch()){
	$orgContactId = $dao->contact_id;
      }
      else{
	$orgContactId = null;
      }

      return $orgContactId;
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
/*    
    static function removeChildGroup( $groupId, $childGroupId ) {
        $dao = new CRM_Contact_DAO_GroupNesting( );
        $query = "DELETE FROM civicrm_group_nesting WHERE child_group_id = $childGroupId AND parent_group_id = $groupId";
        $dao->query( $query );
    }
  */  
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
/*    
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
  */  
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
/*    
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
   */ 

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
/*
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
      //          print "One of these: <pre>";
        //        print_r($groupIds);
          //      print "</pre> has groupId $checkGroupId as an ancestor.<br/>"; 
                return true;
            }
        }
	return false;


    }
*/

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
/*    static function isChildGroup($groupIds, $checkGroupId){

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
        //        print "One of these: <pre>";
         //       print_r($groupIds);
           //     print "</pre> has groupId $checkGroupId as a descendent.<br/><br/>"; 
                return true;
            }
        }
	return false;
    }
*/
  
       
}

?>
