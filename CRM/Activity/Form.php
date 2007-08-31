<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.8                                                |
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
    protected $_id;

    /**
     * The contact id, used when add / edit 
     *
     * @var int
     */
    protected $_contactId;
    protected $_sourceCID;
    protected $_targetCID;

    /**
     * The id of the logged in user, used when add / edit 
     *
     * @var int
     */
    protected $_userId;

    /**
     *  Boolean variable to show followup if it is set to true
     *
     */
    protected $_status;

    /**
     *  Boolean variable set for differentiating between log and schedule
     *
     */
    protected $_log;

    /**
     * this variable to store parent id for the follow up activity
     *
     */
    protected $_pid;

    function preProcess( ) 
    {
        $session =& CRM_Core_Session::singleton( );
        $this->_userId = $session->get( 'userID' );

        $page =& new CRM_Contact_Page_View();
        
        $this->_pid  = $this->get( 'pid' );
        $this->_log  = $this->get( 'log' );
        $this->assign( 'log', $this->_log);
                
        $this->_contactId = $this->get('contactId');
        if ($this->_action != CRM_Core_Action::ADD) {
            $this->_id = $this->get('id');
        }
        $this->_status = CRM_Utils_Request::retrieve( 'status', 'String',
                                                      $this, false );
        $this->_context = CRM_Utils_Request::retrieve('context', 'String',$this );
        $this->_caseID = CRM_Utils_Request::retrieve('caseid', 'Integer', $this );     
        if (  $this->_context == 'case' && $this->_action == CRM_Core_Action::ADD ){
            
            $this->_subject = CRM_Core_DAO::getFieldValue('CRM_Case_BAO_Case', $this->_caseID,'subject' );
        }
        require_once 'CRM/Core/BAO/OptionValue.php';        
        if ( $this->_activityType > 4 ) {
            $ActivityTypeDescription = CRM_Core_BAO_OptionValue::getActivityDescription();
            ksort($ActivityTypeDescription);
            $this->assign('ActivityTypeDescription', $ActivityTypeDescription );            
        }
        
        $this->_groupTree =& CRM_Core_BAO_CustomGroup::getTree("Activity", $this->_id, 0,$this->_activityType);
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
        
        if ( isset( $this->_id ) ) {
            $params = array( 'id' => $this->_id );
            
            require_once "CRM/Activity/BAO/Activity.php";
            CRM_Activity_BAO_Activity::retrieve( $params, $defaults, $this->_activityType );
            $this->_assignCID = CRM_Activity_BAO_Activity::retrieveActivityAssign( $this->_activityType,$defaults['id']);
            
            if (! $this->_subject){
                require_once "CRM/Case/BAO/Case.php";
                $subjectID = CRM_Case_BAO_Case::getCaseID($this->_activityType, $defaults['id']);
                if ($subjectID){
                    $this->_subject = CRM_Core_DAO::getFieldValue('CRM_Case_BAO_Case', $subjectID,'subject' );
                }
                
            }
            if ( CRM_Utils_Array::value( 'scheduled_date_time', $defaults ) ) {
                $this->assign('scheduled_date_time', $defaults['scheduled_date_time']);
            }
            
            // change _contactId to be the target of the activity
            $this->_sourceCID = $defaults['source_contact_id'];
            $this->_targetCID = $defaults['target_entity_id'];
        } else {
            $this->_sourceCID = $this->_userId;
            $this->_targetCID = $this->_contactId;
        }
        
        if ($this->_action == CRM_Core_Action::DELETE) {
            $this->assign( 'delName', $defaults['subject'] );
        }
       
        if ($this->_log) { 
            $defaults['status'] = 'Completed';
        }

        // set the default date if we are creating a new meeting/call or 
        // marking one as complete

        if ( $this->_log || ! isset( $this->_id ) ) {
            // rounding of minutes
            $defaults['scheduled_date_time'] = array( );
            CRM_Utils_Date::getAllDefaultValues( $defaults['scheduled_date_time'] );
            $defaults['scheduled_date_time']['i'] = (int ) ( $defaults['scheduled_date_time']['i'] / 15 ) * 15;
        }
        
        if ($this->_action & ( CRM_Core_Action::VIEW | CRM_Core_Action::BROWSE ) ) {
            $inactiveNeeded = true;
            $viewMode = true;
        } else {
            $viewMode = false;
            $inactiveNeeded = false;
        }
        
        $subType = CRM_Utils_Request::retrieve( 'subType', 'Positive', CRM_Core_DAO::$_nullObject );
        if ( $subType ) {
            $defaults["activity_type_id"] = $subType;
        }
       
        if( isset($this->_groupTree) ) {
            CRM_Core_BAO_CustomGroup::setDefaults( $this->_groupTree, $defaults, $viewMode, $inactiveNeeded );
        }
        
        require_once 'CRM/Activity/BAO/Activity.php';
        $defaults['activity_tag3_id'] = explode(CRM_Activity_BAO_Activity::VALUE_SEPERATOR, substr($defaults['activity_tag3_id'],1,-1));
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
        $contactID = $this->_contactId;
        if ( $this->_action & CRM_Core_Action::DELETE ) {
            
            $params = "activity_id={$this->_activityType}&action=view&selectedChild=activity&id={$this->_id}&cid={$this->_contactId}&history=0&subType={$this->_activityType}&context={$this->_context}&caseid={$this->_caseID}&reset=1";
            $cancelURL = CRM_Utils_System::url('civicrm/contact/view/activity',$params ,true,null, false);
            
            $this->addButtons(array( 
                                    array ( 'type'      => 'next', 
                                            'name'      => ts('Delete'), 
                                            'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 
                                            'isDefault' => true   ), 
                                    array ( 'type'      => 'cancel', 
                                            'name'      => ts('Cancel'),
                                            'js'        => array( 'onclick' => "location.href='{$cancelURL}'; return false;" ) ),
                              ));
            return;
        }
        $fromName = CRM_Contact_BAO_Contact::sortName( $this->_sourceCID );
        $regardName = CRM_Contact_BAO_Contact::sortName( $this->_targetCID );
        $toname     = CRM_Contact_BAO_Contact::sortName( $this->_assignCID );
        $domainID = CRM_Core_Config::domainID( );
        $attributes = array( 'dojoType'       => 'ComboBox',
                             'mode'           => 'remote',
                             'style'          => 'width: 160px;',
                             'dataUrl'        => CRM_Utils_System::url( "civicrm/ajax/search",
                                                                           "d={$domainID}&s=%{searchString}",
                                                                           true, null, false ),
                                );
        $from = $this->add( 'text','from_contact',ts('From'),$attributes,true );
        if ( $from->getValue( ) ) {
            $this->assign( 'from_contact_value',  $from->getValue( ) );
        } else {
            $this->assign('from_contact_value',$fromName );
        }

        $to = $this->add( 'text','to_contact',ts('To'),$attributes,true );
        if ( $to->getValue( ) ) {
            $this->assign( 'to_contact_value',  $to->getValue( ) );
        } else {
            $this->assign( 'to_contact_value', $toname );
        }
        
        $regard = $this->add( 'text','regarding_contact',ts('Regarding'),$attributes,true );
        if ( $regard->getValue( ) ) {
            $this->assign( 'regard_contact_value',  $regard->getValue( ) );
        } else {
            $this->assign('regard_contact_value',$regardName );
        }
        
        $attributeCase = array( 'dojoType'       => 'ComboBox',
                                'mode'           => 'remote',
                                'style'          => 'width: 300px;',
                                'dataUrl'        => CRM_Utils_System::url( "civicrm/ajax/caseSubject",
                                                                           "c={$contactID}&s=%{searchString}",
                                                                           true, null, false ),
                                );
        $subject = $this->add( 'text','case_subject',ts('Case Subject'),$attributeCase );
        if ( $subject->getValue( ) ) {
            $this->assign( 'subject_value',  $subject->getValue( ) );
        } else {
            $this->assign( 'subject_value',  $this->_subject );
        }
          
        require_once 'CRM/Core/OptionGroup.php';
        $caseActivityType = CRM_Core_OptionGroup::values('case_activity_type');
        $this->add('select', 'activity_tag1_id',  ts( 'Case Activity Type' ),  
                   array( '' => ts( '-select-' ) ) + $caseActivityType );
        
        $comunicationMedium = CRM_Core_OptionGroup::values('communication_medium'); 
        $this->add('select', 'activity_tag2_id',  ts( 'Communication' ),  
                   array( '' => ts( '-select-' ) ) + $comunicationMedium );

        $caseViolation = CRM_Core_OptionGroup::values('f1_case_violation');
        $this->add('select', 'activity_tag3_id',  ts( 'Violation Type' ),
                   $caseViolation , false, array("size"=>"5",  "multiple"));
     
        if ($this->_action == CRM_Core_Action::VIEW) { 
            $this->freeze();
        }

        if ($this->_status || ($this->_action == CRM_Core_Action::VIEW)) { 
            if ($this->_status) {
                $this->assign('status', $this->_status);
                $this->assign('pid'   , $this->_id);
                $this->assign('history'   , 1);
            } else {
                $this->assign('history'   , 0);
            }
            $this->addButtons( array(
                                     array ( 'type'      => 'cancel',
                                             'name'      => ts('Done') ),
                                     )
                               );

        } else {
            $session = & CRM_Core_Session::singleton( );
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
        if  ( ($fields['_qf_Meeting_next_'] == 'Delete') ||($fields['_qf_Phonecall_next_'] == 'Delete') || ($fields['_qf_Activity_next_'] == 'Delete')  ) {
            return true;
        }
        $errors = array( ); 
        require_once 'CRM/Case/BAO/Case.php';
        $sourceCID = CRM_Case_BAO_Case::retrieveCid($fields['from_contact']);
        $targetCID = CRM_Case_BAO_Case::retrieveCid($fields['regarding_contact']);
        $toCID     = CRM_Case_BAO_Case::retrieveCid($fields['to_contact']);
        
        if(!$sourceCID){
            $errors['from_contact'] = ts('Invalid From Contact');
        }
        if(!$targetCID){
            $errors['regarding_contact'] = ts('Invalid Regarding Contact');
            }
        if(!$toCID){
            $errors['to_contact'] = ts('Invalid To Contact');
        }
        if ( $fields['case_subject'] ){
            require_once 'CRM/Case/DAO/Case.php';
            $caseDAO =& new CRM_Case_DAO_Case();
            $caseDAO->subject = $fields['case_subject'];
            $caseDAO->find(true);
            
            if(!$caseDAO->id){
                $errors['case_subject'] = ts('Invalid Case Subject');
            }
        }
        return $errors;
    }
}


?>
