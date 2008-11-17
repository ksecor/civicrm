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

require_once "CRM/Core/Form.php";
require_once "CRM/Activity/BAO/Activity.php";
require_once 'CRM/Core/BAO/File.php';
require_once "CRM/Core/BAO/CustomGroup.php";
require_once "CRM/Custom/Form/CustomData.php";
require_once "CRM/Contact/Form/AddContact.php";
require_once 'CRM/Core/OptionGroup.php';        
require_once 'CRM/Case/BAO/Case.php';
require_once "CRM/Case/PseudoConstant.php";

/**
 * This class create activities for a case
 * 
 */
class CRM_Case_Form_Activity extends CRM_Core_Form
{

    /**
     * The context
     *
     * @var string
     */
    public $_context = 'activity';

    /**
     * The case id 
     *
     * @var int
     */
    public $_id;

    /**
     * The id of activity type 
     *
     * @var int
     */
    public $_activityTypeId;

    /**
     * The id of activity type 
     *
     * @var int
     */
    public $_activityTypeName;

    /**
     * The activity id 
     *
     * @var int
     */
    public $_activityId;

    /**
     * The id of logged in user
     * 
     * @var int
     */
    public $_uid;

    /**
     * The id of contact being viewed
     * 
     * @var int
     */
    public $_clientId;

    /**
     * The default values of an activity
     *
     * @var array
     */
    public $_defaults = array();

    /**
     * Case Activity Action
     */
    public $_caseAction = null;

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
        $this->_id             = CRM_Utils_Request::retrieve( 'id',    'Positive', $this,  true );
        $this->_activityId     = CRM_Utils_Request::retrieve( 'aid'  , 'Positive', $this );
        
        $isActTypeReqd = true;
        if ( $this->_activityId ) {
            $isActTypeReqd = false;
        }
        $this->_activityTypeId = CRM_Utils_Request::retrieve( 'atype', 'Positive', $this, $isActTypeReqd );
        
        if ( !$this->_activityTypeId && $this->_activityId ) {
            $this->_activityTypeId = CRM_Core_DAO::getFieldValue( 'CRM_Activity_DAO_Activity',
                                                                  $this->_activityId,
                                                                  'activity_type_id' );
        }

        if ( $this->_caseAction = CRM_Case_BAO_Case::getFileForActivityTypeId($this->_activityTypeId) ) {
            require_once "CRM/Case/Form/Activity/{$this->_caseAction}.php";
            $this->assign( 'caseAction', $this->_caseAction );
        }
        
        //retrieve details about case
        $caseParams       = array( 'id' => $this->_caseId );
        $returnProperties = array( 'case_type_id', 'subject' );
        CRM_Core_DAO::commonRetrieve('CRM_Case_BAO_Case', $caseParams, $values, $returnProperties );
        $values['case_type_id'] = explode( CRM_Case_BAO_Case::VALUE_SEPERATOR, 
                                           trim(CRM_Utils_Array::value('case_type_id', $values), 
                                                CRM_Case_BAO_Case::VALUE_SEPERATOR) );
        $caseTypes   = CRM_Case_PseudoConstant::caseType( );
        $caseType    = $caseTypes[$values['case_type_id'][0]];
        $this->assign('caseType', $caseType);

        $caseSubject = $values['subject'];
        $this->assign( 'caseSubject', $caseSubject );

        $session    =& CRM_Core_Session::singleton();
        // logged in contact
        $this->_uid = $session->get('userID');

        // contact being viewed
        $this->_clientId = $this->get('contactId');
        if ( ! $this->_clientId ) {
            $this->_clientId = CRM_Utils_Request::retrieve( 'cid', 'Positive', $this );
        }

        $clientName = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact',
                                                   $this->_clientId,
                                                   'sort_name' );
        $this->assign( 'client_name', $clientName );
        
        $this->_action = CRM_Utils_Request::retrieve('action', 'String', $this );
        $this->assign( 'action', $this->_action);

        $this->_addAssigneeContact = CRM_Utils_Array::value( 'assignee_contact', $_GET );
        $this->assign('addAssigneeContact', false);
        if ( $this->_addAssigneeContact ) {
            $this->assign('addAssigneeContact', true);
        }

        $this->assign( 'urlPath', 'civicrm/case/activity' );

        // add attachments part
        CRM_Core_BAO_File::buildAttachment( $this,
                                            'civicrm_activity',
                                            $this->_activityId );
        
        // assign activity type name and description to template
        require_once 'CRM/Core/BAO/OptionValue.php';
        $categoryParams = array('id' => $this->_activityTypeId);
        CRM_Core_DAO::commonRetrieve('CRM_Core_DAO_Category', $categoryParams, $details );
        
        $this->_activityTypeName    = $details['label'];
        $activityTypeDescription    = $details['description'];

        $this->assign( 'activityTypeName',        $this->_activityTypeName );
        $this->assign( 'activityTypeDescription', $activityTypeDescription );

        if ( !$this->_activityId ) { 
            // check if activity count is within the limit
            require_once 'CRM/Case/XMLProcessor/Process.php';
            $xmlProcessor  = new CRM_Case_XMLProcessor_Process( );
            $activityInst  = $xmlProcessor->getMaxInstance($caseType);

            // Activity type is only included in getMaxInstance array if a max_instance property is set. If not, no limit on that type.
            if ( isset( $activityInst[$this->_activityTypeName] ) ) {
                $activityCount = CRM_Case_BAO_Case::getCaseActivityCount( $this->_id, $this->_activityTypeId );
                if ( $activityCount >= $activityInst[$this->_activityTypeName] ) {
                    if ( $activityInst[$this->_activityTypeName] == 1 ) {
                        $activities = 
                            CRM_Case_BAO_Case::getCaseActivity( $this->_id, 
                                                                array('activity_type_id' => 
                                                                      $this->_activityTypeId), 
                                                                $this->_uid );
                        $activities = array_keys($activities);
                        $activities = $activities[0];
                        $editUrl    = 
                            CRM_Utils_System::url( 'civicrm/case/activity', 
                                                   "reset=1&cid={$this->_clientId}&id={$this->_id}&aid={$activities}" );
                    }
                    CRM_Core_Error::statusBounce( ts("You can not add another '%1' activity to this case. %2", 
                                                     array( 1 => $this->_activityTypeName,
                                                            2 => "Do you want to <a href='$editUrl'>edit the existing activity</a> ?" )) );
                }
            }
        }

        CRM_Utils_System::setTitle( $this->_activityTypeName );

        //when custom data is included in this page
        $this->set( 'type'    , 'Activity' );
        $this->set( 'subType' , $this->_activityTypeId );
        $this->set( 'entityId', $this->_activityId );
        CRM_Custom_Form_Customdata::preProcess( $this );

        // add 2 attachments
        require_once 'CRM/Core/BAO/File.php';
        CRM_Core_BAO_File::buildAttachment( $this,
                                            'civicrm_activity',
                                            $this->_activityId, 2 );
       
        if ( $this->_caseAction ) {
            eval("CRM_Case_Form_Activity_{$this->_caseAction}::preProcess( \$this );");
        }
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
        if ( isset($this->_activityId) ) {
            $params = array( 'id' => $this->_activityId );
            CRM_Activity_BAO_Activity::retrieve( $params, $this->_defaults );
            //set the assigneed contact count to template
            if ( !empty( $defaults['assignee_contact'] ) ) {
                $this->assign( 'assigneeContactCount', count( $defaults['assignee_contact'] ) );
            } else {
                $this->assign( 'assigneeContactCount', 1 );
            }
            
            // custom data defaults
            $this->_defaults += CRM_Custom_Form_Customdata::setDefaultValues( $this );
            
        } else {
            CRM_Utils_Date::getAllDefaultValues( $this->_defaults['due_date_time'] );
            $this->_defaults['due_date_time']['i'] = 
                (int ) ( $this->_defaults['activity_date_time']['i'] / 15 ) * 15;
            
            CRM_Utils_Date::getAllDefaultValues( $this->_defaults['activity_date_time'] );
            $this->_defaults['activity_date_time']['i'] = 
                (int ) ( $this->_defaults['activity_date_time']['i'] / 15 ) * 15;

            // set default encounter medium if an option_value default is set for that option_group
            $medium = CRM_Core_OptionGroup::values('encounter_medium', false, false, false, 'AND is_default = 1');
            if ( count($medium) == 1 ) {
                $this->_defaults['medium_id'] = key($medium);
            }
        }
        
        if ( $this->_caseAction ) {
            eval('$this->_defaults += CRM_Case_Form_Activity_'. $this->_caseAction . '::setDefaultValues($this);');
        }
        return $this->_defaults;
    }
    
    public function buildQuickForm( ) 
    {
        if ( $this->_addAssigneeContact ) {
            $contactCount = CRM_Utils_Array::value( 'count', $_GET );
            $this->assign('contactCount', $contactCount );
            $this->assign('nextContactCount', $contactCount + 1 );
            $this->assign('contactFieldName', 'assignee_contact' );
            return CRM_Contact_Form_AddContact::buildQuickForm( $this, "assignee_contact[{$contactCount}]" );
        }

        CRM_Custom_Form_Customdata::buildQuickForm( $this );
        // we don't want to show button on top of custom form
        $this->assign('noPreCustomButton', true);

        // add a dojo facility for searching contacts
        $this->assign( 'dojoIncludes', " dojo.require('dojox.data.QueryReadStore'); dojo.require('dojo.parser');" );
        $attributes = array( 'dojoType'       => 'civicrm.FilteringSelect',
                             'mode'           => 'remote',
                             'store'          => 'contactStore',
                             'pageSize'       => 10  );
        
        $dataUrl = CRM_Utils_System::url( "civicrm/ajax/search",
                                         "reset=1",
                                         false, null, false );
        $this->assign('dataUrl',$dataUrl );
        
        $admin = CRM_Core_Permission::check( 'administer CiviCRM' );
        $this->assign('admin', $admin);
        
        $sourceContactField =& $this->add( 'text','source_contact_id', ts('Reported By'), $attributes, $admin );
        $this->assign( 'source_contact', $this->_uid );

        // FIXME: Need to add "Case Role" field as spec'd in CRM-3743
        // FIXME: Need to add fields for "Send Copy To" functionality as spec'd in CRM-3743
        
        $this->add('text', 'subject', ts('Subject') , 
                   CRM_Core_DAO::getAttribute( 'CRM_Activity_DAO_Activity', 'subject' ), true);
        
        $this->add('select', 'medium_id',  ts( 'Medium' ), 
                   CRM_Core_OptionGroup::values('encounter_medium'), true);

        $this->add('date', 'due_date_time', ts('Due Date'), CRM_Core_SelectValues::date('activityDatetime'), true);
        $this->addRule('due_date_time', ts('Select a valid date.'), 'qfDate');

        $this->add('date', 'activity_date_time', ts('Actual Date'), 
                   CRM_Core_SelectValues::date('activityDatetime'));
        $this->addRule('activity_date_time', ts('Select a valid date.'), 'qfDate');
        
        $this->add( 'text', 'duration', ts('Duration'),array( 'size'=> 4,'maxlength' => 8 ) );
        $this->addRule('duration', ts('Please enter the duration as number of minutes (integers only).'), 'positiveInteger');  

        $this->add('text', 'location', ts('Location'), CRM_Core_DAO::getAttribute( 'CRM_Activity_DAO_Activity', 'location' ) );
      
        $this->add('select','status_id',ts('Activity Status'), CRM_Core_PseudoConstant::activityStatus( ), true );
        
        $this->add('textarea', 'details', ts('Details'), 
                   CRM_Core_DAO::getAttribute( 'CRM_Activity_DAO_Activity', 'details' ) );

        $this->add( 'text', 'interval',ts('in'),array( 'size'=> 4,'maxlength' => 8 ) );
        $this->addRule('interval', ts('Please enter the valid interval as number (integers only).'), 
                       'positiveInteger');  
       
        $this->add( 'text', 'followup_activity', ts('Followup Activity') );

        $freqUnits = CRM_Core_OptionGroup::values( 'recur_frequency_units', false, false, false, null, 'name' );
        foreach ( $freqUnits as $name => $label ) {
            $freqUnits[$name] = $label . '(s)';
        }
        $this->add( 'select', 'interval_unit', null, $freqUnits );

        if ( $this->_caseAction ) {
            eval("CRM_Case_Form_Activity_{$this->_caseAction}::buildQuickForm( \$this );");
        }

        $this->addUploadElement( CRM_Core_BAO_File::uploadNames( ) );
        $this->addButtons( array(
                                 array ( 'type'      => $this->buttonType( ),
                                         'name'      => ts('Save'),
                                         'isDefault' => true   ),
                                 array ( 'type'      => 'cancel',
                                         'name'      => ts('Cancel') ),
                                 )
                           );
               
        $this->_relatedContacts = CRM_Case_BAO_Case::getRelatedContacts( $this->_id );
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

        if ( $this->_caseAction ) {
            eval('$this->addFormRule' . "(array('CRM_Case_Form_Activity_{$this->_caseAction}', 'formrule'), \$this);");
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
        $errors = array( );
        
        //FIX me temp. comment
        // make sure if associated contacts exist
        require_once 'CRM/Contact/BAO/Contact.php';
        
        if ( $fields['source_contact_id'] && ! is_numeric($fields['source_contact_id'])) {
            $errors['source_contact_id'] = ts('Reported By contact does not exist. Please select a contact from the available options.');
        }
        
        foreach ( $fields['assignee_contact'] as $key => $id ) {
            if ( $id && ! is_numeric($id)) {
                $errors["assignee_contact[$key]"] = ts('Assignee Contact %1 does not exist.', array(1 => $key));
            }
        }
        
        return $errors;
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
        if ( $this->_caseAction ) {
            require_once 'CRM/Case/XMLProcessor/Process.php';
            eval("CRM_Case_Form_Activity_{$this->_caseAction}" . "::beginPostProcess( \$this, \$params );");
        }

        // store the dates with proper format
        $params['activity_date_time'] = CRM_Utils_Date::format( $params['activity_date_time'] );
        $params['due_date_time']      = CRM_Utils_Date::format( $params['due_date_time'] );
        $params['activity_type_id']   = $this->_activityTypeId;
        
        // update existing case record if needed
        if ( $this->_caseAction ) {
            $params['id'] = $this->_id;
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
            $customData = array( );
            foreach ( $params as $key => $value ) {
                if ( $customFieldId = CRM_Core_BAO_CustomField::getKeyID( $key ) ) { 
                    CRM_Core_BAO_CustomField::formatCustomField( $customFieldId, $customData,
                                                                 $value, 'Activity', null, $activityId );
                }
            }
            
            if ( !empty($customData) ) {
                $params['custom'] = $customData;
            }
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
            $recordStatus = 'edited';
        }
        
        // call end post process
        if ( $this->_caseAction ) {
            eval("CRM_Case_Form_Activity_{$this->_caseAction}" . 
                 "::endPostProcess( \$this, \$params );");
        }

        // add attachments as needed (for more old activity)
        if ( ! isset($newActParams) ) {
            CRM_Core_BAO_File::formatAttachment( $params,
                                                 $params,
                                                 'civicrm_activity' );
        }

        // activity create
        $activity = CRM_Activity_BAO_Activity::create( $params );
        
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
            
            // call end post process
            if ( $this->_caseAction ) {
                eval("CRM_Case_Form_Activity_{$this->_caseAction}" . 
                     "::endPostProcess( \$this, \$newActParams );");
            }

            // add attachments if any
            CRM_Core_BAO_File::formatAttachment( $newActParams,
                                                 $newActParams,
                                                 'civicrm_activity' );

            $activity = CRM_Activity_BAO_Activity::create( $newActParams );

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
                             'case_id'     => $this->_id   );
        CRM_Case_BAO_Case::processCaseActivity( $caseParams );


        // Insert civicrm_log record for the activity (e.g. store the
        // created / edited by contact id and date for the activity)
        // Note - civicrm_log is already created by CRM_Activity_BAO_Activity::create()


        //send copy to selected contacts.        
        $mailStatus = '';
        if ( array_key_exists('contact_check', $params) ) {
            $mailToContacts = array();
            foreach( $params['contact_check'] as $cid => $dnc ) {
                $mailToContacts[$cid] = $this->_relatedContacts[$cid];
            }
            $result = CRM_Case_BAO_Case::sendActivityCopy( $this->_clientId, $activity->id, $mailToContacts );
            $mailStatus = "A copy of the activity has also been sent to selected contacts(s).";
        }

        // create follow up activity if needed
        if ( CRM_Utils_Array::value('followup_activity', $params) ) {
            $followupParams                      = array( );
            $followupParams['parent_id']         = $activity->id;
            $followupParams['source_contact_id'] = $params['source_contact_id'];
            $followupParams['subject']           = $params['subject'];
            $followupParams['status_id']         = 
                CRM_Core_OptionGroup::getValue( 'activity_status', 'Scheduled', 'name' );
            $followupParams['activity_type_id']  = 
                CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_Category',
                                             $params['followup_activity'],
                                             'id', 'label' );
            CRM_Utils_Date::getAllDefaultValues( $currentDate );
            $followupParams['due_date_time']        = 
                CRM_Utils_Date::intervalAdd($params['interval_unit'], 
                                            $params['interval'], $currentDate); 
            $followupParams['due_date_time']     =  CRM_Utils_Date::format($followupParams['due_date_time']);

            $followupActivity = CRM_Activity_BAO_Activity::create( $followupParams );
        }

        CRM_Core_Session::setStatus( ts("'%1' activity has been successfully %2. %3", 
                                        array('1' => $this->_activityTypeName, 
                                              '2' => $recordStatus, 
                                              '3' => $mailStatus)) );
    }
}
