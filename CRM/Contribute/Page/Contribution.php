<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Core/Page.php';
require_once 'CRM/Contribute/DAO/Contribution.php';

/**
 * Create a page for displaying Contributions
 *
 */
class CRM_Contribute_Page_Contribution extends CRM_Core_Page {

    /** 
     * compose the url to show details of this specific contribution 
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
        $contributionId = CRM_Utils_Array::value('activity_id', $defaults); 
 
        if ($contributionId) { 
            $cid = CRM_Core_DAO::getFieldValue( 'CRM_Contribute_DAO_Contribution', $contributionId, 'contact_id' );
            return CRM_Utils_System::url('civicrm/contact/view/contribution', "reset=1&action=view&id=$contributionId&cid=$cid&context=basic");
        } else { 
            return CRM_Utils_System::url('civicrm'); 
        } 
    } 

}

?>
