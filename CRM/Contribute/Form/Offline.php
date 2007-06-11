<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
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
require_once 'CRM/Contribute/PseudoConstant.php';
require_once 'CRM/Core/BAO/CustomGroup.php';

class CRM_Contribute_Form_Offline extends CRM_Core_Form {

    public $_contactID;

    public $_mode;
    public $_action;

    public $_bltID;

    public $_fields;

    public $_paymentProcessor;

    function preProcess( ) {
        $this->_contactID = CRM_Utils_Request::retrieve( 'cid', 'Positive', $this, true );
        $this->_action    = CRM_Utils_Request::retrieve( 'action', 'String',
                                                         $this, false, 'add' );
        $this->assign( 'action'  , $this->_action   ); 

        $this->_processors = CRM_Core_PseudoConstant::paymentProcessor( false, false,
                                                                        "billing_mode IN ( 1, 3 )" );
        if ( count( $this->_processors ) == 0 ) {
            CRM_Core_Error::fatal( ts( 'You do not have any  payment processors that support this feature' ) );
        }

        $this->_mode       = $this->_action & CRM_Core_Action::PREVIEW ? 'test' : 'live';

        $this->_paymentProcessor = array( 'billing_mode' => 1 );

        // also check for billing informatin
        // get the billing location type
        $locationTypes =& CRM_Core_PseudoConstant::locationType( );
        $this->_bltID = array_search( ts('Billing'),  $locationTypes );
        if ( ! $this->_bltID ) {
            CRM_Core_Error::fatal( ts( 'Please set a location type of %1', array( 1 => 'Billing' ) ) );
        }
        $this->set   ( 'bltID', $this->_bltID );
        $this->assign( 'bltID', $this->_bltID );

        $this->_fields = array( );

        require_once 'CRM/Core/Payment/Form.php';
        CRM_Core_Payment_Form::setCreditCardFields( $this );
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
        CRM_Core_Payment_Form::buildCreditCard( $this );
        
        $this->add( 'select', 'payment_processor_id',
                    ts( 'Payment Processor' ),
                    $this->_processors );

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

        $this->add( 'text', 'source', ts('Contribution Source'), $attributes['source'] );
        $this->addElement('checkbox', 'is_email_receipt', ts('Is email receipt'), null );

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

        $contactID = CRM_Contact_BAO_Contact::createProfileContact( $params, $fields,
                                                                   $this->_contactID, 
                                                                   null, null, 
                                                                   $ctype );

        // add all the additioanl payment params we need
        $this->_params["state_province-{$this->_bltID}"] =
            CRM_Core_PseudoConstant::stateProvinceAbbreviation( $this->_params["state_province_id-{$this->_bltID}"] );
        $this->_params["country-{$this->_bltID}"] =
            CRM_Core_PseudoConstant::countryIsoCode( $this->_params["country_id-{$this->_bltID}"] );

        $this->_params['year'      ] = $this->_params['credit_card_exp_date']['Y'];
        $this->_params['month'     ] = $this->_params['credit_card_exp_date']['M'];
        $this->_params['ip_address'] = $_SERVER['REMOTE_ADDR'];
        // hack for safari
        if ( $this->_params['ip_address'] == '::1' ) {
            $this->_params['ip_address'] = '127.0.0.1';
        }
        $this->_params['amount'        ] = $this->_params['total_amount'];
        $this->_params['amount_level'  ] = 0;
        $this->_params['currencyID'    ] = $config->defaultCurrency;
        $this->_params['payment_action'] = 'Sale';
        $this->_params['invoiceID']      = md5( uniqid( rand( ), true ) );
        
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
        if ( empty( $this->_params['source'] ) ) {
            $this->_params['source'] = ts( 'Online Contribution: CiviCRM Admin Interface' );
        }

        require_once 'CRM/Contribute/Form/Contribution/Confirm.php';
        $contribution =& CRM_Contribute_Form_Contribution_Confirm::processContribution( $this, $this->_params, $result, $this->_contactID, 
                                                                                        $contributionType,  false, false, false );

        if ( $contribution->id &&
             CRM_Utils_Array::value( 'is_email_receipt', $this->_params ) ) {

            $formValues = array( );

            //Retrieve Contribution Typr Name from contribution_type_id
            $formValues['contributionType_name'] = CRM_Core_DAO::getFieldValue( 'CRM_Contribute_DAO_ContributionType',
                                                                                $this->_params['contribution_type_id'] );
            
            // Retrieve the name and email from receipt is to be send
            $session =& CRM_Core_Session::singleton( );
            $userID = $session->get( 'userID' );
            list( $userName, $userEmail ) = CRM_Contact_BAO_Contact::getEmailDetails( userID );
            
            $formValues['receipt_from_name']  = $userName;
            $formValues['receipt_from_email'] = $userEmail;

            // assigned various dates to the templates
            $this->assign('receive_date',
                          CRM_Utils_Date::MysqlToIso(CRM_Utils_Date::format($this->_params['receive_date'])));
            $this->assign('receipt_date',
                          CRM_Utils_Date::MysqlToIso(CRM_Utils_Date::format($this->_params['receipt_date'])));

            list( $contributorDisplayName, $contributorEmail ) = CRM_Contact_BAO_Contact::getEmailDetails( $contactID );

            $this->assign_by_ref( 'formValues', $formValues );

            $template =& CRM_Core_Smarty::singleton( );
            $message = $template->fetch( 'CRM/Contribute/Form/Message.tpl' );

            $subject = 'Receipt: Off line Contribution Received';
            $receiptFrom = '"' . $formValues['receipt_from_name'] . '" <' . $formValues['receipt_from_email'] . '>';
         
            require_once 'CRM/Utils/Mail.php';
            CRM_Utils_Mail::send( $receiptFrom,
                                  $contributorDisplayName,
                                  $contributorEmail,
                                  $subject,
                                  $message);
        }

    }
    
}

?>