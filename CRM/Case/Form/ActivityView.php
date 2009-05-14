<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
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
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

require_once "CRM/Core/Form.php";
require_once "CRM/Activity/BAO/Activity.php";

/**
 * This class does pre processing for viewing an activity or their revisions
 * 
 */
class CRM_Case_Form_ActivityView extends CRM_Core_Form
{
    /**
     * Function to process the view
     *
     * @access public
     * @return None
     */
    public function preProcess() 
    {
        $contactID  = CRM_Utils_Request::retrieve( 'cid' , 'Integer', $this, true );
        $activityID = CRM_Utils_Request::retrieve( 'aid' , 'Integer', $this, true );
        $revs       = CRM_Utils_Request::retrieve( 'revs', 'Boolean', CRM_Core_DAO::$_nullObject );
        $caseID     = CRM_Utils_Request::retrieve( 'caseID', 'Boolean', CRM_Core_DAO::$_nullObject );

        $this->assign('contactID', $contactID );
        $this->assign('caseID', $caseID );
       
        require_once 'CRM/Case/XMLProcessor/Report.php';
        $xmlProcessor = new CRM_Case_XMLProcessor_Report( );
        $report       = $xmlProcessor->getActivityInfo( $contactID, $activityID, true );
        
        require_once 'CRM/Core/BAO/File.php';
        $attachmentUrl = CRM_Core_BAO_File::attachmentInfo( 'civicrm_activity', $activityID );
        if ( $attachmentUrl ) {
            $report['fields'][] = array ( 'label' => 'Attachment(s)',
                                          'value' => $attachmentUrl,
                                          'type'  => 'Link'
                                          );
        }  
        
        $this->assign('report', $report );

        $latestRevisionID = CRM_Activity_BAO_Activity::getLatestActivityId( $activityID );

        if ( $revs ) {
            $this->assign('revs',$revs);
            
            $priorActivities = CRM_Activity_BAO_Activity::getPriorAcitivities( $activityID );

            $this->assign( 'result' , $priorActivities );
            $this->assign( 'subject',
                           CRM_Core_DAO::getFieldValue( 'CRM_Activity_DAO_Activity', 
                                                        $activityID,
                                                        'subject' ) );
            $this->assign( 'latestRevisionID', $latestRevisionID );
        } else {
            $countPriorActivities = CRM_Activity_BAO_Activity::getPriorCount( $activityID );

            if ( $countPriorActivities >= 1 ) {
                $this->assign( 'activityID', $activityID ); 
            }

            if ( $latestRevisionID != $activityID ) {
                $this->assign( 'latestRevisionID', $latestRevisionID );
            }
        }

        $parentID =  CRM_Activity_BAO_Activity::getParentActivity( $activityID );
        if ( $parentID ) { 
            $this->assign( 'parentID', $parentID );
        }
    }
}
