<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
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
     * Converts a group into an organization by creating an organization and
     * adding the association in the civicrm_group_org table.
     *
     * @param            $groupId               The id of the group to add to
     *                                           the civicrm_group_org table
     * @param            $groupName             Optional parameter that allows
     *                                           the organization contact to
     *                                           be appropriately named.
     *
     * @return           void
     *
     * @access public
     */
    
  static function add( $groupId, $groupName = null ) {
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


    /**
     * Checks whether a group is associated with an organization contact.
     * @param            $groupId               The id of the group
     *
     * @return           boolean                true if the group is found
     *                                           in the civicrm_group_org
     *                                           table, false otherwise
     *
     * @access public
     */

    static function exists( $groupId ) {
        $dao = new CRM_Contact_DAO_GroupOrg( );
	$query = "SELECT org_id FROM civicrm_group_org WHERE group_id = $groupId";
	$dao->query($query);
	
	if ($dao->fetch()){
	    return true;
	}
	else{
	    return false;
	}
    

    }

    /**
     * Retrieves the id in the civcrm_organization table for the corresponding
     * organization contact to the given group.
     *
     * @param            $groupId               The id of the group
     *
     *
     * @return           $orgId                 Returns the id of the
     *                                           organization, if there is one;
     *                                           null otherwise.
     *
     * @access public
     */

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
    
    /**
     * Removes an association from the civicrm_group_org table. Does not
     * delete the organization.
     * @param            $groupId               The id of the group
     *     
     * @return           void
     *
     * @access public
     */

    static function remove( $groupId ) {
        $dao = new CRM_Contact_DAO_GroupOrg( );
	$query = "DELETE FROM civicrm_group_org WHERE group_id = $groupId";
	$dao->query($query);



    }

    /**
     * Retrieves the id in the civicrm_contact table for the organization
     * associated with the given group.
     *
     * @param            $groupId               The id of the group
     *
     * @return           $orgContactId          The id of the org in the
     *                                           civicrm_contact table,
     *                                           if it exists; null otherwise.
     *
     * @access public
     */

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
