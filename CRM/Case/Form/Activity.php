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

/**
 * This class create activities for a case
 * 
 */
class CRM_Case_Form_Activity extends CRM_Core_Form
{

    /**
     * The case id 
     *
     * @var int
     */
    protected $_id;

    /**
     * The id of activity type 
     *
     * @var int
     */
    protected $_activityTypeId;

    /**
     * The activity id 
     *
     * @var int
     */
    protected $_activityId;


    /**
     * The id of logged in user
     * 
     * @var int
     */
    protected $_uid;

    /**
     * The id of contact being viewed
     * 
     * @var int
     */
    protected $_currentlyViewedContactId;

    /**
     * The default values of an activity
     *
     * @var array
     */
    protected $_defaults = array();

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

        $caseSub = CRM_Core_DAO::getFieldValue( 'CRM_Case_DAO_Case',
                                                $this->_id,
                                                'subject' );

        $session    =& CRM_Core_Session::singleton();
        // logged in contact
        $this->_uid = $session->get('userID');

        // contact being viewed
        $this->_currentlyViewedContactId = $this->get('contactId');
        if ( ! $this->_currentlyViewedContactId ) {
            $this->_currentlyViewedContactId = CRM_Utils_Request::retrieve( 'cid', 'Positive', $this );
        }

        $clientName = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact',
                                                   $this->_currentlyViewedContactId,
                                                   'sort_name' );
        $this->assign( 'client_name', $clientName );
        
        $this->_action = CRM_Utils_Request::retrieve('action', 'String', $this );
        $this->assign( 'action', $this->_action);

        $this->_addAssigneeContact = CRM_Utils_Array::value( 'assignee_contact', $_GET );
        $this->assign('addAssigneeContact', false);
        if ( $this->_addAssigneeContact ) {
            $this->assign('addAssigneeContact', true);
        }

        $this->assign( 'urlPath', 'civicrm/contact/view/activity' );

        // add attachments part
        CRM_Core_BAO_File::buildAttachment( $this,
                                            'civicrm_activity',
                                            $this->_activityId );
        
        // assign activity type name and description to template
        require_once 'CRM/Core/BAO/OptionValue.php';
        $categoryParams = array('id' => $this->_activityTypeId);
        CRM_Core_DAO::commonRetrieve('CRM_Core_DAO_Category', $categoryParams, $details );
        
        $activityTypeName           = $details['label'];
        $activityTypeDescription    = $details['description'];
        $this->_defaults['subject'] = $details['label'];

        $this->assign( 'activityTypeName', $activityTypeName );
        $this->assign( 'activityTypeDescription', $activityTypeDescription );

        CRM_Utils_System::setTitle( ts('%1 >> %2', array('1' => $caseSub,'2' => $activityTypeName)) );
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
        } else {
            $this->_defaults['due_date_time'] = array( );
            CRM_Utils_Date::getAllDefaultValues( $this->_defaults['due_date_time'] );
            $this->_defaults['due_date_time']['i'] = 
                (int ) ( $this->_defaults['activity_date_time']['i'] / 15 ) * 15;

            $this->_defaults['activity_date_time'] = array( );
            CRM_Utils_Date::getAllDefaultValues( $this->_defaults['activity_date_time'] );
            $this->_defaults['activity_date_time']['i'] = 
                (int ) ( $this->_defaults['activity_date_time']['i'] / 15 ) * 15;
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
                   CRM_Core_DAO::getAttribute( 'CRM_Activity_DAO_Activity', 'subject' ));
        
        require_once 'CRM/Core/OptionGroup.php';        
        $this->add('select', 'medium_id',  ts( 'Medium' ), 
                   CRM_Core_OptionGroup::values('Encounter Medium'), true);

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

        $this->addButtons( array(
                                 array ( 'type'      => 'next',
                                         'name'      => ts('Save'),
                                         'isDefault' => true   ),
                                 array ( 'type'      => 'cancel',
                                         'name'      => ts('Cancel') ),
                                     )
                           );
        
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

        if ( isset($this->_activityId) ) { 
            $params['id'] = $this->_activityId;

            // activity which hasn't been modified by a user yet
            if ( $this->_defaults['is_auto'] == 1 ) { 
                $params['is_auto'] = 0;
            }

            // activity which has been created or modified by a user
            if ( $this->_defaults['is_auto'] == 0 ) {
                $newActParams = $params;
                $params = array();
                $params['is_current_revision'] = 0;
            }
        }

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
 
            $newActivity = CRM_Activity_BAO_Activity::create( $newActParams );
        }

        // Insert civicrm_log record for the activity (e.g. store the
        // created / edited by contact id and date for the activity)
        
        // Note - civicrm_log is already created by CRM_Activity_BAO_Activity::create()
        
    }
}
