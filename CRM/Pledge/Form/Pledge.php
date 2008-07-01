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
require_once 'CRM/Core/BAO/CustomGroup.php';
require_once 'CRM/Contribute/Form/AdditionalInfo.php';
require_once 'CRM/Custom/Form/CustomData.php';

/**
 * This class generates form components for processing a ontribution 
 * 
 */
class CRM_Pledge_Form_Pledge extends CRM_Core_Form
{
    public $_mode;

    public $_action;
    
    public $_bltID;
    
    public $_fields;
    
    public $_paymentProcessor;
    
    /**
     * the id of the pledge that we are proceessing
     *
     * @var int
     * @protected
     */
    public $_id;

    /**
     * the id of the contribution that we are proceessing
     *
     * @var int
     * @protected
     */
    public $_contributionID;
    
    /**
     * the id of the premium that we are proceessing
     *
     * @var int
     * @protected
     */
    public $_premiumID  = null;
    public $_productDAO = null;
    
    /**
     * the id of the note 
     *
     * @var int
     * @protected
     */
    public $_noteID;
    
    /**
     * the id of the contact associated with this contribution
     *
     * @var int
     * @protected
     */
    public $_contactID;
    
    /**
     * is this contribution associated with an online
     * financial transaction
     *
     * @var boolean
     * @protected 
     */ 
    public $_online = false;
    
    
    /**
     * Stores all product option
     *
     * @var boolean
     * @protected 
     */ 
    public $_options ;
    
    
    /**
     * stores the honor id
     *
     * @var boolean
     * @protected 
     */ 
    public $_honorID = null ;
    
    /**
     * Store the contribution Type ID
     *
     * @var array
     */
    public $_contributionType;
    
    /**
     * The Pledge values if an existing pledge
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

        $this->_cdType     = CRM_Utils_Array::value( 'type', $_GET );

        $this->assign('cdType', false);
        if ( $this->_cdType ) {
            $this->assign('cdType', true);
            return CRM_Custom_Form_CustomData::preProcess( $this );
        }
        
        $this->_contactID = CRM_Utils_Request::retrieve( 'cid', 'Positive', $this, true );
        $this->_action    = CRM_Utils_Request::retrieve( 'action', 'String',
                                                         $this, false, 'add' );
        $this->assign( 'action', $this->_action );
        $this->_id        = CRM_Utils_Request::retrieve( 'id', 'Positive', $this );
        
        //set the pledge mode.
        $this->_mode = CRM_Utils_Request::retrieve( 'mode', 'String', $this );
        
        $this->assign( 'pledgeMode', $this->_mode );
        $this->_paymentProcessor = array( 'billing_mode' => 1 );
        
        //ensure that processor has a valid config
        //only valid processors get display to user  
        if ( $this->_mode ) {
            $validProcessors = array( );
            $processors = CRM_Core_PseudoConstant::paymentProcessor( false, false, "billing_mode IN ( 1, 3 )" );
            foreach ( $processors as $ppID => $label ) {
                require_once 'CRM/Core/BAO/PaymentProcessor.php';
                require_once 'CRM/Core/Payment.php';
                $paymentProcessor =& CRM_Core_BAO_PaymentProcessor::getPayment( $ppID, $this->_mode );
                if ( $paymentProcessor['payment_processor_type'] == 'PayPal' && !$paymentProcessor['user_name'] ) {
                    continue;
                } else if ( $paymentProcessor['payment_processor_type'] == 'Dummy' && $this->_mode == 'live' ) {
                    continue;
                } else {
                    $paymentObject =& CRM_Core_Payment::singleton( $this->_mode, 'Contribute', $paymentProcessor );
                    $error = $paymentObject->checkConfig( );
                    if ( empty( $error ) ) {
                        $validProcessors[$ppID] = $label;
                    }
                    $paymentObject = null;
                }
            }
            if ( empty( $validProcessors )  ) {
                CRM_Core_Error::fatal( ts( 'You will need to configure the %1 settings for your Payment Processor before you can submit credit card transactions.', array( 1 => $this->_mode ) ) );
            } else {
                $this->_processors = $validProcessors;  
            }
        }
        
        require_once 'CRM/Contact/BAO/Contact/Location.php';
        list( $this->userDisplayName, 
              $this->userEmail ) = CRM_Contact_BAO_Contact_Location::getEmailDetails( $this->_contactID );
        $this->assign( 'displayName', $this->userDisplayName );
        
        // also check for billing information
        // get the billing location type
        $locationTypes =& CRM_Core_PseudoConstant::locationType( );
        $this->_bltID = array_search( 'Billing',  $locationTypes );
        if ( ! $this->_bltID ) {
            CRM_Core_Error::fatal( ts( 'Please set a location type of %1', array( 1 => 'Billing' ) ) );
        }
        $this->set   ( 'bltID', $this->_bltID );
        $this->assign( 'bltID', $this->_bltID );
        $this->_fields = array( );
        require_once 'CRM/Core/Payment/Form.php';
        CRM_Core_Payment_Form::setCreditCardFields( $this );
        
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
                                                                  $this->_id, 'contribution_id', 'pledge_id');
            if ( $this->_contributionID ) {
                $this->_online = CRM_Core_DAO::getFieldValue( 'CRM_CRM_Contribute_DAO_FinancialTrxn',
                                                              $this->_contributionID, 'id', 'contribution_id' );
                
                //to get Premium id
                $sql = "
SELECT *
FROM   civicrm_contribution_product
WHERE  contribution_id = {$this->_contributionID}
";
                $dao = CRM_Core_DAO::executeQuery( $sql,
                                                   CRM_Core_DAO::$_nullArray );
                if ( $dao->fetch( ) ) {
                    $this->_premiumID  = $dao->id;
                    $this->_productDAO = $dao;
                }
                $dao->free( );
                
                //get the contribution values.
                $contribIDs    = array( );
                $contribValues = array( );
                $contribParams = array( 'id' => $this->_contributionID );
                require_once "CRM/Contribute/BAO/Contribution.php";
                CRM_Contribute_BAO_Contribution::getValues( $contribParams, $contribValues, $contribIDs );
                
                if ( ! empty( $contribValues ) ) {
                    $this->_values = array_merge( $this->_values, $contribValues );
                }
                //to get note id 
                require_once 'CRM/Core/BAO/Note.php';
                $daoNote = & new CRM_Core_BAO_Note();
                $daoNote->entity_table = 'civicrm_contribution';
                $daoNote->entity_id = $this->_id;
                if ( $daoNote->find(true) ) {
                    $this->_noteID = $daoNote->id;
                    $this->_values['note'] = $daoNote->note;
                }
                
                $this->_contributionType = $this->_values['contribution_type_id'];
            }
            
            $ids    = array( );
            $pledgeValues = array( );
            $params = array( 'id' => $this->_id );
            require_once "CRM/Contribute/BAO/Contribution.php";
            CRM_Pledge_BAO_Pledge::getValues( $params, $pledgeValues, $ids );
            if ( ! empty( $pledgeValues ) ) {
                $this->_values = array_merge( $this->_values, $pledgeValues );
            }
        }
        
        // when custom data is included in this page
        if ( CRM_Utils_Array::value( "hidden_custom", $_POST ) ) {
            CRM_Custom_Form_Customdata::preProcess( $this );
            CRM_Custom_Form_Customdata::buildQuickForm( $this );
            CRM_Custom_Form_Customdata::setDefaultValues( $this );
        }
        
        // also set the post url
        $postURL = CRM_Utils_System::url( 'civicrm/contact/view',
                                          "reset=1&force=1&cid={$this->_contactID}&selectedChild=contribute" );
        $session =& CRM_Core_Session::singleton( ); 
        $session->pushUserContext( $postURL );
    }

    function setDefaultValues( ) 
    {
        if ( $this->_cdType ) {
            return CRM_Custom_Form_CustomData::setDefaultValues( $this );
        }
       
        $defaults = $this->_values;
        $fields   = array( );
        if ( $this->_action & CRM_Core_Action::DELETE ) {
            return $defaults;
        }
        
        if ( $this->_mode ) {
            foreach ( $this->_fields as $name => $dontCare ) {
                $fields[$name] = 1;
            }
            $names = array("first_name", "middle_name", "last_name");
            foreach ($names as $name) {
                $fields[$name] = 1;
            }
            $fields["state_province-{$this->_bltID}"] = 1;
            $fields["country-{$this->_bltID}"       ] = 1;
            
            require_once "CRM/Core/BAO/UFGroup.php";
            CRM_Core_BAO_UFGroup::setProfileDefaults( $this->_contactID, $fields, $defaults  );
            
            foreach ($names as $name) {
                if ( ! empty( $defaults[$name] ) ) {
                    $defaults["billing_" . $name] = $defaults[$name];
                }
            }
        }
        
        if ( $this->_contributionID ) {
            // throw out a warning if pay later contrib in pending state
            // check if its an online contrib or event registration
            if ( $defaults['contribution_status_id'] == 2 &&
                 ( strpos( $defaults['contribution_source'], ts( 'Online Contribution' ) ) !== false ||
                   strpos( $defaults['contribution_source'], ts( 'Online Event Registration' ) ) !== false ) ) {
                $message = ts( 'If you have received payment for this Pending online contribution, record it using <strong>Update Pending Contribution Status</strong> from <strong><a href=\'%1\'>CiviContribute &raquo; Find Contributions</a></strong>. If you update the status from here the contributor may not got complete information on their receipt. Also, if there is an associated membership or event registration record - it\'s status will not be updated.',
                               array( 1 => CRM_Utils_System::url( 'civicrm/contribute/search', "reset=1" )) );
                CRM_Core_Session::setStatus( $message );
            }
            $this->_contactID = $defaults['contact_id'];
        } else {
            $now = date("Y-m-d");
            $defaults['receive_date'] = $now;
        }
        
        if ( $this->_contributionType ) {
            $defaults['contribution_type_id'] = $this->_contributionType;
        }
        
        if (  CRM_Utils_Array::value( 'is_test', $defaults ) ) {
            $this->assign( "is_test" , true);
        } 
        
        if ( isset ( $defaults["honor_contact_id"] ) ) {
            $honorDefault = array();
            $this->_honorID = $defaults["honor_contact_id"];
            $idParams = array( 'id' => $defaults["honor_contact_id"], 'contact_id' => $defaults["honor_contact_id"] );
            CRM_Contact_BAO_Contact::retrieve( $idParams, $honorDefault, $ids );
            $honorType = CRM_Core_PseudoConstant::honor( );   
            $defaults["honor_prefix_id"]    = $honorDefault["prefix_id"];
            $defaults["honor_first_name"] = CRM_Utils_Array::value("first_name",$honorDefault);
            $defaults["honor_last_name"]  = CRM_Utils_Array::value("last_name",$honorDefault);
            $defaults["honor_email"]     = CRM_Utils_Array::value("email",$honorDefault["location"][1]["email"][1]);
            $defaults["honor_type"]      = $honorType[$defaults["honor_type_id"]];
        }
        
        $this->assign('showOption',true);
        // for Premium section
        if( $this->_premiumID ) {
            $this->assign('showOption',false);
            $options = isset($this->_options[$this->_productDAO->product_id]) ? $this->_options[$this->_productDAO->product_id] : "";
            if ( ! $options ) {
                $this->assign('showOption',true);
            }
            $options_key = CRM_Utils_Array::key($this->_productDAO->product_option,$options);
            if( $options_key) {
                $defaults['product_name']   = array ( $this->_productDAO->product_id , trim($options_key) );
            } else {
                $defaults['product_name']   = array ( $this->_productDAO->product_id);
            }
            $defaults['fulfilled_date'] = $this->_productDAO->fulfilled_date;
        }
        
        $this->assign( 'email', $this->userEmail );
        if ( CRM_Utils_Array::value( 'is_pay_later',$defaults ) ) {
            $this->assign( 'is_pay_later', true ); 
        }
        $this->assign( 'contribution_status_id', CRM_Utils_Array::value('contribution_status_id',$defaults ) );
        
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
        if ( $this->_cdType ) {
            return CRM_Custom_Form_CustomData::buildQuickForm( $this );
        }
        
        $showAdditionalInfo = false;
        $this->_formType = CRM_Utils_Array::value( 'formType', $_GET );
        
        require_once 'CRM/Contribute/Form/AdditionalInfo.php';
        
        $defaults = $this->_values;
        
        $additionalDetailFields = array( 'note', 'thankyou_date', 'invoice_id', 'non_deductible_amount', 'fee_amount', 'net_amount');
        foreach ( $additionalDetailFields as $key ) {
            if ( ! empty( $defaults[$key] ) ) {
                $defaults['hidden_AdditionalDetail'] = 1;
                break;
            }
        }
        
        $honorFields = array('honor_type_id', 'honor_prefix_id', 'honor_first_name', 
                             'honor_lastname','honor_email');
        foreach ( $honorFields as $key ) {
            if ( ! empty( $defaults[$key] ) ) {
                $defaults['hidden_Honoree'] = 1;
                break;
            }
        }
        
        if ( $this->_productDAO ) {
            if ( $this->_productDAO->product_id ) {
                $defaults['hidden_Premium'] = 1;
            }
        }
        
        if ( $this->_noteID &&
             isset( $this->_values['note'] ) ) {
            $defaults['hidden_AdditionalDetail'] = 1;
        }
        
        $paneNames =  array ( 'Additional Details'  => 'AdditionalDetail',
                              'Honoree Information' => 'Honoree', 
                              'Premium Information' => 'Premium'
                              );
        $ccPane = null;
        if ( $this->_mode ) { 
            $ccPane = array( 'Credit Card Information' => 'CreditCard' );
        }
        if ( is_array( $ccPane ) ) {
            $paneNames = array_merge( $ccPane, $paneNames );
        }
        
        foreach ( $paneNames as $name => $type ) {
            $urlParams = "snippet=1&formType={$type}";
            if ( $this->_mode ) {
                $urlParams .= "&mode={$this->_mode}";
            }
            
            $open = 'false';
            if ( $type == 'CreditCard' ) {
                $open = 'true';
            }
            $allPanes[$name] = array( 'url'  => CRM_Utils_System::url( 'civicrm/contact/view/pledge', $urlParams ),
                                      'open' => $open,
                                      'id'   => $type,
                                      );
            
            //see if we need to include this paneName in the current form
            if ( $this->_formType == $type ||
                 CRM_Utils_Array::value( "hidden_{$type}", $_POST ) ||
                 CRM_Utils_Array::value( "hidden_{$type}", $defaults ) ) {
                $showAdditionalInfo = true;
                $allPanes[$name]['open'] = 'true';
                if ( $type == 'CreditCard' ) {
                    $this->add('hidden', 'hidden_CreditCard', 1 );
                    CRM_Core_Payment_Form::buildCreditCard( $this, true );
                } else {
                    eval( 'CRM_Contribute_Form_AdditionalInfo::build' . $type . '( $this );' );
                }
            }
        }
        
        $this->assign( 'allPanes', $allPanes );
        $this->assign( 'dojoIncludes', "dojo.require('civicrm.TitlePane');dojo.require('dojo.parser');" );
        $this->assign( 'showAdditionalInfo', $showAdditionalInfo );
        
        if ( $this->_formType ) {
            $this->assign('formType', $this->_formType );
            return;
        }
        
        $this->applyFilter('__ALL__', 'trim');
        
        if ( $this->_action & CRM_Core_Action::DELETE ) {
            $this->addButtons(array( 
                                    array ( 'type'      => 'next', 
                                            'name'      => ts('Delete'), 
                                            'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 
                                            'isDefault' => true   ), 
                                    array ( 'type'      => 'cancel', 
                                            'name'      => ts('Cancel') ), 
                                    ) 
                              );
            return;
        }
        
        //need to assign custom data type and subtype to the template
        $this->assign('customDataType', 'Pledge');
        $this->assign('entityId',  $this->_id );
        
        //contribution related fields
        $contribAttributes = CRM_Core_DAO::getAttribute( 'CRM_Contribute_DAO_Contribution' );
        
        $element =& $this->add( 'select', 'contribution_type_id', 
                                ts( 'Contribution Type' ), 
                                array(''=>ts( '- select -' )) + CRM_Contribute_PseudoConstant::contributionType( ),
                                true );
        if ( $this->_online ) {
            $element->freeze( );
        }
        
        $element =& $this->add('select', 'payment_instrument_id', 
                               ts( 'Paid By' ), 
                               array(''=>ts( '- select -' )) + CRM_Contribute_PseudoConstant::paymentInstrument( )
                               );
        if ( $this->_online ) {
            $element->freeze( );
        }
        
        $element =& $this->add( 'text', 'trxn_id', ts('Transaction ID'), 
                                $attributes['trxn_id'] );
        if ( $this->_online ) {
            $element->freeze( );
        } else {
            $this->addRule( 'trxn_id',
                            ts( 'This Transaction ID already exists in the database. Include the account number for checks.' ),
                            'objectExists', 
                            array( 'CRM_Contribute_DAO_Contribution', $this->_id, 'trxn_id' ) );
        }
        
        $this->addElement('checkbox','is_email_receipt', ts('Send Receipt?'),null, array('onclick' =>"return showHideByValue('is_email_receipt','','receiptDate','table-row','radio',true);") );
        
        // add various dates
        $element =& $this->add('date', 'receive_date', ts('Received'), CRM_Core_SelectValues::date('activityDate'), false );         
        $this->addRule('receive_date', ts('Select a valid date.'), 'qfDate');
        if ( $this->_online ) {
            $this->assign("hideCalender" , true );
            $element->freeze( );
        }
        $this->addElement('date', 'receipt_date', ts('Receipt Date'), CRM_Core_SelectValues::date('activityDate')); 
        $this->addRule('receipt_date', ts('Select a valid date.'), 'qfDate');
        
        $this->addElement('date', 'cancel_date', ts('Cancelled Date'), CRM_Core_SelectValues::date('activityDate')); 
        $this->addRule('cancel_date', ts('Select a valid date.'), 'qfDate');
        
        $this->add('textarea', 'cancel_reason', ts('Cancellation Reason'), $attributes['cancel_reason'] );
        $element =& $this->add( 'text', 'source', ts('Contribution Source'), CRM_Utils_Array::value('source',$attributes) );
        if ( $this->_online ) {
            $element->freeze( );
        }
        
        //pledge fields. 
        $pledgeAttrib = CRM_Core_DAO::getAttribute( 'CRM_Pledge_DAO_Pledge' );
        $element =& $this->add( 'text', 'amount', ts('Amount'),
                                $pledgeAttrib['amount'], true );
        $this->addRule('amount', ts('Please enter a valid amount.'), 'money');
        if ( $this->_online ) {
            $element->freeze( );
        }
        
        $this->add('select', 'status_id',
                   ts('Pledge Status'), 
                   CRM_Contribute_PseudoConstant::contributionStatus( ),
                   false, array(
                                'onClick'  => "if (this.value != 3) status(); else return false",
                                'onChange' => "return showHideByValue('status_id','3','cancelInfo','table-row','select',false);")); 
        
        $element =& $this->add('select', 'frequency_unit', 
                               ts( 'Frequency Unit' ), 
                               array(''=>ts( '- select -' )) + CRM_Core_SelectValues::unitList( ), 
                               true );
        if ( $this->_online ) {
            $element->freeze( );
        }
        
        $element =& $this->add( 'text', 'frequency_interval', ts('Frequency Interval'), $pledgeAttrib['frequency_interval'], true );
        $this->addRule('frequency_interval', ts('Please enter a valid Frequency Interval.'), 'integer');
        if ( $this->_online ) {
            $element->freeze( );
        }
        
        $element =& $this->add( 'text', 'frequency_day', ts('Frequency Day'), $pledgeAttrib['frequency_day'], true );
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
        $this->addRule('receipt_date', ts('Select a valid date.'), 'qfDate');
        
        $this->addElement('date', 'acknowledge_date', ts('Acknowledge Date'), CRM_Core_SelectValues::date('activityDate')); 
        $this->addRule('acknowledge_date', ts('Select a valid date.'), 'qfDate');
        
        
        $element =& $this->add( 'select', 'payment_processor_id',
                                ts( 'Payment Processor' ),
                                $this->_processors );
        if ( $this->_online ) {
            $element->freeze( );
        }
        
        $session = & CRM_Core_Session::singleton( );
        $uploadNames = $session->get( 'uploadNames' );
        if ( is_array( $uploadNames ) && ! empty ( $uploadNames ) ) {
            $buttonType = 'upload';
        } else {
            $buttonType = 'next';
        }      
        
        $js = null;
        if ( !$this->_mode && $this->userEmail ) {
            $js = array( 'onclick' => "return verify( );" );    
        }
        
        $this->addButtons(array( 
                                array ( 'type'      => $buttonType, 
                                        'name'      => ts('Save'), 
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 
                                        'js'        => $js,
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
        if ( isset( $fields["honor_type_id"] ) ) {
            if ( !((  CRM_Utils_Array::value( 'honor_first_name', $fields ) && 
                      CRM_Utils_Array::value( 'honor_last_name' , $fields )) ||
                   CRM_Utils_Array::value( 'honor_email' , $fields ) )) {
                $errors['honor_first_name'] = ts('Honor First Name and Last Name OR an email should be set.');
            }
        }
        
        //check for Credit Card Contribution.
        if ( $self->_mode ) {
            if ( empty( $fields['payment_processor_id'] ) ) {
                $errors['payment_processor_id'] = ts( 'Payment Processor is a required field.' );
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
    public function postProcess( )  
    {
        exit( );
    }
    
}

