<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
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
 *
 */

/**
 * This class contains all the function that are called using AJAX (jQuery)
 */
class CRM_Core_Page_AJAX_Activity
{
    static function getCaseActivity( &$config ) 
    {
        $caseID  = CRM_Utils_Type::escape( $_GET['caseID'], 'Integer' );
        
        // get the activities related to given case
        require_once "CRM/Case/BAO/Case.php";
        $activities = CRM_Case_BAO_Case::getCaseActivity( $caseID, $_POST );

        $page = $_POST['page'];
        if (!$page) $page = 1;
        $total = count($activities);

        $json = "";
        $json .= "{\n";
        $json .= "page: $page,\n";
        $json .= "total: $total,\n";
        $json .= "rows: [";
        $rc = false;

        foreach( $activities as $key => $value) {
            if ($rc) $json .= ",";
            $json .= "\n{";
            $json .= "id:'".$value['id']."',";
            $json .= "cell:['".$value['category']."','".$value['type']."'";
            $json .= ",'".addslashes($value['reporter'])."'";
            $json .= ",'".addslashes($value['due_date'])."'";
            $json .= ",'".addslashes($value['actual_date'])."'";
            $json .= ",'".addslashes($value['status'])."','".addslashes($value['links'])."']";
            $json .= "}";
            $rc = true;
        }
        
        $json .= "]\n";
        $json .= "}";
        echo $json;
    }
}
