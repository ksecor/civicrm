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
           
}

?>
