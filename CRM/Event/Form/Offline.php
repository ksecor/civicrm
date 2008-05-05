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

class CRM_Event_Form_Offline extends CRM_Core_Form 
{

    public $_contactID;

    public $_mode;
    public $_action;

    public $_bltID;

    public $_fields;

    /**
     * the id of the event
     *
     * @var int
     * @protected
     */
    protected $_eId = null;

    public $_paymentProcessor;
    
    /**
     * Stores all producuct option
     *
     * @var boolean
     * @public 
     */ 
    public $_options ;
    
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

        require_once 'CRM/Contact/BAO/Contact.php';
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
        
    }
    
}


