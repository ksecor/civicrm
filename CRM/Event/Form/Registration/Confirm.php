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

        // lineItem isn't set until Register postProcess
        $this->_lineItem = $this->get( 'lineItem' );
        
        $config =& CRM_Core_Config::singleton( );
        if ( $this->_contributeMode == 'express' ) {
            // rfp == redirect from paypal
            $rfp = CRM_Utils_Request::retrieve( 'rfp', 'Boolean',
                                                CRM_Core_DAO::$_nullObject, false, null, 'GET' );
            if ( $rfp ) {
                require_once 'CRM/Core/Payment.php'; 
                $payment =& CRM_Core_Payment::singleton( $this->_mode, 'Event', $this->_paymentProcessor );
                $expressParams = $payment->getExpressCheckoutDetails( $this->get( 'token' ) );
                
                $this->_params['payer'       ] = $expressParams['payer'       ];
                $this->_params['payer_id'    ] = $expressParams['payer_id'    ];
                $this->_params['payer_status'] = $expressParams['payer_status'];

                require_once 'CRM/Core/Payment/Form.php';
                CRM_Core_Payment_Form::mapParams( $this->_bltID, $expressParams, $this->_params, false );
                
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
                $this->_params['ip_address']     = CRM_Utils_System::ipAddress( );
                
                $this->_params['amount'        ] = $this->get( 'amount' );
                $this->_params['amount_level'  ] = $this->get( 'amount_level' );
                $this->_params['currencyID'    ] = $config->defaultCurrency;
                $this->_params['payment_action'] = 'Sale';
            }
        }

        if ( $this->_values['event']['is_monetary'] ) {
            $this->_params['invoiceID'] = $this->get( 'invoiceID' );
        }

        if ( ! isset( $this->_params['participant_role_id'] ) && $this->_values['event']['default_role_id'] ) {
            $this->_params['participant_role_id'] = $this->_values['event']['default_role_id'];
        }
        
        if ( isset ($this->_values['event_page']['confirm_title'] ) ) 
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

        $this->assign( 'lineItem', $this->_lineItem );

        if ( $this->_paymentProcessor['payment_processor_type'] == 'Google_Checkout' && 
             ! CRM_Utils_Array::value( 'is_pay_later', $this->_params ) ) {
            $this->_checkoutButtonName = $this->getButtonName( 'next', 'checkout' );
            $this->add('image',
                       $this->_checkoutButtonName,
                       $this->_paymentProcessor['url_button'],
                       array( 'class' => 'form-submit' ) );
            
            $this->addButtons(array(
                                    array ( 'type'      => 'back',
                                            'name'      => ts('<< Go Back')),
                                    )
                              );
            
        } else {
            $contribButton = ts('Continue >>');
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

        // lets store the contactID in the session
        // we dont store in userID in case the user is doing multiple
        // transactions etc
        // for things like tell a friend
        if ( ! $session->get( 'userID' ) ) {
            $session->set( 'transaction.userID', $contactID );
        } else {
            $session->set( 'transaction.userID', null );
        }
        
        $this->_params['description'] = ts( 'Online Event Registration:' ) . ' ' . $this->_values['event']['title'];

        // required only if paid event
        if ( $this->_values['event']['is_monetary'] ) {
        
            require_once 'CRM/Core/Payment.php';
            $payment =& CRM_Core_Payment::singleton( $this->_mode, 'Event', $this->_paymentProcessor );

            $pending = false;
            $result  = null;
            if ( CRM_Utils_Array::value( 'is_pay_later', $this->_params ) ||
                 $this->_contributeMode == 'checkout'                     ||
                 $this->_contributeMode == 'notify' ) {
                $pending = true;
                $this->_params['participant_status_id'] = 5; // pending
            } else if ( $this->_contributeMode == 'express' ) {
                $result =& $payment->doExpressCheckout( $this->_params );
            } else {
                require_once 'CRM/Core/Payment/Form.php';
                CRM_Core_Payment_Form::mapParams( $this->_bltID, $this->_params, $this->_params, true );
                $result =& $payment->doDirectPayment( $this->_params );
            }
            
            if ( is_a( $result, 'CRM_Core_Error' ) ) {
                CRM_Core_Error::displaySessionError( $result );
                CRM_Utils_System::redirect( CRM_Utils_System::url( 'civicrm/event/info', "id={$this->_id}&reset=1" ) );
            }
            
            if ( $result ) {
                $this->_params = array_merge( $this->_params, $result );
            }

            $this->_params['receive_date'] = $now;
            
            if ( ! $pending ) {
                // transactionID & receive date required while building email template
                $this->assign( 'trxn_id', $result['trxn_id'] );
                $this->assign( 'receive_date', CRM_Utils_Date::mysqlToIso( $this->_params['receive_date']) );
            }

            // if paid event add a contribution record
            $contribution =& $this->processContribution( $this->_params, $result, $contactID, $pending );

            $this->_params['contactID']          = $contactID;
            $this->_params['eventID']            = $this->_id;
            $this->_params['contributionID'    ] = $contribution->id;
            $this->_params['contributionTypeID'] = $contribution->contribution_type_id;
            $this->_params['item_name'         ] = $this->_params['description'];
        }
        $this->set( 'params', $this->_params );
        $this->confirmPostProcess( $this, $contactID, $contribution, $payment);
        
    } //end of function
    
    /**
     * Process the contribution
     *
     * @return void
     * @access public
     */
    public function addParticipant( $params, $contactID ) 
    {
        require_once 'CRM/Core/Transaction.php';
        $transaction = new CRM_Core_Transaction( );
        
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
                                   'status_id'     => CRM_Utils_Array::value( 'participant_status_id',
                                                                              $params, 1 ),
                                   'role_id'       => CRM_Utils_Array::value( 'participant_role_id',
                                                                              $params, $roleID ),
                                   'register_date' => isset( $params['participant_register_date'] ) ?
                                   CRM_Utils_Date::format( $params['participant_register_date'] ) :
                                   date( 'YmdHis' ),
                                   'source'        => isset( $params['participant_source'] ) ?
                                   $params['participant_source'] :
                                   $params['description'],
                                   'event_level'   => $params['amount_level']
                                   );
        
        if ( $this->_action & CRM_Core_Action::PREVIEW ) {
            $participantParams['is_test'] = 1;
        }

        if ( $this->_params['note'] ) {
            $participantParams['note'] = $this->_params['note'];
        } else if ( $this->_params['participant_note'] ) {
            $participantParams['note'] = $this->_params['participant_note'];
        }
        
        $ids = array();
        $participant = CRM_Event_BAO_Participant::create($participantParams, $ids);
        
        $transaction->commit( );
        
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
        require_once 'CRM/Core/Transaction.php';
        $transaction = new CRM_Core_Transaction( );
        
        $config =& CRM_Core_Config::singleton( );
        $now         = date( 'YmdHis' );
        $receiptDate = null;
        
        if ( $this->_values['event_page']['is_email_confirm'] ) {
            $receiptDate = $now ;
        }
        
        $contribParams = array(
                               'contact_id'            => $contactID,
                               'contribution_type_id'  => $this->_values['event']['contribution_type_id'],
                               'receive_date'          => $now,
                               'total_amount'          => $params['amount'],
                               'amount_level'          => $params['amount_level'],
                               'invoice_id'            => $params['invoiceID'],
                               'currency'              => $params['currencyID'],
                               'source'                => $params['description'],
                               );
        
        if ( ! $params['is_pay_later'] ) {
            $contribParams['payment_instrument_id'] = 1;
        }

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
        require_once 'CRM/Contribute/BAO/Contribution.php';
        $ids = array( );
        $contribution =& CRM_Contribute_BAO_Contribution::add( $contribParams, $ids );

        // store line items
        if ( $this->_lineItem ) {
            require_once 'CRM/Core/BAO/LineItem.php';
            foreach ( $this->_lineItem as $line ) {
                $unused = array();
                $line['entity_table'] = 'civicrm_contribution';
                $line['entity_id'] = $contribution->id;
                CRM_Core_BAO_LineItem::create( $line, $unused );
            }
        }

        // return if pending
        if ( $pending ) {
            $transaction->commit( );
            return $contribution;
        }
        
        // next create the transaction record
        $trxnParams = array(                            
                            'contribution_id'   => $contribution->id,
                            'trxn_date'         => $now,
                            'trxn_type'         => 'Debit',
                            'total_amount'      => $params['amount'],
                            'fee_amount'        => CRM_Utils_Array::value( 'fee_amount', $result ),
                            'net_amount'        => CRM_Utils_Array::value( 'net_amount', $result, $params['amount'] ),
                            'currency'          => $params['currencyID'],
                            'payment_processor' => $this->_paymentProcessor['payment_processor_type'],
                            'trxn_id'           => $result['trxn_id'],
                            );
        
        require_once 'CRM/Contribute/BAO/FinancialTrxn.php';
        $trxn =& CRM_Contribute_BAO_FinancialTrxn::create( $trxnParams );

        $transaction->commit( );
        
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
        if( ! empty($this->_fields) ) {
            foreach ( $this->_fields as $name => $dontCare ) {
                $fields[$name] = 1;
            }
        }

        if ( is_array($fields) ) {
            if ( ! array_key_exists( 'first_name', $fields ) ) {
                $nameFields = array( 'first_name', 'middle_name', 'last_name' );
                foreach ( $nameFields as $name ) {
                    $fields[$name] = 1;
                    if ( array_key_exists( "billing_$name", $params ) ) {
                        $params[$name] = $params["billing_{$name}"];
                    }
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
        //add the contact to group, if add to group is selected for a
        //particular uf group
 
        // get the add to groups
        $addToGroups = array( );
   
        if ( !empty($this->_fields) ) {
            foreach ( $this->_fields as $key => $value) {
                if ( $value['add_to_group_id'] ) {
                    $addToGroups[$value['add_to_group_id']] = $value['add_to_group_id'];
                }
            } 
        }

        require_once "CRM/Contact/BAO/Contact.php";

        if ($contactID) {
            $ctype = CRM_Core_DAO::getFieldValue("CRM_Contact_DAO_Contact", $contactID, "contact_type");
            $contactID =& CRM_Contact_BAO_Contact::createProfileContact( $params, $fields, $contactID, $addToGroups, null,$ctype);
        } else {
            // finding contact record based on duplicate match 
            require_once 'api/crm.php';
            $ids = CRM_Core_BAO_UFGroup::findContact( $params );
            $contactsIDs = explode( ',', $ids );
            
            // if we find more than one contact, use the first one
            $contact_id  = $contactsIDs[0];
            $contactID =& CRM_Contact_BAO_Contact::createProfileContact( $params, $fields, $contact_id, $addToGroups );
            $this->set( 'contactID', $contactID );
        }


        return $contactID;
    }

}
?>
