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

require_once 'Mail/mime.php';


class CRM_Mailing_Event_BAO_Reply extends CRM_Mailing_Event_DAO_Reply {

    /**
     * class constructor
     */
    function __construct( ) {
        parent::__construct( );
    }

    /**
     * Register a reply event. 
     *
     * @param int $job_id       The job ID of the reply
     * @param int $queue_id     The queue event id
     * @param string $hash      The hash
     * @return object|null      The mailing object, or null on failure
     * @access public
     * @static
     */
    public static function &reply($job_id, $queue_id, $hash) {
        /* First make sure there's a matching queue event */
        $q =& CRM_Mailing_Event_BAO_Queue::verify($job_id, $queue_id, $hash);

        if (! $q) {
            return null;
        }

        $mailing =& new CRM_Mailing_BAO_Mailing();
        $mailings = CRM_Mailing_BAO_Mailing::getTableName();
        $jobs = CRM_Mailing_BAO_Job::getTableName();
        $mailing->query(
            "SELECT * FROM  $mailings 
            INNER JOIN      $jobs 
                ON          $jobs.mailing_id = $mailings.id
            WHERE           $jobs.id = {$q->job_id}");
        $mailing->fetch();

        if (! $mailing->forward_replies || empty($mailing->replyto_email)) {
            return null;
        }

        $re =& new CRM_Mailing_Event_BAO_Reply();
        $re->event_queue_id = $queue_id;
        $re->time_stamp = date('YmdHis');
        $re->save();
        return $mailing;
    }

    /**
     * Forward a mailing reply 
     *
     * @param int $queue_id     Queue event ID of the sender
     * @param string $mailing   The mailing object
     * @param string $body      Body of the message
     * @param string $replyto   Reply-to of the incoming message
     * @return void
     * @access public
     * @static
     */
    public static function send($queue_id, &$mailing, &$body, $replyto) {
        $config =& CRM_Core_Config::singleton();
        $mailer =& $config->getMailer();
        $domain =& CRM_Core_BAO_Domain::getCurrentDomain();
        
        $emails = CRM_Core_BAO_Email::getTableName();
        $eq = CRM_Mailing_Event_BAO_Queue::getTableName();
        $contacts = CRM_Contact_BAO_Contact::getTableName();
        
        $dao =& new CRM_Core_DAO();
        $dao->query("SELECT     $contacts.display_name as display_name,
                                $emails.email as email
                    FROM        $eq
                    INNER JOIN  $contacts
                            ON  $eq.contact_id = $contacts.id
                    INNER JOIN  $emails
                            ON  $eq.email_id = $emails.id
                    WHERE       $eq.id = $queue_id");
        $dao->fetch();
        
        
        if (empty($dao->display_name)) {
            $from = $dao->email;
        } else {
            $from = "\"{$dao->display_name}\" <{$dao->email}>";
        }
        
        $message =& new Mail_Mime("\n");
        $headers = array(
            'Subject'       => "Re: {$mailing->subject}",
            'To'            => $mailing->replyto_email,
            'From'          => $from,
            'Reply-To'      => empty($replyto) ? $dao->email : $replyto,
            'Return-path'   => "do-not-reply@{$domain->email_domain}",
        );
        $message->setTxtBody($body);
        $b = $message->get();
        $h = $message->headers($headers);
        PEAR::setErrorHandling( PEAR_ERROR_CALLBACK,
                        array('CRM_Mailing_BAO_Mailing', 'catchSMTP'));
        $mailer->send($mailing->replyto_email, $h, $b);
        CRM_Core_Error::setCallback();
    }
}

?>
