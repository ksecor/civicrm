<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                  |
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Utils/System.php';
require_once 'CRM/Core/DAO/EmailHistory.php';
require_once 'api/crm.php';
require_once 'CRM/Utils/Mail.php';

/**
 * BAO object for crm_email_history table
 */
class CRM_Core_BAO_EmailHistory extends CRM_Core_DAO_EmailHistory {

    static function &add( &$params ) {
        $email =& new CRM_Core_DAO_EmailHistory( );
        
        $email->subject    = CRM_Utils_Array::value( 'subject'   , $params );
        $email->message    = CRM_Utils_Array::value( 'message'   , $params );
        $email->contact_id = CRM_Utils_Array::value( 'contact_id', $params );
        $email->sent_date  = CRM_Utils_Array::value( 'sent_date' , $params , date( 'Ymd' ) );

        $email->save( );

        return $email;
    }

    /**
     * send the message to all the contacts and also insert a
     * contact activity in each contacts record
     *
     * @param array  $contactIds   the array of contact ids to send the email
     * @param string $subject      the subject of the message
     * @param string $message      the message contents
     * @param string $emailAddress use this 'to' email address instead of the default Primary address
     * @param int    userID        use this userID if set
     * @return array             (total, added, notAdded) count of emails sent
     * @access public
     * @static
     */
    static function sendEmail( &$contactIds, &$subject, &$message, $emailAddress, $userID = null ) {
        if ( $userID == null ) {
            $session =& CRM_Core_Session::singleton( );
            $userID  =  $session->get( 'userID' );
        }
        list( $fromDisplayName, $fromEmail, $fromDoNotEmail ) = CRM_Contact_BAO_Contact::getContactDetails( $userID );
        if ( ! $fromEmail ) {
            return array( count($contactIds), 0, count($contactIds) );
        }
        if ( ! trim($fromDisplayName) ) {
            $fromDisplayName = $fromEmail;
        }

        $from = CRM_Utils_Mail::encodeAddressHeader($fromDisplayName, $fromEmail);

        // create the meta level record first
        $params =  array( 'subject'    => $subject,
                          'message'    => $message,
                          'contact_id' => $userID );
        $email  =& self::add( $params );

        $sent = $notSent = array();
        foreach ( $contactIds as $contactId ) {
            if ( self::sendMessage( $from, $userID, $contactId, $subject, $message, $emailAddress, $email->id ) ) {
                $sent[] =  $contactId;
            } else {
                $notSent[] = $contactId;
            }
        }

        return array( count($contactIds), $sent, $notSent );
    }
    
    /**
     * send the message to a specific contact
     *
     * @param string $from         the name and email of the sender
     * @param int    $toID         the contact id of the recipient       
     * @param string $subject      the subject of the message
     * @param string $message      the message contents
     * @param string $emailAddress use this 'to' email address instead of the default Primary address 
     * @param int    $activityID   the activity ID that tracks the message
     *
     * @return boolean             true if successfull else false.
     * @access public
     * @static
     */
    static function sendMessage( $from, $fromID, $toID, &$subject, &$message, $emailAddress, $activityID ) {
        list( $toDisplayName, $toEmail, $toDoNotEmail ) = CRM_Contact_BAO_Contact::getContactDetails( $toID );
        if ( $emailAddress ) {
            $toEmail = trim( $emailAddress );
        }
        
        // make sure both email addresses are valid
        // and that the recipient wants to receive email
        if ( empty( $toEmail ) or $toDoNotEmail ) {
            return false;
        }
        if ( ! trim($toDisplayName) ) {
            $toDisplayName = $toEmail;
        }
        
        if ( ! CRM_Utils_Mail::send( $from,
                                     $toDisplayName, $toEmail,
                                     $subject,
                                     $message ) ) {
            return false;
        }
        
        // we need to insert an activity history record here
        $params = array('entity_table'     => 'civicrm_contact',
                        'entity_id'        => $toID,
                        'activity_type'    => ts('Email Sent'),
                        'module'           => 'CiviCRM',
                        'callback'         => 'CRM_Core_BAO_EmailHistory::showEmailDetails',
                        'activity_id'      => $activityID,
                        'activity_summary' => ts('From: %1; Subject: %2', array(1 => $from, 2 => $subject)),
                        'activity_date'    => date('YmdHis')
                        );

        if ( is_a( crm_create_activity_history($params), CRM_Core_Error ) ) {
            return false;
        }

        // also insert an activity history record from the sender
        $params['entity_id'] = $fromID;
        $params['activity_summary'] = ts('To: %1; Subject: %2', array(1 => "$toDisplayName <$toEmail>", 2 => $subject) );
        if ( is_a( crm_create_activity_history($params), CRM_Core_Error ) ) {
            return false;
        }

        return true;
    }
    
    /**
     * compose the url to show details of this specific email
     * 
     * @param  int     $id
     * 
     * @return string            an HTML string containing a link to the given path.
     * 
     * @access public
     */
    public function showEmailDetails( $id )
    {
        return CRM_Utils_System::url('civicrm/contact/view/activity', "activity_id=3&details=1&action=view&id=$id&selectedChild=activity");
    }
    
    /**
     * delete all email history records for this contact id.
     *
     * @param int   $id   ID of the contact for which 
     *                    the email history need to be deleted.
     * 
     * @return void
     * 
     * @access public
     * @static
     */
    public static function deleteContact($id)
    {
        $dao =& new CRM_Core_DAO_EmailHistory();
        $dao->contact_id = $id;
        $dao->delete();
    }
}

?>
