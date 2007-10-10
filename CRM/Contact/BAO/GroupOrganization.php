<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.8                                                |
 +--------------------------------------------------------------------+
 | Copyright U.S. PIRG Education Fund (c) 2007                        |
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
 * @copyright U.S. PIRG Education Fund 2007
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
    static function add( $groupId, $groupTitle = null ) {
        require_once('CRM/Contact/BAO/Organization.php');
        $countObj =& new CRM_Contact_BAO_Contact();
        $count = $countObj->count();
        $params = array('organization_name' => $groupTitle, 'contact_type' => 'Organization');
        $ids = array();
        require_once ( 'CRM/Contact/BAO/Organization.php' ) ;
        require_once ( 'CRM/Contact/BAO/Contact.php' );
        require_once ( 'CRM/Contact/DAO/Organization.php' ); 
        require_once ( 'CRM/Contact/DAO/Contact.php' );
        $dao = new CRM_Contact_DAO_Contact( );
        $query = "SELECT contact_id, id FROM civicrm_organization WHERE organization_name = \"$groupTitle\"";
        $dao->query($query);
        if ( $dao->fetch() ) {
            $contactId = $dao->contact_id;
	        $orgId = $dao->id;
        }

        if ( isset ($contactId) ) {
	        $params['contact_id'] = $contactId;
	        $ids['contact'] = $contactId;
	        CRM_Contact_BAO_Contact::add($params, $ids);
	        //CRM_Core_Error::debug('calling add', $params);
        } else {
	        CRM_Core_Error::debug('calling create', $params);
	        CRM_Contact_BAO_Contact::create($params, $ids, 1);
        }
        //      CRM_Core_Error::debug('p', $params);
        if ( ! isset ($contactId) ) {
	        $orgCount =& new  CRM_Contact_BAO_Organization();
	        $count = $orgCount->count();
	        $query = "REPLACE INTO civicrm_group_organization SET group_id = $groupId, organization_id = $count";
        } else {
	        $query = "REPLACE INTO civicrm_group_organization SET group_id = $groupId, organization_id = $orgId";
        }
        $dao = new CRM_Contact_DAO_GroupOrganization( );
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
	    $query = "SELECT organization_id FROM civicrm_group_organization WHERE group_id = '$groupId'";
	    $dao->query( $query );
	
	    if ( $dao->fetch( ) ) {
	        return true;
	    } else {
	        return false;
	    }
    }


    /**
     * Retrieves the id in the civicrm_group table for the corresponding group
     * contact to the given organization.
     * 
     * @param          $orgId                The id of the organization
     * 
     *
     * @return         $groupId              
     *
     */
    static function getGroupIds( $orgId ) {
        $groupIds = array( );
        $dao = new CRM_Contact_DAO_GroupOrganization( );
	    $query = "SELECT group_id FROM civicrm_group_organization WHERE organization_id = '$orgId'";
	    $dao->query($query);
	    while ( $dao->fetch() ) {
	        $groupIds[] = $dao->group_id;
	    }
	    return $groupIds;
    }

    /**
     * Ascends the group hierarchy to find all organizations that effectively
     * own any of the given groups.
     *
     * @param           $groupIds                Array of group ids
     * 
     * @return          $orgIds                  Array of organization ids
     *                                      array( 0 => array( 
     *                                        'organization_id' => 19,
     *                                        'contact_id' => 13 ), ... )
     *
     *
     */
    static function getOrganizationsForGroups( $groupIds ) {
        require_once 'CRM/Contact/BAO/GroupNesting.php';
	    $ancestorIds = CRM_Contact_BAO_GroupNesting::getAncestorGroupIds($groupIds, true);
	    $orgIds = array( );
	    foreach( $ancestorIds as $ancestorId ) {
	        $ids = self::getOrganizationIds( $ancestorId );
	        if ( $ids ) {
	            $orgIds[] = $ids;
	        }
	    }
	    return $orgIds;
    }

    /**
     * Retrieves the id in the civcrm_organization table for the corresponding
     * organization contact to the given group.
     *
     * @param            $groupId               The id of the group
     *
     *
     * @return           $orgId                 Returns the organization_id
     *                                          and contact_id of the
     *                                          organization, if there is one;
     *                                          null otherwise.
     *
     * @access public
     */
    static function getOrganizationIds( $groupId ) {
        $dao = new CRM_Contact_DAO_GroupOrganization( );
        $query = "SELECT cgo.organization_id, co.contact_id
                  FROM   civicrm_group_organization AS cgo
                  INNER JOIN civicrm_organization AS co
                  ON     cgo.organization_id = co.id
                  WHERE  cgo.group_id = $groupId";
        $dao->query( $query );
        if ( $dao->fetch( ) ) {
            $orgId = $dao->organization_id;
            $contactId = $dao->contact_id;
	        return array( 'organization_id' => $orgId, 'contact_id' => $contactId );
        }
        return null;
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
    static function getOrganizationContactId( $groupId ) {
        $dao = new CRM_Contact_DAO_GroupOrganization( );
        $query = "SELECT organization_id FROM civicrm_group_organization WHERE group_id = $groupId";
        $dao->query($query);
        if ( $dao->fetch( ) ) {
            $orgId = $dao->organization_id;
	    } else {
	        $orgId = null;
	    }
	    if ( $orgId != null ) {
	        $dao = new CRM_Contact_DAO_Organization();
	        $query = "SELECT contact_id FROM civicrm_organization WHERE id = $orgId";
	        $dao->query($query);
	        if ( $dao->fetch( ) ) {
	            $contactId = $dao->contact_id;
	        } else {
	            $contactId = null;
	        }
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
