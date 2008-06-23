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
require_once 'CRM/Core/PseudoConstant.php';
require_once 'CRM/Core/BAO/CustomGroup.php';
require_once 'CRM/Contact/BAO/Contact/Location.php';

class CRM_Event_Form_Offline extends CRM_Core_Form 
{
    /**
     * the id of the contact associated with this participation
     *
     * @var int
     * @public
     */
    public $_contactID;

    /**
     * the mode that we are in
     * 
     * @var string
     * @public
     */
    public $_mode;
    
    /**
     * Page action
     */
    public $_action;
    
    /**
     * Price Set ID, if the new price set method is used
     *
     * @var int
     * @public
     */
    public $_priceSetId;
   
    /**
     * The billing location id for this contribiution page
     *
     * @var int
     * @public
     */
    public $_bltID;
    
    /** 
     * The fields involved in this contribution page
     * 
     * @var array 
     * @public 
     */
    public $_fields;

    /**
     * If event is paid or unpaid
     */
    public $_isPaidEvent;
    
    /**
     * array of event values
     * 
     * @var array
     * @protected
     */
    protected $_event;

    /**
     * the id of the event
     *
     * @var int
     * @protected
     */
    protected $_eId ;

    public $_paymentProcessor;
    
       
    /** 
     * Function to set variables up before form is built 
     *                                                           
     * @return void 
     * @access public 
     */ 
    function preProcess( ) 
    {
        CRM_Utils_System::setTitle(ts('Submit Credit Card Event Registration'));
        
        $this->_contactID = CRM_Utils_Request::retrieve( 'cid', 'Positive', $this, true );
        $this->_action    = CRM_Utils_Request::retrieve( 'action', 'String',
                                                         $this, false, '' );
        $this->assign( 'action'  , $this->_action   ); 

        if ( CRM_Utils_Request::retrieve( 'eid', 'Positive', $this ) ) {
            $this->_eId       = CRM_Utils_Request::retrieve( 'eid', 'Positive', $this );     
        }

        $this->_processors = CRM_Core_PseudoConstant::paymentProcessor( false, false,
                                                                        "billing_mode IN ( 1, 3 )" );
        if ( count( $this->_processors ) == 0 ) {
            CRM_Core_Error::fatal( ts( 'You do not have any payment processors that support this feature' ) );
        }

        $this->_mode       = $this->_action & CRM_Core_Action::PREVIEW ? 'test' : 'live';

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
                                          "reset=1&force=1&cid={$this->_contactID}&selectedChild=participant" );
        $session =& CRM_Core_Session::singleton( ); 
        $session->pushUserContext( $postURL );
    }
    
    /**
     * This function sets the default values for the form in edit mode
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) 
    {
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
        $this->_defaults['event_id'] = $this->_eId;
        if ( $this->_eId ) {
            $this->_defaults['amount'] = CRM_Core_DAO::getFieldValue( "CRM_Event_DAO_EventPage", 
                                                                      $this->_eId, 
                                                                      'default_fee_id', 
                                                                      'event_id' );
        }
        
        return $this->_defaults;
        
    }
    
    /** 
     * Function to build the form 
     * 
     * @return None 
     * @access public 
     */ 
    function buildQuickForm( ) 
    {
        CRM_Core_Payment_Form::buildCreditCard( $this, true );
        
        $this->_formType = CRM_Utils_Array::value( 'formType', $_GET );
        $this->add( 'select', 'payment_processor_id',
                    ts( 'Payment Processor' ),
                    $this->_processors, true );

        $this->add( 'text', "email-{$this->_bltID}",
                    ts( 'Email Address' ), array( 'size' => 30, 'maxlength' => 60 ), true );
        require_once "CRM/Event/BAO/Event.php";

        $urlParams = "reset=1&cid={$this->_contactID}";
        $url = CRM_Utils_System::url( 'civicrm/event/offline',
                                      $urlParams, true, null, false );
        
        $this->assign("pastURL", $url."&past=true" );
        
        if ( CRM_Utils_Request::retrieve( 'past', 'Boolean', $this ) ) {
            $events = CRM_Event_BAO_Event::getEvents( true );
            $this->assign("past", true);
            $url .= "&past=true";
            $this->assign("refreshURL",$url);
        } else {
            $events = CRM_Event_BAO_Event::getEvents( );
            $this->assign("refreshURL",$url);
        }
        
        $this->add('select', 'event_id',  ts( 'Event' ),  
                   array( '' => ts( '- select -' ) ) + $events,
                   true,
                   array('onchange' => "if (this.value) reload(true); else return false") );

        if ( isset( $this->_eId ) ) {
            $params = array( 'id' => $this->_eId );
            CRM_Event_BAO_Event::retrieve( $params, $this->_event );
        }
        
        if ( $this->_event['is_monetary'] ) {
            require_once "CRM/Event/BAO/EventPage.php";
            $params = array( 'event_id' => $this->_eId );
            CRM_Event_BAO_EventPage::retrieve( $params, $eventPage );

            //retrieve custom information
            $this->_values = array( );
            require_once "CRM/Event/Form/Registration/Register.php";
            CRM_Event_Form_Registration::initPriceSet($this, $eventPage['id'] );
            CRM_Event_Form_Registration_Register::buildAmount( $this, false );
        } else {
            $this->add( 'text', 'amount', ts('Event Fee(s)') );
            $this->addRule('amount', ts('Please enter a valid amount.'), 'money');
        }

        $this->addElement('checkbox', 'is_email_receipt', ts('Send Receipt?'), null );

        $this->addButtons(array( 
                                array ( 'type'      => 'next',
                                        'name'      => ts('Submit'), 
                                        'js'        => array( 'onclick' => "return submitOnce(this,'" . $this->_name . "','" . ts('Processing') ."');" ),
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                                        'isDefault' => true   ), 
                                array ( 'type'      => 'cancel', 
                                        'name'      => ts('Cancel') ), 
                                ) 
                          );
        
    }
    
    /** 
     * Function to process the form 
     * 
     * @access public 
     * @return None 
     */ 
    function postProcess( ) 
    {
        
        $config  =& CRM_Core_Config::singleton( );
        $session =& CRM_Core_Session::singleton( );
    
        // get the submitted form values. 
        $params = $this->_params = $this->controller->exportValues( $this->_name );
        $this->_params['participant_role_id'] = $this->_event['default_role_id'];
        $this->_params['event_id'] =  $this->_eId;
       
        if ( ! isset( $params['priceSetId'] ) ) {
            $params['amount_level'] = $this->_values['custom']['label'][array_search( $params['amount'], 
                                                                                      $this->_values['custom']['amount_id'])];
            
            $params['amount']       = $this->_values['custom']['value'][array_search( $params['amount'], 
                                                                                      $this->_values['custom']['amount_id'])];
            $this->assign( 'amount_level', $params['amount_level'] );
        } else {
            $lineItem = array( );
            CRM_Event_Form_Registration_Register::processPriceSetAmount( $this->_values['custom']['fields'], 
                                                                         $params, $lineItem );
            $this->set( 'lineItem', $lineItem );
            $this->assign( 'lineItem', $lineItem );
        }

        require_once 'CRM/Core/BAO/PaymentProcessor.php';
        $this->_paymentProcessor = CRM_Core_BAO_PaymentProcessor::getPayment( $this->_params['payment_processor_id'],
                                                                              $this->_mode );
       
        require_once "CRM/Contact/BAO/Contact.php";
       
        $now = date( 'YmdHis' );
        $fields = array( );
        
        // set email for primary location.
        $fields["email-Primary"] = 1;
        $params["email-Primary"] = $params["email-{$this->_bltID}"];
        
        $params['register_date'] = $now;
        
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

        $ctype = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact', $this->_contactID, 'contact_type' );
        
        $nameFields = array( 'first_name', 'middle_name', 'last_name' );
        
        foreach ( $nameFields as $name ) {
            $fields[$name] = 1;
            if ( array_key_exists( "billing_$name", $params ) ) {
                $params[$name] = $params["billing_{$name}"];
            }
        }
        
        $contactID = CRM_Contact_BAO_Contact::createProfileContact( $params, $fields, $this->_contactID, null, null, $ctype );
        
        // add all the additioanl payment params we need
        $this->_params["state_province-{$this->_bltID}"] =
            CRM_Core_PseudoConstant::stateProvinceAbbreviation( $this->_params["state_province_id-{$this->_bltID}"] );
        $this->_params["country-{$this->_bltID}"] =
            CRM_Core_PseudoConstant::countryIsoCode( $this->_params["country_id-{$this->_bltID}"] );

        $this->_params['year'      ]     = $this->_params['credit_card_exp_date']['Y'];
        $this->_params['month'     ]     = $this->_params['credit_card_exp_date']['M'];
        $this->_params['ip_address']     = CRM_Utils_System::ipAddress( );
        $this->_params['amount'        ] =  $this->_params['fee_amount'] = $params['amount'];
        $this->_params['amount_level'  ] = $params['amount_level'];
        $this->_params['currencyID'    ] = $config->defaultCurrency;
        $this->_params['payment_action'] = 'Sale';
        $this->_params['invoiceID']      = md5( uniqid( rand( ), true ) );
        
        // at this point we've created a contact and stored its address etc
        // all the payment processors expect the name and address to be in the 
        // so we copy stuff over to first_name etc. 
        $paymentParams = $this->_params;
       
        require_once 'CRM/Core/Payment/Form.php';
        CRM_Core_Payment_Form::mapParams( $this->_bltID, $this->_params, $paymentParams, true );
        
        $payment =& CRM_Core_Payment::singleton( $this->_mode, 'Event', $this->_paymentProcessor );
        
        $result =& $payment->doDirectPayment( $paymentParams );
        
        if ( is_a( $result, 'CRM_Core_Error' ) ) {
            CRM_Core_Error::displaySessionError( $result );
            CRM_Utils_System::redirect( CRM_Utils_System::url( 'civicrm/event/offline',
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
        // set source if not set 
        
        $this->_params['description'] = ts( 'Online Event: CiviCRM Admin Interface' );
        require_once 'CRM/Event/Form/Registration/Confirm.php';
        require_once 'CRM/Event/Form/Registration.php';
        //add contribution record
        $contribution = CRM_Event_Form_Registration_Confirm::processContribution( $this->_params, $result, $contactID, false );
         // add participant record
        $participant  = CRM_Event_Form_Registration::addParticipant( $this->_params, $contactID );

   
        require_once 'CRM/Event/BAO/ParticipantPayment.php';
        $paymentPartcipant = array( 'participant_id'  => $participant->id ,
                                    'contribution_id' => $contribution->id, ); 
        $ids = array();       
        
        CRM_Event_BAO_ParticipantPayment::create( $paymentPartcipant, $ids);
        if ( CRM_Utils_Array::value( 'is_email_receipt', $this->_params ) ) {
            $this->assign_by_ref( 'formValues', $this->_params );
            $template =& CRM_Core_Smarty::singleton( );
            $message = $template->fetch( 'CRM/Event/Form/Registration/ReceiptMessage.tpl' );
            
            $session =& CRM_Core_Session::singleton( );
     
            $userID = $session->get( 'userID' );
            list( $userName, $userEmail ) = CRM_Contact_BAO_Contact_Location::getEmailDetails( $userID );
            $receiptFrom = '"' . $userName . '" <' . $userEmail . '>';
            
            list( $participantDisplayName, $participantEmail ) = CRM_Contact_BAO_Contact_Location::getEmailDetails( $contactID );
            $subject = ts('Event Registration Receipt');
            
            require_once 'CRM/Utils/Mail.php';
            CRM_Utils_Mail::send( $receiptFrom,
                                  $participantDisplayName,
                                  $participantEmail,
                                  $subject,
                                  $message);
        }
        CRM_Core_Session::setStatus( 'Your registration has been processed successfully and a receipt has been emailed to the Participant.' );
    }
    
}


