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
class CRM_Contribute_Form_Contribution extends CRM_Core_Form
{

    public $_mode;
    public $_action;
    
    public $_bltID;
    
    public $_fields;
    
    public $_paymentProcessor;
    
    /**
     * the id of the contribution that we are proceessing
     *
     * @var int
     * @protected
     */
    public $_id;

    /**
     * the id of the premium that we are proceessing
     *
     * @var int
     * @protected
     */
    public $_premiumId;

    /**
     * the id of the note 
     *
     * @var int
     * @protected
     */
    public $_noteId;

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
     * Stores all producuct option
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
     * Function to set variables up before form is built 
     *                                                           
     * @return void 
     * @access public 
     */ 
    
    public function preProcess()  
    {  
        $this->_cdType     = CRM_Utils_Array::value( 'type', $_GET );

        $this->assign('cdType', false);
        if ( $this->_cdType ) {
            $this->assign('cdType', true);
            return CRM_Custom_Form_CustomData::preProcess( $this );
        }
        
        //CRM_Utils_System::setTitle(ts('Record Contribution') );
        $this->_contactID = CRM_Utils_Request::retrieve( 'cid', 'Positive', $this, true );
        $this->_action    = CRM_Utils_Request::retrieve( 'action', 'String',
                                                         $this, false, 'add' );
        $this->assign( 'action', $this->_action );
        $this->_id        = CRM_Utils_Request::retrieve( 'id', 'Positive', $this );
        
        //set the contribution mode.
        $this->_mode = CRM_Utils_Request::retrieve( 'mode', 'String', $this );
        
        $this->assign( 'contributionMode', $this->_mode );
        
        $this->_processors = CRM_Core_PseudoConstant::paymentProcessor( false, false,
                                                                        "billing_mode IN ( 1, 3 )" );
        $this->_paymentProcessor = array( 'billing_mode' => 1 );
        
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
        
        // also set the post url
        $postURL = CRM_Utils_System::url( 'civicrm/contact/view',
                                          "reset=1&force=1&cid={$this->_contactID}&selectedChild=contribute" );
        $session =& CRM_Core_Session::singleton( ); 
        $session->pushUserContext( $postURL );
        
        require_once 'CRM/Contact/BAO/Contact/Location.php';
        $session =& CRM_Core_Session::singleton( );
        $contactID = $this->_contactID ? $this->_contactID : $session->get( 'userID' );
        list( $this->userDisplayName, 
              $this->userEmail ) = CRM_Contact_BAO_Contact_Location::getEmailDetails( $contactID );
        // check for edit permission
        if ( ! CRM_Core_Permission::check( 'edit contributions' ) ) {
            CRM_Core_Error::fatal( ts( 'You do not have permission to access this page' ) );
        }
        
        if ( $this->_action & CRM_Core_Action::DELETE ) {
            return;
        }
        
        // current contribution id
        if ( $this->_id ) {
            require_once 'CRM/Contribute/DAO/FinancialTrxn.php';
            $trxn =& new CRM_Contribute_DAO_FinancialTrxn( );
            $trxn->contribution_id = $this->_id;
            if ( $trxn->find( true ) ) {
                $this->_online = true;
            }

            //to get Premium id
            require_once 'CRM/Contribute/DAO/ContributionProduct.php';
            $dao = & new CRM_Contribute_DAO_ContributionProduct();
            $dao->contribution_id = $this->_id;
            if ( $dao->find(true) ) {
                $this->_premiumId = $dao->id;
            }

            //to get note id 
            require_once 'CRM/Core/BAO/Note.php';
            $daoNote = & new CRM_Core_BAO_Note();
            $daoNote->entity_table = 'civicrm_contribution';
            $daoNote->entity_id = $this->_id;
            if ( $daoNote->find(true) ) {
                $this->_noteId = $daoNote->id;
            }
            
            $this->_contributionType = CRM_Core_DAO::getFieldValue( "CRM_Contribute_DAO_Contribution", 
                                                                    $this->_id, 
                                                                    'contribution_type_id' );
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
       
        $defaults = array( );
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
            $fields["email-{$this->_bltID}"         ] = 1;
            $fields["email-Primary"                 ] = 1;
            
            require_once "CRM/Core/BAO/UFGroup.php";
            CRM_Core_BAO_UFGroup::setProfileDefaults( $this->_contactID, $fields, $defaults  );
            
            // use primary email address if billing email address is empty
            if ( empty( $defaults["email-{$this->_bltID}"] ) &&
                 ! empty( $defaults["email-Primary"] ) ) {
                $defaults["email-{$this->_bltID}"] = $defaults["email-Primary"];
            }
            foreach ($names as $name) {
                if ( ! empty( $defaults[$name] ) ) {
                    $defaults["billing_" . $name] = $defaults[$name];
                }
            }
        }
        
        if ( $this->_id ) {
            $ids = array( );
            $params = array( 'id' => $this->_id );
            require_once "CRM/Contribute/BAO/Contribution.php";
            CRM_Contribute_BAO_Contribution::getValues( $params, $defaults, $ids );
            
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
        
        if ($this->_contributionType) {
            $defaults['contribution_type_id'] = $this->_contributionType;
        }
        
        //get Note
        if($this->_noteId) {
            $defaults['note'] = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_Note', $this->_noteId, 'note' );
        }
        
        if (  CRM_Utils_Array::value('is_test',$defaults) ){
            $this->assign( "is_test" , true);
        } 
        if (isset ( $defaults["honor_contact_id"] ) ) {
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
        if( $this->_premiumId ) {
            $this->assign('showOption',false);
            require_once 'CRM/Contribute/DAO/ContributionProduct.php';
            $dao = & new CRM_Contribute_DAO_ContributionProduct();
            $dao->id = $this->_premiumId;
            $dao->find(true);
            $options = isset($this->_options[$dao->product_id]) ? $this->_options[$dao->product_id] : "";
            if ( ! $options ) {
                $this->assign('showOption',true);
            }
            $options_key = CRM_Utils_Array::key($dao->product_option,$options);
            if( $options_key) {
                $defaults['product_name']   = array ( $dao->product_id , trim($options_key) );
            } else {
                $defaults['product_name']   = array ( $dao->product_id);
            }
            $defaults['fulfilled_date'] = $dao->fulfilled_date;
        }
        
        list( $displayName, $email ) = CRM_Contact_BAO_Contact_Location::getEmailDetails( $this->_contactID );
        $this->assign( 'email', $email ); 
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
            
        if ( $this->_id ) {
            $ids = array( );
            $params = array( 'id' => $this->_id );
            require_once "CRM/Contribute/BAO/Contribution.php";
            CRM_Contribute_BAO_Contribution::getValues( $params, $defaults, $ids );
        }
        
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
        
        if ( $this->_premiumId ) {
            require_once 'CRM/Contribute/DAO/ContributionProduct.php';
            $dao = & new CRM_Contribute_DAO_ContributionProduct();
            $dao->id = $this->_premiumId;
            $dao->find(true);
            if ( $dao->product_id ) {
                $defaults['hidden_Premium'] = 1;
            }
        }
        
        if ( $this->_noteId ) {
            $defaults['note'] = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_Note', $this->_noteId, 'note' );
            if ( ! empty( $defaults['note'] ) ) {
                $defaults['hidden_AdditionalDetail'] = 1;
            }
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

            $allPanes[$name] = array( 'url'  => CRM_Utils_System::url( 'civicrm/contact/view/contribution', $urlParams ),
                                      'open' => $open,
                                      'id'   => $type,
                                      );
            
            // see if we need to include this paneName in the current form
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
        $this->assign('customDataType', 'Contribution');
        $this->assign('customDataSubType',  $this->_contributionType );
        $this->assign('entityId',  $this->_id );
        
        $attributes = CRM_Core_DAO::getAttribute( 'CRM_Contribute_DAO_Contribution' );
        
        $element =& $this->add('select', 'contribution_type_id', 
                               ts( 'Contribution Type' ), 
                               array(''=>ts( '- select -' )) + CRM_Contribute_PseudoConstant::contributionType( ),
                               true, array('onChange' => "buildCustomData( this.value );"));
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
        //add receipt for offline contribution
        $this->addElement('checkbox','is_email_receipt', ts('Send Receipt?'),null, array('onclick' =>"return showHideByValue('is_email_receipt','','receiptDate','table-row','radio',true);") );
        
        $this->add('select', 'contribution_status_id',
                   ts('Contribution Status'), 
                   CRM_Contribute_PseudoConstant::contributionStatus( ),
                   false, array(
                                'onClick' => "if (this.value != 3) status(); else return false",
                                'onChange' => "return showHideByValue('contribution_status_id','3','cancelInfo','table-row','select',false);"));
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
        
        $element =& $this->add( 'select', 'payment_processor_id',
                                ts( 'Payment Processor' ),
                                $this->_processors );
        if ( $this->_online ) {
            $element->freeze( );
        }
        
        $element =& $this->add( 'text', "email-{$this->_bltID}",
                                ts( 'Email Address' ), array( 'size' => 30, 'maxlength' => 60 ) );
        if ( $this->_online ) {
            $element->freeze( );
        }
        //add receipt for credit card contribution
        $this->addElement('checkbox', 'is_email_receipt_cc', ts('Send Receipt?'), null );
        
        $element =& $this->add( 'text', 'total_amount', ts('Total Amount'),
                                $attributes['total_amount'], true );
        $this->addRule('total_amount', ts('Please enter a valid amount.'), 'money');
        if ( $this->_online ) {
            $element->freeze( );
        }
        
        $element =& $this->add( 'text', 'source', ts('Source'), CRM_Utils_Array::value('source',$attributes) );
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
        
        $this->addButtons(array( 
                                array ( 'type'      => $buttonType, 
                                        'name'      => ts('Save'), 
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 
                                        'js'        => $this->_mode ? null : array( 'onclick' => "return verify( );" ),
                                        'isDefault' => true   ), 
                                array ( 'type'      => 'cancel', 
                                        'name'      => ts('Cancel') ), 
                                ) 
                          );
        
        $this->addFormRule( array( 'CRM_Contribute_Form_Contribution', 'formRule' ), $this );
        
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
            if ( empty( $fields["email-{$self->_bltID}"] ) ) {
                $errors["email-{$self->_bltID}"] = ts( 'Email Address is a required field.' );
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
        if ( $this->_action & CRM_Core_Action::DELETE ) {
            require_once 'CRM/Contribute/BAO/Contribution.php';
            CRM_Contribute_BAO_Contribution::deleteContribution( $this->_id );
            return;
        }    
        
        // get the submitted form values.  
        $submittedValues = $this->controller->exportValues( $this->_name );
        $config  =& CRM_Core_Config::singleton( );
        $session =& CRM_Core_Session::singleton( );
        
        //Credit Card Contribution.
        if ( $this->_mode ) {
            $unsetParams = array('trxn_id','payment_instrument_id', 'contribution_status_id',
                                 'receive_date', 'receipt_date','cancel_date','cancel_reason');
            foreach ( $unsetParams as $key ) {
                if ( isset( $submittedValues[$key] ) ) {
                    unset( $submittedValues[$key] );
                }
            }
            //unset custom values.
            foreach ( $submittedValues as $key => $value ) {
                if ( $customFieldId = CRM_Core_BAO_CustomField::getKeyID($key) ) {
                    unset( $submittedValues[$key] );
                }
            }
            
            //Get the rquire fields value only.
            $params = $this->_params = $submittedValues;   
            
            require_once 'CRM/Core/BAO/PaymentProcessor.php';
            $this->_paymentProcessor = CRM_Core_BAO_PaymentProcessor::getPayment( $this->_params['payment_processor_id'],
                                                                                  $this->_mode );
            require_once "CRM/Contact/BAO/Contact.php";
            
            $now = date( 'YmdHis' );
            $fields = array( );
            
            // set email for primary location.
            $fields["email-Primary"] = 1;
            $params["email-Primary"] = $params["email-{$this->_bltID}"];
            
            // now set the values for the billing location.
            foreach ( $this->_fields as $name => $dontCare ) {
                $fields[$name] = 1;
            }
            
            // also add location name to the array
            $params["location_name-{$this->_bltID}"] =
                CRM_Utils_Array::value( 'billing_first_name' , $params ) . ' ' .
                CRM_Utils_Array::value( 'billing_middle_name', $params ) . ' ' .
                CRM_Utils_Array::value( 'billing_last_name'  , $params );
            $params["location_name-{$this->_bltID}"] = trim( $params["location_name-{$this->_bltID}"] );
            $fields["location_name-{$this->_bltID}"] = 1;
            $fields["email-{$this->_bltID}"] = 1;
            
            $ctype = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact',
                                                  $this->_contactID,
                                                  'contact_type' );
            
            $nameFields = array( 'first_name', 'middle_name', 'last_name' );
            foreach ( $nameFields as $name ) {
                $fields[$name] = 1;
                if ( array_key_exists( "billing_$name", $params ) ) {
                    $params[$name] = $params["billing_{$name}"];
                    $params['preserveDBName'] = true;
                }
            }
            
            if ( CRM_Utils_Array::value( 'source', $params ) ) {
                unset( $params['source'] );
            }
            $contactID = CRM_Contact_BAO_Contact::createProfileContact( $params, $fields,
                                                                        $this->_contactID, 
                                                                        null, null, 
                                                                        $ctype );
            
            // add all the additioanl payment params we need
            $this->_params["state_province-{$this->_bltID}"] =
                CRM_Core_PseudoConstant::stateProvinceAbbreviation( $this->_params["state_province_id-{$this->_bltID}"] );
            $this->_params["country-{$this->_bltID}"] =
                CRM_Core_PseudoConstant::countryIsoCode( $this->_params["country_id-{$this->_bltID}"] );
            
            $this->_params['year'      ]     = $this->_params['credit_card_exp_date']['Y'];
            $this->_params['month'     ]     = $this->_params['credit_card_exp_date']['M'];
            $this->_params['ip_address']     = CRM_Utils_System::ipAddress( );
            $this->_params['amount'        ] = $this->_params['total_amount'];
            $this->_params['amount_level'  ] = 0;
            $this->_params['currencyID'    ] = $config->defaultCurrency;
            $this->_params['payment_action'] = 'Sale';
            
            //Add common data to formatted params
            CRM_Contribute_Form_AdditionalInfo::postProcessCommon( $params, $this->_params );
            
            if ( empty( $this->_params['invoice_id'] ) ) {
                $this->_params['invoiceID'] = md5( uniqid( rand( ), true ) );
            } else {
                $this->_params['invoiceID'] = $this->_params['invoice_id'];
            }
            
            // at this point we've created a contact and stored its address etc
            // all the payment processors expect the name and address to be in the 
            // so we copy stuff over to first_name etc. 
            $paymentParams = $this->_params;
            require_once 'CRM/Core/Payment/Form.php';
            CRM_Core_Payment_Form::mapParams( $this->_bltID, $this->_params, $paymentParams, true );
            
            $contributionType =& new CRM_Contribute_DAO_ContributionType( );
            $contributionType->id = $params['contribution_type_id'];
            if ( ! $contributionType->find( true ) ) {
                CRM_Core_Error::fatal( "Could not find a system table" );
            }
            
            // add some contribution type details to the params list
            // if folks need to use it
            $paymentParams['contributionType_name']                = 
                $this->_params['contributionType_name']            = $contributionType->name;
            $paymentParams['contributionType_accounting_code']     = 
                $this->_params['contributionType_accounting_code'] = $contributionType->accounting_code;
            $paymentParams['contributionPageID']                   = null;
            
            
            $payment =& CRM_Core_Payment::singleton( $this->_mode, 'Contribute', $this->_paymentProcessor );
            
            $result =& $payment->doDirectPayment( $paymentParams );
            
            if ( is_a( $result, 'CRM_Core_Error' ) ) {
                //set the contribution mode.
                $urlParams = "action=add&cid={$this->_contactID}";
                if ( $this->_mode ) {
                    $urlParams .= "&mode={$this->_mode}";
                } 
                CRM_Core_Error::displaySessionError( $result );
                CRM_Utils_System::redirect( CRM_Utils_System::url( 'civicrm/contact/view/contribution', $urlParams ) );
            }
            
            if ( $result ) {
                $this->_params = array_merge( $this->_params, $result );
            }
            
            $this->_params['receive_date'] = $now;
            
            if ( CRM_Utils_Array::value( 'is_email_receipt_cc', $this->_params ) ) {
                $this->_params['receipt_date'] = $now;
            } else {
                $this->_params['receipt_date'] = null;
            }
            
            $this->set( 'params', $this->_params );
            $this->assign( 'trxn_id', $result['trxn_id'] );
            $this->assign( 'receive_date',
                           CRM_Utils_Date::mysqlToIso( $this->_params['receive_date']) );
            
            // result has all the stuff we need
            // lets archive it to a financial transaction
            if ( $contributionType->is_deductible ) {
                $this->assign('is_deductible',  true );
                $this->set   ('is_deductible',  true );
            }
            
            // set source if not set 
            if ( empty( $this->_params['source'] ) ) {
                $this->_params['source'] = ts( 'Online Contribution: CiviCRM Admin Interface' );
            } else {
                $this->_params['source'] = $this->_params['source'];
            }
            
            require_once 'CRM/Contribute/Form/Contribution/Confirm.php';
            $contribution 
                =& CRM_Contribute_Form_Contribution_Confirm::processContribution( $this, 
                                                                                  $this->_params, 
                                                                                  $result, 
                                                                                  $this->_contactID, 
                                                                                  $contributionType,  
                                                                                  false, false, false );
            if ( $contribution->id &&
                 CRM_Utils_Array::value( 'is_email_receipt_cc', $this->_params ) ) {
                $this->_params['trxn_id']    =  CRM_Utils_Array::value( 'trxn_id', $result );
                $this->_params['contact_id'] =  $this->_contactID;
                CRM_Contribute_Form_AdditionalInfo::emailReceipt( $this, $this->_params, true );
            }
            
            //process the note
            if ( $contribution->id && isset($params['note']) ) {
                CRM_Contribute_Form_AdditionalInfo::processNote( $params, $contactID, $contribution->id, null );
            }
            //process premium
            if ( $contribution->id && isset($params['product_name'][0]) ) {
                CRM_Contribute_Form_AdditionalInfo::processPremium( $params, $contribution->id, null, $this->_options );
            }
            
            if ( $contribution->id ) {
                $statusMsg = ts('The contribution record has been processed.');
                if ( CRM_Utils_Array::value( 'is_email_receipt_cc', $this->_params ) ) {
                    $statusMsg .= ' ' . ts('A receipt has been emailed to the contributor.');
                }
                CRM_Core_Session::setStatus( $statusMsg );
            }
            //submit credit card contribution ends.
        } else {
            //Offline Contribution.
            $unsetParams = array( "payment_processor_id", "email-{$this->_bltID}", "hidden_buildCreditCard",
                                  "billing_first_name","billing_middle_name","billing_last_name", "street_address-5",
                                  "city-{$this->_bltID}","state_province_id-{$this->_bltID}","postal_code-{$this->_bltID}",
                                  "country_id-{$this->_bltID}","credit_card_number", "cvv2","credit_card_exp_date","credit_card_type",);
            foreach ( $unsetParams as $key ) {
                if ( isset( $submittedValues[$key] ) ) {
                    unset( $submittedValues[$key] );
                }
            }
                        
            // get the required field value only.
            $formValues = $submittedValues;
            $params     = array( );
            $ids        = array( );
            
            $params['contact_id'] = $this->_contactID;
            $params['currency'  ] = $config->defaultCurrency;
            
            $fields = array( 'contribution_type_id',
                             'contribution_status_id',
                             'payment_instrument_id',
                             'cancel_reason',
                             'source'
                             );
            
            foreach ( $fields as $f ) {
                $params[$f] = CRM_Utils_Array::value( $f, $formValues );
            }
            
            $dates = array( 'receive_date',
                            'receipt_date',
                            'cancel_date' );
            
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
            if ( $formValues['is_email_receipt'] ) {
                $params['receipt_date'] = date("Y-m-d");
            }
            if ( $params["contribution_status_id"] == 3 ) {
                if ( CRM_Utils_System::isNull( CRM_Utils_Array::value( 'cancel_date', $params ) ) ) {
                    $params['cancel_date'] = date("Y-m-d");
                }
            } else { 
                $params['cancel_date']   = 'null';
                $params['cancel_reason'] = 'null';
            }
            
            $ids['contribution'] = $params['id'] = $this->_id;
            
            //Add Additinal common information  to formatted params
            CRM_Contribute_Form_AdditionalInfo::postProcessCommon( $formValues, $params );
            
            // format custom data
            // get mime type of the uploaded file
            if ( !empty($_FILES) ) {
                foreach ( $_FILES as $key => $value) {
                    $files = array( );
                    if ( $formValues[$key] ) {
                        $files['name'] = $formValues[$key];
                    }
                    if ( $value['type'] ) {
                        $files['type'] = $value['type']; 
                    }
                    $formValues[$key] = $files;
                }
            }
            
            $customData = array( );
            foreach ( $formValues as $key => $value ) {
                if ( $customFieldId = CRM_Core_BAO_CustomField::getKeyID($key) ) {
                    CRM_Core_BAO_CustomField::formatCustomField( $customFieldId, $customData,
                                                                 $value, 'Contribution', null, $this->_id);
                }
            }
            
            if (! empty($customData) ) {
                $params['custom'] = $customData;
            }
            
            //special case to handle if all checkboxes are unchecked
            $customFields = CRM_Core_BAO_CustomField::getFields( 'Contribution' );
            
            if ( !empty($customFields) ) {
                foreach ( $customFields as $k => $val ) {
                    if ( in_array ( $val[3], array ('CheckBox','Multi-Select') ) &&
                         ! CRM_Utils_Array::value( $k, $params['custom'] ) ) {
                        CRM_Core_BAO_CustomField::formatCustomField( $k, $params['custom'],
                                                                     '', 'Contribution', null, $this->_id);
                    }
                }
            }
            
            require_once 'CRM/Contribute/BAO/Contribution.php';
            $contribution =& CRM_Contribute_BAO_Contribution::create( $params, $ids );
            
            //process associated membership / participant
            if ( $this->_action & CRM_Core_Action::UPDATE ) {
                require_once 'CRM/Core/Payment/BaseIPN.php';
                $baseIPN = new CRM_Core_Payment_BaseIPN( );
                
                $input = $ids = $objects = array( );
                $IdDetails = $this->getDetails( $contribution->id );
                
                $input['component']       = $IdDetails['component'];
                $ids['contact'     ]      = $contribution->contact_id;
                $ids['contribution']      = $contribution->id;
                $ids['contributionRecur'] = null;
                $ids['contributionPage']  = null;
                $ids['membership']        = $IdDetails['membership'];
                $ids['participant']       = $IdDetails['participant'];
                $ids['event']             = $IdDetails['event'];
                
                if ( ! $baseIPN->validateData( $input, $ids, $objects, false ) ) {
                    CRM_Core_Error::fatal( );
                }
                
                $membership   =& $objects['membership']  ;
                $participant  =& $objects['participant'] ;
                
                if ( $contribution->contribution_status_id == 3 ) {
                    if ( $membership ) {
                        $membership->status_id = 6;
                        $membership->save( );
                    }
                    if ( $participant ) {
                        $participant->status_id = 4;
                        $participant->save( );
                    }
                } elseif ( $contribution->contribution_status_id == 4 ) {
                    if ( $membership ) {
                        $membership->status_id = 4;
                        $membership->save( );
                    }
                    if ( $participant ) {
                        $participant->status_id = 4;
                        $participant->save( );
                    }
                } elseif ( $contribution->contribution_status_id == 1 ) {
                    if ( $membership ) {
                        $format       = '%Y%m%d';
                        require_once 'CRM/Member/BAO/MembershipType.php';  
                        $dates = CRM_Member_BAO_MembershipType::getDatesForMembershipType($membership->membership_type_id);
                        
                        $membership->join_date     = 
                            CRM_Utils_Date::customFormat( $dates['join_date'],     $format );
                        $membership->start_date    = 
                            CRM_Utils_Date::customFormat( $dates['start_date'],    $format );
                        $membership->end_date      = 
                            CRM_Utils_Date::customFormat( $dates['end_date'],      $format );
                        $membership->reminder_date = 
                            CRM_Utils_Date::customFormat( $dates['reminder_date'], $format );
                        
                        $membership->status_id = 2;
                        $membership->save( );
                    }
                    if ( $participant ) {
                        $participant->status_id = 1;
                        $participant->save( );
                    }
                }
            }
            
            //process  note
            if ( $contribution->id && isset( $formValues['note'] ) ) {
                CRM_Contribute_Form_AdditionalInfo::processNote( $formValues, $this->_contactID, $contribution->id, $this->_noteId );
            }
            
            //process premium
            if ( $contribution->id && isset( $formValues['product_name'][0] ) ) {
                CRM_Contribute_Form_AdditionalInfo::processPremium( $formValues, $contribution->id, 
                                                                    $this->_premiumId, $this->_options ); 
            }
            
            // Code Added to Send ReceiptMail, Assigned variables to
            // Message generating templates
            if ( $formValues['is_email_receipt'] ) {
                $formValues['contact_id'] =  $this->_contactID;
                CRM_Contribute_Form_AdditionalInfo::emailReceipt( $this, $formValues );
            }
            
            $statusMsg = ts('The contribution record has been saved.');
            if ( $formValues['is_email_receipt'] ) {
                $statusMsg .= ' ' . ts('A receipt has been emailed to the contributor.');
            }
            CRM_Core_Session::setStatus( $statusMsg );
            //Offline Contribution ends.
        }
        
    }
    
    function &getDetails( $contributionID ) {
        $query = "
SELECT    c.id                 as contribution_id,
          mp.membership_id     as membership_id,
          m.membership_type_id as membership_type_id,
          pp.participant_id    as participant_id,
          p.event_id           as event_id
FROM      civicrm_contribution c
LEFT JOIN civicrm_membership_payment  mp ON mp.contribution_id = c.id
LEFT JOIN civicrm_participant_payment pp ON pp.contribution_id = c.id
LEFT JOIN civicrm_participant         p  ON pp.participant_id  = p.id
LEFT JOIN civicrm_membership          m  ON m.id  = mp.membership_id
WHERE     c.id = $contributionID";

        $rows = array( );
        $dao = CRM_Core_DAO::executeQuery( $query,
                                           CRM_Core_DAO::$_nullArray );
        while ( $dao->fetch( ) ) {
            $rows = array(
                          'component'       => $dao->participant_id ? 'event' : 'contribute',
                          'membership'      => $dao->membership_id,
                          'membership_type' => $dao->membership_type_id,
                          'participant'     => $dao->participant_id,
                          'event'           => $dao->event_id,
                          );
        }
        return $rows;
    }
    
}

?>
