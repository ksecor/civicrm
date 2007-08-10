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
 * This class generates form components for processing a case
 * 
 */
class CRM_Grant_Form_Grant extends CRM_Core_Form
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
        $this->_id        = CRM_Utils_Request::retrieve( 'id', 'Positive', $this );

        if ( $this->_id) {
            require_once 'CRM/Core/BAO/Note.php';
            $noteDAO               = & new CRM_Core_BAO_Note();
            $noteDAO->entity_table = 'civicrm_grant';
            $noteDAO->entity_id    = $this->_id;
            if ( $noteDAO->find(true) ) {
                $this->_noteId = $noteDAO->id;
            }
        } 
    }
    
    function setDefaultValues( ) 
    {
        $defaults = array( );
        $defaults = parent::setDefaultValues();
        
        $params['id'] =  $this->_id;
        if ( $this->_noteId ) {
            $defaults['note'] = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_Note', $this->_noteId, 'note' );
        }
        if ( $this->_id){
            CRM_Grant_BAO_Grant::retrieve( $params, $defaults);
        } else {
            $now = date("Y-m-d");
            $defaults['decision_date']             = $now;
            $defaults['application_received_date'] = $now;
            $defaults['grant_due_date']            = $now;
            $defaults['money_transfer_date']       = $now;
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

        if ( $this->_action & CRM_Core_Action::VIEW ) {
            $this->freeze( );
        }
       

        require_once 'CRM/Core/OptionGroup.php';
        require_once 'CRM/Grant/BAO/Grant.php';        
        $grantType = CRM_Core_OptionGroup::values( 'grant_type' );
        $this->add('select', 'grant_type_id',  ts( 'Grant Type' ),
                   array( '' => ts( '-select-' ) ) + $grantType , true);

        $grantStatus = CRM_Core_OptionGroup::values( 'grant_status' );
        $this->add('select', 'status_id',  ts( 'Grant Status' ),
                   array( '' => ts( '-select-' ) ) + $grantStatus , true);


        $this->add( 'date', 'application_received_date', ts('Application Received date'),
                    CRM_Core_SelectValues::date( 'manual',20,10 ),
                    false);
        $this->addRule('application_received_date', ts('Select a valid date.'), 'qfDate'); 

        $this->add( 'date', 'decision_date', ts('Grant decision date'),
                    CRM_Core_SelectValues::date( 'manual',20,10 ),
                    false);
        $this->addRule('decision_date', ts('Select a valid date.'), 'qfDate');
                    
        $this->add( 'date', 'money_transfer_date', ts('Money transfer date'),
                    CRM_Core_SelectValues::date( 'manual',20,10 ),
                    false);
        $this->addRule('money_transfer_date', ts('Select a valid date.'), 'qfDate');  

        $this->add( 'date', 'grant_due_date', ts('Grant report due date'),
                    CRM_Core_SelectValues::date('manual',20,10 ),
                    false);
        $this->addRule('grant_due_date', ts('Select a valid date.'), 'qfDate');

        $this->addElement('checkbox','grant_report_received', ts('Grant report received?'),null );
        $this->add('textarea', 'rationale', ts('Rationale'));
        
        $this->add( 'text', 'amount_total', ts('Amount total'), null, true );
        $this->addRule('amount_total', ts('Please enter a valid amount.'), 'money'); 
        
        $this->add( 'text', 'amount_granted', ts('Amount granted') );         
        $this->addRule('amount_granted', ts('Please enter a valid amount.'), 'money'); 

        $this->add( 'text', 'amount_requested', ts('Amount requested') );
        $this->addRule('amount_requested', ts('Please enter a valid amount.'), 'money'); 

        $this->add( 'textarea', 'note', ts('Notes'));
        
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
     * @static  
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
        if ( $this->_action & CRM_Core_Action::DELETE ) {
            return;
        }
        if ( $this->_action & CRM_Core_Action::UPDATE ) {
            $ids['grant'] = $this->_id ;
        }
        
        // get the submitted form values.  
        $formValues = $this->controller->exportValues( $this->_name );
        if (!$formValues['grant_report_received']){
            $formValues['grant_report_received']="null";
        }
        $formValues['contact_id'] = $this->_contactID;
        $formValues['application_received_date'] = CRM_Utils_Date::format($formValues['application_received_date']);
        $formValues['decision_date'] = CRM_Utils_Date::format($formValues['decision_date']);
        $formValues['money_transfer_date'] = CRM_Utils_Date::format($formValues['money_transfer_date']);
        $formValues['grant_due_date'] = CRM_Utils_Date::format($formValues['grant_due_date']);
       
        $ids['note'] = array( );
        if ( $this->_noteId ) {
            $ids['note']['id']   = $this->_noteId;
        }

        require_once 'CRM/Grant/BAO/Grant.php';
        $case =  CRM_Grant_BAO_Grant::create($formValues ,$ids);
        
    }
}

?>
