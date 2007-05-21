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

class CRM_Event_Payment_PayPalIPN 
{
    static function retrieve( $name, $type, $location = 'POST', $abort = true ) 
    {
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
    
    static function main( ) 
    {
        CRM_Core_Error::debug_var( 'GET' , $_GET , true, true );
        CRM_Core_Error::debug_var( 'POST', $_POST, true, true );

        require_once 'CRM/Utils/Request.php';

        // get the contribution, contact and contributionType ids from the GET params
        //$store              = null;
        $contactID          = self::retrieve( 'contactID'         , 'Integer', 'GET', true );
        $contributionID     = self::retrieve( 'contributionID'    , 'Integer', 'GET', true );
        $contributionTypeID = self::retrieve( 'contributionTypeID', 'Integer', 'GET', true );
        $eventID            = self::retrieve( 'eventID'           , 'Integer', 'GET', true );
        
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
        
        // make sure event exists and is valid
        require_once 'CRM/Event/DAO/Event.php';
        $event =& new CRM_Event_DAO_Event( );
        $event->id = $eventID;
        if ( ! $event->find( true ) ) {
            CRM_Core_Error::debug_log_message( "Could not find event: $eventID" );
            echo "Failure: Could not find event: $eventID<p>";
            return;
        }

        return self::single( $contactID, $contribution, $contributionType, $eventID );
    }

    static function single( $contactID, &$contribution, &$contributionType, $eventID ) 
    {
        $invoice = self::retrieve( 'invoice', 'String' , 'POST', true );
        if ( $contribution->invoice_id != $invoice ) {
            CRM_Core_Error::debug_log_message( "Invoice values dont match between database and IPN request" );
            echo "Failure: Invoice values dont match between database and IPN request<p>";
            return;
        }
        
        $now = date( 'YmdHis' );
        $amount =  self::retrieve( 'mc_gross', 'Money', 'POST', true );
        if ( $contribution->total_amount != $amount ) {
            CRM_Core_Error::debug_log_message( "Amount values dont match between database and IPN request" );
            echo "Failure: Amount values dont match between database and IPN request<p>";
            return;
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
            $idParams = array( 'id'      => $contactID,
                               'contact' => $contactID );
            $ids = $defaults = array( );
            require_once "CRM/Contact/BAO/Contact.php";
            CRM_Contact_BAO_Contact::retrieve( $idParams, $defaults, $ids );
            $contact = CRM_Contact_BAO_Contact::createFlat( $params, $ids );
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

        $contribution->contribution_status_id  = 1;
        $contribution->source                  = ts( 'Online Event Registration:' ) . ' ' . $values['event']['title'];
        $contribution->is_test    = self::retrieve( 'test_ipn'     , 'Integer', 'POST', false );
        $contribution->fee_amount = self::retrieve( 'payment_fee'  , 'Money'  , 'POST', false );
        $contribution->net_amount = self::retrieve( 'settle_amount', 'Money'  , 'POST', false );
        $contribution->trxn_id    = self::retrieve( 'txn_id'       , 'String' , 'POST', false );

        if ( $values['event_page']['is_email_confirm'] ) {
            $contribution->receipt_date = $now;
        }

        CRM_Core_DAO::transaction( 'BEGIN' );

        $contribution->save( );

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
                            'payment_processor' => $config->paymentProcessor,
                            'trxn_id'           => $contribution->trxn_id,
                            );
        
        require_once 'CRM/Contribute/BAO/FinancialTrxn.php';
        $trxn =& CRM_Contribute_BAO_FinancialTrxn::create( $trxnParams );

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
        
        $participant = CRM_Event_BAO_Participant::add($participantParams, CRM_Core_DAO::$_nullArray);

        require_once 'CRM/Event/BAO/ParticipantPayment.php';
        $paymentParams = array('participant_id'       => $participant->id,
                               'payment_entity_id'    => $contribution->id,
                               'payment_entity_table' => 'civicrm_contribution'
                               );   

        $paymentPartcipant = CRM_Event_BAO_ParticipantPayment::create($paymentParams, CRM_Core_DAO::$_nullArray);

        // also create an activity history record
        CRM_Event_BAO_Participant::setActivityHistory( $participant );

        CRM_Core_Error::debug_log_message( "Contribution record updated successfully" );
        CRM_Core_DAO::transaction( 'COMMIT' );

        // add the new contribution values
        $template =& CRM_Core_Smarty::singleton( );
        $template->assign( 'title', $values['event']['title']);
        $template->assign( 'amount' , $amount );
        $template->assign( 'amount_level' , $contribution->amount_level );
        $template->assign( 'trxn_id', $contribution->trxn_id );
        $template->assign( 'receive_date', 
                           CRM_Utils_Date::mysqlToIso( $contribution->receive_date ) );
        $template->assign( 'contributeMode', 'notify' );
        $template->assign( 'action', $contribution->is_test ? 1024 : 1 );
        $template->assign( 'receipt_text', $values['receipt_text'] );
        $template->assign( 'is_monetary', 1 );

        $template->assign( 'event', $values['event'] );
        $template->assign( 'eventPage', $values['event_page'] );
        $template->assign( 'location', $values['location'] );
        $template->assign( 'customPre', $values['custom_pre_id'] );
        $template->assign( 'customPost', $values['custom_post_id'] );

        require_once 'CRM/Utils/Address.php';
        $template->assign( 'address', CRM_Utils_Address::format( $params ) );

        require_once "CRM/Event/BAO/EventPage.php";
        CRM_Event_BAO_EventPage::sendMail( $contactID, $values, $participant->id );

        CRM_Core_Error::debug_log_message( "Sucess: Database updated and email sent" );    
        echo "Success: Database updated<p>";
    }
}
?>
