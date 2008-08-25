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
    function preProcess( ) 
    {
        parent::preProcess( );
        
        // lineItem isn't set until Register postProcess
        $this->_lineItem = $this->get( 'lineItem' );
        $this->_params = $this->get( 'params' );
        $config =& CRM_Core_Config::singleton( );
        if ( $this->_contributeMode == 'express' ) {
            $params = array(); 
            // rfp == redirect from paypal
            $rfp = CRM_Utils_Request::retrieve( 'rfp', 'Boolean',
                                                CRM_Core_DAO::$_nullObject, false, null, 'GET' );
           
            //we lost rfp in case of additional participant. So set it explicitly.
            if ( $rfp || CRM_Utils_Array::value( 'additional_participants', $this->_params[0], false ) ) {
                require_once 'CRM/Core/Payment.php'; 
                $payment =& CRM_Core_Payment::singleton( $this->_mode, 'Event', $this->_paymentProcessor );
                $expressParams = $payment->getExpressCheckoutDetails( $this->get( 'token' ) );
                             
                $params['payer'       ] = $expressParams['payer'       ];
                $params['payer_id'    ] = $expressParams['payer_id'    ];
                $params['payer_status'] = $expressParams['payer_status'];

                require_once 'CRM/Core/Payment/Form.php';
                CRM_Core_Payment_Form::mapParams( $this->_bltID, $expressParams, $params, false );
                
                // fix state and country id if present
                if ( isset( $params["state_province_id-{$this->_bltID}"] ) ) {
                    $params["state_province-{$this->_bltID}"] =
                        CRM_Core_PseudoConstant::stateProvinceAbbreviation( $params["state_province_id-{$this->_bltID}"] ); 
                }
                if ( isset( $params['country_id'] ) ) {
                    $params["country-{$this->_bltID}"]        =
                        CRM_Core_PseudoConstant::countryIsoCode( $params["country_id-{$this->_bltID}"] ); 
                }

                // set a few other parameters for PayPal
                $params['token']          = $this->get( 'token' );
                $params['amount'        ] = $this->get( 'amount' );
                $params['amount_level'  ] = $this->get( 'amount_level' );
                $params['currencyID'    ] = $config->defaultCurrency;
                $params['payment_action'] = 'Sale';
                
                // also merge all the other values from the profile fields
                $values = $this->controller->exportValues( 'Register' );
                $skipFields = array( 'amount',
                                     "street_address-{$this->_bltID}",
                                     "city-{$this->_bltID}",
                                     "state_province_id-{$this->_bltID}",
                                     "postal_code-{$this->_bltID}",
                                     "country_id-{$this->_bltID}" );

                foreach ( $values as $name => $value ) {
                    // skip amount field
                    if ( ! in_array( $name, $skipFields ) ) {
                        $params[$name] = $value;
                    }
                }
                $this->set( 'getExpressCheckoutDetails', $params );
            } else {
                $params = $this->get( 'getExpressCheckoutDetails' );
            }
            $this->_params[0] = $params;
            $this->_params[0]['is_primary'] = 1; 
        } else {
            //$this->_params = $this->controller->exportValues( 'Register' );
            //process only primary participant params.
            $registerParams = $this->_params[0];
            if ( isset( $registerParams["state_province_id-{$this->_bltID}"] ) 
                 && $registerParams["state_province_id-{$this->_bltID}"] ) {
                $registerParams["state_province-{$this->_bltID}"] =
                    CRM_Core_PseudoConstant::stateProvinceAbbreviation( $registerParams["state_province_id-{$this->_bltID}"] ); 
            }
            
            if ( isset( $registerParams["country_id-{$this->_bltID}"] ) && $registerParams["country_id-{$this->_bltID}"] ) {
                $registerParams["country-{$this->_bltID}"]        =
                    CRM_Core_PseudoConstant::countryIsoCode( $registerParams["country_id-{$this->_bltID}"] ); 
            }
            if ( isset( $registerParams['credit_card_exp_date'] ) ) {
                $registerParams['year'   ]        = $registerParams['credit_card_exp_date']['Y'];  
                $registerParams['month'  ]        = $registerParams['credit_card_exp_date']['M'];  
            }
            if ( $this->_values['event']['is_monetary'] ) {
                $registerParams['ip_address']     = CRM_Utils_System::ipAddress( );
                $registerParams['amount'        ] = $this->get( 'amount' );
                $registerParams['amount_level'  ] = $this->get( 'amount_level' );
                $registerParams['currencyID'    ] = $config->defaultCurrency;
                $registerParams['payment_action'] = 'Sale';
            }
            //assign back primary participant params.
            $this->_params[0] = $registerParams;
        }
        
        if ( $this->_values['event']['is_monetary'] ) {
            $this->_params[0]['invoiceID'] = $this->get( 'invoiceID' );
        }
        $this->assign('defaultRole', false );
        if ( CRM_Utils_Array::value( 'defaultRole', $this->_params[0] ) == 1 ) {
            $this->assign( 'defaultRole', true );
        }
        
        if ( ! CRM_Utils_Array::value( 'participant_role_id', $this->_params[0] ) && $this->_values['event']['default_role_id'] ) {
            $this->_params[0]['participant_role_id'] = $this->_values['event']['default_role_id'];
        }
        
        if ( isset ($this->_values['event_page']['confirm_title'] ) ) {
            CRM_Utils_System::setTitle($this->_values['event_page']['confirm_title']);
            $this->set( 'params', $this->_params );
        }
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
        //to set amount & levels
        $this->_params = $this->get( 'params' );
        if( $this->_params[0]['amount'] || $this->_params[0]['amount'] == 0 ) {
            $this->_amount       = array();
                     
            foreach( $this->_params as $k => $v ) {
                if ( is_array( $v ) ) {
                    if ( CRM_Utils_Array::value( 'is_primary', $v ) ) {
                        $this->set( 'primaryParticipantAmount',$v['amount'] );
                    }
                    $this->_amount[ $v['amount_level'].'  -  '. $v['email-5'] ] = $v['amount'];
                    $this->_totalAmount = $this->_totalAmount + $v['amount'];
                }
            }
            $this->assign('amount', $this->_amount);
            $this->assign('totalAmount', $this->_totalAmount);
            $this->set( 'totalAmount', $this->_totalAmount );
        }

        $config =& CRM_Core_Config::singleton( );
        
        $this->buildCustom( $this->_values['custom_pre_id'] , 'customPre' , true );
        $this->buildCustom( $this->_values['custom_post_id'], 'customPost', true );
        
        $this->assign( 'lineItem', $this->_lineItem );
        //display additional participants profile.
        require_once 'CRM/Event/BAO/EventPage.php';
        $participantParams = $this->_params;
        $formattedValues = array( );
        $count = 1;
        foreach ( $participantParams as $participantNum => $participantValue ) {
            if ( $participantNum && $participantValue != 'skip') {
                //get the customPre profile info
                if ( CRM_Utils_Array::value( 'custom_pre_id', $this->_values ) ) {
                    $values = array( );
                    $groupName = array( );
                    CRM_Event_BAO_EventPage::displayProfile( $participantValue, $this->_values['custom_pre_id'], $groupName, $values );
                    if ( count( $values ) ) {
                        $formattedValues[$count]['customPre'] = $values;
                    }
                    $formattedValues[$count]['customPreGroupTitle'] = CRM_Utils_Array::value( 'groupTitle', $groupName );
                }
                //get the customPost profile info
                if ( CRM_Utils_Array::value( 'custom_post_id', $this->_values ) ) {
                    $values = array( );
                    $groupName = array( );
                    CRM_Event_BAO_EventPage::displayProfile( $participantValue, $this->_values['custom_post_id'], $groupName, $values );
                    if ( count( $values ) ) {
                        $formattedValues[$count]['customPost'] = $values;
                    }
                    $formattedValues[$count]['customPostGroupTitle'] = CRM_Utils_Array::value( 'groupTitle', $groupName );
                }
                $count++; 
            }
        }
        
        if ( ! empty( $formattedValues ) && $count > 1 ) {
            $this->assign( 'addParticipantProfile' , $formattedValues );
        }
        
        if( $this->_params[0]['amount'] == 0 ) {
            $this->assign( 'isAmountzero', 1 );
        }

        if ( $this->_paymentProcessor['payment_processor_type'] == 'Google_Checkout' && 
             ! CRM_Utils_Array::value( 'is_pay_later', $this->_params[0] ) && ! ( $this->_params[0]['amount'] == 0 )  ) {
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
                                    array ( 'type'      => 'back',
                                            'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                                            'name'      => ts('<< Go Back'),
                                           ),
                                    array ( 'type'      => 'next',
                                            'name'      => $contribButton,
                                            'isDefault' => true,
                                            'js'        => array( 'onclick' => "return submitOnce(this,'" . $this->_name . "','" . ts('Processing') ."');" ),
                                           ),
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
            if ( isset($this->_params[0][$name]) ) {
                    $defaults[$name] = $this->_params[0][$name];
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
        $this->_params = $this->get( 'params' );
        $params = $this->_params;
        $this->set( 'finalAmount' ,$this->_amount );
        $participantCount = array( );
        //unset the skip participant from params.
        //build the $participantCount array.
        //maintain record for all participants.
        foreach ( $params as $participantNum => $record ) {
            if ( $record == 'skip' ) {
                unset( $params[$participantNum] );
                $participantCount[$participantNum] = 'skip';
            } else if ( $participantNum ) {
                $participantCount[$participantNum] = 'participant';
            }
        }
        
        foreach ( $params as $key => $value ) {
            $this->fixLocationFields( $value, $fields );
            //unset the billing parameters if it is pay later mode
            //to avoid creation of billing location
            if ( $value['is_pay_later'] || !CRM_Utils_Array::value( 'is_primary', $value ) ) {
                $billingFields = array( "email-{$this->_bltID}",
                                        "billing_first_name",
                                        "billing_middle_name",
                                        "billing_last_name",
                                        "street_address-{$this->_bltID}",
                                        "city-{$this->_bltID}",
                                        "state_province-{$this->_bltID}",
                                        "state_province_id-{$this->_bltID}",
                                        "postal_code-{$this->_bltID}",
                                        "country-{$this->_bltID}",
                                        "country_id-{$this->_bltID}"
                                        );
                foreach( $billingFields as $field ) {
                    unset( $value[$field] );
                }
            }
            
            //Unset ContactID for additional participants and set RegisterBy Id.
            if ( !CRM_Utils_Array::value( 'is_primary', $value ) ) {
                $contactID = null;
                $registerByID = $this->get( 'registerByID' );
                if ( $registerByID ) {
                    $value['registered_by_id'] = $registerByID;
                }
            } else {
                $value['amount'] = $this->_totalAmount;
            }
            
            $contactID =& $this->updateContactFields( $contactID, $value, $fields );
            
            // lets store the contactID in the session
            // we dont store in userID in case the user is doing multiple
            // transactions etc
            // for things like tell a friend
            if ( ! $session->get( 'userID' ) && CRM_Utils_Array::value( 'is_primary', $value ) ) {
                $session->set( 'transaction.userID', $contactID );
            } 
            
            $value['description'] = ts( 'Online Event Registration' ) . ': ' . $this->_values['event']['title'];
            
            // required only if paid event
            if ( $this->_values['event']['is_monetary'] ) {
                
                require_once 'CRM/Core/Payment.php';
                if ( is_array( $this->_paymentProcessor ) ) {
                    $payment =& CRM_Core_Payment::singleton( $this->_mode, 'Event', $this->_paymentProcessor );
                }
                $pending = false;
                $result  = null;
                if ( CRM_Utils_Array::value( 'is_pay_later', $value ) ||
                     $value['amount']         == 0                    ||
                     $this->_contributeMode   == 'checkout'           ||
                     $this->_contributeMode   == 'notify' ) {
                    if ( $value['amount'] != 0 ) {
                        $pending = true;
                        $value['participant_status_id'] = 5; // pending
                    }
                } else if ( $this->_contributeMode == 'express' && CRM_Utils_Array::value( 'is_primary', $value ) ) {
                    $result =& $payment->doExpressCheckout( $value );
                } else if ( CRM_Utils_Array::value( 'is_primary', $value ) ) {
                    require_once 'CRM/Core/Payment/Form.php';
                    CRM_Core_Payment_Form::mapParams( $this->_bltID, $value, $value, true );
                    $result =& $payment->doDirectPayment( $value );
                }
                
                if ( is_a( $result, 'CRM_Core_Error' ) ) {
                    CRM_Core_Error::displaySessionError( $result );
                    CRM_Utils_System::redirect( CRM_Utils_System::url( 'civicrm/event/info', "id={$this->_id}&reset=1" ) );
                }
                
                if ( $result ) {
                    $value = array_merge( $value, $result );
                }
                
                $value['receive_date'] = $now;
                
                if ( ! $pending && CRM_Utils_Array::value( 'is_primary', $value ) ) {
                    // transactionID & receive date required while building email template
                    $this->assign( 'trxn_id', $result['trxn_id'] );
                    $this->assign( 'receive_date', CRM_Utils_Date::mysqlToIso( $value['receive_date']) );
                    $this->set( 'receiveDate', CRM_Utils_Date::mysqlToIso( $value['receive_date']) );
                    $this->set( 'trxnId', CRM_Utils_Array::value( 'trxn_id', $value ) );
                }
                $contribution = null;
                // if paid event add a contribution record
                if( $value['amount'] != 0 && CRM_Utils_Array::value( 'is_primary', $value ) ) {
                    $contribution =& self::processContribution( $this, $value, $result, $contactID, $pending );
                }
                $value['contactID']          = $contactID;
                $value['eventID']            = $this->_id;
                $value['contributionID'    ] = $contribution->id;
                $value['contributionTypeID'] = $contribution->contribution_type_id;
                $value['item_name'         ] = $value['description'];
            }
            $value['fee_amount'] =  $value['amount'];
            $this->set( 'value', $value );
            $registerDate = isset( $value['participant_register_date'] ) ?
                CRM_Utils_Date::format( $value['participant_register_date'] ) : date( 'YmdHis' );
            $this->assign( 'register_date', $registerDate );
            
            $this->confirmPostProcess( $contactID, $contribution, $payment );
        }

        $isTest = false;
        if ( $this->_action & CRM_Core_Action::PREVIEW ) {
            $isTest = true;
        }
        
        //handle if no additional participant.
        if ( ! $registerByID ) {
            $registerByID = $this->get('registerByID');
        }

        // for Transfer checkout.
        require_once "CRM/Event/BAO/EventPage.php";
        if ( ( $this->_contributeMode == 'checkout' ||
               $this->_contributeMode == 'notify'   ) && 
             ! CRM_Utils_Array::value( 'is_pay_later', $params[0] ) &&
             $this->_params['amount'] > 0 ) {

            $primaryParticipant = $this->get ( 'primaryParticipant' );

            if ( !CRM_Utils_Array::value( 'participantID', $primaryParticipant ) ) {
                $primaryParticipant['participantID'] = $registerByID;
            } 
            
            //build an array of custom profile and assigning it to template
            $customProfile = CRM_Event_BAO_EventPage::buildCustomProfile( $registerByID, $this->_values, null, $isTest );
            
            if ( count($customProfile) ) {
                $this->assign( 'customProfile', $customProfile );
                $this->set   ( 'customProfile', $customProfile );
            }
            
            // do a transfer only if a monetary payment greater than 0
            if ( $this->_values['event']['is_monetary'] && $primaryParticipant ) {
                $payment->doTransferCheckout( $primaryParticipant );
            }

        } else {
            //otherwise send mail Confirmation/Receipt
            $primaryContactId = $this->get('primaryContactId');
            
            //build an array of cId/pId of participants
            require_once "CRM/Event/BAO/EventPage.php";
            $additionalIDs = CRM_Event_BAO_EventPage::buildCustomProfile( $registerByID, null, $primaryContactId, $isTest, true );
           
            foreach( $additionalIDs as $participantID => $contactId ) {
                if ( $participantID == $registerByID ) {
                    //set as Primary Participant
                    $this->assign ( 'isPrimary' , 1 );
                    //build an array of custom profile and assigning it to template.
                    $customProfile = CRM_Event_BAO_EventPage::buildCustomProfile( $participantID, $this->_values, null, $isTest );
                                                     
                    if ( count($customProfile) ) {
                        $this->assign( 'customProfile', $customProfile );
                        $this->set   ( 'customProfile', $customProfile );
                    }
                    
                } else {
                    //take the Additional participant number. 
                    if ( $paticipantNum = array_search( 'participant', $participantCount ) ) {
                        unset( $participantCount[$paticipantNum] );
                    }
                    $this->assign ( 'isPrimary' , 0 );
                    $this->assign( 'customProfile', null );
                    //Additional Participant should get only it's payment information
                    if ( $this->_amount ) {
                        $amount = array();
                        $params = $this->get( 'params' );
                        $amount[$params[$paticipantNum]['amount_level']] = CRM_Utils_Array::value( 'amount', $params[$paticipantNum] );
                        $this->assign( 'amount', $amount );
                    }
                    if ( $this->_lineItem ) {
                        $lineItems = $this->_lineItem;
                        $lineItem = array();
                        $lineItem[] = CRM_Utils_Array::value( $paticipantNum, $lineItems );
                        $this->assign( 'lineItem',$lineItem );
                    } 
                }
                
                //send Confirmation mail to Primary & additional Participants if exists
                CRM_Event_BAO_EventPage::sendMail( $contactId, $this->_values, $participantID, $isTest );
            } 
        }
        
    } //end of function
    
    /**
     * Process the contribution
     *
     * @return void
     * @access public
     */
    static function processContribution( &$form, $params, $result, $contactID, $pending = false ) 
    {
        require_once 'CRM/Core/Transaction.php';
        $transaction = new CRM_Core_Transaction( );
        
        $config =& CRM_Core_Config::singleton( );
        $now         = date( 'YmdHis' );
        $receiptDate = null;
        
        if ( $form->_values['event_page']['is_email_confirm'] ) {
            $receiptDate = $now ;
        }
        
        $contribParams = array(
                               'contact_id'            => $contactID,
                               'contribution_type_id'  => $form->_values['event']['contribution_type_id'] ?
                               $form->_values['event']['contribution_type_id'] : $params['contribution_type_id'],
                               'receive_date'          => $now,
                               'total_amount'          => $params['amount'],
                               'amount_level'          => $params['amount_level'],
                               'invoice_id'            => $params['invoiceID'],
                               'currency'              => $params['currencyID'],
                               'source'                => $params['description'],
                               'is_pay_later'          => CRM_Utils_Array::value( 'is_pay_later', $params, 0 ),
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

        if( $form->_action & CRM_Core_Action::PREVIEW || $params['mode'] == 'test' ) {
            $contribParams["is_test"] = 1;
        }
        require_once 'CRM/Contribute/BAO/Contribution.php';
        $ids = array( );
        if ( isset( $contribParams['invoice_id'] ) ) {
            $contribID = CRM_Core_DAO::getFieldValue( 'CRM_Contribute_DAO_Contribution',
                                                      $contribParams['invoice_id'],
                                                      'id',
                                                      'invoice_id' );
            if ( isset( $contribID ) ) {
                $ids['contribution'] = $contribID;
                $contribParams['id'] = $contribID;
            }
        }
        $contribution =& CRM_Contribute_BAO_Contribution::add( $contribParams, $ids );
        
        // store line items
        if ( $form->_lineItem ) {
            require_once 'CRM/Core/BAO/LineItem.php';
            foreach ( $form->_lineItem as $key => $value ) {
                if ( $value != 'skip' ) {
                    foreach( $value as $line ) {
                        $unused = array();
                        $line['entity_table'] = 'civicrm_contribution';
                        $line['entity_id'] = $contribution->id;
                        CRM_Core_BAO_LineItem::create( $line, $unused );
                    }
                }
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
                            'payment_processor' => $form->_paymentProcessor['payment_processor_type'],
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
                        $params['preserveDBName'] = true;
                    }
                }
            }
        }

        // also add location name to the array
        $params["address_name-{$this->_bltID}"] = 
           $params["billing_first_name"] . ' ' . $params["billing_middle_name"] . ' ' . $params["billing_last_name"];
        $fields["address_name-{$this->_bltID}"] = 1;
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
            require_once 'CRM/Dedupe/Finder.php';
            //Dedupe couldn't recognize "email-Primary".So modify params temporary.
            if ( CRM_Utils_Array::value('email-Primary', $params) ) {
                $params ['email'] = $params['email-Primary'];
            }
            $dedupeParams = CRM_Dedupe_Finder::formatParams($params, 'Individual');
            $ids = CRM_Dedupe_Finder::dupesByParams($dedupeParams, 'Individual');
           
            // if we find more than one contact, use the first one
            $contact_id  = $ids[0];
            $contactID =& CRM_Contact_BAO_Contact::createProfileContact( $params, $fields, $contact_id, $addToGroups );
            $this->set( 'contactID', $contactID );
        }


        return $contactID;
    }

}

