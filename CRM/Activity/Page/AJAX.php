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
class CRM_Activity_Page_AJAX
{
    static function getCaseActivity( ) 
    {
        $caseID     = CRM_Utils_Type::escape( $_GET['caseID'], 'Integer' );
        $contactID  = CRM_Utils_Type::escape( $_GET['cid'], 'Integer' );
        
        $params     = $_POST;

        // get the activities related to given case
        require_once "CRM/Case/BAO/Case.php";
        $activities = CRM_Case_BAO_Case::getCaseActivity( $caseID, $params, $contactID );

        $page  = $_POST['page'];
        $total = $params['total'];

        require_once "CRM/Utils/JSON.php";
        $selectorElements = array( 'due_date', 'actual_date', 'subject', 'type', 'reporter', 'status', 'links' );
        $json = CRM_Utils_JSON::encodeSelector( $activities, $page, $total, $selectorElements );
        echo $json;
    }

    static function getActivityTypeList( )
    {
        $caseType     = CRM_Utils_Type::escape( $_GET['caseType'], 'String' );
        $activityType = CRM_Utils_Type::escape( $_GET['s'], 'String' );

        require_once 'CRM/Case/XMLProcessor/Process.php';
        $xmlProcessor = new CRM_Case_XMLProcessor_Process( );
        $activities = $xmlProcessor->get( $caseType, 'ActivityTypes' );

        //unset Open Case
        unset( $activities['12'] );

        asort($activities);

        foreach( $activities as $key => $value ) {
			if ( strtolower( $activityType ) == strtolower( substr( $value, 0, strlen( $activityType ) ) ) ) {
				echo "{$value}|{$key}\n";
			}
        }
    }
}
