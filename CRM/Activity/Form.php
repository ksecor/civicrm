<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.9                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the Affero General Public License Version 1,    |
 | March 2002.                                                        |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the Affero General Public License for more details.            |
 |                                                                    |
 | You should have received a copy of the Affero General Public       |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
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

require_once 'CRM/Core/Form.php';

/**
 * This class generates form components
 * 
 */
class CRM_Activity_Form extends CRM_Core_Form
{
    /**
     * The id of the object being edited / created
     *
     * @var int
     */
    protected $_activityId;

    /**
     * The id of activity type 
     *
     * @var int
     */
    protected $_activityTypeId;

    /**
     * The id of currently viewed contact
     *
     * @var int
     */
    protected $_currentlyViewedContactId;

    /**
     * The id of source contact and target contact
     *
     * @var int
     */
    protected $_sourceContactId;
    protected $_targetContactId;
    protected $_asigneeContactId;

    /**
     * The id of the logged in user, used when add / edit 
     *
     * @var int
     */
    protected $_currentUserId;




    function preProcess( ) 
    {

        $session =& CRM_Core_Session::singleton( );
        $this->_currentUserId = $session->get( 'userID' );

        $this->_activityTypeId = CRM_Utils_Request::retrieve( 'activity_type_id', 'Positive', $this );

        $this->_currentlyViewedContactId = $this->get('contactId');

        // if we're not adding new one, there must be an id to
        // an activity we're trying to work on.
        if ($this->_action != CRM_Core_Action::ADD) {
            $this->_activityId = $this->get('id');
        }

        // what's the context we're currently working on?
        // DRAFTING: Try to eliminate this.
        $this->_context = CRM_Utils_Request::retrieve('context', 'String', $this );
        
        // get the tree of custom fields
        $this->_groupTree =& CRM_Core_BAO_CustomGroup::getTree("Activity", $this->_activityId, 0, $this->_activityTypeId);

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
        $defaults = array( );
        $params   = array( );

        $defaults['activity_type_id'] = $this->_activityTypeId;

        // if we're editing...
        if ( isset( $this->_activityId ) ) {
            $params = array( 'id' => $this->_activityId );
            
            require_once "CRM/Activity/BAO/Activity.php";
            $bao = new CRM_Activity_BAO_Activity();
            $bao->retrieve( $params, $defaults, $this->_activityTypeId );

//            $this->_assignCID = CRM_Activity_BAO_Activity::retrieveActivityAssign( $this->_activityType,$defaults['id']);
            
//            if (! $this->_subject){
//                require_once "CRM/Case/BAO/Case.php";
//                $subjectID = CRM_Case_BAO_Case::getCaseID($this->_activityType, $defaults['id']);
//                if ($subjectID){
//                    $this->_subject = CRM_Core_DAO::getFieldValue('CRM_Case_BAO_Case', $subjectID,'subject' );
//                }
//                
//            }

//            if ( CRM_Utils_Array::value( 'activity_date_time', $defaults ) ) {
//                $this->assign('activity_date_time', $defaults['scheduled_date_time']);
//            }
            
            // change _currentlyViewedContactId to be the target of the activity
//            $this->_sourceContactId = $defaults['source_contact_id'];
//            $this->_targetContactId = $defaults['target_contact_id'];

        // otherwise, we're adding new activity.
        } else {
            // if it's a new activity, we need to set default values for associated contact fields
            // since those are dojo fields, unfortunately we cannot use defaults directly
            $this->_sourceContactId = $this->_currentUserId;
            $this->_targetContactId = $this->_currentlyViewedContactId;
            $this->_assigneeContactId = null;

            $defaults['activity_date_time'] = array( );
            CRM_Utils_Date::getAllDefaultValues( $defaults['activity_date_time'] );
            $defaults['activity_date_time']['i'] = (int ) ( $defaults['activity_date_time']['i'] / 15 ) * 15;
        }

        $subType = CRM_Utils_Request::retrieve( 'subType', 'Positive', CRM_Core_DAO::$_nullObject );
        if ( $subType ) {
            $defaults["activity_type_id"] = $subType;
        }

        
        // DRAFTING: Check this in the template
        if ($this->_action == CRM_Core_Action::DELETE) {
            $this->assign( 'delName', $defaults['subject'] );
        }
       
        // Set defaults for custom values
        if( isset($this->_groupTree) ) {
            if ($this->_action & ( CRM_Core_Action::VIEW | CRM_Core_Action::BROWSE ) ) {
                $inactiveNeeded = true; $viewMode = true;
            } else {
                $viewMode = false; $inactiveNeeded = false;
            }
            CRM_Core_BAO_CustomGroup::setDefaults( $this->_groupTree, $defaults, $viewMode, $inactiveNeeded );
        }
        
        return $defaults;
    }

    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
     
        $config =& CRM_Core_Config::singleton( );
        $domainID = CRM_Core_Config::domainID( );

        // prepare an URL for reloading after choosing activity type
        $urlParams = "activity_type_id={$this->_activityTypeId}&reset=1&cid={$this->_currentlyViewedContactId}&selectedChild=activity";
        if ( $this->_activity_id ) {
            $urlParams .= "&action=update&id={$this->_activity_id}";
        } else {
            $urlParams .= "&action=add";
        }
        $url = CRM_Utils_System::url( 'civicrm/contact/view/activity', $urlParams, true, null, false );
        $this->assign( "refreshURL", $url );
        
        $activityType = CRM_Core_PseudoConstant::activityType();

        $this->applyFilter('__ALL__', 'trim');
        $this->add('select', 'activity_type_id', ts('Activity Type'), 
        array('' => ts('- select activity type -')) + $activityType, true, 
        array('onchange' => "if (this.value) reload(true); else return false"));


        // we're deleting
//        if ( $this->_action & CRM_Core_Action::DELETE ) {
//            
//            $params = "activity_id={$this->_activityType}&action=view&selectedChild=activity&id={$this->_activityId}&cid={$this->_currentlyViewedContactId}&history=0&subType={$this->_activityType}&context={$this->_context}&caseid={$this->_caseID}&reset=1";
//            $cancelURL = CRM_Utils_System::url('civicrm/contact/view/activity',$params ,true,null, false);
//            
//            $this->addButtons(array( 
//                                    array ( 'type'      => 'next', 
//                                            'name'      => ts('Delete'), 
//                                            'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 
//                                            'isDefault' => true   ), 
//                                    array ( 'type'      => 'cancel', 
//                                            'name'      => ts('Cancel'),
//                                            'js'        => array( 'onclick' => "location.href='{$cancelURL}'; return false;" ) ),
//                              ));
//            return;
//        }



 


        // add a dojo facility for searching contacts
        $this->assign( 'dojoIncludes', " dojo.require('dojo.data.ItemFileReadStore'); dojo.require('dijit.form.ComboBox');dojo.require('dojo.parser');" );

        $attributes = array( 'dojoType'       => 'dijit.form.ComboBox',
                             'mode'           => 'remote',
                             'store'          => 'contactStore',
                             'class '         => 'tundra'
                             );
        $dataUrl = CRM_Utils_System::url( "civicrm/ajax/search",
                                          "d={$domainID}&s=",
                                          true, null, false );
        $this->assign('dataUrl',$dataUrl );


        $defaultSourceContactName = CRM_Contact_BAO_Contact::sortName( $this->_sourceContactId );
        $sourceContactField = $this->add( 'text','source_contact', ts('Source contact'), $attributes, true );
        if ( $sourceContactField->getValue( ) ) {
            $this->assign( 'source_contact_value',  $sourceContactField->getValue( ) );
        } else {
            // we're setting currently LOGGED IN user as source for this activity
            $this->assign( 'source_contact_value', $defaultSourceContactName );
        }

        $defaultTargetContactName   = CRM_Contact_BAO_Contact::sortName( $this->_targetContactId );
        $targetContactField = $this->add( 'text','target_contact', ts('Target contact'), $attributes, true );
        if ( $targetContactField->getValue( ) ) {
            $this->assign( 'target_contact_value',  $targetContactField->getValue( ) );
        } else {
            // we're setting currently VIEWED user as target for this activity
            $this->assign( 'target_contact_value', $defaultTargetContactName );
        }

        $defaultAssigneeContactName = CRM_Contact_BAO_Contact::sortName( $this->_assigneeContactId );
        $assigneeContactField = $this->add( 'text','assignee_contact', ts('Assignee contact'), $attributes, true );
        if ( $assigneeContactField->getValue( ) ) {
            $this->assign( 'assignee_contact_value',  $assigneeContactField->getValue( ) );
        } else {
            // at this stage, we're not assigning any default contact to assigned user - it
            // was earlier set to null in setDefaultValues
            $this->assign('assignee_contact_value', $defaultAssigneeContactName );
        }
        
//        $attributeCase = array( 'dojoType'       => 'dijit.form.ComboBox',
//                                'mode'           => 'remote',
//                                'store'          => 'caseStore',
//                                'class'          => 'tundra',
//                             );
                                
//        $caseUrl = CRM_Utils_System::url( "civicrm/ajax/caseSubject",
//                                          "c={$contactID}&s=",
//                                          true, null, false );
//        $this->assign('caseUrl',$caseUrl );

//        $subject = $this->add( 'text','case_subject',ts('Case Subject'),$attributeCase );
//        if ( $subject->getValue( ) ) {
//            $this->assign( 'subject_value',  $subject->getValue( ) );
//        } else {
//            $this->assign( 'subject_value',  $this->_subject );
//        }
          
        if ($this->_action == CRM_Core_Action::VIEW) {
            $this->freeze();
        }

        // if we're viewing, we're assigning different buttons than for adding/editing
        if ( $this->_action == CRM_Core_Action::VIEW ) { 
            $this->addButtons( array(
                                     array ( 'type'      => 'cancel',
                                             'name'      => ts('Done') ),
                                     )
                               );
        } else {

            // DRAFTING: This probably is a hack for custom field uploads
            // DRAFTING: Try to eradicate it at later stage
            $session =& CRM_Core_Session::singleton( );
            $uploadNames = $session->get( 'uploadNames' );
            if ( is_array( $uploadNames ) && ! empty ( $uploadNames ) ) {
                $buttonType = 'upload';
            } else {
                $buttonType = 'next';
            }
            
            $this->addButtons( array(
                                     array ( 'type'      => $buttonType,
                                             'name'      => ts('Save'),
                                             'isDefault' => true   ),
                                     array ( 'type'      => 'cancel',
                                             'name'      => ts('Cancel') ),
                                     )
                               );
        }

        // add custom fields elements
        if ($this->_action & CRM_Core_Action::VIEW ) { 
            CRM_Core_BAO_CustomGroup::buildViewHTML( $this, $this->_groupTree );
        } else {
            CRM_Core_BAO_CustomGroup::buildQuickForm( $this, $this->_groupTree, 'showBlocks1', 'hideBlocks1' );
        }
        $this->addFormRule( array( 'CRM_Activity_Form', 'formRule' ), $this );
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
    static function formRule( &$fields ) 
    {  
        // skip form rule if deleting
        if  ( $fields['_qf_Activity_next_'] == 'Delete' ) {
            return true;
        }
        
        $errors = array( );

        // make sure if associated contacts exist
        require_once 'CRM/Contact/BAO/Contact.php';
        $source_contact_id   = CRM_Contact_BAO_Contact::getIdByDisplayName( $fields['source_contact'] );
        $assignee_contact_id = CRM_Contact_BAO_Contact::getIdByDisplayName( $fields['assignee_contact']);
        $target_contact_id   = CRM_Contact_BAO_Contact::getIdByDisplayName( $fields['target_contact']);
        
        if( !$source_contact_id ) {
            $errors['source_contact'] = ts('Source Contact non-existant!');
        }
        if( !$assignee_contact_id ) {
            $errors['assignee_contact'] = ts('Assignee Contact non-existant!');
            }
        if( !$target_contact_id ) {
            $errors['target_contact'] = ts('Target Contact non-existant!');
        }

//        if ( $fields['case_subject'] ){
//            require_once 'CRM/Case/DAO/Case.php';
//            $caseDAO =& new CRM_Case_DAO_Case();
//            $caseDAO->subject = $fields['case_subject'];
//            $caseDAO->find(true);
            
//            if(!$caseDAO->id){
//                $errors['case_subject'] = ts('Invalid Case Subject');
//            }
//        }
        return $errors;
    }
}


?>
