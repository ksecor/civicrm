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
require_once "CRM/Core/BAO/CustomGroup.php";
require_once 'CRM/Core/BAO/File.php';
require_once 'CRM/Core/BAO/Preferences.php';
require_once "CRM/Contact/Form/AddContact.php";
require_once "CRM/Contact/Form/Task.php";
require_once "CRM/Activity/BAO/Activity.php";
require_once "CRM/Case/BAO/Case.php";
require_once "CRM/Custom/Form/CustomData.php";

/**
 * This class generates form components for Activity
 * 
 */
class CRM_Activity_Form_Activity extends CRM_Contact_Form_Task
{

    /**
     * The id of the object being edited / created
     *
     * @var int
     */
    public $_activityId;

    /**
     * The id of activity type 
     *
     * @var int
     */
    public $_activityTypeId;

    /**
     * The id of currently viewed contact
     *
     * @var int
     */
    public $_currentlyViewedContactId;

    /**
     * The id of source contact and target contact
     *
     * @var int
     */
    protected $_sourceContactId;
    protected $_targetContactId;
    protected $_asigneeContactId;
    
    /**
     * The default variable defined
     *
     * @var int
     */
    public    $_caseId;
    protected $_single;

    /**
     * The flag for case enabled or not
     *
     * @var boolean
     */
    public $_caseEnabled = false;

    /**
     * The id of the logged in user, used when add / edit 
     *
     * @var int
     */
    public $_currentUserId;

    /**
     * The array of form field attributes
     *
     * @var array
     */
    public $_fields;

    /**
     * The the directory inside CRM, to include activity type file from 
     *
     * @var string
     */
    protected $_crmDir = 'Activity';

    /**
     * The _fields var can be used by sub class to set/unset/edit the 
     * form fields based on their requirement  
     *
     */
    function setFields() {
        $this->_fields = 
            array(
                  'subject'            =>  array( 'type'        => 'text',
                                                  'label'       => 'Subject',
                                                  'attributes'  =>  
                                                  CRM_Core_DAO::getAttribute('CRM_Activity_DAO_Activity', 
                                                                             'subject' ),
                                                  'required'    => true,
                                                  ),
                  'activity_date_time' =>  array( 'type'        => 'date',
                                                  'label'       => 'Date and Time',
                                                  'attributes'  => 
                                                  CRM_Core_SelectValues::date('activityDatetime'),
                                                  'required'    => true,
                                                  ),
                  'duration'           =>  array( 'type'        => 'text',
                                                  'label'       => 'Duration',
                                                  'attributes'  => array( 'size'=> 4,'maxlength' => 8 ),
                                                  'required'    => false,
                                                  ),
                  
                  'location'           =>  array( 'type'       => 'text',
                                                  'label'      => 'Location',
                                                  'attributes' => 
                                                  CRM_Core_DAO::getAttribute('CRM_Activity_DAO_Activity', 
                                                                             'location' ),
                                                  'required'   => false,
                                                  ),
                  'details'            => array(  'type'       => 'textarea',
                                                  'label'      => 'Details',
                                                  'attributes' => 
                                                  CRM_Core_DAO::getAttribute('CRM_Activity_DAO_Activity', 
                                                                             'details' ),
                                                  'required'   => false, 
                                                  ),
                  'status_id'          =>  array( 'type'       => 'select',
                                                  'label'      => 'Status',
                                                  'attributes' => 
                                                  CRM_Core_PseudoConstant::activityStatus( ),
                                                  'required'   => true, 
                                                  ),
                  'source_contact_id'  =>  array( 'type'       => 'text',
                                                  'label'      => 'Added By',
                                                  'attributes' => 
                                                  array('dojoType'=> 'civicrm.FilteringSelect',
                                                        'mode'    => 'remote',
                                                        'store'   => 'contactStore',
                                                        'pageSize'=> 10  ),
                                                  'required'   => false,
                                                  ),
                  );
    }

    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    function preProcess( ) 
    {        
        $this->_cdType     = CRM_Utils_Array::value( 'type', $_GET );
        $this->assign('cdType', false);
        if ( $this->_cdType ) {
            $this->assign('cdType', true);
            return CRM_Custom_Form_CustomData::preProcess( $this );
        }

        $this->_atypefile  = CRM_Utils_Array::value( 'atypefile', $_GET );
        $this->assign('atypefile', false);
        if ( $this->_atypefile ) {
            $this->assign('atypefile', true);
        }

        $this->_addAssigneeContact = CRM_Utils_Array::value( 'assignee_contact', $_GET );
        $this->assign('addAssigneeContact', false);
        if ( $this->_addAssigneeContact ) {
            $this->assign('addAssigneeContact', true);
        }

        $this->_addTargetContact = CRM_Utils_Array::value( 'target_contact', $_GET );
        $this->assign('addTargetContact', false);
        if ( $this->_addTargetContact ) {
            $this->assign('addTargetContact', true);
        }

        $session =& CRM_Core_Session::singleton( );
        $this->_currentUserId = $session->get( 'userID' );

        // this is used for setting dojo tabs
        if ( ! $this->_context ) {
            $this->_context = CRM_Utils_Request::retrieve('context', 'String', $this );
        }
        $this->assign( 'context', $this->_context );

        $this->_action = CRM_Utils_Request::retrieve('action', 'String', $this );

        if ( $this->_context != 'search') {
            // if we're not adding new one, there must be an id to
            // an activity we're trying to work on.
            if ( $this->_action != CRM_Core_Action::ADD ) {
                $this->_activityId = CRM_Utils_Request::retrieve( 'id', 'Positive', $this );
            }
        }
        
        $this->_currentlyViewedContactId = $this->get('contactId');
        if ( ! $this->_currentlyViewedContactId ) {
            $this->_currentlyViewedContactId = CRM_Utils_Request::retrieve( 'cid', 'Positive', $this );
        }
        
        $this->_activityTypeId = CRM_Utils_Request::retrieve( 'atype', 'Positive', $this );
        $this->assign( 'atype', $this->_activityTypeId );

        if ( !$this->_activityTypeId && $this->_activityId ) {
            $this->_activityTypeId = CRM_Core_DAO::getFieldValue( 'CRM_Activity_DAO_Activity',
                                                                  $this->_activityId,
                                                                  'activity_type_id' );
        }

        //check the mode when this form is called either single or as
        //search task action
        if ( $this->_activityTypeId          || 
             $this->_context == 'standalone' || 
             $this->_currentlyViewedContactId ) { 
            $this->_single = true;
            $this->assign( 'urlPath', 'civicrm/contact/view/activity' );
        } else {
            //set the appropriate action
            $advanced = null;
            $builder  = null;
            
            $session =& CRM_Core_Session::singleton();
            $advanced = $session->get('isAdvanced');
            $builder  = $session->get('isSearchBuilder');

            $searchType = "basic";
            if ( $advanced == 1 ) {
                $this->_action = CRM_Core_Action::ADVANCED;
                $searchType = "advanced";
            } else if ( $advanced == 2 && $builder = 1) {
                $this->_action = CRM_Core_Action::PROFILE;
                $searchType = "builder";
            } else if ( $advanced == 3 ) {
                $searchType = "custom";
            }
            
            parent::preProcess( );
            $this->_single    = false;

            $this->assign( 'urlPath'   , "civicrm/contact/search/$searchType" );
            $this->assign( 'urlPathVar', "_qf_Activity_display=true&qfKey={$this->controller->_key}" ); 
        }
        
        $this->assign( 'single', $this->_single );
        $this->assign( 'action', $this->_action);
        
        if ( $this->_action & CRM_Core_Action::VIEW ) {
            // get the tree of custom fields
            $this->_groupTree =& CRM_Core_BAO_CustomGroup::getTree("Activity", $this,
                                                                   $this->_activityId, 0, $this->_activityTypeId );
        }

        if ( $this->_activityTypeId ) {
            //set activity type name and description to template
            require_once 'CRM/Core/BAO/OptionValue.php';
            list( $this->_activityTypeName, $activityTypeDescription ) = 
                CRM_Core_BAO_OptionValue::getActivityTypeDetails( $this->_activityTypeId );
            $this->assign( 'activityTypeName',        $this->_activityTypeName );
            $this->assign( 'activityTypeDescription', $activityTypeDescription );
        }
        
              
        $this->_caseId      = CRM_Utils_Request::retrieve( 'caseid', 'Positive', $this );
        if ( !$this->_caseId && $this->_activityId ) {
            $this->_caseId  = CRM_Core_DAO::getFieldValue( 'CRM_Case_DAO_CaseActivity', $this->_activityId,
                                                           'case_id', 'activity_id' );
        }
        if ( $this->_caseId ) {
            $this->assign( 'caseId', $this->_caseId );
        }

        // set user context
        if ( in_array( $this->_context, array( 'standalone', 'home', 'search') ) ) {
            $url = CRM_Utils_System::url('civicrm/dashboard', 'reset=1' );
        } else if ( $this->_context == 'case' ) {
            $url = CRM_Utils_System::url('civicrm/contact/view/case',
                                         "action=view&reset=1&cid={$this->_currentlyViewedContactId}&id={$this->_caseId}&selectedChild=case" );
        } else if ( $this->_context == 'activity' ) {
            $url = CRM_Utils_System::url('civicrm/contact/view',
                                         "action=browse&reset=1&cid={$this->_currentlyViewedContactId}&selectedChild=activity" );
        }
        $session->pushUserContext( $url );
        
        // hack to retrieve activity type id from post variables
        if ( ! $this->_activityTypeId ) {
            $this->_activityTypeId = CRM_Utils_Array::value( 'activity_type_id', $_POST );
        }

        // when custom data is included in this page
        if ( CRM_Utils_Array::value( "hidden_custom", $_POST ) ) {
            // we need to set it in the session for the below code to work
            // CRM-3014
            //need to assign custom data subtype to the template
            $this->set( 'type'    , 'Activity' );
            $this->set( 'subType' , $this->_activityTypeId );
            $this->set( 'entityId', $this->_activityId );
            CRM_Custom_Form_CustomData::preProcess( $this );
            CRM_Custom_Form_CustomData::buildQuickForm( $this );
            CRM_Custom_Form_CustomData::setDefaultValues( $this );           
        }

        // build assignee contact combo
        if ( CRM_Utils_Array::value( 'assignee_contact', $_POST ) ) {
            foreach ( $_POST['assignee_contact'] as $key => $value ) {
                CRM_Contact_Form_AddContact::buildQuickForm( $this, "assignee_contact[{$key}]" );
            }
            $this->assign( 'assigneeContactCount', count( $_POST['assignee_contact'] ) );
        }

        // build target contact combo
        if ( CRM_Utils_Array::value( 'target_contact', $_POST ) ) {
            foreach ( $_POST['target_contact'] as $key => $value ) {
                CRM_Contact_Form_AddContact::buildQuickForm( $this, "target_contact[{$key}]" );
            }
            $this->assign( 'targetContactCount', count( $_POST['target_contact'] ) );
        }

        // add attachments part
        CRM_Core_BAO_File::buildAttachment( $this,
                                            'civicrm_activity',
                                            $this->_activityId );

        // figure out the file name for activity type, if any
        if ( $this->_activityTypeId   &&
             $this->_activityTypeFile = 
             CRM_Activity_BAO_Activity::getFileForActivityTypeId($this->_activityTypeId, $this->_crmDir) ) {
            
            require_once "CRM/{$this->_crmDir}/Form/Activity/{$this->_activityTypeFile}.php";
            $this->assign( 'activityTypeFile', $this->_activityTypeFile );
            $this->assign( 'crmDir', $this->_crmDir );
        }

        $this->setFields( );

        if ( $this->_activityTypeFile ) {
            eval("CRM_Case_Form_Activity_{$this->_activityTypeFile}::preProcess( \$this );");
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
        if ( $this->_cdType ) {
            return CRM_Custom_Form_CustomData::setDefaultValues( $this );
        }
        
        $defaults = array( );
        $params   = array( );
        $config   =& CRM_Core_Config::singleton( );

        // if we're editing...
        if ( isset( $this->_activityId ) ) {
            $params = array( 'id' => $this->_activityId );
            CRM_Activity_BAO_Activity::retrieve( $params, $defaults );

            if ( !CRM_Utils_Array::value('activity_date_time', $defaults) ) {
                $defaults['activity_date_time'] = array( );
                CRM_Utils_Date::getAllDefaultValues( $defaults['activity_date_time'] );
            }

            $this->assign('caseSubject', $defaults['case_subject']);

            //set the assigneed contact count to template
            if ( !empty( $defaults['assignee_contact'] ) ) {
                $this->assign( 'assigneeContactCount', count( $defaults['assignee_contact'] ) );
            } else {
                $this->assign( 'assigneeContactCount', 1 );
            }

            //set the target contact count to template
            if ( !empty( $defaults['target_contact'] ) ) {
                $this->assign( 'targetContactCount', count( $defaults['target_contact'] ) );
            } else {
                $this->assign( 'targetContactCount', 1 );
            }

            if ( $this->_context != 'standalone' )  {
                $this->assign( 'target_contact_value'  , 
                               CRM_Utils_Array::value( 'target_contact_value', $defaults ) );
                $this->assign( 'assignee_contact_value', 
                               CRM_Utils_Array::value( 'assignee_contact_value', $defaults ) );
                $this->assign( 'source_contact_value'  , 
                               CRM_Utils_Array::value( 'source_contact', $defaults ) );
            }
            $defaults['activity_date_time'] = array( );
            CRM_Utils_Date::getAllDefaultValues( $defaults['activity_date_time'] );
        } else {
            // if it's a new activity, we need to set default values for associated contact fields
            // since those are dojo fields, unfortunately we cannot use defaults directly
            $this->_sourceContactId = $this->_currentUserId;
            $this->_targetContactId = $this->_currentlyViewedContactId;

            $defaults['source_contact_id'] = $this->_sourceContactId;
            $defaults['target_contact[1]'] = $this->_targetContactId;
            $defaults['source_contact_id'] = $this->_sourceContactId;

            $defaults['activity_date_time'] = array( );
            CRM_Utils_Date::getAllDefaultValues( $defaults['activity_date_time'] );
        }

        if (  $this->_activityTypeId ) {
            $defaults["activity_type_id"] =  $this->_activityTypeId;
        }
        
        // DRAFTING: Check this in the template
        if ( $this->_action & ( CRM_Core_Action::DELETE | CRM_Core_Action::RENEW ) ) {
            $this->assign( 'delName', $defaults['subject'] );
        }
        
        if ( $this->_activityTypeFile ) {
            eval('$defaults += CRM_Case_Form_Activity_'. $this->_activityTypeFile . '::setDefaultValues($this);');
        }
        return $defaults;
    }

    public function buildQuickForm( ) 
    {
        if ( $this->_action & ( CRM_Core_Action::DELETE | CRM_Core_Action::RENEW ) ) { 
            $button = ts('Delete');
            if (  $this->_action & CRM_Core_Action::RENEW ) {
                $button = ts('Restore');
            } 
            $this->addButtons(array( 
                                    array ( 'type'      => 'next', 
                                            'name'      => $button, 
                                            'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 
                                            'isDefault' => true   ), 
                                    array ( 'type'      => 'cancel', 
                                            'name'      => ts('Cancel'),
                                            )
                                     ));
            return;
        }
        
        if ( ! $this->_single && !empty($this->_contactIds) ) {
            $withArray          = array();
            require_once 'CRM/Contact/BAO/Contact.php';
            foreach ( $this->_contactIds as $contactId ) {
                $withDisplayName = self::_getDisplayNameById($contactId);
                $withArray[] = "\"$withDisplayName\" ";
            }
            $this->assign('with', implode(', ', $withArray));
        } 
        
        if ( $this->_cdType ) {
            return CRM_Custom_Form_CustomData::buildQuickForm( $this );
        }

        if ( $this->_addAssigneeContact ) {
            $contactCount = CRM_Utils_Array::value( 'count', $_GET );
            $nextContactCount = $contactCount + 1;
            $this->assign('contactCount', $contactCount );
            $this->assign('nextContactCount', $nextContactCount );
            $this->assign('contactFieldName', 'assignee_contact' );
            return CRM_Contact_Form_AddContact::buildQuickForm( $this, "assignee_contact[{$contactCount}]" );
        }

        if ( $this->_addTargetContact ) {
            $contactCount = CRM_Utils_Array::value( 'count', $_GET );
            $nextContactCount = $contactCount + 1;
            $this->assign('contactCount', $contactCount );
            $this->assign('nextContactCount', $nextContactCount );
            $this->assign('contactFieldName', 'target_contact' );
            return CRM_Contact_Form_AddContact::buildQuickForm( $this, "target_contact[{$contactCount}]" );
        }

        //build other activity links
        require_once "CRM/Activity/Form/ActivityLinks.php";
        CRM_Activity_Form_ActivityLinks::buildQuickForm( );

        //enable form element
        $this->assign( 'suppressForm', false );
            
        $this->_viewOptions = CRM_Core_BAO_Preferences::valueOptions( 'contact_view_options', true, null, true );
        
        $config =& CRM_Core_Config::singleton( );
        if ( $this->_viewOptions['CiviCase'] && in_array('CiviCase', $config->enableComponents) ) {
            $this->_caseEnabled = true;
        }

        $activityOPtions = CRM_Core_PseudoConstant::ActivityType( false );
        asort( $activityOPtions );
        $this->_activityType = array( ''   => 
                                      ' - select activity - ' ) + $activityOPtions;
        
        unset( $this->_activityType[8] );
        $element =& $this->add('select', 
                               'activity_type_id', 
                               ts('Activity Type'),
                               $this->_activityType,
                               false, 
                               array('onchange' => 
                                     "buildCustomData( 'Activity', this.value );injectActTypeFileFields( this.value );") );

        //freeze for update mode.
        if ( $this->_action & CRM_Core_Action::UPDATE ) {
            $element->freeze( );
        }
       
        foreach ( $this->_fields as $field => $values ) {
            if( CRM_Utils_Array::value($field, $this->_fields ) ) {
                $attribute = null;
                if( CRM_Utils_Array::value('attributes', $values ) ) {
                    $attribute = $values['attributes'];
                }
                $this->add($values['type'], $field, ts($values['label']), $attribute, $values['required'] );
            }
        }

        $this->addRule('duration', 
                       ts('Please enter the duration as number of minutes (integers only).'), 'positiveInteger');  
        
       
        // add a dojo facility for searching contacts
        $this->assign( 'dojoIncludes', " dojo.require('dojox.data.QueryReadStore'); dojo.require('dojo.parser');" );
        
        $dataUrl = CRM_Utils_System::url( "civicrm/ajax/search",
                                          "reset=1",
                                          false, null, false );
        $this->assign('dataUrl',$dataUrl );

        $admin = CRM_Core_Permission::check( 'administer CiviCRM' );
        $this->assign('admin', $admin);

        $sourceContactField =& $this->add( $this->_fields['source_contact_id']['type'],
                                           'source_contact_id', 
                                           $this->_fields['source_contact_id']['label'], 
                                           $this->_fields['source_contact_id']['attributes'], 
                                           $admin );

        if ( $sourceContactField->getValue( ) ) {
            $this->assign( 'source_contact',  $sourceContactField->getValue( ) );
        } else if ( $this->_sourceContactId ) {
            // we're setting currently LOGGED IN user as source for this activity
            $this->assign( 'source_contact', $this->_sourceContactId );
        }

        //need to assign custom data type and subtype to the template
        $this->assign('customDataType',     'Activity');
        $this->assign('customDataSubType',  $this->_activityTypeId );
        $this->assign('entityID',           $this->_activityId );

        if ( $this->_targetContactId ) {
            $defaultTargetContactName = 
                CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact',
                                             $this->_targetContactId,
                                             'sort_name' );
            $this->assign( 'target_contact_value', $defaultTargetContactName );
        }
        
        // if we're viewing, we're assigning different buttons than for adding/editing
        if ( $this->_action & CRM_Core_Action::VIEW ) { 
            if ( isset( $this->_groupTree ) ) {
				CRM_Core_BAO_CustomGroup::buildCustomDataView( $this, $this->_groupTree );
            }
            
            $this->freeze();
            $this->addButtons( array(
                                     array ( 'type'      => 'cancel',
                                             'name'      => ts('Done') ),
                                     )
                               );
        } else {
            $this->addUploadElement( CRM_Core_BAO_File::uploadNames( ) );
            $buttonType = $this->buttonType( );
            $this->addButtons( array(
                                     array ( 'type'      => $buttonType,
                                             'name'      => ts('Save'),
                                             'isDefault' => true   ),
                                     array ( 'type'      => 'cancel',
                                             'name'      => ts('Cancel') ),
                                     )
                               );
        }

        if ( $this->_activityTypeFile ) {
            eval("CRM_Case_Form_Activity_{$this->_activityTypeFile}::buildQuickForm( \$this );");
        }

        if ( $this->_activityTypeFile ) {
            eval('$this->addFormRule' . 
                 "(array('CRM_Case_Form_Activity_{$this->_activityTypeFile}', 'formrule'), \$this);");
        }

        $this->addFormRule( array( 'CRM_Activity_Form_Activity', 'formRule' ), $this );
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
        if ( ! $self->_single && ! $fields['activity_type_id']) {
            $errors['activity_type_id'] = ts('Activity Type is a required field');
        }
        
        //FIX me temp. comment
        // make sure if associated contacts exist
        require_once 'CRM/Contact/BAO/Contact.php';
       
        if ( $fields['source_contact_id'] && ! is_numeric($fields['source_contact_id'])) {
            $errors['source_contact_id'] = ts('Source Contact non-existant!');
        }
        if ( is_array( $fields['assignee_contact'] ) ) {
            foreach ( $fields['assignee_contact'] as $key => $id ) {
                if ( $id && ! is_numeric($id)) {
                    $errors["assignee_contact[$key]"] = ts('Assignee Contact %1 does not exist.', array(1 => $key));
                }
            }
        }
        if ( !empty($fields['target_contact']) ) {
            foreach ( $fields['target_contact'] as $key => $id ) {
                if ( $id && ! is_numeric($id)) {
                    $errors["target_contact[$key]"] = ts('Target Contact %1 does not exist.', array(1 => $key));
                }
            }
        }
        
        if ( CRM_Utils_Array::value( 'activity_type_id', $fields ) == 3 && 
             CRM_Utils_Array::value( 'status_id', $fields ) == 1 ) {
            $errors['status_id'] = ts('You cannot record scheduled email activity.');
        } else if ( CRM_Utils_Array::value( 'activity_type_id', $fields ) == 4 && 
                    CRM_Utils_Array::value( 'status_id', $fields ) == 1) {
            $errors['status_id'] = ts('You cannot record scheduled SMS activity.');
        }
        
        if ( CRM_Utils_Array::value( 'case_id', $fields) && !is_numeric($fields['case_id'] ) ) {
            $errors['case_id'] = ts('Plesase select valid Case.');
        }

        return $errors;
    }
    
    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess( $params = null ) 
    {
        if ( $this->_action & CRM_Core_Action::DELETE ) { 
            $deleteParams = array( 'id' => $this->_activityId );
            CRM_Activity_BAO_Activity::deleteActivity( $deleteParams );
            CRM_Core_Session::setStatus( ts("Selected Activity is deleted sucessfully.") );
            return;
        }
        
        if ( $this->_action & CRM_Core_Action::DETACH ) { 
            CRM_Case_BAO_Case::deleteCaseActivity( $this->_activityId );
            CRM_Core_Session::setStatus( ts("Selected Activity has been sucessfully detached from a case.") );
            return;
        }
        
        // store the submitted values in an array
        if ( ! $params ) {
            $params = $this->controller->exportValues( $this->_name );
        }

        //set activity type id
        if ( ! $params['activity_type_id'] ) {
            $params['activity_type_id']   = $this->_activityTypeId;
        }
        
        if ( CRM_Utils_Array::value( 'hidden_custom', $params ) &&
             !isset($params['custom']) ) {
            $customFields     = 
                CRM_Core_BAO_CustomField::getFields( 'Activity', false, false, 
                                                     $this->_activityTypeId  );
            $customFields     = 
                CRM_Utils_Array::crmArrayMerge( $customFields, 
                                                CRM_Core_BAO_CustomField::getFields( 'Activity', false, false, 
                                                                                     null, null, true ) );
            $params['custom'] = CRM_Core_BAO_CustomField::postProcess( $params,
                                                                       $customFields,
                                                                       $this->_activityId,
                                                                       'Activity' );
        }

        // store the date with proper format
        $params['activity_date_time'] = CRM_Utils_Date::format( $params['activity_date_time'] );

        // get ids for associated contacts
        if ( ! $params['source_contact_id'] ) {
            $params['source_contact_id'] = $this->_currentUserId;
        } 

        if ( isset($this->_activityId) ) {
            $params['id'] = $this->_activityId;
        }

        // add attachments as needed
        CRM_Core_BAO_File::formatAttachment( $params,
                                             $params,
                                             'civicrm_activity',
                                             $this->_activityId );
              
        // format target params
        if ( $this->_single ) {
            $params['target_contact_id']   = empty($params['target_contact']) ?  
                array( 1 => $this->_currentlyViewedContactId ) : $params['target_contact'];
        } else {
            $params['target_contact_id']   = $this->_contactIds;
        }

        // format assignee params
        if ( ! empty($params['assignee_contact']) ) {
            $params['assignee_contact_id'] = $params['assignee_contact'];
        }

        // call begin post process. Idea is to let injecting file do
        // any processing before the activity is added/updated.
        $this->beginPostProcess( $params );

        $activity = CRM_Activity_BAO_Activity::create( $params );
        
        // add case activity
        if ( $this->_caseEnabled && $params['case_id']  ) {
            $caseParams = array( 'activity_id' => $activity->id,
                                 'case_id'     => $params['case_id'] );
            CRM_Case_BAO_Case::processCaseActivity( $caseParams );        
        }

        // call end post process. Idea is to let injecting file do any
        // processing needed, after the activity has been added/updated.
        $this->endPostProcess( $params, $activity );

        // set status message
        CRM_Core_Session::setStatus( ts('Activity \'%1\' has been saved.', 
                                        array( 1 => $params['subject'] ) ) );

        return array( 'activity' => $activity );
    }
    

    /**
     * Shorthand for getting id by display name (makes code more readable)
     *
     * @access protected
     */
    protected function _getIdByDisplayName( $displayName ) {
        return CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact',
                                            $displayName,
                                            'id',
                                            'sort_name' );
    }
    
    /**
     * Shorthand for getting display name by id (makes code more readable)
     *
     * @access protected
     */
    protected function _getDisplayNameById( $id ) {
        return CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact',
                                            $id,
                                            'sort_name',
                                            'id' );
    }

    /**
     * Function to let injecting activity type file do any processing
     * needed, before the activity is added/updated
     *
     */
    function beginPostProcess( &$params ) {
        if ( $this->_activityTypeFile ) {
            eval("CRM_{$this->_crmDir}_Form_Activity_{$this->_activityTypeFile}" . 
                 "::beginPostProcess( \$this, \$params );");
        }
    }

    /**
     * Function to let injecting activity type file do any processing
     * needed, after the activity has been added/updated
     *
     */
    function endPostProcess( &$params, &$activity ) {
        if ( $this->_activityTypeFile ) {
            eval("CRM_{$this->_crmDir}_Form_Activity_{$this->_activityTypeFile}" . 
                 "::endPostProcess( \$this, \$params, \$activity );");
        }
    }
}
