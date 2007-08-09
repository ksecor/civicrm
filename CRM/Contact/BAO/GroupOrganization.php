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
 
require_once 'CRM/Contact/DAO/GroupOrganization.php';

class CRM_Contact_BAO_GroupOrganization extends CRM_Contact_DAO_GroupOrganization {
    
    /**
     * Converts a group into an organization by creating an organization and
     * adding the association in the civicrm_group_organization table.
     *
     * @param            $groupId               The id of the group to add to
     *                                          the civicrm_group_organization
     *                                          table.
     *
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
      //     CRM_Core_Error::debug('p', $params);
      //CRM_Core_Error::debug('p', $ids);
      CRM_Contact_BAO_Contact::add($params, $ids);
      //        CRM_Core_Error::debug('p', $ids);
      //   CRM_Contact_BAO_Organization::add( $params , $id);
      $contactCount =& new  CRM_Contact_BAO_Contact();
      $count = $contactCount->count();
      $dao = new CRM_Contact_DAO_GroupOrganization( );
      $query = "REPLACE INTO civicrm_group_organization SET group_id = $groupId, contact_id = $count";
      //CRM_Core_Error::debug('p', $count);
      $dao->query($query);
    	
    }


    /**
     * Checks whether a group is associated with an organization contact.
     * @param            $groupId               The id of the group
     *
     * @return           boolean            true if the group is found
     *                                      in the civicrm_group_organization
     *                                      table, false otherwise
     *
     * @access public
     */

    static function exists( $groupId ) {
        $dao = new CRM_Contact_DAO_GroupOrganization( );
	$query = "SELECT contact_id FROM civicrm_group_organization WHERE group_id = $groupId";
	$dao->query($query);
	
	if ($dao->fetch()){
	    return true;
	}
	else{
	    return false;
	}
    

    }

    /**
     * Retrieves the id in the civcrm_contact table for the corresponding
     * organization contact to the given group.
     *
     * @param            $groupId               The id of the group
     *
     *
     * @return           $contactId                 Returns the id of the
     *                                           organization, if there is one;
     *                                           null otherwise.
     *
     * @access public
     */

    static function getContactId( $groupId ) {
        $dao = new CRM_Contact_DAO_GroupOrganization( );
        $query = "SELECT contact_id FROM civicrm_group_organization WHERE group_id = $groupId";
        $dao->query($query);
        if ($dao->fetch()){
            $contactId = $dao->contact_id;
	}
	else{
	    $contactId = null;
	}
	return $contactId;
    }
    
    /**
     * Removes an association from the civicrm_group_organization table.
     * Does not delete the organization.
     *
     * @param            $groupId               The id of the group
     *     
     * @return           void
     *
     * @access public
     */

    static function remove( $groupId ) {
      $dao = new CRM_Contact_DAO_GroupOrganization( );
      $query = "DELETE FROM civicrm_group_organization WHERE group_id = $groupId";
      $dao->query($query);
	


    }
}
?>
