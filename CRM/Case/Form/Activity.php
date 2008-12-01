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
 * $Id$
 *
 */

require_once 'CRM/Core/OptionGroup.php';        
require_once "CRM/Case/PseudoConstant.php";
require_once 'CRM/Case/XMLProcessor/Process.php';
require_once "CRM/Activity/Form/Activity.php";
require_once 'CRM/Contact/BAO/Contact.php';

/**
 * This class create activities for a case
 * 
 */
class CRM_Case_Form_Activity extends CRM_Activity_Form_Activity
{
    /**
     * The default values of an activity
     *
     * @var array
     */
    public $_defaults = array();

    /**
     * The array of releted contact info  
     *
     * @var array
     */
    public $_relatedContacts;

    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    function preProcess( ) 
    {        
        $result = parent::preProcess( );

        if ( $this->_cdType ) {
            return $result;
        }

        if ( !$this->_caseId ||
             (!$this->_activityId && !$this->_activityTypeId) ) {
            CRM_Core_Error::fatal('required params missing.');            
        }

        $caseType  = CRM_Case_PseudoConstant::caseTypeName( $this->_caseId );
        $caseType  = $caseType['name'];
        $this->assign('caseType', $caseType);

        $clientName = $this->_getDisplayNameById( $this->_currentlyViewedContactId );
        $this->assign( 'client_name', $clientName );
        
        if ( !$this->_activityId ) { 
            // check if activity count is within the limit
            $xmlProcessor  = new CRM_Case_XMLProcessor_Process( );
            $activityInst  = $xmlProcessor->getMaxInstance($caseType);

            // If not bounce back and also provide activity edit link
            if ( isset( $activityInst[$this->_activityTypeName] ) ) {
                $activityCount = CRM_Case_BAO_Case::getCaseActivityCount( $this->_caseId, $this->_activityTypeId );
                if ( $activityCount >= $activityInst[$this->_activityTypeName] ) {
                    if ( $activityInst[$this->_activityTypeName] == 1 ) {
                        $activities = 
                            CRM_Case_BAO_Case::getCaseActivity( $this->_caseId, 
                                                                array('activity_type_id' => 
                                                                      $this->_activityTypeId), 
                                                                $this->_currentUserId );
                        $activities = array_keys($activities);
                        $activities = $activities[0];
                        $editUrl    = 
                            CRM_Utils_System::url( 'civicrm/case/activity', 
                                                   "reset=1&cid={$this->_currentlyViewedContactId}&id={$this->_caseId}&aid={$activities}" );
                    }
                    CRM_Core_Error::statusBounce( ts("You can not add another '%1' activity to this case. %2", 
                                                     array( 1 => $this->_activityTypeName,
                                                            2 => "Do you want to <a href='$editUrl'>edit the existing activity</a> ?" )) );
                }
            }
        }

        CRM_Utils_System::setTitle( $this->_activityTypeName );

        $this->_crmDir  = 'Case';
        $this->_context = 'activity';

        // set context
        $url = CRM_Utils_System::url( 'civicrm/contact/view/case',
                                      "reset=1&action=view&cid={$this->_currentlyViewedContactId}&id={$this->_caseId}&show=1" );
        $session =& CRM_Core_Session::singleton( );
        $session->pushUserContext( $url );
    }
    
    /**
     * This function sets the default values for the form. For edit/view mode
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) 
    {
        $this->_defaults = parent::setDefaultValues( );
        
        return $this->_defaults;
    }
    
    public function buildQuickForm( ) 
    {
        // FIXME: Need to add "Case Role" field as spec'd in CRM-3743

        $this->_fields['activity_date_time']['label'] = 'Actual Date'; 
        $result = parent::buildQuickForm( );

        if ( $this->_cdType || $this->_addAssigneeContact || $this->_addTargetContact ) {
            return $result;
        }

        $this->assign( 'urlPath', 'civicrm/case/activity' );

        $this->add('select', 'medium_id',  ts( 'Medium' ), 
                   CRM_Core_OptionGroup::values('encounter_medium'), true);

        $this->add('date', 'due_date_time', ts('Due Date'), CRM_Core_SelectValues::date('activityDatetime'), true);
        $this->addRule('due_date_time', ts('Select a valid date.'), 'qfDate');
        
        $this->addRule('activity_date_time', ts('Select a valid date.'), 'qfDate');
        
        $this->addRule('duration', 
                       ts('Please enter the duration as number of minutes (integers only).'), 'positiveInteger');  

        $this->add( 'text', 'interval',ts('in'),array( 'size'=> 4,'maxlength' => 8 ) );
        $this->addRule('interval', ts('Please enter the valid interval as number (integers only).'), 
                       'positiveInteger');  
       
        $this->add( 'text', 'followup_activity', ts('Followup Activity') );

        $freqUnits = CRM_Core_OptionGroup::values( 'recur_frequency_units', false, false, false, null, 'name' );
        foreach ( $freqUnits as $name => $label ) {
            $freqUnits[$name] = $label . '(s)';
        }
        $this->add( 'select', 'interval_unit', null, $freqUnits );

        $this->_relatedContacts = CRM_Case_BAO_Case::getRelatedContacts( $this->_caseId );
        if ( ! empty($this->_relatedContacts) ) {
            $checkBoxes = array( );
            foreach ( $this->_relatedContacts as $id => $row ) {
                $checkBoxes[$id] = $this->addElement('checkbox', $id, null, '' );
            }
            
            $this->addGroup  ( $checkBoxes, 'contact_check' );
            $this->addElement( 'checkbox', 'toggleSelect', null, null, 
                               array( 'onclick' => "return toggleCheckboxVals('contact_check',this.form);" ) );
            $this->assign    ('searchRows', $this->_relatedContacts );
        }

        $this->addFormRule( array( 'CRM_Case_Form_Activity', 'formRule' ), $this );
    }
        
    
    /**  
     * global form rule  
     *  
     * @param array $fields  the input form values  
     * @param array $files   the uploaded files if any  
     * @param array $options additional user data  
     *  
     * @return true if no errors, else array of errors  
     * @access public  
     * @static  
     */  
    static function formRule( &$fields, &$files, $self ) 
    {  
        // skip form rule if deleting
        if  ( CRM_Utils_Array::value( '_qf_Activity_next_',$fields) == 'Delete' ) {
            return true;
        }
        
        return parent::formrule( $fields, $files, $self );
    }
    
    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        // store the submitted values in an array
        $params = $this->controller->exportValues( $this->_name );
        $params['now'] = date("YmdhisA");

        if( !CRM_Utils_Array::value( 'activity_date_time', $params ) ) {
            $params['activity_date_time'] = $params['now'];
        } 
        // required for status msg
        $recordStatus = 'created';

        // call begin post process
        $this->beginPostProcess( $params );

        // store the dates with proper format
        $params['activity_date_time'] = CRM_Utils_Date::format( $params['activity_date_time'] );
        $params['due_date_time']      = CRM_Utils_Date::format( $params['due_date_time'] );
        $params['activity_type_id']   = $this->_activityTypeId;
        
        // update existing case record if needed
        if ( $this->_activityTypeFile ) {
            $params['id'] = $this->_caseId;
            if ( CRM_Utils_Array::value('case_type_id', $params ) ) {
                $caseType = CRM_Core_OptionGroup::values('case_type');
                $params['case_type'] = $caseType[$params['case_type_id']];
                $params['case_type_id'] = CRM_Case_BAO_Case::VALUE_SEPERATOR . 
                    $params['case_type_id'] . CRM_Case_BAO_Case::VALUE_SEPERATOR;
            }
            // unset activity's status_id, subject and details so they aren't written case record
            $caseParams = $params;
            unset( $caseParams['subject'], $caseParams['details'], $caseParams['status_id'] );
            $caseObj = CRM_Case_BAO_Case::create( $caseParams );
            $params['case_id'] = $caseObj->id;
            // unset any ids belonging to case, custom data
            unset($params['id'], $params['custom']);
        }

        // format activity custom data
        if ( CRM_Utils_Array::value( 'hidden_custom', $params ) ) {
            if ( $this->_activityId && $this->_defaults['is_auto'] != 0 ) {
                // since we want same custom data to be attached to
                // new activity.
                $activityId = $this->_activityId;
            }
			// build custom data getFields array
			$customFields = CRM_Core_BAO_CustomField::getFields( 'Activity', false, false, $this->_activityTypeId );
			$customFields = 
                CRM_Utils_Array::crmArrayMerge( $customFields, 
                                                CRM_Core_BAO_CustomField::getFields( 'Activity', false, false, 
                                                                                     null, null, true ) );
	        $params['custom'] = CRM_Core_BAO_CustomField::postProcess( $params,
	                                                                   $customFields,
	                                                                   $activityId,
	                                                                   'Activity' );
        }

        if ( isset($this->_activityId) ) { 
            $params['id'] = $this->_activityId;

            // activity which hasn't been modified by a user yet
            if ( $this->_defaults['is_auto'] == 1 ) { 
                $params['is_auto'] = 0;
            }

            // activity which has been created or modified by a user
            if ( $this->_defaults['is_auto'] == 0 ) {
                $newActParams = $params;
                $params = array('id' => $this->_activityId);
                $params['is_current_revision'] = 0;
            }
            
            // record status for status msg
            $recordStatus = 'updated';
        }
        
        // add attachments as needed (for more old activity)
        if ( ! isset($newActParams) ) {
            CRM_Core_BAO_File::formatAttachment( $params,
                                                 $params,
                                                 'civicrm_activity' );
        }

        // activity create
        $activity = CRM_Activity_BAO_Activity::create( $params );

        $this->endPostProcess( $params, $activity );
                
        // create a new version of activity if activity was found to
        // have been modified/created by user
        if ( isset($newActParams) ) {
            unset($newActParams['id']);
            // set proper original_id
            if ( CRM_Utils_Array::value('original_id', $this->_defaults) ) {
                $newActParams['original_id'] = $this->_defaults['original_id'];
            } else {
                $newActParams['original_id'] = $activity->id;
            }
            //is_current_revision will be set to 1 by default.
            
            $this->beginPostProcess( $newActParams );

            // add attachments if any
            CRM_Core_BAO_File::formatAttachment( $newActParams,
                                                 $newActParams,
                                                 'civicrm_activity' );
            
            $activity = CRM_Activity_BAO_Activity::create( $newActParams );
            
            $this->endPostProcess( $newActParams, $activity );

            // copy files attached to old activity if any, to new one,
            // as long as users have not selected the 'delete attachment' option.  
            if ( ! CRM_Utils_Array::value( 'is_delete_attachment', $newActParams ) ) {
                CRM_Core_BAO_File::copyEntityFile( 'civicrm_activity', $this->_activityId, 
                                                   'civicrm_activity', $activity->id );
            }

            // copy back params to original var
            $params = $newActParams;
        }

        // create case activity record
        $caseParams = array( 'activity_id' => $activity->id,
                             'case_id'     => $this->_caseId   );
        CRM_Case_BAO_Case::processCaseActivity( $caseParams );

        // create activity assignee records
        $assigneeParams = array( 'activity_id' => $activity->id );
        if (! empty($params['assignee_contact']) ) {
            foreach ( $params['assignee_contact'] as $key => $id ) {
                $assigneeParams['assignee_contact_id'] = $id;
                CRM_Activity_BAO_Activity::createActivityAssignment( $assigneeParams );
            }
        }

        // Insert civicrm_log record for the activity (e.g. store the
        // created / edited by contact id and date for the activity)
        // Note - civicrm_log is already created by CRM_Activity_BAO_Activity::create()


        // send copy to selected contacts.        
        $mailStatus = '';
        if ( array_key_exists('contact_check', $params) ) {
            $mailToContacts = array();
            foreach( $params['contact_check'] as $cid => $dnc ) {
                $mailToContacts[$cid] = $this->_relatedContacts[$cid];
            }
            $result = CRM_Case_BAO_Case::sendActivityCopy( $this->_currentlyViewedContactId, 
                                                           $activity->id, $mailToContacts );
            $mailStatus = "A copy of the activity has also been sent to selected contacts(s).";
        }

        // create follow up activity if needed
        if ( CRM_Utils_Array::value('followup_activity', $params) ) {
            $followupActivity = CRM_Activity_BAO_Activity::createFollowupActivity( $activity->id, $params );

            if ( $followupActivity ) {
                $caseParams = array( 'activity_id' => $followupActivity->id,
                                     'case_id'     => $this->_caseId   );
                CRM_Case_BAO_Case::processCaseActivity( $caseParams );
            }
        }
        
        CRM_Core_Session::setStatus( ts("'%1' activity has been %2. %3", 
                                        array('1' => $this->_activityTypeName, 
                                              '2' => $recordStatus, 
                                              '3' => $mailStatus)) );
    }
}
