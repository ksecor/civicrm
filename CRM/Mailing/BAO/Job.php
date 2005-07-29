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
        
        /* FIXME: we might want to go to a progress table.. */
        $query = "  SELECT      *
                    FROM        $jobTable
                    WHERE       (start_date IS null
                        AND         scheduled_date <= NOW()
                        AND         status = 'Scheduled')
                    OR          (status = 'Running'
                        AND         end_date IS null)
                    ORDER BY    scheduled_date";

        $job->query($query);
        $job->find();

        $mailer =& Mail::factory('smtp', array('host' => 'SMTP.FIXME.COM'));

        /* TODO We should parallelize or prioritize this */
        while ($job->fetch()) {
            /* Queue up recipients for all jobs being launched */
            if ($job->status != 'Running') {
                CRM_Core_DAO::transaction('BEGIN');
                $job->queue();
                
                /* Start the job */
                $job->start_date = time();
                $job->status = 'Running';
                $job->save();
                CRM_Core_DAO::transaction('COMMIT');
            }
            
        
            /* Compose and deliver */
            $job->deliver($mailer);

            /* Finish the job */
            $job->end_date = time();
            $job->status = 'Complete';
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
        
        if ($this->is_retry) {
            $recipients =& $mailing->retryRecipients($this->id);
        } else {
            $recipients =& $mailing->getRecipients($this->id);
        }
        
        foreach ($recipients as $recipient) {
            $params = array(
                'job_id'        => $this->id,
                'email_id'      => $recipient['email_id'],
                'contact_id'    => $recipient['contact_id']
            );
            CRM_Mailing_Event_BAO_Queue::create($params);
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

        $eq =& new CRM_Mailing_Event_BAO_Queue();
        $eqTable        = CRM_Mailing_Event_BAO_Queue::getTableName();
        $emailTable     = CRM_Core_BAO_Email::getTableName();
        $contactTable   = CRM_Contact_BAO_Contact::getTableName();
        $edTable        = CRM_Mailing_Event_BAO_Delivered::getTableName();
        $ebTable        = CRM_Mailing_Event_BAO_Bounce::getTableName();
        
        $query = "  SELECT      $eqTable.id,
                                $emailTable.email as email,
                                $eqTable.contact_id
                    FROM        $eqTable
                    INNER JOIN  $emailTable
                            ON  $eqTable.email_id = $emailTable.id
                    LEFT JOIN   $edTable
                            ON  $eqTable.id = $edTable.event_queue_id
                    LEFT JOIN   $ebTable
                            ON  $eqTable.id = $ebTable.event_queue_id
                    WHERE       $eqTable.job_id = " . $this->id . "
                    HAVING      $edTable.id IS null
                        AND     $ebTable.id IS null";
                    
        $eq->query($query);
        $eq->find();

        while ($eq->fetch()) {
            /* Compose the mailing */
            $recipient = null;
            $message = $mailing->compose(   $this->id, $eq->id, $eq->hash,
                                            $eq->contact_id, $eq->email,
                                            $recipient);
            /* Send the mailing */
            $body = $message->get();
            $headers = $message->headers();
            
            /* TODO: when we separate the content generator from the delivery
             * engine, maybe we should dump the messages into a table */
            
            return;
            
            PEAR::setErrorHandling( PEAR_ERROR_CALLBACK,
                                    array('CRM_Mailing_BAO_Mailing', 
                                    'catchSMTP'));
            $result = $mailer->send($recipient, $headers, $body);
            CRM_Core_Error::setCallback();
            
            if (is_a($result, PEAR_Error)) {
                /* Register the bounce event */
                $params =&
                    CRM_Mailing_BAO_BouncePattern::match($result->getMessage());
                $params['event_queue_id'] = $eq->id;
                
                CRM_Mailing_Event_BAO_Bounce::create($params);
            } else {
                /* Register the delivery event */
                $params = array('event_queue_id' => $eq->id);
                CRM_Mailing_Event_BAO_Delivered::create($params);
            }
        }
    }
}

?>
