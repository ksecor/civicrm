<?php 
 
/**
 * Copyright (C) 2006 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

 /* This is the response handler code that will be invoked every time
  * a notification or request is sent by the Google Server
  *
  * To allow this code to receive responses, the url for this file
  * must be set on the seller page under Settings->Integration as the
  * "API Callback URL'
  * Order processing commands can be sent automatically by placing these
  * commands appropriately
  *
  * To use this code for merchant-calculated feedback, this url must be
  * set also as the merchant-calculations-url when the cart is posted
  * Depending on your calculations for shipping, taxes, coupons and gift
  * certificates update parts of the code as required
  *
  */

class CRM_Core_Payment_GoogleIPN {

    /**
     * We only need one instance of this object. So we use the singleton
     * pattern and cache the instance in this variable
     *
     * @var object
     * @static
     */
    static private $_singleton = null;

    /**
     * mode of operation: live or test
     *
     * @var object
     * @static
     */
    static protected $_mode = null;
    
    /** 
     * Constructor 
     * 
     * @param string $mode the mode of operation: live or test
     *
     * @return void 
     */ 
    function __construct( $mode, &$paymentProcessor ) {
        $this->_mode = $mode;

        $this->_paymentProcessor = $paymentProcessor;
    }

    /**  
     * The function gets called when a new order takes place.
     *  
     * @param xml   $dataRoot    response send by google in xml format
     * @param array $privateData contains the name value pair of <merchant-private-data>
     *  
     * @return void  
     *  
     */  
    function newOrderNotify( $dataRoot, $privateData, $component ) {
        $component          = strtolower($component);

        $contactID          = $privateData['contactID'];
        $contributionID     = $privateData['contributionID'];

        if ( $component == "contribute" ) {
            $membershipTypeID   = $privateData['membershipTypeID'];
        } elseif ( $component == "event" ) {
            $eventID            = $privateData['eventID'];
        }

        // make sure contact exists and is valid
        require_once 'CRM/Contact/DAO/Contact.php';
        $contact =& new CRM_Contact_DAO_Contact( );
        $contact->id = $contactID;
        if ( ! $contact->find( true ) ) {
            CRM_Core_Error::debug_log_message( "Could not find contact record: $contactID" );
            echo "Failure: Could not find contact record: $contactID<p>";
            return;
        }

        // make sure contribution exists and is valid
        require_once 'CRM/Contribute/DAO/Contribution.php';
        $contribution =& new CRM_Contribute_DAO_Contribution( );
        $contribution->id = $contributionID;
        if ( ! $contribution->find( true ) ) {
            CRM_Core_Error::debug_log_message( "Could not find contribution record: $contributionID" );
            echo "Failure: Could not find contribution record for $contributionID<p>";
            return;
        }
        
        if ( $component == "event" ) {
            // make sure event exists and is valid
            require_once 'CRM/Event/DAO/Event.php';
            $event =& new CRM_Event_DAO_Event( );
            $event->id = $eventID;
            if ( ! $event->find( true ) ) {
                CRM_Core_Error::debug_log_message( "Could not find event: $eventID" );
                echo "Failure: Could not find event: $eventID<p>";
                return;
            }
        }

        // make sure the invoice is valid and matches what we have in the contribution record
        $invoice = $privateData['invoiceID'];
        if ( $contribution->invoice_id != $invoice ) {
            CRM_Core_Error::debug_log_message( "Invoice values dont match between database and IPN request" );
            echo "Failure: Invoice values dont match between database and IPN request<p>";
            return;
        } else {
            // lets replace invoice-id with google-order-number because thats what is common and unique 
            // in subsequent calls or notifications send by google.
            $contribution->invoice_id = $dataRoot['google-order-number']['VALUE'];
        }

        $now    = date( 'YmdHis' );
        $amount =  $dataRoot['order-total']['VALUE'];
        
        if ( $contribution->total_amount != $amount ) {
            CRM_Core_Error::debug_log_message( "Amount values dont match between database and IPN request" );
            echo "Failure: Amount values dont match between database and IPN request<p>";
            return;
        }

        // ok we are done with error checking, now let the real work begin
        // update the contact record with the name and address
        $params = array( );
        $lookup = array( 'first_name'     => 'contact-name',
                         // "last-name" not available with google (every thing in contact-name)
                         'last_name'      => 'last_name' , 
                         'street_address' => 'address1',
                         'city'           => 'city',
                         'state'          => 'region',
                         'postal_code'    => 'postal-code',
                         'country'        => 'country-code' );
        foreach ( $lookup as $name => $googleName ) {
            $value = $dataRoot['buyer-billing-address'][$googleName]['VALUE'];
            if ( $value ) {
                $params[$name] = $value;
            } else {
                $params[$name] = null;
            }
        }

        if ( ! empty( $params ) ) {
            // update contact record
            $idParams = array( 'id'         => $contactID, 
                               'contact'    => $contactID );
            $ids = $defaults = array( );
            require_once "CRM/Contact/BAO/Contact.php";
            CRM_Contact_BAO_Contact::retrieve( $idParams, $defaults, $ids );
            $contact = CRM_Contact_BAO_Contact::createFlat($params, $ids );
        }
        
        // lets keep this the same
        $contribution->receive_date = CRM_Utils_Date::isoToMysql($contribution->receive_date); 
        
        // check if contribution is already completed, if so we ignore this ipn
        if ( $contribution->contribution_status_id == 1 ) {
            CRM_Core_Error::debug_log_message( "returning since contribution has already been handled" );
            echo "Success: Contribution has already been handled<p>";
            return;
        }

        /* Since trxn_id hasn't got any use here, 
         lets make use of it by passing the eventID/membershipTypeID to next level.
         And change trxn_id to google-order-number before finishing db update */
        if ( $eventID ) {
            $contribution->trxn_id = "eid" . $eventID;
        }
        if ( $membershipTypeID ) {
            $contribution->trxn_id = "mid" . $membershipTypeID;
        }

        $contribution->save( );
    }
    
    /**  
     * The function gets called when the state(CHARGED, CANCELLED..) changes for an order
     *  
     * @param string $status      status of the transaction send by google
     * @param array  $privateData contains the name value pair of <merchant-private-data>
     *  
     * @return void  
     *  
     */  
    function orderStateChange( $status, $dataRoot, $component ) {
        $component = strtolower($component);

        $orderNo   = $dataRoot['google-order-number']['VALUE'];
        
        require_once 'CRM/Contribute/DAO/Contribution.php';
        $contribution =& new CRM_Contribute_DAO_Contribution( );
        $contribution->invoice_id = $orderNo;
        if ( ! $contribution->find( true ) ) {
            CRM_Core_Error::debug_log_message( "Could not find contribution record with invoice id: $orderNo" );
            echo "Failure: Could not find contribution record with invoice id: $orderNo <p>";
            return;
        }
        
        if ( $status == 'PAYMENT_DECLINED' || 
             $status == 'CANCELLED_BY_GOOGLE' || 
             $status == 'CANCELLED' ) {        

            $contribution->contribution_status_id = 4;
            $contribution->trxn_id = $orderNo;
            $contribution->save( );
            CRM_Core_DAO::transaction( 'COMMIT' );
            CRM_Core_Error::debug_log_message( "Setting contribution status to failed" );
            echo "Success: Setting contribution status to failed<p>";
            return;
        }

        require_once 'CRM/Contribute/DAO/ContributionType.php';
        $contributionType =& new CRM_Contribute_DAO_ContributionType( );
        $contributionType->id = $contribution->contribution_type_id;
        if ( ! $contributionType->find( true ) ) {
            CRM_Core_Error::debug_log_message( "Could not find contribution type record: $contributionTypeID" );
            echo "Failure: Could not find contribution type record for $contributionTypeID<p>";
            return;
        }

        if ( $component == "contribute" ) {
            // get the payment processor id from contribution page
            $paymentProcessorID = 
                CRM_Core_DAO::getFieldValue( 'CRM_Contribute_DAO_ContributionPage',
                                             $contribution->contribution_page_id,
                                             'payment_processor_id' );
            
            if ( ! $paymentProcessorID ) {
                CRM_Core_Error::debug_log_message( "Could not find payment processor for contribution record: $contributionID" );
                echo "Failure: Could not find payment processor for contribution record: $contributionID<p>";
                return;
            }
            $isTest = self::retrieve( 'test_ipn'     , 'Integer', 'POST', false );
        }
        
        // lets start since payment has been made
        $now       = date( 'YmdHis' );
        $contactID = $contribution->contact_id;
        $amount    = $contribution->total_amount;
        $eventID   = $contribution->trxn_id; 

        if ( $component == "event" ) {
            $eventID     = (int)str_replace('eid', "", $eventID);
            
            $eventParams = array( 'id' => $eventID );
            require_once 'CRM/Event/BAO/Event.php';
            CRM_Event_BAO_Event::retrieve( $eventParams, $values['event'] );
            
            $eventParams = array( 'event_id' => $eventID );
            require_once 'CRM/Event/BAO/EventPage.php';
            CRM_Event_BAO_EventPage::retrieve( $eventParams, $values['page'] );
            
            $contribution->source = 
                ts( 'Online Event Registration:' ) . ' ' . $values['event']['title'];
            
        } elseif ( $component == "contribute" ) {
            require_once 'CRM/Contribute/BAO/ContributionPage.php';
            CRM_Contribute_BAO_ContributionPage::setValues( $contribution->contribution_page_id, $values );
        }

        $contribution->receive_date           = 
            CRM_Utils_Date::isoToMysql($contribution->receive_date); 

        $contribution->contribution_status_id = 1;
        $contribution->fee_amount             = $dataRoot['fee_amount']['VALUE']; //not available
        $contribution->net_amount             = $dataRoot['net_amount']['VALUE']; //not available
        
        // storing google-order-no
        $contribution->trxn_id                = $dataRoot['google-order-number']['VALUE']; 

        if ( $values['page']['is_email_confirm'] || $values['is_email_receipt'] ) {
            $contribution->receipt_date = $now;
        }

        CRM_Core_DAO::transaction( 'BEGIN' );
        
        $contribution->save( );
        
        require_once 'CRM/Core/Config.php';
        $config =& CRM_Core_Config::singleton( );
        
        // next create the transaction record
        $trxnParams = array(
                            'entity_table'      => 'civicrm_contribution',
                            'entity_id'         => $contribution->id,
                            'trxn_date'         => $now,
                            'trxn_type'         => 'Debit',
                            'total_amount'      => $amount,
                            'fee_amount'        => $contribution->fee_amount,
                            'net_amount'        => $contribution->net_amount,
                            'currency'          => $contribution->currency,
                            'payment_processor' => 'Google_Checkout',
                            'trxn_id'           => $contribution->trxn_id,
                            );
        
        require_once 'CRM/Contribute/BAO/FinancialTrxn.php';
        $trxn =& CRM_Contribute_BAO_FinancialTrxn::create( $trxnParams );

        if ( $component == "event" ) {
            //create participant record
            require_once 'CRM/Event/BAO/Participant.php';
            
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
                                       'event_id'      => $eventID,
                                       'status_id'     => 1,
                                       'role_id'       => $roleID,
                                       'register_date' => $now,
                                       'source'        => ts( 'Online Event Registration:' ) . ' ' . $values['event']['title'],
                                       'event_level'   => $contribution->amount_level,
                                       'is_test'       => $contribution->is_test
                                       );
            
            $ids = array();
            $participant = CRM_Event_BAO_Participant::add($participantParams, $ids);
            
            // also create an activity history record
            CRM_Event_BAO_Participant::setActivityHistory( $participant );
            
        } elseif ( $component == "contribute" ) {
            // get the title of the contribution page
            $title = CRM_Core_DAO::getFieldValue( 'CRM_Contribute_DAO_ContributionPage',
                                                  $contribution->contribution_page_id,
                                                  'title' );
        
            require_once 'CRM/Utils/Money.php';
            $formattedAmount = CRM_Utils_Money::format($amount);
            
            // also create an activity history record
            $ahParams = array('entity_table'     => 'civicrm_contact', 
                              'entity_id'        => $contactID,
                              'activity_type'    => $contributionType->name,
                              'module'           => 'CiviContribute', 
                              'callback'         => 'CRM_Contribute_Page_Contribution::details',
                              'activity_id'      => $contribution->id, 
                              'activity_summary' => "$formattedAmount - $title (online)",
                              'activity_date'    => $now,
                              );
        
            require_once 'api/History.php';
            if ( is_a( crm_create_activity_history($ahParams), 'CRM_Core_Error' ) ) { 
                CRM_Core_Error::debug_log_message( "error in updating activity" );
            }
        
            // create membership record
            if ($membershipTypeID) {
                $template =& CRM_Core_Smarty::singleton( );
                $template->assign('membership_assign' , true );
                
                require_once 'CRM/Member/BAO/Membership.php';
                require_once 'CRM/Member/DAO/MembershipLog.php';
                require_once 'CRM/Member/BAO/MembershipType.php';
                $membershipDetails = 
                    CRM_Member_BAO_MembershipType::getMembershipTypeDetails( $membershipTypeID );
                $template->assign('membership_name',$membershipDetails['name']);
                
                $minimumFee = $membershipDetails['minimum_fee'];
                $template->assign('membership_amount'  , $minimumFee);
                
                if ($currentMembership = 
                    CRM_Member_BAO_Membership::getContactMembership($contactID,  $membershipTypeID)) {
                    if ( ! $currentMembership['is_current_member'] ) {
                        $dao   =& new CRM_Member_DAO_Membership();
                        $dates = 
                            CRM_Member_BAO_MembershipType::getRenewalDatesForMembershipType( $currentMembership['id']);
                        $currentMembership['start_date'] = 
                            CRM_Utils_Date::customFormat($dates['start_date'],'%Y%m%d');
                        $currentMembership['end_date']   = 
                            CRM_Utils_Date::customFormat($dates['end_date'],'%Y%m%d');
                        $currentMembership['source']     = $contribution->source;

                        $dao->copyValues($currentMembership);
                        $membership = $dao->save();
                        
                        //insert log here 
                        $dao = new CRM_Member_DAO_MembershipLog();
                        $dao->membership_id = $membership->id;
                        $dao->status_id     = $membership->status_id;
                        $dao->start_date    = 
                            CRM_Utils_Date::customFormat($dates['start_date'],'%Y%m%d');
                        $dao->end_date      = 
                            CRM_Utils_Date::customFormat($dates['end_date'],'%Y%m%d'); 
                        $dao->modified_id   = $contactID;
                        $dao->modified_date = date('Ymd');
                        $dao->save();
                    
                        $template->assign('mem_start_date', 
                                          CRM_Utils_Date::customFormat($dates['start_date'],'%Y%m%d'));
                        $template->assign('mem_end_date',   
                                          CRM_Utils_Date::customFormat($dates['end_date'],'%Y%m%d'));
                    } else {
                        $dao =& new CRM_Member_DAO_Membership();
                        $dao->id = $currentMembership['id'];
                        $dao->find(true); 
                        $membership = $dao ;
                        
                        //insert log here 
                        $dates = CRM_Member_BAO_MembershipType::getRenewalDatesForMembershipType( $membership->id);
                        $dao                = new CRM_Member_DAO_MembershipLog();
                        $dao->membership_id = $membership->id;
                        $dao->status_id     = $membership->status_id;
                        $dao->start_date    = 
                            CRM_Utils_Date::customFormat($dates['log_start_date'],'%Y%m%d');
                        $dao->end_date      = 
                            CRM_Utils_Date::customFormat($dates['end_date'],'%Y%m%d'); 
                        $dao->modified_id   = $contactID;
                        $dao->modified_date = date('Ymd');
                        $dao->save();
                        
                        $template->assign('mem_start_date', 
                                          CRM_Utils_Date::customFormat($dates['start_date'],'%Y%m%d'));
                        $template->assign('mem_end_date',   
                                          CRM_Utils_Date::customFormat($dates['end_date'],'%Y%m%d'));
                    }
                } else {
                    require_once 'CRM/Member/BAO/MembershipStatus.php';
                    $memParams                       = array();
                    $memParams['contact_id']         = $contactID;
                    $memParams['membership_type_id'] = $membershipTypeID;
                    $dates = 
                        CRM_Member_BAO_MembershipType::getDatesForMembershipType($membershipTypeID);
                    
                    $memParams['join_date']     = 
                        CRM_Utils_Date::customFormat($dates['join_date'],'%Y%m%d');
                    $memParams['start_date']    = 
                        CRM_Utils_Date::customFormat($dates['start_date'],'%Y%m%d');
                    $memParams['end_date']      = 
                        CRM_Utils_Date::customFormat($dates['end_date'],'%Y%m%d');
                    $memParams['reminder_date'] = 
                        CRM_Utils_Date::customFormat($dates['reminder_date'],'%Y%m%d'); 
                    $memParams['source'  ]      = $contribution->source;

                    $status = 
                        CRM_Member_BAO_MembershipStatus::getMembershipStatusByDate( CRM_Utils_Date::customFormat($dates['start_date'],'%Y-%m-%d'),CRM_Utils_Date::customFormat($dates['end_date'],'%Y-%m-%d'),CRM_Utils_Date::customFormat($dates['join_date'],'%Y-%m-%d')) ;
                    
                    $memParams['status_id']   = $status['id'];
                    $memParams['is_override'] = false;

                    $dao =& new CRM_Member_DAO_Membership();
                    $dao->copyValues($memParams);

                    $membership = $dao->save();
                    $template->assign('mem_start_date',  
                                      CRM_Utils_Date::customFormat($dates['start_date'],'%Y%m%d'));
                    $template->assign('mem_end_date', 
                                      CRM_Utils_Date::customFormat($dates['end_date'],'%Y%m%d'));
                }

                require_once 'CRM/Member/DAO/MembershipBlock.php';
                $dao =& new CRM_Member_DAO_MembershipBlock();
                $dao->entity_table = 'civicrm_contribution_page';
                $dao->entity_id = $contribution->contribution_page_id; 
                $dao->is_active = 1;
                if ( $dao->find(true) ) {
                    $membershipBlock   = array(); 
                    CRM_Core_DAO::storeValues($dao, $membershipBlock );
                    $template->assign( 'membershipBlock' , $membershipBlock );
                }
            }
        } 
        
        CRM_Core_Error::debug_log_message( "Contribution record updated successfully" );
        CRM_Core_DAO::transaction( 'COMMIT' );

        // add the new contribution values
        $template =& CRM_Core_Smarty::singleton( );
        $template->assign( 'amount' , $amount );
        $template->assign( 'trxn_id', $contribution->trxn_id );
        $template->assign( 'receive_date', 
                           CRM_Utils_Date::mysqlToIso( $contribution->receive_date ) );
        $template->assign( 'contributeMode', 'notify' );
        $template->assign( 'action', $contribution->is_test ? 1024 : 1 );
        $template->assign( 'receipt_text', $values['receipt_text'] );
        $template->assign( 'is_monetary', 1 );

        require_once 'CRM/Utils/Address.php';
        $template->assign( 'address', CRM_Utils_Address::format( $params ) );
        
        if ( $component == "event" ) {
            $template->assign( 'title', $values['event']['title']);

            require_once "CRM/Event/BAO/EventPage.php";
            CRM_Event_BAO_EventPage::sendMail( $contactID, $values['page'] );
            
        } elseif ( $component == "contribute" ) {
            $template->assign( 'title',   $values['title']);
            
            require_once 'CRM/Contribute/BAO/ContributionPage.php';
            CRM_Contribute_BAO_ContributionPage::sendMail( $contactID, $values );
        }

        echo "Success: Database updated<p>";
    }

    /**  
     * singleton function used to manage this object  
     *  
     * @param string $mode the mode of operation: live or test
     *  
     * @return object  
     * @static  
     */  
    static function &singleton( $mode, $component, &$paymentProcessor ) {
        if ( self::$_singleton === null ) {
            $config       =& CRM_Core_Config::singleton( );
            $paymentClass = "CRM_{$component}_" . $paymentProcessor['class_name'] . "IPN";
            
            $classPath = str_replace( '_', '/', $paymentClass ) . '.php';
            require_once($classPath);
            self::$_singleton = eval( 'return ' . $paymentClass . '::singleton( $mode, $paymentProcessor );' );
        }
        return self::$_singleton;
    }
    
    /**  
     * The function retrieves the amount the contribution is for, based on the order-no google sends
     *  
     * @param int $orderNo <order-total> send by google
     *  
     * @return amount  
     * @access public 
     */  
    function getAmount($orderNo) {
        require_once 'CRM/Contribute/DAO/Contribution.php';
        $contribution =& new CRM_Contribute_DAO_Contribution( );
        $contribution->invoice_id = $orderNo;
        if ( ! $contribution->find( true ) ) {
            CRM_Core_Error::debug_log_message( "Could not find contribution record with invoice id: $orderNo" );
            echo "Failure: Could not find contribution record with invoice id: $orderNo <p>";
            return;
        }
        return $contribution->total_amount;
    }

    /**  
     * The function returns the component(Event/Contribute..), given the google-order-no and merchant-private-data
     *  
     * @param xml     $xml_response   response send by google in xml format
     * @param array   $privateData    contains the name value pair of <merchant-private-data>
     * @param int     $orderNo        <order-total> send by google
     * @param string  $root           root of xml-response
     *  
     * @return array context of this call (test, module, payment processor id)
     * @static  
     */  
    static function getContext($xml_response, $privateData, $orderNo, $root) {
        require_once 'CRM/Contribute/DAO/Contribution.php';

        $isTest = null;
        $module = null;
        if ($root == 'new-order-notification') {
            $contributionID   = $privateData['contributionID'];
            $contribution     =& new CRM_Contribute_DAO_Contribution( );
            $contribution->id = $contributionID;
            if ( ! $contribution->find( true ) ) {
                CRM_Core_Error::debug_log_message( "Could not find contribution record: $contributionID" );
                echo "Failure: Could not find contribution record for $contributionID<p>";
                exit( );
            }
            if (stristr($contribution->source, 'Online Contribution')) {
                $module = 'Contribute';
            } elseif (stristr($contribution->source, 'Online Event Registration')) {
                $module = 'Event';
            }
            $isTest = $contribution->is_test;
        } else {
            $contribution =& new CRM_Contribute_DAO_Contribution( );
            $contribution->invoice_id = $orderNo;
            if ( ! $contribution->find( true ) ) {
                CRM_Core_Error::debug_log_message( "Could not find contribution record with invoice id: $orderNo" );
                echo "Failure: Could not find contribution record with invoice id: $orderNo <p>";
                exit( );
            }
            if (stristr($contribution->source, 'Online Contribution')) {
                $module = 'Contribute';
            } elseif (stristr($contribution->source, 'Online Event Registration')) {
                $module = 'Event';
            }
            $isTest = $contribution->is_test;
        }

        if ( $module == 'Contribute' ) {
            if ( ! $contribution->contribution_page_id ) {
                CRM_Core_Error::debug_log_message( "Could not find contribution page for contribution record: $contributionID" );
                echo "Failure: Could not find contribution page for contribution record: $contributionID<p>";
                exit( );
            }
            // get the payment processor id from contribution page
            $paymentProcessorID = CRM_Core_DAO::getFieldValue( 'CRM_Contribute_DAO_ContributionPage',
                                                               $contribution->contribution_page_id,
                                                               'payment_processor_id' );
        } else {
            if ( empty( $privateData['eventID'] ) ) {
                CRM_Core_Error::debug_log_message( "Could not find event ID" );
                echo "Failure: Could not find eventID<p>";
                exit( );
            }

            // we are in event mode
            // make sure event exists and is valid
            require_once 'CRM/Event/DAO/Event.php';
            $event =& new CRM_Event_DAO_Event( );
            $event->id = $privateData['eventID'];
            if ( ! $event->find( true ) ) {
                CRM_Core_Error::debug_log_message( "Could not find event: $eventID" );
                echo "Failure: Could not find event: $eventID<p>";
                exit( );
            }
            
            // get the payment processor id from contribution page
            $paymentProcessorID = $event->payment_processor_id;
        }

        if ( ! $paymentProcessorID ) {
            CRM_Core_Error::debug_log_message( "Could not find payment processor for contribution record: $contributionID" );
            echo "Failure: Could not find payment processor for contribution record: $contributionID<p>";
            exit( );
        }

        return array( $isTest, $module, $paymentProcessorID );
    }

    /**
     * This method is handles the response that will be invoked (from extern/googleNotify) every time
     * a notification or request is sent by the Google Server.
     *
     */
    static function main( ) 
    {
        require_once('Google/library/googleresponse.php');
        require_once('Google/library/googlemerchantcalculations.php');
        require_once('Google/library/googleresult.php');
        require_once('Google/library/xml-processing/xmlparser.php');
        
        //require_once('CRM/Core/Payment/GoogleIPN.php');

        $config =& CRM_Core_Config::singleton();
        define('RESPONSE_HANDLER_LOG_FILE', $config->uploadDir . 'CiviCRM.log');
        
        //Setup the log file
        if (!$message_log = fopen(RESPONSE_HANDLER_LOG_FILE, "a")) {
            error_func("Cannot open " . RESPONSE_HANDLER_LOG_FILE . " file.\n", 0);
            exit(1);
        }
        
        // Retrieve the XML sent in the HTTP POST request to the ResponseHandler
        $xml_response = $HTTP_RAW_POST_DATA;
        if (get_magic_quotes_gpc()) {
            $xml_response = stripslashes($xml_response);
        }
        $headers = getallheaders();
        fwrite($message_log, sprintf("\n\r%s:- %s\n",date("D M j G:i:s T Y"),
                                     $xml_response));
        
        // Retrieve the root and data from the xml response
        $xmlParser = new XmlParser($xml_response);
        $root      = $xmlParser->GetRoot();
        $data      = $xmlParser->GetData();
        
        $orderNo   = $data[$root]['google-order-number']['VALUE'];
        
        // lets retrieve the private-data
        $privateData = $data[$root]['shopping-cart']['merchant-private-data']['VALUE'];
        $privateData = $privateData ? self::stringToArray($privateData) : '';
        
        list( $mode, $module, $paymentProcessorID ) = self::getContext($xml_response, $privateData, $orderNo, $root);
        $mode   = $mode ? 'test' : 'live';
        
        require_once 'CRM/Core/BAO/PaymentProcessor.php';
        $paymentProcessor = CRM_Core_BAO_PaymentProcessor::getPayment( $paymentProcessorID,
                                                                       $mode );
        
        $ipn    =& self::singleton( $mode, $module, $paymentProcessor );
        
        // Create new response object
        $merchant_id  = $paymentProcessor['user_name'];
        $merchant_key = $paymentProcessor['password'];
        $server_type  = ($mode == 'test') ? "sandbox" : '';
        
        $response = new GoogleResponse($merchant_id, $merchant_key,
                                       $xml_response, $server_type);
        fwrite($message_log, sprintf("\n\r%s:- %s\n",date("D M j G:i:s T Y"),
                                     $response->root));
        
        //Check status and take appropriate action
        $status = $response->HttpAuthentication($headers);
        
        switch ($root) {
        case "request-received": {
            break;
        }
        case "error": {
            break;
        }
        case "diagnosis": {
            break;
        }
        case "checkout-redirect": {
            break;
        }
        case "merchant-calculation-callback": {
            break;
        }
        case "new-order-notification": {
            $response->SendAck();
            $ipn->newOrderNotify($data[$root], $privateData);
            break;
        }
        case "order-state-change-notification": {
            $response->SendAck();
            $new_financial_state = $data[$root]['new-financial-order-state']['VALUE'];
            $new_fulfillment_order = $data[$root]['new-fulfillment-order-state']['VALUE'];
            
            switch($new_financial_state) {
            case 'REVIEWING': {
                break;
            }
            case 'CHARGEABLE': {
                $amount = $ipn->getAmount($orderNo);
                if ($amount) {
                    $response->SendChargeOrder($data[$root]['google-order-number']['VALUE'], 
                                               $amount, $message_log);
                    $response->SendProcessOrder($data[$root]['google-order-number']['VALUE'], 
                                                $message_log);
                }
                break;
            }
            case 'CHARGING': {
                break;
            }
            case 'CHARGED': {
                $ipn->orderStateChange('CHARGED', $data[$root]);
                break;
            }
            case 'PAYMENT_DECLINED': {
                $ipn->orderStateChange('PAYMENT_DECLINED', $data[$root]);
                break;
            }
            case 'CANCELLED': {
                $ipn->orderStateChange('CANCELLED', $data[$root]);
                break;
            }
            case 'CANCELLED_BY_GOOGLE': {
                //$response->SendBuyerMessage($data[$root]['google-order-number']['VALUE'],
                //    "Sorry, your order is cancelled by Google", true, $message_log);
                break;
            }
            default:
                break;
            }
            
            switch($new_fulfillment_order) {
            case 'NEW': {
                break;
            }
            case 'PROCESSING': {
                break;
            }
            case 'DELIVERED': {
                break;
            }
            case 'WILL_NOT_DELIVER': {
                break;
            }
            default:
                break;
            }
        }
        case "charge-amount-notification": {
            $response->SendAck();
            //      $response->SendDeliverOrder($data[$root]['google-order-number']['VALUE'], 
            //                                  <carrier>, <tracking-number>, <send-email>, $message_log);
            //      $response->SendArchiveOrder($data[$root]['google-order-number']['VALUE'], 
            //                                  $message_log);
            break;
        }
        case "chargeback-amount-notification": {
            $response->SendAck();
            break;
        }
        case "refund-amount-notification": {
            $response->SendAck();
            break;
        }
        case "risk-information-notification": {
            $response->SendAck();
            break;
        }
        default: {
            break;
        }
        }
    }


    /**
     * In case the XML API contains multiple open tags
     * with the same value, then invoke this function and
     * perform a foreach on the resultant array.
     * This takes care of cases when there is only one unique tag
     * or multiple tags.
     * Examples of this are "anonymous-address", "merchant-code-string"
     * from the merchant-calculations-callback API
     */
    static function get_arr_result($child_node) {
        $result = array();
        if(isset($child_node)) {
            if(self::is_associative_array($child_node)) {
                $result[] = $child_node;
            }
            else {
                foreach($child_node as $curr_node){
                    $result[] = $curr_node;
                }
            }
        }
        return $result;
    }
    
    /**
     * Returns true if a given variable represents an associative array 
     */
    static function is_associative_array( $var ) {
        return is_array( $var ) && !is_numeric( implode( '', array_keys( $var ) ) );
    }
    
    /**
     * Converts the comma separated name-value pairs in <merchant-private-data> 
     * to an array of name-value pairs.
     */
    static function stringToArray($str) {
        $vars = $labels = array();
        $labels = explode(',', $str);
        foreach ($labels as $label) {
            $terms = explode('=', $label);
            $vars[$terms[0]] = $terms[1];
        }
        return $vars;
    }
}

?>
