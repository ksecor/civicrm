<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.1                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Social Source Foundation                        |
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
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Social Source Foundation (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Utils/System.php';
require_once 'CRM/Core/DAO/EmailHistory.php';
require_once 'api/crm.php';

/**
 * BAO object for crm_email_history table
 */
class CRM_Core_BAO_EmailHistory extends CRM_Core_DAO_EmailHistory {

    /**
     * send the message to all the contacts and also insert a
     * contact activity in each contacts record
     *
     * @param array  $contactIds   the array of contact ids to send the email
     * @param string $subject      the subject of the message
     * @param string $message      the message contents
     * @param string $emailAddress use this 'to' email address instead of the default Primary address
     *
     * @return array             (total, added, notAdded) count of emails sent
     * @access public
     * @static
     */
    static function sendEmail( &$contactIds, &$subject, &$message, $emailAddress ) {
        $session =& CRM_Core_Session::singleton( );
        $userID  =  $session->get( 'userID' );
        list( $fromDisplayName, $fromEmail ) = CRM_Contact_BAO_Contact::getEmailDetails( $userID );
        if ( ! $fromEmail ) {
            return array( count($contactIds), 0, count($contactIds) );
        }
        $from = "'$fromDisplayName' <$fromEmail>";

        // create the meta level record first
        $email             =& new CRM_Core_BAO_EmailHistory( );
        $email->subject    = $subject;
        $email->message    = $message;
        $email->contact_id = $userID;
        $email->sent_date  = date( 'Ymd' );
        $email->save( );

        $sent = $notSent = 0;
        foreach ( $contactIds as $contactId ) {
            if ( self::sendMessage( $from, $contactId, $subject, $message, $emailAddress, $email->id ) ) {
                $sent++;
            } else {
                $notSent++;
            }
        }

        return array( count($contactIds), $sent, $notSent );
    }

    /**
     * send the message to a specific contact
     *
     * @param string $from       the name and email of the sender
     * @param int    $toID       the contact id of the recipient       
     * @param string $subject    the subject of the message
     * @param string $message    the message contents
     * @param string $emailAddress use this 'to' email address instead of the default Primary address 
     * @param int    $activityID the activity ID that tracks the message
     *
     * @return array             (total, added, notAdded) count of emails sent
     * @access public
     * @static
     */
    static function sendMessage( $from, $toID, &$subject, &$message, $emailAddress, $activityID ) {
        list( $toDisplayName  , $toEmail   ) = CRM_Contact_BAO_Contact::getEmailDetails( $toID   );
        if ( $emailAddress ) {
            $toEmail = trim( $emailAddress );
        }

        // make sure both email addresses are valid
        if ( empty( $toEmail ) ) {
            return false;
        }
        
        $headers = array( );
        $headers['From']    = $from;
        $headers['To'  ]    = "'$toDisplayName' <$toEmail>";
        $headers['subject'] = $subject;

        $mailer = CRM_Core_Config::getMailer( );
        if ($mailer->send($toEmail, $headers, $message) !== true) {
            return false;
        }
        
        // we need to insert an activity history record here
        $params = array('entity_table'     => 'civicrm_contact',
                        'entity_id'        => $toID,
                        'activity_type'    => ts('Email Sent'),
                        'module'           => 'CiviCRM',
                        'callback'         => 'CRM_Core_BAO_EmailHistory::showEmailDetails',
                        'activity_id'      => $activityID,
                        'activity_summary' => ts('To: %1; Subject: %2', array(1 => $headers['To'], 2 => $headers['subject'])),
                        'activity_date'    => date('YmdHis')
                        );
        
        if ( is_a( crm_create_activity_history($params), CRM_Core_Error ) ) {
            return false;
        }
        return true;
    }


    /**
     * compose the url to show details of this specific email
     *
     * @param int $id
     */
    public function showEmailDetails( $cid )
    {
        return CRM_Utils_System::url('civicrm/contact/view/activity', "activity_id=3&details=1&action=view&id=$cid");
    }



    /**
     * delete all records for this contact id
     *
     * @param int $id
     */
    public static function deleteContact($id)
    {
        $dao =& new CRM_Core_DAO_EmailHistory();
        $dao->contact_id = $id;
        $dao->delete();
    }
}

?>
