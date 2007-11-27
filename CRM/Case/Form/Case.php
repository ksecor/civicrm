<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
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
 * This class generates form components for processing a case
 * 
 */
class CRM_Case_Form_Case extends CRM_Core_Form
{
    /**
     * the id of the case that we are proceessing
     *
     * @var int
     * @protected
     */
    protected $_id;


    /**
     * the id of the contact associated with this contribution
     *
     * @var int
     * @protected
     */
    protected $_contactID;

    /** 
     * Function to set variables up before form is built 
     *                                                           
     * @return void 
     * @access public 
     */ 
    public function preProcess()  
    {  
        $this->_contactID = CRM_Utils_Request::retrieve( 'cid', 'Positive', $this );
        $this->_id        = CRM_Utils_Request::retrieve( 'id', 'Integer', $this );
        $this->_activityID = CRM_Utils_Request::retrieve('activity_id','Integer',$this);
        $this->_context = CRM_Utils_Request::retrieve('context','String',$this); 
        $this->assign('context', $this->_context);
        $this->_caseid = CRM_Utils_Request::retrieve('caseid','Integer',$this);
    }

    function setDefaultValues( ) 
    {
        $defaults = array( );
        require_once 'CRM/Case/BAO/Case.php' ;
        if ( isset( $this->_id ) ) {
            $params = array( 'id' => $this->_id );
            CRM_Case_BAO_Case::retrieve($params, $defaults, $ids);
        }        
        $defaults['case_type_id'] = explode(CRM_Case_BAO_Case::VALUE_SEPERATOR, $defaults['case_type_id']);
        
        if ( $this->_action & CRM_Core_Action::ADD ) {
            $defaults['start_date'] = array( );
            CRM_Utils_Date::getAllDefaultValues( $defaults['start_date'] );
        }

        return $defaults;
    }
    
    /** 
     * Function to build the form 
     * 
     * @return None 
     * @access public 
     */ 
    public function buildQuickForm( )
    {
        if ( $this->_action & CRM_Core_Action::DELETE ) {
            $this->addButtons(array( 
                                    array ( 'type'      => 'next', 
                                            'name'      => ts('Remove'), 
                                            'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 
                                            'isDefault' => true   ), 
                                    array ( 'type'      => 'cancel', 
                                            'name'      => ts('Cancel') ), 
                                    ) 
                              );
            return;
        }

        require_once 'CRM/Core/OptionGroup.php';        
        $caseStatus  = CRM_Core_OptionGroup::values('case_status');
        $this->add('select', 'status_id',  ts( 'Case Status' ),  
                    $caseStatus , true  );

        $caseType = CRM_Core_OptionGroup::values('case_type');
        $this->add('select', 'case_type_id',  ts( 'Case Type' ),  
                   $caseType , true, array("size"=>"5",  "multiple"));
        
        $this->add( 'text', 'subject', ts('Subject'),null, true);
        $this->addRule( 'subject', ts('Case subject already exists in Database.'), 
                        'objectExists', array( 'CRM_Case_DAO_Case', $this->_id, 'subject' ) );
        $this->add( 'date', 'start_date', ts('Start Date'),
                    CRM_Core_SelectValues::date('manual',20,10 ),
                    true);   
        $this->addRule('start_date', ts('Select a valid date.'), 'qfDate');
        
        $this->add( 'date', 'end_date', ts('End Date'),
                    CRM_Core_SelectValues::date('manual',20,10 ),
                    false); 
        $this->addRule('end_date', ts('Select a valid date.'), 'qfDate');
        
        $this->add('textarea', 'details', ts('Notes'));
        
        if ( $this->_action & CRM_Core_Action::VIEW ) {
            $this->freeze( );
            $this->addButtons(array(  
                                    array ( 'type'      => 'next',  
                                            'name'      => ts('Done'),  
                                            'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',  

                                            'isDefault' => true   )
                                    )
                              );
        }else {
            $this->addButtons(array( 
                                    array ( 'type'      => 'next',
                                            'name'      => ts('Save'), 
                                            'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 
                                            'isDefault' => true   ), 
                                    array ( 'type'      => 'cancel', 
                                            'name'      => ts('Cancel') ), 
                                    ) 
                              );
        }
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
     * @static  s
     */  
    static function formRule( &$fields, &$files, $self ) {
        $errors = array( ); 
        return $errors;
    }
    
    
    /** 
     * Function to process the form 
     * 
     * @access public 
     * @return None 
     */ 
    public function postProcess( )
    {
        if( $this->_action & CRM_Core_Action::VIEW ) {
            return;
        }
        if ($this->_action & CRM_Core_Action::DELETE ) { 
            $session =& CRM_Core_Session::singleton();
            if (!$this->_context) {
                $url = CRM_Utils_System::url('civicrm/contact/view', 'action=browse&selectedChild=case&cid=' . $this->_contactID );
                $session->pushUserContext( $url );
                require_once 'CRM/Case/BAO/Case.php';
                require_once 'CRM/Case/DAO/CaseActivity.php';
                $caseAcitivity = new CRM_Case_DAO_CaseActivity();
                $caseAcitivity->case_id = $this->_id;
                $caseAcitivity->find();
                while ( $caseAcitivity->fetch() ) {
                    $caseAcitivity->delete();
                }
                CRM_Case_BAO_Case::deleteCase( $this->_id );
                CRM_Utils_System::redirect($url);
            }else{
                require_once 'CRM/Case/BAO/Case.php';
                if ($this->_activityID == 1){
                    $entityTable = 'civicrm_meeting';
                } else if ($this->_activityID == 2){
                    $entityTable = 'civicrm_phonecall';
                } else {
                    $entityTable = 'civicrm_activity';
                }
                
                require_once 'CRM/Case/DAO/CaseActivity.php';
                $caseActivity = new CRM_Case_DAO_CaseActivity();
                $caseActivity->activity_entity_table = $entityTable;
                $caseActivity->activity_entity_id = $this->_id;
                $caseActivity->find(true);
                
                $url = CRM_Utils_System::url('civicrm/contact/view/activity',"activity_id={$this->_activityID}&action=view&reset=1&selectedChild=activity&id={$this->_id}&cid=". $this->_contactID ."&history=0&subType={$this->_activityID}&context={$this->_context}&caseid={$this->_caseid}");
                
                $session->pushUserContext( $url );
                
                CRM_Case_BAO_Case::deleteCaseActivity( $caseActivity->id );
                CRM_Core_Session::setStatus( ts("Selected Activity has been detached from case."));
                CRM_Utils_System::redirect($url);
            }
            return;
        }
        if ( $this->_action & CRM_Core_Action::UPDATE ) {
            $ids['case'] = $this->_id ;
        }
        
        // get the submitted form values.  
        $formValues = $this->controller->exportValues( $this->_name );
        $formValues['contact_id'  ] = $this->_contactID;
        $formValues['start_date'  ] = CRM_Utils_Date::format($formValues['start_date']);
        $formValues['end_date'    ] = CRM_Utils_Date::format($formValues['end_date']);
        $formValues['case_type_id'] = CRM_Case_BAO_Case::VALUE_SEPERATOR.implode(CRM_Case_BAO_Case::VALUE_SEPERATOR, $formValues['case_type_id'] ).CRM_Case_BAO_Case::VALUE_SEPERATOR;
        
        require_once 'CRM/Case/BAO/Case.php';
        $case =  CRM_Case_BAO_Case::create($formValues ,$ids);
    }
}

?>
