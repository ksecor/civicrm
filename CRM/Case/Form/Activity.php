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
     * Function to build the form
     *
     * @return None
     * @access public
     */
    function preProcess( ) 
    {        
        $this->_id             = CRM_Utils_Request::retrieve( 'id',    'Positive', $this,  true );
        $this->_activityTypeId = CRM_Utils_Request::retrieve( 'atype', 'Positive', $this,  true );
        $this->_activityId     = CRM_Utils_Request::retrieve( 'aid'  , 'Positive', $this );

        if ( $this->_caseAction = CRM_Case_BAO_Case::getFileForActivityTypeId($this->_activityTypeId) ) {
            require_once "CRM/Case/Form/Activity/{$this->_caseAction}.php";
            $this->assign( 'caseAction', $this->_caseAction );
        }

        $caseSub = CRM_Core_DAO::getFieldValue( 'CRM_Case_DAO_Case',
                                                $this->_id,
                                                'subject' );

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
        $this->_defaults['subject'] = $details['label'];

        $this->assign( 'activityTypeName',        $this->_activityTypeName );
        $this->assign( 'activityTypeDescription', $activityTypeDescription );

        CRM_Utils_System::setTitle( ts('%1 : %2', array('1' => $caseSub,'2' => $this->_activityTypeName)) );

        //when custom data is included in this page
        $this->set( 'type'    , 'Activity' );
        $this->set( 'subType' , $this->_activityTypeId );
        $this->set( 'entityId', $this->_activityId );
        CRM_Custom_Form_Customdata::preProcess( $this );

        // user context
        $url = CRM_Utils_System::url( 'civicrm/contact/view/case',
                                      "reset=1&cid={$this->_clientId}&action=view&id={$this->_id}&show=1&selectedChild=case" );
        $session->pushUserContext( $url );

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
 
            // duration
            if ( CRM_Utils_Array::value('duration',$this->_defaults) ) {
                require_once "CRM/Utils/Date.php";
                list( $this->_defaults['duration_hours'], 
                      $this->_defaults['duration_minutes'] ) = 
                    CRM_Utils_Date::unstandardizeTime( $defaults['duration'] );
            }
        } else {
            CRM_Utils_Date::getAllDefaultValues( $this->_defaults['due_date_time'] );
            $this->_defaults['due_date_time']['i'] = 
                (int ) ( $this->_defaults['activity_date_time']['i'] / 15 ) * 15;

            CRM_Utils_Date::getAllDefaultValues( $this->_defaults['activity_date_time'] );
            $this->_defaults['activity_date_time']['i'] = 
                (int ) ( $this->_defaults['activity_date_time']['i'] / 15 ) * 15;
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
                   CRM_Core_SelectValues::date('activityDatetime'), true);
        $this->addRule('activity_date_time', ts('Select a valid date.'), 'qfDate');
        
        $this->add('select','duration_hours',ts('Duration'), CRM_Core_SelectValues::getHours());
        $this->add('select','duration_minutes', null, CRM_Core_SelectValues::getMinutes());
        
        $this->add('select','status_id',ts('Activity Status'), CRM_Core_PseudoConstant::activityStatus( ), true );
        
        $this->add('textarea', 'details', ts('Details'), 
                   CRM_Core_DAO::getAttribute( 'CRM_Activity_DAO_Activity', 'details' ) );

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
        $params['now'] = date("Ymd");

        // required for status msg
        $recordStatus = 'created';

        // call begin post process
        if ( $this->_caseAction ) {
            require_once 'CRM/Case/XMLProcessor/Process.php';
            eval("CRM_Case_Form_Activity_{$this->_caseAction}" . "::beginPostProcess( \$this, \$params );");
        }

        // edit existing case if needed
        if ( $this->_caseAction ) {
            $params['id'] = $this->_id;
            require_once 'CRM/Case/BAO/Case.php';
            if ( CRM_Utils_Array::value('case_type_id', $params ) ) {
                $caseType = CRM_Core_OptionGroup::values('case_type');
                $params['case_type'] = $caseType[$params['case_type_id']];
                $params['case_type_id'] = CRM_Case_BAO_Case::VALUE_SEPERATOR . 
                    $params['case_type_id'] . CRM_Case_BAO_Case::VALUE_SEPERATOR;
            }
            $caseObj = CRM_Case_BAO_Case::create( $params );
            $params['case_id'] = $caseObj->id;
            // unset any ids, custom data
            unset($params['id'], $params['custom']);
        }

        // store the date with proper format
        $params['activity_date_time'] = CRM_Utils_Date::format( $params['activity_date_time'] );
        $params['due_date_time']      = CRM_Utils_Date::format( $params['due_date_time'] );
        $params['activity_type_id']   = $this->_activityTypeId;

        // format activity custom data
        if ( CRM_Utils_Array::value( 'hidden_custom', $params ) ) {
            $customData = array( );
            foreach ( $params as $key => $value ) {
                if ( $customFieldId = CRM_Core_BAO_CustomField::getKeyID( $key ) ) { 
                    CRM_Core_BAO_CustomField::formatCustomField( $customFieldId, $customData,
                                                                 $value, 'Activity', null, $this->_activityId );
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
        }

        // create case activity record
        $caseParams = array( 'activity_id' => $activity->id,
                             'case_id'     => $this->_id   );
        CRM_Case_BAO_Case::processCaseActivity( $caseParams );

        // Insert civicrm_log record for the activity (e.g. store the
        // created / edited by contact id and date for the activity)
        
        // Note - civicrm_log is already created by CRM_Activity_BAO_Activity::create()
        
        CRM_Core_Session::setStatus( ts("The activity of type '%1' has been successfully %2.", 
                                        array('1' => $this->_activityTypeName, '2' => $recordStatus)) );
    }
}
