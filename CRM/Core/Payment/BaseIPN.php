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

class CRM_Core_Payment_BaseIPN.php {

    function validateData( $data ) {

         // make sure contact exists and is valid
         require_once 'CRM/Contact/DAO/Contact.php';
         $contact =& new CRM_Contact_DAO_Contact( );
         $contact->id = $data['contactID'];
         if ( ! $contact->find( true ) ) {
             CRM_Core_Error::debug_log_message( "Could not find contact record: $contactID" );
             echo "Failure: Could not find contact record: $contactID<p>";
             return false;
         }

         // make sure contribution exists and is valid
         require_once 'CRM/Contribute/DAO/Contribution.php';
         $contribution =& new CRM_Contribute_DAO_Contribution( );
         $contribution->id = $data['contributionID'];
         if ( ! $contribution->find( true ) ) {
             CRM_Core_Error::debug_log_message( "Could not find contribution record: $contributionID" );
             echo "Failure: Could not find contribution record for $contributionID<p>";
             return false;
         }

         // make sure contribution type exists and is valid
         require_once 'CRM/Contribute/DAO/ContributionType.php';
         $contributionType =& new CRM_Contribute_DAO_ContributionType( );
         $contributionType->id = $data['contributionTypeID'];
         if ( ! $contributionType->find( true ) ) {
             CRM_Core_Error::debug_log_message( "Could not find contribution type record: $contributionTypeID" );
             echo "Failure: Could not find contribution type record for $contributionTypeID<p>";
             return false;
         }

         if ( $data['component'] == 'contribute' ) {
             // get the contribution page id from the contribution
             // and then initialize the payment processor from it
             if ( ! $contribution->contribution_page_id ) {
                 CRM_Core_Error::debug_log_message( "Could not find contribution page for contribution record: $contributionID" );
                 echo "Failure: Could not find contribution page for contribution record: $contributionID<p>";
                 return false;
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
             $event->id = $data['eventID'];
             if ( ! $event->find( true ) ) {
                 CRM_Core_Error::debug_log_message( "Could not find event: $eventID" );
                 echo "Failure: Could not find event: $eventID<p>";
                 return false;
             }

             require_once 'CRM/Event/DAO/Participant.php';
             $participant =& new CRM_Event_DAO_Participant( );
             $participant->id = $data['participantID'];
             if ( ! $participant->find( true ) ) {
                 CRM_Core_Error::debug_log_message( "Could not find participant: $participantID" );
                 echo "Failure: Could not find participant: $participantID<p>";
                 return false;
             }

             // get the payment processor id from contribution page
             $paymentProcessorID = $event->payment_processor_id;
         }


         if ( ! $paymentProcessorID ) {
             CRM_Core_Error::debug_log_message( "Could not find payment processor for contribution record: $contributionID" );
             echo "Failure: Could not find payment processor for contribution record: $contributionID<p>";
             return false;
         }

         require_once 'CRM/Core/BAO/PaymentProcessor.php';
         $paymentProcessor = CRM_Core_BAO_PaymentProcessor::getPayment( $paymentProcessorID,
                                                                        $contribution->is_test ? 'test' : 'live' );
        
    }

}

?>
