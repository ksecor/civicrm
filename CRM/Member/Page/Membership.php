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
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Core/Page.php';

/**
 * Create a page for displaying Memberships
 *
 */
class CRM_Member_Page_Membership extends CRM_Core_Page {

    /** 
     * compose the url to show details of this specific membership
     * 
     * @param int $id 
     * @param int $activityHistoryId 
     * 
     * @static 
     * @access public 
     */ 
    static function details($id, $activityHistoryId) { 
        $params   = array(); 
        $defaults = array(); 
        $params['id'          ] = $activityHistoryId; 
        $params['entity_table'] = 'civicrm_contact'; 
 
        require_once 'CRM/Core/BAO/History.php'; 
        $history        = CRM_Core_BAO_History::retrieve($params, $defaults); 
        $membershipId = CRM_Utils_Array::value('activity_id', $defaults); 
 
        if ($membershipId) { 
            require_once 'CRM/Member/DAO/Membership.php';
            $cid = CRM_Core_DAO::getFieldValue( 'CRM_Member_DAO_Membership', $membershipId, 'contact_id' );
            return CRM_Utils_System::url('civicrm/contact/view/membership', "reset=1&action=view&id=$membershipId&cid=$cid&context=basic");
        } else { 
            return CRM_Utils_System::url('civicrm'); 
        } 
    } 

}

?>