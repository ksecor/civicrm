<?php
/*
 +----------------------------------------------------------------------+
 | CiviCRM version 1.0                                                  |
 +----------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                    |
 +----------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                      |
 |                                                                      |
 | CiviCRM is free software; you can redistribute it and/or modify it   |
 | under the terms of the Affero General Public License Version 1,      |
 | March 2002.                                                          |
 |                                                                      |
 | CiviCRM is distributed in the hope that it will be useful, but       |
 | WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
 | See the Affero General Public License for more details at            |
 | http://www.affero.org/oagpl.html                                     |
 |                                                                      |
 | A copy of the Affero General Public License has been been            |
 | distributed along with this program (affero_gpl.txt)                 |
 +----------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'Mail.php';


class CRM_Mailing_BAO_Job extends CRM_Mailing_DAO_Job {

    /**
     * class constructor
     */
    function __construct( ) {
        parent::__construct( );
    }

    /**
     * Initiate all pending/ready jobs
     *
     * @param void
     * @return void
     * @access public
     * @static
     */
    public static function runJobs() {
        $job =& new CRM_Mailing_BAO_Job();
        $jobTable = CRM_Mailing_DAO_Job::getTableName();
        
        /* TODO include unfinished jobs */
        $query = "  SELECT      *
                    FROM        $jobTable
                    WHERE       start_date IS null
                    AND         scheduled_date <= NOW()
                    ORDER BY    scheduled_date";

        $job->query($query);
        $job->find();

        $mailer =& Mail::factory('smtp', array('host' => 'SMTP.FIXME.COM'));

        /* TODO We should parallelize or prioritize this */
        while ($job->fetch()) {
            /* TODO only queue if they haven't already been queued */
            /* Queue up recipients for all jobs being launched */
            $job->queue();
            
            /* Start the job */
            $job->start_date = time();
            /* TODO set status */
            $job->save();
        
            /* Compose and deliver */
            $job->deliver($mailer);

            /* Finish the job */
            $job->end_date = time();
            /* TODO set status */
            $job->save();
        }
    }

    /**
     * Queue recipients of a job.
     *
     * @param void
     * @return void
     * @access public
     */
    public function queue() {
        $mailing =& new CRM_Mailing_BAO_Mailing();
        $mailing->id = $this->mailing_id;
        
        $recipients =& $mailing->getRecipients();

        foreach ($recipients as $recipient) {
            $params = array(
                'job_id'        => $this->id,
                'email_id'      => $recipient['email_id'],
                'contact_id'    => $recipient['contact_id']
            );
            CRM_Mailing_BAO_MailingEventQueue::create($params);
        }
    }


    /**
     * Send the mailing
     *
     * @param object $mailer        A Mail object to send the messages
     * @return void
     * @access public
     */
    public function deliver(&$mailer) {
        $mailing =& new CRM_Mailing_BAO_Mailing();
        $mailing->id = $this->mailing_id;
        $mailing->find(true);

        $eq =& new CRM_Mailing_BAO_MailingEventQueue();
        $eqTable        = CRM_Mailing_BAO_MailingEventQueue::tableName();
        $emailTable     = CRM_Contact_BAO_Email::tableName();
        $contactTable   = CRM_Contact_BAO_Contact::tableName();

        $query = "  SELECT      $eqTable.id,
                                $emailTable.email as email,
                                $contactTable.display_name as display_name
                    FROM        $eqTable
                    INNER JOIN  $emailTable
                            ON  $eqTable.email_id = $emailTable.id
                    INNER JOIN  $contactTable
                            ON  $eqTable.contact_id = $contactTable.id
                    WHERE       $eqTable.job_id = " . $this->id;
                    
        $eq->query($query);
        $eq->find();

        while ($eq->fetch()) {
            /* Compose the mailing */
            $message = $mailing->compose(   $this->id, $eq->id, $eq->hash,
                                            $eq->display_name, $eq->email,
                                            $recipient);
            /* Send the mailing */
            $body = $message->get();
            $headers = $message->headers();

            $result = $mailer->send($recipient, $headers, $body);
            
            if (is_a($result, PEAR_Error)) {
                $error = $result->getMessage();
                /* Register the bounce event */
                $params = array('event_queue_id' => $eq->id);
                
                /* TODO process the bounce message */
                
                CRM_Mailing_BAO_MailingEventBounce::create($params);
            } else {
                /* Register the delivery event */
                $ed =& new CRM_Mailing_DAO_MailingEventDelivered();
                $ed->event_queue_id = $eq->id;
                $ed->save();
            }
        }
    }
}

?>
