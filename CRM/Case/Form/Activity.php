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
require_once "CRM/Core/BAO/CustomGroup.php";
require_once "CRM/Custom/Form/CustomData.php";

/**
 * This class create activities for a case
 * 
 */
class CRM_Case_Form_Activity extends CRM_Core_Form
{

    /**
     * The activity id 
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
     * The id of case 
     *
     * @var int
     */
    protected $_caseId;


    /**
     * The id of logged in user
     * 
     * @var int
     */
    protected $_uid;

    /**
     * The id of source (reporter) contact and target contact
     *
     * @var int
     */
    protected $_sourceContactId;
    protected $_assigneeContactId;
    
    // not sure if we need both target and currently viewed - since both should be the same ??
    protected $_targetContactId;
    protected $_currentlyViewedContactId;
    
    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    function preProcess( ) 
    {        
        $this->_id             = CRM_Utils_Request::retrieve( 'id',    'Positive', $this );
        $this->_activityTypeId = CRM_Utils_Request::retrieve( 'atype', 'Positive', $this );
        $this->_caseId         = CRM_Utils_Request::retrieve( 'caseid', 'Positive', $this );

        $session    =& CRM_Core_Session::singleton();
        $this->_uid = $session->get('userID');
        
        $this->_currentlyViewedContactId = $this->get('contactId');
        
        if ( ! $this->_currentlyViewedContactId ) {
            $this->_currentlyViewedContactId = CRM_Utils_Request::retrieve( 'cid', 'Positive', $this );
        }

        $clientName = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact',
                                                  $this->_currentlyViewedContactId,
                                                  'sort_name' );
        $this->assign( 'client_name', $clientName );
        
        // add attachments part
        require_once 'CRM/Core/BAO/File.php';
        CRM_Core_BAO_File::buildAttachment( $this,
                                           'civicrm_activity',
                                           $this->_activityId );

        //assign activity type name and description to template
        // FIXME - needs to retrieve label and description from civicrm_category
        if ( $this->_activityTypeId ) {
            require_once 'CRM/Core/BAO/OptionValue.php';
            list( $activityTypeName, $activityTypeDescription ) = CRM_Core_BAO_OptionValue::getActivityTypeDetails( $this->_activityTypeId );
            
            $this->assign( 'activityTypeName', $activityTypeName );
            $this->assign( 'activityTypeDescription', $activityTypeDescription );
        }
        
        $this->setDefaultValues();
        
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
        $config =& CRM_Core_Config::singleton( );
    }

    public function buildQuickForm( ) 
    {
        // FIXME: Do we need client (target contact) ID as a hidden field in the form?
        
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
        if ( $sourceContactField->getValue( ) ) {
            $this->assign( 'source_contact',  $sourceContactField->getValue( ) );
        } else if ( $this->_sourceContactId ) {
            // we're setting currently LOGGED IN user as source for this activity
            $this->assign( 'source_contact', $this->_sourceContactId );
        }

        // FIXME: Need to add "Case Role" field as spec'd in CRM-3743
        // FIXME: Need to add fields for "Send Copy To" functionality as spec'd in CRM-3743
        
        $this->add('text', 'subject', ts('Subject') , CRM_Core_DAO::getAttribute( 'CRM_Activity_DAO_Activity', 'subject' ), true );
        
        $this->add('date', 'due_date_time', ts('Due Date'), CRM_Core_SelectValues::date('dueDatetime'), true);
        $this->addRule('activity_date_time', ts('Select a valid date.'), 'qfDate');

        $this->add('date', 'activity_date_time', ts('Actual Date'), CRM_Core_SelectValues::date('activityDatetime'), true);
        $this->addRule('activity_date_time', ts('Select a valid date.'), 'qfDate');
        
        $this->add('select','duration_hours',ts('Duration'),CRM_Core_SelectValues::getHours());
        $this->add('select','duration_minutes', null,CRM_Core_SelectValues::getMinutes());
        
        $this->add('textarea', 'details', ts('Details'), CRM_Core_DAO::getAttribute( 'CRM_Activity_DAO_Activity', 'details' ) );
        
        $this->add('select','status_id',ts('Activity Status'), CRM_Core_PseudoConstant::activityStatus( ), true );
        
        $config =& CRM_Core_Config::singleton( );
                
        //need to assign custom data type and subtype to the template
        $this->assign('customDataType', 'Activity');
        $this->assign('customDataSubType',  $this->_activityTypeId );
        $this->assign('entityId',  $this->_activityId );
        // if we're viewing, we're assigning different buttons than for adding/editing
        if ( $this->_action & CRM_Core_Action::VIEW ) { 
            if ( isset( $this->_groupTree ) ) {
                CRM_Core_BAO_CustomGroup::buildViewHTML( $this, $this->_groupTree );
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
    }
}
