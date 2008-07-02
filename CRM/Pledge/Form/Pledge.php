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

require_once 'CRM/Core/Form.php';
require_once 'CRM/Contribute/PseudoConstant.php';
require_once 'CRM/Custom/Form/CustomData.php';

/**
 * This class generates form components for processing a pledge 
 * 
 */
class CRM_Pledge_Form_Pledge extends CRM_Core_Form
{
    public $_action;
    
    /**
     * the id of the pledge that we are proceessing
     *
     * @var int
     * @public
     */
    public $_id;
    
    /**
     * the id of the contact associated with this pledge
     *
     * @var int
     * @public
     */
    public $_contactID;
    
    /**
     * is this pledge associated with an online
     * financial transaction
     *
     * @var boolean
     * @public 
     */ 
    public $_online = false;
    
    /**
     * The Pledge values if an existing pledge
     * @public
     */
    public $_values;
    
    /** 
     * Function to set variables up before form is built 
     *                                                           
     * @return void 
     * @access public 
     */ 
    public function preProcess()  
    {  
        // check for edit permission
        if ( ! CRM_Core_Permission::check( 'edit pledges' ) ) {
            CRM_Core_Error::fatal( ts( 'You do not have permission to access this page' ) );
        }
        
//         $this->_cdType     = CRM_Utils_Array::value( 'type', $_GET );
//         $this->assign('cdType', false);
//         if ( $this->_cdType ) {
//             $this->assign('cdType', true);
//             return CRM_Custom_Form_CustomData::preProcess( $this );
//         }
        
        $this->_contactID = CRM_Utils_Request::retrieve( 'cid', 'Positive', $this, true );
        $this->_action    = CRM_Utils_Request::retrieve( 'action', 'String',
                                                         $this, false, 'add' );
        $this->assign( 'action', $this->_action );
        $this->_id        = CRM_Utils_Request::retrieve( 'id', 'Positive', $this );
        
        require_once 'CRM/Contact/BAO/Contact/Location.php';
        list( $this->userDisplayName, 
              $this->userEmail ) = CRM_Contact_BAO_Contact_Location::getEmailDetails( $this->_contactID );
        $this->assign( 'displayName', $this->userDisplayName );
        
        //set the post url
        $postURL = CRM_Utils_System::url( 'civicrm/contact/view',
                                          "reset=1&force=1&cid={$this->_contactID}&selectedChild=pledge" );
        $session =& CRM_Core_Session::singleton( ); 
        $session->pushUserContext( $postURL );
        
        if ( $this->_action & CRM_Core_Action::DELETE ) {
            return;
        }
        
        $this->_values = array( );
        // current pledge id
        if ( $this->_id ) {
            //get the contribution id
            $this->_contributionID = CRM_Core_DAO::getFieldValue( 'CRM_Contribute_DAO_PledgePayment',
                                                                  $this->_id, 'contribution_id', 'pledge_id' );
            if ( $this->_contributionID ) {
                $this->_online = CRM_Core_DAO::getFieldValue( 'CRM_Contribute_DAO_FinancialTrxn',
                                                              $this->_contributionID, 'id', 'contribution_id' );
            }
            $ids    = array( );
            $params = array( 'id' => $this->_id );
            require_once "CRM/Contribute/BAO/Contribution.php";
            CRM_Pledge_BAO_Pledge::getValues( $params, $this->_values, $ids );
        }
        
//         // when custom data is included in this page
//         if ( CRM_Utils_Array::value( "hidden_custom", $_POST ) ) {
//             CRM_Custom_Form_Customdata::preProcess( $this );
//             CRM_Custom_Form_Customdata::buildQuickForm( $this );
//             CRM_Custom_Form_Customdata::setDefaultValues( $this );
//         }
        
        // also set the post url
        $postURL = CRM_Utils_System::url( 'civicrm/contact/view',
                                          "reset=1&force=1&cid={$this->_contactID}&selectedChild=pledge" );
        $session =& CRM_Core_Session::singleton( ); 
        $session->pushUserContext( $postURL );
    }
    
    /**
     * This function sets the default values for the form. 
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) 
    {
//         if ( $this->_cdType ) {
//             return CRM_Custom_Form_CustomData::setDefaultValues( $this );
//         }
        
        $defaults = $this->_values;
        $fields   = array( );
        if ( $this->_action & CRM_Core_Action::DELETE ) {
            return $defaults;
        }
        
        if (  CRM_Utils_Array::value( 'is_test', $defaults ) ) {
            $this->assign( "is_test" , true );
        } 
        
        $this->assign( 'email', $this->userEmail );
        
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
//         if ( $this->_cdType ) {
//             return CRM_Custom_Form_CustomData::buildQuickForm( $this );
//         }
        $this->applyFilter('__ALL__', 'trim');
//         if ( $this->_action & CRM_Core_Action::DELETE ) {
//             $this->addButtons(array( 
//                                     array ( 'type'      => 'next', 
//                                             'name'      => ts('Delete'), 
//                                             'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 
//                                             'isDefault' => true   ), 
//                                     array ( 'type'      => 'cancel', 
//                                             'name'      => ts('Cancel') ), 
//                                     ) 
//                               );
//             return;
//         }
        
//         //need to assign custom data type and subtype to the template
//         $this->assign('customDataType', 'Pledge');
//         $this->assign('entityId',  $this->_id );
        
        //pledge fields. 
        $this->addElement('date', 'cancel_date', ts('Cancelled Date'), CRM_Core_SelectValues::date('activityDate')); 
        $this->addRule('cancel_date', ts('Select a valid date.'), 'qfDate');
        
        $attributes = CRM_Core_DAO::getAttribute( 'CRM_Pledge_DAO_Pledge' );
        $element =& $this->add( 'text', 'amount', ts('Amount'),
                                $attributes['amount'], true );
        $this->addRule('amount', ts('Please enter a valid amount.'), 'money');
        if ( $this->_online ) {
            $element->freeze( );
        }
        
        $this->add('select', 'status_id',
                   ts('Pledge Status'), 
                   CRM_Contribute_PseudoConstant::contributionStatus( ),
                   false, array(
                                'onClick'  => "if (this.value != 3) status(); else return false",
                                'onChange' => "return showHideByValue('status_id','3','cancelDate','table-row','select',false);")); 
        
        $element =& $this->add('select', 'frequency_unit', 
                               ts( 'Frequency Unit' ), 
                               array(''=>ts( '- select -' )) + CRM_Core_SelectValues::unitList( ), 
                               true );
        if ( $this->_online ) {
            $element->freeze( );
        }
        
        $element =& $this->add( 'text', 'frequency_interval', ts('Frequency Interval'), $attributes['frequency_interval'], true );
        $this->addRule('frequency_interval', ts('Please enter a valid Frequency Interval.'), 'numeric');
        if ( $this->_online ) {
            $element->freeze( );
        }
        
        $element =& $this->add( 'text', 'frequency_day', ts('Frequency Day'), $attributes['frequency_day'], true );
        if ( $this->_online ) {
            $element->freeze( );
        }
        
        $element =& $this->add( 'text', 'installments', ts('Installments'), $attributes['installments'], true );
        $this->addRule('installments', ts('Please enter a valid Installments.'), 'numeric');
        if ( $this->_online ) {
            $element->freeze( );
        }
        
        $this->addElement('checkbox','is_acknowledge', ts('Acknowledgment?'),null, array('onclick' =>"return showHideByValue('is_acknowledge','','acknowledgeDate','table-row','radio',true);") );
        
        //add various dates
        $element =& $this->add('date', 'create_date', ts('Create Date'), CRM_Core_SelectValues::date('activityDate'), false );         
        $this->addRule('create_date', ts('Select a valid date.'), 'qfDate');
        if ( $this->_online ) {
            $this->assign("hideCalender" , true );
            $element->freeze( );
        }
        
        $this->addElement('date', 'start_date', ts('Start Date'), CRM_Core_SelectValues::date('activityDate')); 
        $this->addRule('start_date', ts('Select a valid date.'), 'qfDate');
        
        $this->addElement('date', 'acknowledge_date', ts('Acknowledge Date'), CRM_Core_SelectValues::date('activityDate')); 
        $this->addRule('acknowledge_date', ts('Select a valid date.'), 'qfDate');
        
        $session = & CRM_Core_Session::singleton( );
        $uploadNames = $session->get( 'uploadNames' );
        if ( is_array( $uploadNames ) && ! empty ( $uploadNames ) ) {
            $buttonType = 'upload';
        } else {
            $buttonType = 'next';
        }      
        
        $this->addButtons(array( 
                                array ( 'type'      => $buttonType, 
                                        'name'      => ts('Save'), 
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 
                                        'js'        => array( 'onclick' => "return verify( );" ),
                                        'isDefault' => true   ), 
                                array ( 'type'      => 'cancel', 
                                        'name'      => ts('Cancel') ), 
                                ) 
                          );
        
        $this->addFormRule( array( 'CRM_Pledge_Form_Pledge', 'formRule' ), $this );
        
        if ( $this->_action & CRM_Core_Action::VIEW ) {
            $this->freeze( );
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
    static function formRule( &$fields, &$files, $self ) 
    {  
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
//         if ( $this->_action & CRM_Core_Action::DELETE ) {
//             require_once 'CRM/Pledge/BAO/Pledge.php';
//             CRM_Pledge_BAO_Pledge::delete( $this->_id );
//             return;
//         }
        
        //get the submitted form values.  
        $formValues = $this->controller->exportValues( $this->_name );
        $config  =& CRM_Core_Config::singleton( );
        $session =& CRM_Core_Session::singleton( ); 
        
        $fields = array( 'status_id',
                         'frequency_unit',
                         'frequency_interval',
                         'frequency_day',
                         'installments' );
        foreach ( $fields as $f ) {
            $params[$f] = CRM_Utils_Array::value( $f, $formValues );
        }
        
        //format amount
        $params['amount'] = CRM_Utils_Rule::cleanMoney( CRM_Utils_Array::value( 'amount', $formValues ) );
        
        $dates = array( 'create_date', 'start_date', 'acknowledge_date', 'cancel_date' );
        foreach ( $dates as $d ) {
            if ( ! CRM_Utils_System::isNull( $formValues[$d] ) ) {
                $formValues[$d]['H'] = '00';
                $formValues[$d]['i'] = '00';
                $formValues[$d]['s'] = '00';
                $params[$d] = CRM_Utils_Date::format( $formValues[$d] );
            } else{
                $params[$d] = 'null';
            }
        }
        if ( $formValues['is_acknowledge'] ) {
            $params['acknowledge_dat'] = date("Y-m-d");
        }
        if ( $params["status_id"] == 3 ) {
            if ( CRM_Utils_System::isNull( CRM_Utils_Array::value( 'cancel_date', $params ) ) ) {
                $params['cancel_date'] = date("Y-m-d");
            }
        } else { 
            $params['cancel_date']   = 'null';
        }
        
        $params['id'] = $this->_id;
        $params['contact_id'] = $this->_contactID;
        
        //create pledge.
        require_once 'CRM/Pledge/BAO/Pledge.php';
        $pledge =& CRM_Pledge_BAO_Pledge::create( $params );
        
        // // Code Added to Send acknowledgment, Assigned variables to
//         // Message generating templates
//         if ( $formValues['is_acknowledge'] ) {
//             $formValues['contact_id'] =  $this->_contactID;
//             self::sendAcknowledgment( $formValues );
//         }
        
        //set the status msg.
        $statusMsg = ts('The Pledge record has been saved.');
        if ( $formValues['is_acknowledge'] ) {
            $statusMsg .= ' ' . ts('A acknowledgment has been emailed to the Pledger.');
        }
        CRM_Core_Session::setStatus( $statusMsg );
        
    }
    
}

