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
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Event/Form/Registration.php';

/**
 * This class generates form components for processing Event  
 * 
 */
class CRM_Event_Form_Registration_Confirm extends CRM_Event_Form_Registration
{
    /**
     * the values for the contribution db object
     *
     * @var array
     * @protected
     */
    public $_values;

    /** 
     * Function to set variables up before form is built 
     *                                                           
     * @return void 
     * @access public 
     */ 
    function preProcess( ) {
        parent::preProcess( );

        $config =& CRM_Core_Config::singleton( );
        if ( $this->_contributeMode == 'express' ) {
            // rfp == redirect from paypal
            $rfp = CRM_Utils_Request::retrieve( 'rfp', 'Boolean',
                                                CRM_Core_DAO::$_nullObject, false, null, 'GET' );
            if ( $rfp ) {
                //require_once 'CRM/Contribute/Payment.php'; 
                require_once 'CRM/Core/Payment.php'; 
                $payment =& CRM_Core_Payment::singleton( $this->_mode, 'Event' );
                $expressParams = $payment->getExpressCheckoutDetails( $this->get( 'token' ) );
                
                $this->_params['payer'       ] = $expressParams['payer'       ];
                $this->_params['payer_id'    ] = $expressParams['payer_id'    ];
                $this->_params['payer_status'] = $expressParams['payer_status'];

                self::mapParams( $this->_bltID, $expressParams, $this->_params, false );
                
                // fix state and country id if present
                if ( isset( $this->_params["state_province_id-{$this->_bltID}"] ) ) {
                    $this->_params["state_province-{$this->_bltID}"] =
                        CRM_Core_PseudoConstant::stateProvinceAbbreviation( $this->_params["state_province_id-{$this->_bltID}"] ); 
                }
                if ( isset( $this->_params['country_id'] ) ) {
                    $this->_params["country-{$this->_bltID}"]        =
                        CRM_Core_PseudoConstant::countryIsoCode( $this->_params["country_id-{$this->_bltID}"] ); 
                }

                // set a few other parameters for PayPal
                $this->_params['token']          = $this->get( 'token' );
                $this->_params['amount'        ] = $this->get( 'amount' );
                $this->_params['amount_level'  ] = $this->get( 'amount_level' );
                $this->_params['currencyID'    ] = $config->defaultCurrency;
                $this->_params['payment_action'] = 'Sale';
                
                // also merge all the other values from the profile fields
                $values = $this->controller->exportValues( 'Register' );
                $skipFields = array( 'amount', 'first_name', 'middle_name', 'last_name',
                                     'street_address', 'city', 'state_province_id', 'postal_code',
                                     'country_id' );
                foreach ( $values as $name => $value ) {
                    // skip amount field
                    if ( ! in_array( $name, $skipFields ) ) {
                        $this->_params[$name] = $value;
                    }
                }
                $this->set( 'getExpressCheckoutDetails', $this->_params );
            } else {
                $this->_params = $this->get( 'getExpressCheckoutDetails' );
            }
        } else {
            $this->_params = $this->controller->exportValues( 'Register' );
          
            if ( isset( $this->_params["state_province_id-{$this->_bltID}"] ) ) {
                $this->_params["state_province-{$this->_bltID}"] =
                    CRM_Core_PseudoConstant::stateProvinceAbbreviation( $this->_params["state_province_id-{$this->_bltID}"] ); 
            }
            if ( isset( $this->_params["country_id-{$this->_bltID}"] ) ) {
                $this->_params["country-{$this->_bltID}"]        =
                    CRM_Core_PseudoConstant::countryIsoCode( $this->_params["country_id-{$this->_bltID}"] ); 
            }
            if ( isset( $this->_params['credit_card_exp_date'] ) ) {
                $this->_params['year'   ]        = $this->_params['credit_card_exp_date']['Y'];  
                $this->_params['month'  ]        = $this->_params['credit_card_exp_date']['M'];  
            }
            if ( $this->_values['event']['is_monetary'] ) {
                $this->_params['ip_address']     = $_SERVER['REMOTE_ADDR']; 
                
                $this->_params['amount'        ] = $this->get( 'amount' );
                $this->_params['amount_level'  ] = $this->get( 'amount_level' );
                $this->_params['currencyID'    ] = $config->defaultCurrency;
                $this->_params['payment_action'] = 'Sale';
            }
        }
        
        if ( $this->_values['event']['is_monetary'] ) {
            $this->_params['invoiceID'] = $this->get( 'invoiceID' );
        }
        CRM_Utils_System::setTitle($this->_values['event_page']['confirm_title']);
        $this->set( 'params', $this->_params );
    }
    
    /**
     * overwrite action, since we are only showing elements in frozen mode
     * no help display needed
     * @return int
     * @access public
     */   
    function getAction( ) 
    {
        if ( $this->_action & CRM_Core_Action::PREVIEW ) {
            return CRM_Core_Action::VIEW | CRM_Core_Action::PREVIEW;
        } else {
            return CRM_Core_Action::VIEW;
        }
    }
 
    /** 
     * Function to build the form 
     * 
     * @return None 
     * @access public 
     */ 
    public function buildQuickForm( )  
    { 
        $this->assignToTemplate( );
        $config =& CRM_Core_Config::singleton( );
        
        $this->buildCustom( $this->_values['custom_pre_id'] , 'customPre'  );
        $this->buildCustom( $this->_values['custom_post_id'], 'customPost' );

        if ($config->paymentProcessor == 'Google_Checkout') {
            $this->_checkoutButtonName = $this->getButtonName( 'next', 'checkout' );
            $this->add('image',
                       $this->_checkoutButtonName,
                       $config->googleCheckoutButton[$this->_mode],
                       array( 'class' => 'form-submit' ) );
            
            $this->addButtons(array(
                                    array ( 'type'      => 'back',
                                            'name'      => ts('<< Go Back')),
                                    )
                              );
            
        } else {
            $contribButton = ts('Make Contribution');
            if ( $this->_contributeMode == 'notify' || ! $this->_values['is_monetary'] ) {
                $contribButton = ts('Continue >>');
            }
            $this->addButtons(array(
                                    array ( 'type'      => 'next',
                                            'name'      => $contribButton,
                                            'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                                            'isDefault' => true,
                                            'js'        => array( 'onclick' => "return submitOnce(this,'Confirm','" . ts('Processing') ."');" ) ),
                                    array ( 'type'      => 'back',
                                            'name'      => ts('<< Go Back')),
                                    )
                              );
        }
        
        $defaults = array( );
        $fields = array( );
        if( ! empty( $this->_fields ) ) {
            foreach ( $this->_fields as $name => $dontCare ) {
                $fields[$name] = 1;
            }
        }
        $fields["state_province-{$this->_bltID}"] =
            $fields["country-{$this->_bltID}"] = $fields["email-{$this->_bltID}"] = 1;

        foreach ($fields as $name => $dontCare ) {
            if ( isset($this->_params[$name]) ) {
                    $defaults[$name] = $this->_params[$name];
            }
        }

        $this->setDefaults( $defaults );
        
        $this->freeze();
    }
    
    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess( ) 
    {
        require_once 'CRM/Event/BAO/Participant.php';

        $config  =& CRM_Core_Config::singleton( );
        $session =& CRM_Core_Session::singleton( );
        $contactID = $session->get( 'userID' );
        $now = date( 'YmdHis' );

        $params = $this->_params;
        $fields = array( );

        $this->fixLocationFields( $params, $fields );
        
        $contactID =& $this->updateContactFields( $contactID, $params, $fields );
        
        // required only if paid event
        if ( $this->_values['event']['is_monetary'] ) {
            require_once 'CRM/Core/Payment.php';
            $payment =& CRM_Core_Payment::singleton( $this->_mode, 'Event' );

            switch ( $this->_contributeMode ) {
            case 'express':
                $result =& $payment->doExpressCheckout( $this->_params );
                break;
            case 'checkout':
            case 'notify':
                $this->_params['contactID'] = $contactID;
                $this->_params['eventID']   = $this->_id;
                
                $contribution =& $this->processContribution( $this->_params, null, $contactID, true );
                $this->_params['contributionID'    ] = $contribution->id;
                $this->_params['contributionTypeID'] = $contribution->contribution_type_id;
                $this->_params['item_name'         ] = ts( 'Online Event Registration:' ) . ' ' . $this->_values['event']['title'];
                $this->_params['receive_date'      ] = $now;
                if ($config->paymentProcessor == 'Google_Checkout') {
                    $payment->doCheckout( $this->_params );
                }
                $result =& $payment->doTransferCheckout( $this->_params );
                break;
            default   :
                self::mapParams( $this->_bltID, $this->_params, $this->_params, true );
                $result =& $payment->doDirectPayment( $this->_params );
                break;
            }
            
            if ( is_a( $result, 'CRM_Core_Error' ) ) {
                CRM_Core_Error::displaySessionError( $result );
                CRM_Utils_System::redirect( CRM_Utils_System::url( 'civicrm/event/info', "id={$this->_id}&reset=1" ) );
            }
            
            if ( $result ) {
                $this->_params = array_merge( $this->_params, $result );
            }

            $this->_params['receive_date'] = $now;
            
            // transactionID & receive date required while building email template
            $this->assign( 'trxn_id', $result['trxn_id'] );
            $this->assign( 'receive_date', CRM_Utils_Date::mysqlToIso( $this->_params['receive_date']) );
          
            // if paid event add a contribution record
            $contribution =& $this->processContribution( $this->_params, $result, $contactID );
        }
        $this->set( 'params', $this->_params );
        
        // insert participant record
        $participant  =& $this->addParticipant( $this->_params, $contactID );

        //hack to add participant custom data which is included in profile
        //format custom data
        $customData = array( );
        foreach ( $this->_params as $key => $value ) {
            if ( $customFieldId = CRM_Core_BAO_CustomField::getKeyID($key) ) {
                CRM_Core_BAO_CustomField::formatCustomField( $customFieldId, $customData,$value, 'Participant');
            }
        }
        
        if ( ! empty($customData) ) {
            foreach ( $customData as $customValue) {
                $cvParams = array(
                                  'entity_table'    => 'civicrm_participant', 
                                  'entity_id'       => $participant->id,
                                  'value'           => $customValue['value'],
                                  'type'            => $customValue['type'],
                                  'custom_field_id' => $customValue['custom_field_id'],
                                  'file_id'         => $customValue['file_id'],
                                  );
                
                if ($customValue['id']) {
                    $cvParams['id'] = $customValue['id'];
                }
                CRM_Core_BAO_CustomValue::create($cvParams);
            }
        }
        
        require_once 'CRM/Event/BAO/ParticipantPayment.php';
        $paymentParams = array('participant_id'       => $participant->id,
                               'payment_entity_id'    => $contribution->id,
                               'payment_entity_table' => 'civicrm_contribution'
                               ); 
        $ids = array();       

        $paymentPartcipant = CRM_Event_BAO_ParticipantPayment::create($paymentParams, $ids);
        
        // insert activity record
        CRM_Event_BAO_Participant::setActivityHistory( $participant );
        
        require_once "CRM/Event/BAO/EventPage.php";

        $this->assign('action',$this->_action);
        
        CRM_Event_BAO_EventPage::sendMail( $contactID, $this->_values, $participant->id );

    }//end of function
    
    /**
     * Process the contribution
     *
     * @return void
     * @access public
     */
    public function addParticipant( $params, $contactID ) 
    {
        CRM_Core_DAO::transaction( 'BEGIN' );

        $domainID = CRM_Core_Config::domainID( );
        $groupName = "participant_role";
        $query = "
SELECT  v.label as label ,v.value as value
FROM   civicrm_option_value v, 
       civicrm_option_group g 
WHERE  v.option_group_id = g.id 
  AND  g.domain_id       = $domainID 
  AND  g.name            = %1 
  AND  v.is_active       = 1  
  AND  g.is_active       = 1  
";
        $p = array( 1 => array( $groupName , 'String' ) );

        $dao =& CRM_Core_DAO::executeQuery( $query, $p );
        if ( $dao->fetch( ) ) {
            $roleID = $dao->value;
        }
        
        $participantParams = array('contact_id'    => $contactID,
                                   'event_id'      => $this->_id,
                                   'status_id'     => 1,
                                   'role_id'       => $roleID,
                                   'register_date' => date( 'YmdHis' ),
                                   'source'        => ts( 'Online Event Registration:' ) . ' ' . $this->_values['event']['title'],
                                   'event_level'   => $params['amount_level']
                                   );
        
        if( $this->_action & CRM_Core_Action::PREVIEW ) {
            $participantParams['is_test'] = 1;
        }
        
        $ids = array();
        $participant = CRM_Event_BAO_Participant::create($participantParams, $ids);
        
        CRM_Core_DAO::transaction( 'COMMIT' );

        return $participant;
    }

    /**
     * Process the contribution
     *
     * @return void
     * @access public
     */
    public function processContribution( $params, $result, $contactID, $pending = false ) 
    {
        CRM_Core_DAO::transaction( 'BEGIN' );

        $config =& CRM_Core_Config::singleton( );
        $now         = date( 'YmdHis' );
        $receiptDate = null;
        
        if ( $this->_values['event_page']['is_email_confirm'] ) {
            $receiptDate = $now ;
        }
        
        $contribParams = array(
                               'contact_id'            => $contactID,
                               'contribution_type_id'  => $this->_values['event']['contribution_type_id'],
                               //'contribution_page_id'  => $this->_id,
                               'payment_instrument_id' => 1,
                               'receive_date'          => $now,
                               //'non_deductible_amount' => $nonDeductibleAmount,
                               'total_amount'          => $params['amount'],
                               'amount_level'          => $params['amount_level'],
                               'invoice_id'            => $params['invoiceID'],
                               'currency'              => $params['currencyID'],
                               'source'                => ts( 'Online Event Registration:' ) . ' ' . $this->_values['event']['title']
                               );
        
        if ( ! $pending && $result ) {
            $contribParams += array(
                                    'fee_amount'   => CRM_Utils_Array::value( 'fee_amount', $result ),
                                    'net_amount'   => CRM_Utils_Array::value( 'net_amount', $result, $params['amount'] ),
                                    'trxn_id'      => $result['trxn_id'],
                                    'receipt_date' => $receiptDate,
                                    );
        }

        $contribParams["contribution_status_id"] = $pending ? 2 : 1;

        if( $this->_action & CRM_Core_Action::PREVIEW ) {
            $contribParams["is_test"] = 1;
        }
        
        $ids = array( );
        $contribution =& CRM_Contribute_BAO_Contribution::add( $contribParams, $ids );
       
        // return if pending
        if ( $pending ) {
            CRM_Core_DAO::transaction( 'COMMIT' );
            return $contribution;
        }
        
        // next create the transaction record
        $trxnParams = array(
                            'entity_table'      => 'civicrm_contribution',
                            'entity_id'         => $contribution->id,
                            'trxn_date'         => $now,
                            'trxn_type'         => 'Debit',
                            'total_amount'      => $params['amount'],
                            'fee_amount'        => CRM_Utils_Array::value( 'fee_amount', $result ),
                            'net_amount'        => CRM_Utils_Array::value( 'net_amount', $result, $params['amount'] ),
                            'currency'          => $params['currencyID'],
                            'payment_processor' => $config->paymentProcessor,
                            'trxn_id'           => $result['trxn_id'],
                            );
        
        require_once 'CRM/Contribute/BAO/FinancialTrxn.php';
        $trxn =& CRM_Contribute_BAO_FinancialTrxn::create( $trxnParams );

        CRM_Core_DAO::transaction( 'COMMIT' );
        
        return $contribution;
    }
    
    /**
     * Fix the Location Fields
     *
     * @return void
     * @access public
     */
    public function fixLocationFields( &$params, &$fields ) 
    {
        if( ! empty($this->_field) ) {
            foreach ( $this->_fields as $name => $dontCare ) {
                $fields[$name] = 1;
            }
        }

        if ( ! array_key_exists( 'first_name', $fields ) ) {
            $nameFields = array( 'first_name', 'middle_name', 'last_name' );
            foreach ( $nameFields as $name ) {
                $fields[$name] = 1;
                if ( array_key_exists( "billing_$name", $params ) ) {
                    $params[$name] = $params["billing_{$name}"];
                }
            }
        }

        // also add location name to the array
        $params["location_name-{$this->_bltID}"] = 
            $params["billing_first_name"] . ' ' . $params["billing_middle_name"] . ' ' . $params["billing_last_name"];
        $fields["location_name-{$this->_bltID}"] = 1;
        $fields["email-{$this->_bltID}"] = 1;
        $fields["email-Primary"] = 1;
        $params["email-Primary"] = $params["email-{$this->_bltID}"];
    }
    
    /**
     * function to update contact fields
     *
     * @return void
     * @access public
     */
    public function updateContactFields( $contactID, $params, $fields ) 
    {
        require_once "CRM/Contact/BAO/Contact.php";

        if ($contactID) {
            $ctype = CRM_Core_DAO::getFieldValue("CRM_Contact_DAO_Contact", $contactID, "contact_type");
            $contactID =& CRM_Contact_BAO_Contact::createProfileContact( $params, $fields, $contactID, null, null,$ctype);
        } else {
            // finding contact record based on duplicate match 
            require_once 'api/crm.php';
            $ids = CRM_Core_BAO_UFGroup::findContact( $params );
            $contactsIDs = explode( ',', $ids );
            
            // if we find more than one contact, use the first one
            $contact_id  = $contactsIDs[0];
            $contactID =& CRM_Contact_BAO_Contact::createProfileContact( $params, $fields, $contact_id );
            $this->set( 'contactID', $contactID );
        }

        return $contactID;
    }

    /**
     * function to map address fields
     *
     * @return void
     * @static
     */
    static function mapParams( $id, &$src, &$dst, $reverse = false ) {
        static $map = null;
        if ( ! $map ) {
            $map = array( 'first_name'             => 'billing_first_name'        ,
                          'middle_name'            => 'billing_middle_name'       ,
                          'last_name'              => 'billing_last_name'         ,
                          'email'                  => "email-$id"                 ,
                          'street_address'         => "street_address-$id"        ,
                          'supplemental_address_1' => "supplemental_address_1-$id",
                          'city'                   => "city-$id"                  ,
                          'state_province'         => "state_province-$id"        ,
                          'postal_code'            => "postal_code-$id"           ,
                          'country'                => "country-$id"               ,
                          );
        }
        
        foreach ( $map as $n => $v ) {
            if ( ! $reverse ) {
                if ( isset( $src[$n] ) ) {
                    $dst[$v] = $src[$n];
                }
            } else {
                if ( isset( $src[$v] ) ) {
                    $dst[$n] = $src[$v];
                }
            }
        }
    }
}
?>
