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

class CRM_Core_Payment_BaseIPN {

    static $_now = null;

    function __construct( ) {
        self::$_now = date( 'YmdHis' );
    }

    function validateData( &$input, &$ids, &$objects, $required = true ) {

        // make sure contact exists and is valid
        require_once 'CRM/Contact/DAO/Contact.php';
        $contact =& new CRM_Contact_DAO_Contact( );
        $contact->id = $ids['contact'];
        if ( ! $contact->find( true ) ) {
            CRM_Core_Error::debug_log_message( "Could not find contact record: $contactID" );
            echo "Failure: Could not find contact record: $contactID<p>";
            return false;
        }

        // make sure contribution exists and is valid
        require_once 'CRM/Contribute/DAO/Contribution.php';
        $contribution =& new CRM_Contribute_DAO_Contribution( );
        $contribution->id = $ids['contribution'];
        if ( ! $contribution->find( true ) ) {
            CRM_Core_Error::debug_log_message( "Could not find contribution record: $contributionID" );
            echo "Failure: Could not find contribution record for $contributionID<p>";
            return false;
        }
        $contribution->receive_date = CRM_Utils_Date::isoToMysql($contribution->receive_date); 

        $objects['contact']          =& $contact;
        $objects['contribution']     =& $contribution;
        if ( ! $this->loadObjects( $input, $ids, $objects, $required ) ) {
            return false;
        }

        return true;
    }

    function createContact( &$input, &$ids, &$objects ) {
        $params    = array( );
        $billingID = $ids['billing'];
        $lookup    = array( "first_name"                  ,
                            "last_name"                   ,
                            "street_address-{$billingID}" ,
                            "city-{$billingID}"           ,
                            "state-{$billingID}"          ,
                            "postal_code-{$billingID}"    ,
                            "country-{$billingID}"        , );
        foreach ( $lookup as $name ) {
            $params[$name] = $input[$name];
        }
        if ( ! empty( $params ) ) {
            // update contact record
            require_once "CRM/Contact/BAO/Contact.php";
            $contact =& CRM_Contact_BAO_Contact::createProfileContact( $params, CRM_Core_DAO::$_nullArray, $ids['contact'] );
        }
        
        return true;
    }

    function loadObjects( &$input, &$ids, &$objects, $required ) {
        $contribution =& $objects['contribution'];
     
        $objects['membership']        = null;
        $objects['contributionRecur'] = null;
        $objects['contributionType']  = null;
        $objects['event']             = null;
        $objects['participant']       = null;
        $objects['pledge_payment']    = null;

        require_once 'CRM/Contribute/DAO/ContributionType.php';
        $contributionType =& new CRM_Contribute_DAO_ContributionType( );
        $contributionType->id = $contribution->contribution_type_id;
        if ( ! $contributionType->find( true ) ) {
            CRM_Core_Error::debug_log_message( "Could not find contribution type record: $contributionTypeID" );
            echo "Failure: Could not find contribution type record for $contributionTypeID<p>";
            return false;
        }
        $objects['contributionType'] = $contributionType;

        if ( $input['component'] == 'contribute' ) {
            // get the contribution page id from the contribution
            // and then initialize the payment processor from it
            if ( ! $contribution->contribution_page_id ) {
                if ( !CRM_Utils_Array::value( 'pledge_payment', $ids ) ) {
                // return if we are just doing an optional validation
                if ( ! $required ) {
                    return true;
                }
                
                CRM_Core_Error::debug_log_message( "Could not find contribution page for contribution record: $contributionID" );
                echo "Failure: Could not find contribution page for contribution record: $contributionID<p>";
                return false;
                }
            }
            //for offline pldedge we dont have contribution page.
            if ( !CRM_Utils_Array::value( 'pledge_payment', $ids ) ) {
                // get the payment processor id from contribution page
                $paymentProcessorID = CRM_Core_DAO::getFieldValue( 'CRM_Contribute_DAO_ContributionPage',
                                                                   $contribution->contribution_page_id,
                                                                   'payment_processor_id' );
            }
                        
            // now retrieve the other optional objects
            if ( isset( $ids['membership'] ) ) {
                require_once 'CRM/Member/DAO/Membership.php';
                $membership = new CRM_Member_DAO_Membership( );
                $membership->id = $ids['membership'];
                if ( ! $membership->find( true ) ) {
                    CRM_Core_Error::debug_log_message( "Could not find membership record: $membershipID" );
                    echo "Failure: Could not find membership record: $membershipID<p>";
                    return false;
                }
                $membership->join_date     = CRM_Utils_Date::isoToMysql( $membership->join_date      );
                $membership->start_date    = CRM_Utils_Date::isoToMysql( $membership->start_date     );
                $membership->end_date      = CRM_Utils_Date::isoToMysql( $membership->end_date       );
                $membership->reminder_date = CRM_Utils_Date::isoToMysql( $membership->reminder_date  );

                $objects['membership'] =& $membership;
            }
                  
            if ( isset( $ids['pledge_payment'] ) ) {
                require_once 'CRM/Pledge/DAO/Payment.php';
                
                $objects['pledge_payment'] = array( );
                foreach ( $ids['pledge_payment'] as $key => $paymentID ) { 
                    $payment = new CRM_Pledge_DAO_Payment( );
                    $payment->id = $paymentID;
                    if ( ! $payment->find( true ) ) {
                        CRM_Core_Error::debug_log_message( "Could not find pledge payment record: $pledge_paymentID" );
                        echo "Failure: Could not find pledge payment record: $pledge_paymentID<p>";
                        return false;
                    } 
                    $objects['pledge_payment'][] = $payment;
                } 
            }
           
            if ( isset( $ids['contributionRecur'] ) ) {
                require_once 'CRM/Contribute/DAO/ContributionRecur.php';
                $recur =& new CRM_Contribute_DAO_ContributionRecur( );
                $recur->id = $ids['contributionRecur'];
                if ( ! $recur->find( true ) ) {
                    CRM_Core_Error::debug_log_message( "Could not find recur record: $contributionRecurID" );
                    echo "Failure: Could not find recur record: $contributionRecurID<p>";
                    return false;
                }
                $objects['contributionRecur'] =& $recur;
            }
        } else {
            // we are in event mode
            // make sure event exists and is valid
            require_once 'CRM/Event/DAO/Event.php';
            $event =& new CRM_Event_DAO_Event( );
            $event->id = $ids['event'];
            if ( $ids['event'] &&
                 ! $event->find( true ) ) {
                CRM_Core_Error::debug_log_message( "Could not find event: $eventID" );
                echo "Failure: Could not find event: $eventID<p>";
                return false;
            }

            $objects['event'] =& $event;

            require_once 'CRM/Event/DAO/Participant.php';
            $participant =& new CRM_Event_DAO_Participant( );
            $participant->id = $ids['participant'];
            if ( $ids['participant'] &&
                 ! $participant->find( true ) ) {
                CRM_Core_Error::debug_log_message( "Could not find participant: $participantID" );
                echo "Failure: Could not find participant: $participantID<p>";
                return false;
            }
            $participant->register_date = CRM_Utils_Date::isoToMysql( $participant->register_date );

            $objects['participant'] =& $participant;

            $paymentProcessorID = $objects['event']->payment_processor_id;
        }

        if ( ! $paymentProcessorID ) {
            if ( $required ) {
                CRM_Core_Error::debug_log_message( "Could not find payment processor for contribution record: $contributionID" );
                echo "Failure: Could not find payment processor for contribution record: $contributionID<p>";
                return false;
            }
        } else {
            require_once 'CRM/Core/BAO/PaymentProcessor.php';
            $paymentProcessor = CRM_Core_BAO_PaymentProcessor::getPayment( $paymentProcessorID,
                                                                           $contribution->is_test ? 'test' : 'live' );
            
            $ids['paymentProcessor']       =  $paymentProcessorID;
            $objects['paymentProcessor']   =& $paymentProcessor;
        }

        return true;
    }

    function failed( &$objects, &$transaction ) {
        $contribution =& $objects['contribution'];
        $membership   =& $objects['membership']  ;
        $participant  =& $objects['participant'] ;

        $contribution->contribution_status_id = 4;
        $contribution->save( );

        if ( $membership ) {
            $membership->status_id = 4;
            $membership->save( );
            
            //update related Memberships.
            require_once 'CRM/Member/BAO/Membership.php';
            $params = array( 'status_id' => 4 );
            CRM_Member_BAO_Membership::updateRelatedMemberships( $membership->id, $params );
        }

        if ( $participant ) {
            $participant->status_id = 4;
            $participant->save( );
        }
            
        $transaction->commit( );
        CRM_Core_Error::debug_log_message( "Setting contribution status to failed" );
        //echo "Success: Setting contribution status to failed<p>";
        return true;
    }

    function pending( &$objects, &$transaction ) {
        $transaction->commit( );
        CRM_Core_Error::debug_log_message( "returning since contribution status is pending" );
        echo "Success: Returning since contribution status is pending<p>";
        return true;
    }

    function cancelled( &$objects, &$transaction ) {
        $contribution =& $objects['contribution'];
        $membership   =& $objects['membership']  ;
        $participant  =& $objects['participant'] ;

        $contribution->contribution_status_id = 3;
        $contribution->cancel_date = self::$_now;
        $contribution->cancel_reason = CRM_Utils_Array::value( 'reasonCode', $input );
        $contribution->save( );

        if ( $membership ) {
            $membership->status_id = 6;
            $membership->save( );
            
            //update related Memberships.
            require_once 'CRM/Member/BAO/Membership.php';
            $params = array( 'status_id' => 6 );
            CRM_Member_BAO_Membership::updateRelatedMemberships( $membership->id, $params );
        }
        
        if ( $participant ) {
            $participant->status_id = 4;
            $participant->save( );
        }

        $transaction->commit( );
        CRM_Core_Error::debug_log_message( "Setting contribution status to cancelled" );
        //echo "Success: Setting contribution status to cancelled<p>";
        return true;
    }

    function unhandled( &$objects, &$transaction ) {
        $transaction->rollback( );
        // we dont handle this as yet
        CRM_Core_Error::debug_log_message( "returning since contribution status: $status is not handled" );
        echo "Failure: contribution status $status is not handled<p>";
        return false;
    }

    function completeTransaction( &$input, &$ids, &$objects, &$transaction, $recur = false ) {
        $contribution =& $objects['contribution'];
        $membership   =& $objects['membership']  ;
        $participant  =& $objects['participant'] ;
        $event        =& $objects['event']       ;
        
        $values = array( );
        if ( $input['component'] == 'contribute' ) {
            require_once 'CRM/Contribute/BAO/ContributionPage.php';
            CRM_Contribute_BAO_ContributionPage::setValues( $contribution->contribution_page_id, $values );
            $contribution->source                  = ts( 'Online Contribution' ) . ': ' . $values['title'];
            
            if ( $values['is_email_receipt'] ) {
                $contribution->receipt_date = self::$_now;
            }
            
            if ( $membership ) {
                $format       = '%Y%m%d';
                require_once 'CRM/Member/BAO/MembershipType.php';  
                $dates = CRM_Member_BAO_MembershipType::getDatesForMembershipType($membership->membership_type_id);
                
                //get the status for membership.
                require_once 'CRM/Member/BAO/MembershipStatus.php';
                $calcStatus = CRM_Member_BAO_MembershipStatus::getMembershipStatusByDate( $dates['start_date'], 
                                                                                          $dates['end_date'], 
                                                                                          $dates['join_date'],
                                                                                          'today', 
                                                                                          true );
                
                $formatedParams = array( 'status_id'     => CRM_Utils_Array::value( 'id', $calcStatus, 2 ),
                                         'join_date'     => CRM_Utils_Date::customFormat( $dates['join_date'],     $format ),
                                         'start_date'    => CRM_Utils_Date::customFormat( $dates['start_date'],    $format ),
                                         'end_date'      => CRM_Utils_Date::customFormat( $dates['end_date'],      $format ),
                                         'reminder_date' => CRM_Utils_Date::customFormat( $dates['reminder_date'], $format ) ); 
                
                $membership->copyValues( $formatedParams );
                $membership->save( );
                
                //update related Memberships.
                require_once 'CRM/Member/BAO/Membership.php';
                CRM_Member_BAO_Membership::updateRelatedMemberships( $membership->id, $formatedParams );
            }
        } else {
            // event
            $eventParams     = array( 'id' => $objects['event']->id );
            $values['event'] = array( );

            require_once 'CRM/Event/BAO/Event.php';
            CRM_Event_BAO_Event::retrieve( $eventParams, $values['event'] );
        
            $eventParams = array( 'id' => $objects['event']->id );
            $values['event'] = array( );

            require_once 'CRM/Event/BAO/Event.php';
            CRM_Event_BAO_Event::retrieve( $eventParams, $values['event'] );

            //get location details
            $locationParams = array( 'entity_id' => $objects['event']->id, 'entity_table' => 'civicrm_event' );
            require_once 'CRM/Core/BAO/Location.php';
            require_once 'CRM/Event/Form/ManageEvent/Location.php';
            CRM_Core_BAO_Location::getValues($locationParams, $values );

            require_once 'CRM/Core/BAO/UFJoin.php';
            $ufJoinParams = array( 'entity_table' => 'civicrm_event',
                                   'entity_id'    => $ids['event'],
                                   'weight'       => 1 );
        
            $values['custom_pre_id'] = CRM_Core_BAO_UFJoin::findUFGroupId( $ufJoinParams );
        
            $ufJoinParams['weight'] = 2;
            $values['custom_post_id'] = CRM_Core_BAO_UFJoin::findUFGroupId( $ufJoinParams );

            $contribution->source                  = ts( 'Online Event Registration' ) . ': ' . $values['event']['title'];

            if ( $values['event']['is_email_confirm'] ) {
                $contribution->receipt_date = self::$_now;
            }

            $participant->status_id = 1;
            $participant->save( );
        }
        if ( $input['net_amount'] == 0 && $input['fee_amount'] != 0 ) {
            $input['net_amount'] = $input['amount'] - $input['fee_amount'];
        }
        $contribution->contribution_status_id  = 1;
        $contribution->is_test      = $input['is_test'];
        $contribution->fee_amount   = $input['fee_amount'];
        $contribution->net_amount   = $input['net_amount'];
        $contribution->trxn_id      = $input['trxn_id'];
        $contribution->receive_date = CRM_Utils_Date::isoToMysql($contribution->receive_date); 
        $contribution->save( );

        // next create the transaction record
        if ( isset( $objects['paymentProcessor'] ) ) {
            $paymentProcessor = $objects['paymentProcessor']['payment_processor_type'];
        } else {
            $paymentProcessor = '';
        }
        $trxnParams = array(
                            'contribution_id'   => $contribution->id,
                            'trxn_date'         => isset( $input['trxn_date'] ) ? $input['trxn_date'] : self::$_now,
                            'trxn_type'         => 'Debit',
                            'total_amount'      => $input['amount'],
                            'fee_amount'        => $contribution->fee_amount,
                            'net_amount'        => $contribution->net_amount,
                            'currency'          => $contribution->currency,
                            'payment_processor' => $paymentProcessor,
                            'trxn_id'           => $contribution->trxn_id,
                            );
        
        require_once 'CRM/Contribute/BAO/FinancialTrxn.php';
        $trxn =& CRM_Contribute_BAO_FinancialTrxn::create( $trxnParams );
      
        //update corresponding pledge payment record
        require_once 'CRM/Core/DAO.php';
        $returnProperties = array( 'id', 'pledge_id' );
        if ( CRM_Core_DAO::commonRetrieveAll( 'CRM_Pledge_DAO_Payment', 'contribution_id', $contribution->id, 
                                              $paymentDetails, $returnProperties ) ) {
            $paymentIDs = array( );
            foreach ( $paymentDetails as $key => $value ) {
                $paymentIDs[] = $value['id'];
                $pledgeId     = $value['pledge_id'];
            }
            
            // update pledge and corresponding payment statuses
            require_once 'CRM/Pledge/BAO/Payment.php';
            CRM_Pledge_BAO_Payment::updatePledgePaymentStatus( $pledgeId, $paymentIDs, $contribution->contribution_status_id );
        }

         // create an activity record
        require_once "CRM/Activity/BAO/Activity.php";
        if ( $input['component'] == 'contribute' ) {
            CRM_Activity_BAO_Activity::addActivity( $contribution );
        } else { // event 
            CRM_Activity_BAO_Activity::addActivity( $participant );
        }

        CRM_Core_Error::debug_log_message( "Contribution record updated successfully" );
        $transaction->commit( );

        self::sendMail( $input, $ids, $objects, $values, $recur, false );

        CRM_Core_Error::debug_log_message( "Success: Database updated and mail sent" );
    }
    
    function getBillingID( &$ids ) {
        // get the billing location type
        require_once "CRM/Core/PseudoConstant.php";
        $locationTypes  =& CRM_Core_PseudoConstant::locationType( );
        $ids['billing'] =  array_search( 'Billing',  $locationTypes );
        if ( ! $ids['billing'] ) {
            CRM_Core_Error::debug_log_message( ts( 'Please set a location type of %1', array( 1 => 'Billing' ) ) );
            echo "Failure: Could not find billing location type<p>";
            return false;
        }
        return true;
    }

    function sendMail( &$input, &$ids, &$objects, &$values, $recur = false, $returnMessageText = false ) {
        $contribution =& $objects['contribution'];
        $membership   =& $objects['membership']  ;
        $participant  =& $objects['participant'] ;
        $event        =& $objects['event']       ;

        if ( empty( $values ) ) {
            $values = array( );
            if ( $input['component'] == 'contribute' ) {
                require_once 'CRM/Contribute/BAO/ContributionPage.php';
                if ( isset( $contribution->contribution_page_id ) ) {
                    CRM_Contribute_BAO_ContributionPage::setValues( $contribution->contribution_page_id, $values );
                } else {
                    // Handle re-print receipt for offline contributions (call from PDF.php - no contribution_page_id)
                    $values['is_email_receipt'] = 1;
                    $values['title']            = 'Contribution';
                }
            } else {
                // event
                $eventParams     = array( 'id' => $objects['event']->id );
                $values['event'] = array( );
                
                require_once 'CRM/Event/BAO/Event.php';
                CRM_Event_BAO_Event::retrieve( $eventParams, $values['event'] );
                
                $eventParams = array( 'id' => $objects['event']->id );
                $values['event'] = array( );
                
                require_once 'CRM/Event/BAO/Event.php';
                CRM_Event_BAO_Event::retrieve( $eventParams, $values['event'] );
                
                //get location details
                $locationParams = array( 'entity_id' => $objects['event']->id, 'entity_table' => 'civicrm_event' );
                require_once 'CRM/Core/BAO/Location.php';
                require_once 'CRM/Event/Form/ManageEvent/Location.php';
                CRM_Core_BAO_Location::getValues($locationParams, $values );
                
                require_once 'CRM/Core/BAO/UFJoin.php';
                $ufJoinParams = array( 'entity_table' => 'civicrm_event',
                                       'entity_id'    => $ids['event'],
                                       'weight'       => 1 );
                
                $values['custom_pre_id'] = CRM_Core_BAO_UFJoin::findUFGroupId( $ufJoinParams );
                
                $ufJoinParams['weight'] = 2;
                $values['custom_post_id'] = CRM_Core_BAO_UFJoin::findUFGroupId( $ufJoinParams );
            }
        }

        $template =& CRM_Core_Smarty::singleton( );
        // CRM_Core_Error::debug('tpl',$template);
        //assign honor infomation to receiptmessage
        if ( $honarID = CRM_Core_DAO::getFieldValue( 'CRM_Contribute_DAO_Contribution',
                                                     $contribution->id,
                                                     'honor_contact_id' ) ) {
            $honorDefault = array( );
            $honorIds     = array( );
            $honorIds['contribution'] =  $contribution->id;
            
            $idParams = array( 'id' => $honarID, 'contact_id' => $honarID );
            
            require_once "CRM/Contact/BAO/Contact.php";
            CRM_Contact_BAO_Contact::retrieve( $idParams, $honorDefault, $honorIds );
            
            require_once "CRM/Core/PseudoConstant.php";
            $honorType = CRM_Core_PseudoConstant::honor( );
            $prefix    = CRM_Core_PseudoConstant::individualPrefix();
            
            $template->assign( 'honor_block_is_active', 1 );
            $template->assign( 'honor_prefix',     $prefix[$honorDefault["prefix_id"]] );
            $template->assign( 'honor_first_name', CRM_Utils_Array::value( "first_name", $honorDefault ) );
            $template->assign( 'honor_last_name',  CRM_Utils_Array::value( "last_name", $honorDefault ) );
            $template->assign( 'honor_email',      CRM_Utils_Array::value( "email", $honorDefault["location"][1]["email"][1] ) );
            $template->assign( 'honor_type',       $honorType[$contribution->honor_type_id] );
        }

        require_once 'CRM/Contribute/DAO/ContributionProduct.php';
        $dao = & new CRM_Contribute_DAO_ContributionProduct();
        $dao->contribution_id =  $contribution->id;
        if ( $dao->find(true) ) {
            $premiumId = $dao->product_id;
            $template->assign('option',       $dao->product_option );
           
            require_once 'CRM/Contribute/DAO/Product.php';
            $productDAO =& new CRM_Contribute_DAO_Product();
            $productDAO->id = $premiumId;
            $productDAO->find(true);
            $template->assign('selectPremium', true );
            $template->assign('product_name',  $productDAO->name );
            $template->assign('price',         $productDAO->price );
            $template->assign('sku',           $productDAO->sku );
        }

        // add the new contribution values
        if ( $input['component'] == 'contribute' ) {
            $template->assign( 'title', $values['title']);
            $template->assign( 'amount' , $input['amount'] );
        } else {
            $template->assign( 'title', $values['event']['title']);
            $template->assign( 'totalAmount' , $input['amount'] );
        }

        $template->assign( 'trxn_id', $contribution->trxn_id );
        $template->assign( 'receive_date', 
                           CRM_Utils_Date::mysqlToIso( $contribution->receive_date ) );
        $template->assign( 'contributeMode', 'notify' );
        $template->assign( 'action', $contribution->is_test ? 1024 : 1 );
        $template->assign( 'receipt_text',
                           CRM_Utils_Array::value( 'receipt_text',
                                                   $values ) );
        $template->assign( 'is_monetary', 1 );
        $template->assign( 'is_recur', $recur );
        if ( $recur ) {
            require_once 'CRM/Core/Payment.php';
            $paymentObject =& CRM_Core_Payment::singleton( $contribution->is_test ? 'test' : 'live', 'Contribute',
                                                           $objects['paymentProcessor'] );
            $url = $paymentObject->cancelSubscriptionURL( );
            $template->assign( 'cancelSubscriptionUrl', $url );
        }
        
        require_once 'CRM/Utils/Address.php';
        $template->assign( 'address', CRM_Utils_Address::format( $input ) );

        if ( $input['component'] == 'event' ) { 
            require_once 'CRM/Core/OptionGroup.php';
            $participant_role = CRM_Core_OptionGroup::values('participant_role');
            $values['event']['participant_role'] = $participant_role[$participant->role_id];

            $template->assign( 'event', $values['event'] );
            $template->assign( 'location', $values['location'] );
            $template->assign( 'customPre', $values['custom_pre_id'] );
            $template->assign( 'customPost', $values['custom_post_id'] );
          
            $isTest = false;
            if ( $participant->is_test ) {
                $isTest = true;
            }
          
            require_once "CRM/Event/BAO/Event.php";
            //to get email of primary participant.
            $primaryEmail = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_Email',  $participant->contact_id, 'email', 'contact_id' );  
            $primaryAmount[$participant->fee_level.' - '.$primaryEmail] = $participant->fee_amount;
            //build an array of cId/pId of participants
            $additionalIDs = CRM_Event_BAO_Event::buildCustomProfile( $participant->id, null, $ids['contact'], $isTest, true );
            unset( $additionalIDs[$participant->id] ); 
            //send receipt to additional participant if exists
            if ( count($additionalIDs) ) {
                $template->assign( 'isPrimary', 0 ); 
                $template->assign( 'customProfile', null );
                foreach ( $additionalIDs as $pId => $cId ) {
                    $amount = array( );
                    //to change the status pending to completed
                    $additional = & new CRM_Event_DAO_Participant( );
                    $additional->id = $pId;
                    $additional->contact_id = $cId; 
                    $additional->find(true);
                    $additional->register_date = $participant->register_date;
                    $additional->status_id = 1;
                    $additionalEmail = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_Email',  $additional->contact_id, 'email', 'contact_id' );  
                    $amount[$additional->fee_level.' - '.$additionalEmail]        =  $additional->fee_amount;
                    $primaryAmount[$additional->fee_level.' - '.$additionalEmail] =  $additional->fee_amount; 
                    $additional->save( );
                    $additional->free( );
                    $template->assign( 'amount', $amount );
                    CRM_Event_BAO_Event::sendMail( $cId, $values, $pId, $isTest, $returnMessageText );
                } 
            }
            
            //build an array of custom profile and assigning it to template
            $customProfile = CRM_Event_BAO_Event::buildCustomProfile( $participant->id, $values, null, $isTest );
            
            if ( count($customProfile) ) {
                $template->assign( 'customProfile', $customProfile );
            }
            $template->assign( 'isPrimary', 1 );
            $template->assign( 'amount', $primaryAmount );
            return CRM_Event_BAO_Event::sendMail( $ids['contact'], $values, $participant->id, $isTest, $returnMessageText );
            
        } else {
            if ( $membership ) {
                $values['membership_id'] = $membership->id;

                // need to set the membership values here
                $template->assign( 'membership_assign', 1 );
                require_once 'CRM/Member/PseudoConstant.php';
                $template->assign( 'membership_name',
                                   CRM_Member_PseudoConstant::membershipType( $membership->membership_type_id ) );
                $template->assign( 'mem_start_date', $membership->start_date );
                $template->assign( 'mem_end_date'  , $membership->end_date );

                // if separate payment there are two contributions recorded and the 
                // admin will need to send a receipt for each of them separately.
                // we dont link the two in the db (but can potentially infer it if needed)
                $template->assign( 'is_separate_payment', 0);
            }
            $values['contribution_id']     = $contribution->id;
            if ( CRM_Utils_Array::value( 'related_contact', $ids ) ) {
                $values['related_contact'] = $ids['related_contact'];
                if ( isset($ids['onbehalf_dupe_alert']) ) {
                    $values['onbehalf_dupe_alert'] = $ids['onbehalf_dupe_alert'];
                }

                require_once 'CRM/Core/BAO/Address.php';
                $entityBlock = array( 'contact_id'       => $ids['contact'], 
                                      'location_type_id' => CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_LocationType', 
                                                                                         'Main', 'id', 'name' ) );
                $address = CRM_Core_BAO_Address::getValues( $entityBlock );
                $template->assign('onBehalfAddress', $address[$entityBlock['location_type_id']]['display']);
            }

            $isTest = false;
            if ( $contribution->is_test ) {
                $isTest = true;
            }
            // CRM_Core_Error::debug('val',$values);

            return CRM_Contribute_BAO_ContributionPage::sendMail( $ids['contact'], $values, $isTest, $returnMessageText );
        }
    }


}


