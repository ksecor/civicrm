<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.8                                                |
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

class CRM_Core_Payment_PayPalIPN {

    static $_paymentProcessor = null;

    static function retrieve( $name, $type, $location = 'POST', $abort = true ) {
        static $store = null;
        $value = CRM_Utils_Request::retrieve( $name, $type, $store,
                                              false, null, $location );
        if ( $abort && $value === null ) {
            CRM_Core_Error::debug_log_message( "Could not find an entry for $name in $location" );
            echo "Failure: Missing Parameter<p>";
            exit( );
        }
        return $value;
    }

    static function recur( $component, $contactID, &$contribution, &$contributionType, $first ) {
        $contributionRecurID = self::retrieve( 'contributionRecurID', 'Integer', 'GET' , true );
        $contributionPageID  = self::retrieve( 'contributionPageID' , 'Integer', 'GET' , true );
        $txnType             = self::retrieve( 'txn_type'           , 'String' , 'POST', true );

        if ( $txnType == 'subscr_payment' &&
             $_POST['payment_status'] != 'Completed' ) {
            CRM_Core_Error::debug_log_message( "Ignore all IPN payments that are not completed" );
            echo "Failure: Invalid parameters<p>";
            return;
        }

        require_once 'CRM/Contribute/DAO/ContributionRecur.php';
        $recur =& new CRM_Contribute_DAO_ContributionRecur( );
        $recur->id = $contributionRecurID;
        if ( ! $recur->find( true ) ) {
            CRM_Core_Error::debug_log_message( "Could not find recur record: $contributionRecurID" );
            echo "Failure: Could not find recur record: $contributionRecurID<p>";
            return;
        }

        // make sure the invoice ids match
        // make sure the invoice is valid and matches what we have in the contribution record
        $invoice             = self::retrieve( 'invoice'           , 'String' , 'POST', true );
        if ( $recur->invoice_id != $invoice ) {
            CRM_Core_Error::debug_log_message( "Invoice values dont match between database and IPN request" );
            echo "Failure: Invoice values dont match between database and IPN request<p>";
            return;
        }

        $now = date( 'YmdHis' );

        // fix dates that already exist
        $dates = array( 'create', 'start', 'end', 'cancel', 'modified' );
        foreach ( $dates as $date ) {
            $name = "{$date}_date";
            if ( $recur->$name ) {
                $recur->$name = CRM_Utils_Date::isoToMysql( $recur->$name );
            }
        }

        switch ( $txnType ) {

        case 'subscr_signup':
            $recur->create_date            = $now;
            $recur->contribution_status_id = 2;
            $recur->processor_id           = $_POST['subscr_id'];
            $recur->trxn_id                = $recur->processor_id;
            break;
            
        case 'subscr_eot':
            $recur->contribution_status_id = 1;
            $recur->end_date               = $now;
            break;

        case 'subscr_cancel':
            $recur->contribution_status_id = 3;
            $recur->cancel_date            = $now;
            break;

        case 'subscr_failed':
            $recur->contribution_status_id = 4;
            $recur->cancel_date            = $now;
            break;

        case 'subscr_modify':
            CRM_Core_Error::debug_log_message( "We do not handle modifications to subscriptions right now" );
            echo "Failure: We do not handle modifications to subscriptions right now<p>";
            return;

        case 'subscr_payment':
            if ( $first ) {
                $recur->start_date    = $now;
            } else {
                $recur->modified_date = $now;
            }
            // make sure the contribution status is not done
            // since order of ipn's is unknown
            if ( $recur->contribution_status_id != 1 ) {
                $recur->contribution_status_id = 5;
            }
            break;

        }

        $recur->save( );
        
        CRM_Core_DAO::transaction( 'COMMIT' );

        if ( $txnType != 'subscr_payment' ) {
            return;
        }

        if ( ! $first ) {
            // create a contribution and then get it processed
            $contribution =& new CRM_Contribute_DAO_Contribution( );
            $contribution->domain_id = CRM_Core_Config::domainID( );
            $contribution->contact_id = $contactID;
            $contribution->contribution_type_id  = $contributionType->id;
            $contribution->contribution_page_id  = $contributionPageID;
            $contribution->contribution_recur_id = $contributionRecurID;
            $contribution->receive_date          = $now;
        }

        self::single( $component, $contactID, $contribution, $contributionType, null, true, $first );
    }

    static function single( $component, $contactID, &$contribution, &$contributionType, $eventID, $recur = false, $first = false ) {
        $membershipTypeID   = self::retrieve( 'membershipTypeID', 'Integer', 'GET', false );

        // make sure the invoice is valid and matches what we have in the contribution record
        if ( ( ! $recur ) || ( $recur && $first ) ) {
            $invoice             = self::retrieve( 'invoice', 'String' , 'POST', true );
            if ( $contribution->invoice_id != $invoice ) {
                CRM_Core_Error::debug_log_message( "Invoice values dont match between database and IPN request" );
                echo "Failure: Invoice values dont match between database and IPN request<p>";
                return;
            }
        } else {
            $contribution->invoice_id = md5(uniqid(rand(), true));
        }

        $now = date( 'YmdHis' );
        $amount =  self::retrieve( 'mc_gross', 'Money', 'POST', true );
        $contribAmount = $amount;
        if ( ! $recur ) {
            if ( $contribution->total_amount != $amount ) {
                CRM_Core_Error::debug_log_message( "Amount values dont match between database and IPN request" );
                echo "Failure: Amount values dont match between database and IPN request<p>";
                return;
            }
        } else {
            $contribution->total_amount = $amount;
        }

        // ok we are done with error checking, now let the real work begin
        // update the contact record with the name and address
        $params = array( );
        $lookup = array( 'first_name'     => 'first_name',
                         'last_name'      => 'last_name' ,
                         'street_address' => 'address_street',
                         'city'           => 'address_city',
                         'state'          => 'address_state',
                         'postal_code'    => 'address_zip',
                         'country'        => 'address_country_code' );
        foreach ( $lookup as $name => $paypalName ) {
            $value = self::retrieve( $paypalName, 'String', 'POST', false );
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

        $status = self::retrieve( 'payment_status', 'String', 'POST', true );
        if ( $status == 'Denied' || $status == 'Failed' || $status == 'Voided' ) {
            $contribution->contribution_status_id = 4;
            $contribution->save( );
            CRM_Core_DAO::transaction( 'COMMIT' );
            CRM_Core_Error::debug_log_message( "Setting contribution status to failed" );
            echo "Success: Setting contribution status to failed<p>";
            return;
        } else if ( $status == 'Pending' ) {
            CRM_Core_Error::debug_log_message( "returning since contribution status is pending" );
            
            echo "Success: Returning since contribution status is pending<p>";
            return;
        } else if ( $status == 'Refunded' || $status == 'Reversed' ) {
            $contribution->contribution_status_id = 3;
            $contribution->cancel_date = $now;
            $contribution->cancel_reason = self::retrieve( 'ReasonCode', 'String', 'POST', false );
            $contribution->save( );
            CRM_Core_DAO::transaction( 'COMMIT' );
            CRM_Core_Error::debug_log_message( "Setting contribution status to cancelled" );
            echo "Success: Setting contribution status to cancelled<p>";
            return;
        } else if ( $status != 'Completed' ) {
            // we dont handle this as yet
            CRM_Core_Error::debug_log_message( "returning since contribution status: $status is not handled" );
            echo "Failure: contribution status $status is not handled<p>";
            return;
        }

        // check if contribution is already completed, if so we ignore this ipn
        if ( $contribution->contribution_status_id == 1 ) {
            CRM_Core_Error::debug_log_message( "returning since contribution has already been handled" );
            echo "Success: Contribution has already been handled<p>";
            return;
        }


        if ( $component == 'contribute' ) {
            require_once 'CRM/Contribute/BAO/ContributionPage.php';
            CRM_Contribute_BAO_ContributionPage::setValues( $contribution->contribution_page_id, $values );
        
            $contribution->source                  = ts( 'Online Contribution:' ) . ' ' . $values['title'];
            
            if ( $values['is_email_receipt'] ) {
                $contribution->receipt_date = $now;
            }
        } else {
            // event
            $eventParams = array( 'id' => $eventID );
            require_once 'CRM/Event/BAO/Event.php';
            CRM_Event_BAO_Event::retrieve( $eventParams, $values['event'] );
        
            $eventParams = array( 'event_id' => $eventID );
            require_once 'CRM/Event/BAO/EventPage.php';
            CRM_Event_BAO_EventPage::retrieve( $eventParams, $values['event_page'] );

            //get location details
            $locationParams = array( 'entity_id' => $eventID ,'entity_table' => 'civicrm_event' );
            require_once 'CRM/Core/BAO/Location.php';
            require_once 'CRM/Event/Form/ManageEvent/Location.php';
            CRM_Core_BAO_Location::getValues($locationParams, $values, 
                                             CRM_Core_DAO::$_nullArray, 
                                             CRM_Event_Form_ManageEvent_Location::LOCATION_BLOCKS );

            require_once 'CRM/Core/BAO/UFJoin.php';
            $ufJoinParams = array( 'entity_table' => 'civicrm_event',
                                   'entity_id'    => $eventID,
                                   'weight'       => 1 );
        
            $values['custom_pre_id'] = CRM_Core_BAO_UFJoin::findUFGroupId( $ufJoinParams );
        
            $ufJoinParams['weight'] = 2;
            $values['custom_post_id'] = CRM_Core_BAO_UFJoin::findUFGroupId( $ufJoinParams );

            $contribution->source                  = ts( 'Online Event Registration:' ) . ' ' . $values['event']['title'];

            if ( $values['event_page']['is_email_confirm'] ) {
                $contribution->receipt_date = $now;
            }
            
        }


        CRM_Core_DAO::transaction( 'BEGIN' );

        $contribution->contribution_status_id  = 1;
        $contribution->is_test    = self::retrieve( 'test_ipn'     , 'Integer', 'POST', false );
        $contribution->fee_amount = self::retrieve( 'payment_fee'  , 'Money'  , 'POST', false );
        $contribution->net_amount = self::retrieve( 'settle_amount', 'Money'  , 'POST', false );
        $contribution->trxn_id    = self::retrieve( 'txn_id'       , 'String' , 'POST', false );
        $contribution->save( );
        
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
                            'payment_processor' => self::$_paymentProcessor['payment_processor_type'],
                            'trxn_id'           => $contribution->trxn_id,
                            );
        
        require_once 'CRM/Contribute/BAO/FinancialTrxn.php';
        $trxn =& CRM_Contribute_BAO_FinancialTrxn::create( $trxnParams );

        if ( $component == 'contribute' ) {
            // get the title of the contribution page
            $title = CRM_Core_DAO::getFieldValue( 'CRM_Contribute_DAO_ContributionPage',
                                                  $contribution->contribution_page_id,
                                                  'title' );
            
            require_once 'CRM/Utils/Money.php';
            $formattedAmount = CRM_Utils_Money::format($contribAmount);
            
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
            if ( $membershipTypeID ) {
                require_once 'CRM/Member/BAO/Membership.php';
                CRM_Member_BAO_Membership::processIPNMembership ( $contactID, $contribution, $membershipTypeID, $contribAmount );
            }
        } else { // event 
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
                                       'is_test'       => $contribution->is_test ? 1 : 0,
                                       );
        
            $participant = CRM_Event_BAO_Participant::add($participantParams, CRM_Core_DAO::$_nullArray);

            require_once 'CRM/Event/BAO/ParticipantPayment.php';
            $paymentParams = array('participant_id'       => $participant->id,
                                   'payment_entity_id'    => $contribution->id,
                                   'payment_entity_table' => 'civicrm_contribution'
                                   );   

            $paymentPartcipant = CRM_Event_BAO_ParticipantPayment::create($paymentParams, CRM_Core_DAO::$_nullArray);

            // also create an activity history record
            CRM_Event_BAO_Participant::setActivityHistory( $participant );
        }


        CRM_Core_Error::debug_log_message( "Contribution record updated successfully" );
        CRM_Core_DAO::transaction( 'COMMIT' );

        // add the new contribution values
        $template =& CRM_Core_Smarty::singleton( );
        if ( $component == 'contribute' ) {
            $template->assign( 'title', $values['title']);
            $template->assign( 'amount' , $contribAmount );
        } else {
            $template->assign( 'title', $values['event']['title']);
            $template->assign( 'amount' , $amount );
        }

        $template->assign( 'trxn_id', $contribution->trxn_id );
        $template->assign( 'receive_date', 
                           CRM_Utils_Date::mysqlToIso( $contribution->receive_date ) );
        $template->assign( 'contributeMode', 'notify' );
        $template->assign( 'action', $contribution->is_test ? 1024 : 1 );
        $template->assign( 'receipt_text', $values['receipt_text'] );
        $template->assign( 'is_monetary', 1 );
        $template->assign( 'is_recur', $recur );
        if ( $recur ) {
            require_once 'CRM/Contribute/Form/ContributionBase.php';
            $url = CRM_Contribute_Form_ContributionBase::cancelSubscriptionURL( self::$_paymentProcessor );
            $template->assign( 'cancelSubscriptionUrl', $url );
        }

        require_once 'CRM/Utils/Address.php';
        $template->assign( 'address', CRM_Utils_Address::format( $params ) );
                                                                                        
        if ( $component == 'event' ) { 
            $template->assign( 'event', $values['event'] );
            $template->assign( 'eventPage', $values['event_page'] );
            $template->assign( 'location', $values['location'] );
            $template->assign( 'customPre', $values['custom_pre_id'] );
            $template->assign( 'customPost', $values['custom_post_id'] );

            require_once "CRM/Event/BAO/EventPage.php";
            CRM_Event_BAO_EventPage::sendMail( $contactID, $values, $participant->id );
        } else {
            CRM_Contribute_BAO_ContributionPage::sendMail( $contactID, $values, $contribution->id );
        }

        CRM_Core_Error::debug_log_message( "Success: Database updated and mail sent" );
        echo "Success: Database updated<p>";
    }

    static function main( $component = 'contribute' ) {
        CRM_Core_Error::debug_var( 'GET' , $_GET , true, true );
        CRM_Core_Error::debug_var( 'POST', $_POST, true, true );

        require_once 'CRM/Utils/Request.php';

        // get the contribution, contact and contributionType ids from the GET params
        $contactID          = self::retrieve( 'contactID'         , 'Integer', 'GET', true );
        $contributionID     = self::retrieve( 'contributionID'    , 'Integer', 'GET', true );
        $contributionTypeID = self::retrieve( 'contributionTypeID', 'Integer', 'GET', true );

        if ( $component == 'event' ) {
            $eventID = CRM_Core_Payment_PayPalIPN::retrieve( 'eventID'           , 'Integer', 'GET', true );
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
        
        // make sure contribution type exists and is valid
        require_once 'CRM/Contribute/DAO/ContributionType.php';
        $contributionType =& new CRM_Contribute_DAO_ContributionType( );
        $contributionType->id = $contributionTypeID;
        if ( ! $contributionType->find( true ) ) {
            CRM_Core_Error::debug_log_message( "Could not find contribution type record: $contributionTypeID" );
            echo "Failure: Could not find contribution type record for $contributionTypeID<p>";
            return;
        }

        if ( $component == 'contribute' ) {
            // get the contribution page id from the contribution
            // and then initialize the payment processor from it
            if ( ! $contribution->contribution_page_id ) {
                CRM_Core_Error::debug_log_message( "Could not find contribution page for contribution record: $contributionID" );
                echo "Failure: Could not find contribution page for contribution record: $contributionID<p>";
                return;
            }
            
            // get the payment processor id from contribution page
            $paymentProcessorID = CRM_Core_DAO::getFieldValue( 'CRM_Contribute_DAO_ContributionPage',
                                                               $contribution->contribution_page_id,
                                                               'payment_processor_id' );
        } else {
            // we are in event mode
            // make sure event exists and is valid
            require_once 'CRM/Event/DAO/Event.php';
            $event =& new CRM_Event_DAO_Event( );
            $event->id = $eventID;
            if ( ! $event->find( true ) ) {
                CRM_Core_Error::debug_log_message( "Could not find event: $eventID" );
                echo "Failure: Could not find event: $eventID<p>";
                return;
            }
            
            // get the payment processor id from contribution page
            $paymentProcessorID = $event->payment_processor_id;
        }


        if ( ! $paymentProcessorID ) {
            CRM_Core_Error::debug_log_message( "Could not find payment processor for contribution record: $contributionID" );
            echo "Failure: Could not find payment processor for contribution record: $contributionID<p>";
            return;
        }

        require_once 'CRM/Core/BAO/PaymentProcessor.php';
        self::$_paymentProcessor = CRM_Core_BAO_PaymentProcessor::getPayment( $paymentProcessorID,
                                                                              $contribution->is_test ? 'test' : 'live' );
        
        if ( $component == 'contribute' ) {
            if ( array_key_exists( 'contributionRecurID', $_GET ) ) {
                // check if first contribution is completed, else complete first contribution
                $first = true;
                if ( $contribution->contribution_status_id == 1 ) {
                    $first = false;
                }
                return self::recur( $component, $contactID, $contribution, $contributionType, $first );
            } else {
                return self::single( $component, $contactID, $contribution, $contributionType, null, false, false );
            }
        } else {
            return self::single( $component, $contactID, $contribution, $contributionType, $eventID );
        }
    }

}

?>
