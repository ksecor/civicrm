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

class CRM_Contribute_Form_Offline extends CRM_Core_Form {

    public $_contactID;

    public $_mode;
    public $_action;

    public $_bltID;

    public $_fields;

    public $_paymentProcessor;
    
    /**
     * Stores all producuct option
     *
     * @var boolean
     * @public 
     */ 
    public $_options ;
    

    function preProcess( ) {
        CRM_Utils_System::setTitle(ts('Submit Credit Card Contribution'));
        
        $this->_contactID = CRM_Utils_Request::retrieve( 'cid', 'Positive', $this, true );
        $this->_action    = CRM_Utils_Request::retrieve( 'action', 'String',
                                                         $this, false, '' );
        $this->assign( 'action'  , $this->_action   ); 

        $this->_processors = CRM_Core_PseudoConstant::paymentProcessor( false, false,
                                                                        "billing_mode IN ( 1, 3 )" );
        if ( count( $this->_processors ) == 0 ) {
            CRM_Core_Error::fatal( ts( 'You do not have any payment processors that support this feature' ) );
        }

        $this->_mode       = $this->_action & CRM_Core_Action::PREVIEW ? 'test' : 'live';

        $this->_paymentProcessor = array( 'billing_mode' => 1 );

        require_once 'CRM/Contact/BAO/Contact.php';
        list( $this->userDisplayName, $this->userEmail ) = CRM_Contact_BAO_Contact::getEmailDetails( $this->_contactID );
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
    }

    function setDefaultValues( ) {
        $this->_defaults = array( );
        
        $fields = array( );

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
        CRM_Core_BAO_UFGroup::setProfileDefaults( $this->_contactID, $fields, $this->_defaults );

        // use primary email address if billing email address is empty
        if ( empty( $this->_defaults["email-{$this->_bltID}"] ) &&
             ! empty( $this->_defaults["email-Primary"] ) ) {
            $this->_defaults["email-{$this->_bltID}"] = $this->_defaults["email-Primary"];
        }

        foreach ($names as $name) {
            if ( ! empty( $this->_defaults[$name] ) ) {
                $this->_defaults["billing_" . $name] = $this->_defaults[$name];
            }
        }
        
        return $this->_defaults;
    }

    function buildQuickForm( ) {
        CRM_Core_Payment_Form::buildCreditCard( $this, true );
        
        $this->_formType = CRM_Utils_Array::value( 'formType', $_GET );
        $this->add( 'select', 'payment_processor_id',
                    ts( 'Payment Processor' ),
                    $this->_processors, true );

        $this->add( 'text', "email-{$this->_bltID}",
                    ts( 'Email Address' ), array( 'size' => 30, 'maxlength' => 60 ), true );

        // also add contribution type and amount fields
        $element =& $this->add('select', 'contribution_type_id', 
                               ts( 'Contribution Type' ), 
                               array(''=>ts( '-select-' )) + CRM_Contribute_PseudoConstant::contributionType( ),
                               true);

        $attributes = CRM_Core_DAO::getAttribute( 'CRM_Contribute_DAO_Contribution' );
        $this->add( 'text', 'total_amount', ts('Total Amount'),
                    $attributes['total_amount'], true );
        $this->addRule('total_amount', ts('Please enter a valid amount.'), 'money');

        $this->add( 'text', 'contribution_source', ts('Contribution Source'), CRM_Utils_Array::value('source',$attributes));

        $this->addElement('checkbox', 'is_email_receipt', ts('Send Receipt?'), null );
       
        //CRM - 2673
        require_once 'CRM/Contribute/Form/AdditionalInfo.php';
        $paneNames =  array ( 'Additional Details'  => 'buildAdditionalDetail',
                              'Honoree Information' => 'buildHonoree', 
                              'Premium Information' => 'buildPremium'
                              );
        foreach ( $paneNames as $name => $type ) {
            
            $allPanes[$name] = array( 'url' => CRM_Utils_System::url( 'civicrm/contribute/offline',
                                                                      "snippet=1&formType={$type}" ),
                                      'open' => 'false',
                                      'id'   => $type, 
                                      );
            
            // see if we need to include this paneName in the current form
            if ( $this->_formType == $type ||
                 CRM_Utils_Array::value( "hidden_{$type}", $_POST ) ) {
                $allPanes[$name]['open'] = 'true';
                eval( 'CRM_Contribute_Form_AdditionalInfo::' . $type . '( $this );' );
            }
        }
        
        $this->assign( 'allPanes', $allPanes );
        $this->assign( 'dojoIncludes', "dojo.require('civicrm.TitlePane');dojo.require('dojo.parser');" );
        
        $this->addButtons(array( 
                                array ( 'type'      => 'next',
                                        'name'      => ts('Submit Contribution'), 
                                        'js'        => array( 'onclick' => "return submitOnce(this,'Confirm','" . ts('Processing') ."');" ),
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                                        'isDefault' => true   ), 
                                array ( 'type'      => 'cancel', 
                                        'name'      => ts('Cancel') ), 
                                ) 
                          );
        $this->addFormRule( array( 'CRM_Contribute_Form_Offline', 'formRule' ), $this );
    }
    
    
    function getTemplateFileName() {
        if ( ! $this->_formType ) {
            return parent::getTemplateFileName( );
        } else {
            $name = substr( ucfirst( $this->_formType ), 5 );
            return "CRM/Contribute/Form/AdditionalInfo/{$name}.tpl";
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
    function formRule( &$fields, &$files, $self ) 
    {  
        return CRM_Contribute_Form_AdditionalInfo::formRule( &$fields, &$files, $self );
    }
    
    /** 
     * Function to process the form 
     * 
     * @access public 
     * @return None 
     */ 
    function postProcess( ) {
        $config  =& CRM_Core_Config::singleton( );
        $session =& CRM_Core_Session::singleton( );
        
        // get the submitted form values. 
        $this->_params = $this->controller->exportValues( $this->_name ); 
        
        require_once 'CRM/Core/BAO/PaymentProcessor.php';
        $this->_paymentProcessor = CRM_Core_BAO_PaymentProcessor::getPayment( $this->_params['payment_processor_id'],
                                                                              $this->_mode );

        $params = $this->_params;

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
            }
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
            CRM_Core_Error::displaySessionError( $result );
            CRM_Utils_System::redirect( CRM_Utils_System::url( 'civicrm/contribute/offline',
                                                               "cid={$this->_contactID}" ) );
        }
            
        if ( $result ) {
            $this->_params = array_merge( $this->_params, $result );
        }

        $this->_params['receive_date'] = $now;

        if ( CRM_Utils_Array::value( 'is_email_receipt', $this->_params ) ) {
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
        if ( empty( $this->_params['contribution_source'] ) ) {
            $this->_params['source'] = ts( 'Online Contribution: CiviCRM Admin Interface' );
        } else {
            $this->_params['source'] = $this->_params['contribution_source'];
        }
        
        require_once 'CRM/Contribute/Form/Contribution/Confirm.php';
        $contribution =& CRM_Contribute_Form_Contribution_Confirm::processContribution( $this, $this->_params, $result, $this->_contactID, 
                                                                                        $contributionType,  false, false, false );
        
        if ( $contribution->id &&
             CRM_Utils_Array::value( 'is_email_receipt', $this->_params ) ) {

            // Retrieve Contribution Type Name from contribution_type_id
            $this->_params['contributionType_name'] = CRM_Core_DAO::getFieldValue( 'CRM_Contribute_DAO_ContributionType',
                                                                                $this->_params['contribution_type_id'] );          
            
            // Retrieve payment instrument name (from hard-coded payment_instrument_id = 1, credit card)
            $paymentInstrumentGroup = array();
            $paymentInstrumentGroup['name'] = 'payment_instrument';
            require_once 'CRM/Core/BAO/OptionGroup.php';
            CRM_Core_BAO_OptionGroup::retrieve($paymentInstrumentGroup, $paymentInstrumentGroup);
            $paymentInstrument = array();
            $paymentInstrument['value']            = 1;      
            $paymentInstrument['option_group_id']  = $paymentInstrumentGroup['id'];
            require_once 'CRM/Core/BAO/OptionValue.php';
            CRM_Core_BAO_OptionValue::retrieve($paymentInstrument, $paymentInstrument);
            $this->_params['paidBy'] = $paymentInstrument['label'];

            $this->_params['trxn_id'] = $result['trxn_id'];

            $honor  = CRM_Core_PseudoConstant::honor( );             
            $this->_params["honor_type"] = $honor[$this->_params["honor_type_id"]];

            $this->assign_by_ref( 'formValues', $this->_params );

            $template =& CRM_Core_Smarty::singleton( );
            $message = $template->fetch( 'CRM/Contribute/Form/Message.tpl' );

            // Retrieve the name and email of the current user - this will be the FROM for the receipt email
            $session =& CRM_Core_Session::singleton( );
            $userID = $session->get( 'userID' );
            list( $userName, $userEmail ) = CRM_Contact_BAO_Contact::getEmailDetails( $userID );
            $receiptFrom = '"' . $userName . '" <' . $userEmail . '>';
            list( $contributorDisplayName, $contributorEmail ) = CRM_Contact_BAO_Contact::getEmailDetails( $contactID );
            $subject = ts('Contribution Receipt');
         
            require_once 'CRM/Utils/Mail.php';
            CRM_Utils_Mail::send( $receiptFrom,
                                  $contributorDisplayName,
                                  $contributorEmail,
                                  $subject,
                                  $message);
        }
        
        //process the note
        if ( $contribution->id && isset($params['note']) ) {
            CRM_Contribute_Form_AdditionalInfo::processNote( $params, $contactID, $contribution->id, null );
        }
        //process premium
        if ( $contribution->id && isset($params['product_name'][0]) ) {
            CRM_Contribute_Form_AdditionalInfo::processPremium( $params, $contribution->id, null, $this->_options );
        }
        CRM_Core_Session::setStatus( 'The contribution has been processed and a receipt has been emailed to the contributor.' );
        
    }
    
}


