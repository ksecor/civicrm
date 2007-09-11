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

require_once 'Mail.php';
require_once 'CRM/Mailing/DAO/Job.php';
require_once 'CRM/Mailing/DAO/Mailing.php';

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
     * @return void
     * @access public
     * @static
     */
    public static function runJobs() {
        $job =& new CRM_Mailing_BAO_Job();
        $mailing =& new CRM_Mailing_DAO_Mailing();
        $jobTable = CRM_Mailing_DAO_Job::getTableName();
        
        $config =& CRM_Core_Config::singleton();
        
        /* FIXME: we might want to go to a progress table.. */
        $query = "  SELECT      *
                    FROM        $jobTable
                    WHERE       (start_date IS null
                        AND         scheduled_date <= NOW()
                        AND         status = 'Scheduled')
                    OR          (status = 'Running'
                        AND         end_date IS null)
                    ORDER BY    scheduled_date, start_date";
        
        $job->query($query);
        
        /* TODO We should parallelize or prioritize this */
        while ($job->fetch()) {
            
            /* Queue up recipients for all jobs being launched */
            if ($job->status != 'Running') {
                CRM_Core_DAO::transaction('BEGIN');
                $job->queue();
                
                /* Start the job */
                $job->start_date = date('YmdHis');
                $job->status = 'Running';
                // CRM-992 - MySQL can't eat its own dates
                $job->scheduled_date = CRM_Utils_Date::isoToMysql($job->scheduled_date);
                $job->save();
                CRM_Core_DAO::transaction('COMMIT');
            }
            
            $mailingSize = $job->getMailingSize();
            $mailer =& $config->getMailer($mailingSize);

            /* Compose and deliver */
            $isComplete = $job->deliver($mailer);

            require_once 'CRM/Utils/Hook.php';
            CRM_Utils_Hook::post( 'create', 'CRM_Mailing_DAO_Spool', $job->id, $isComplete);
            
            if (!$isComplete){
                return;
            }
            /* Finish the job */
            CRM_Core_DAO::transaction('BEGIN');
            $job->end_date = date('YmdHis');
            $job->status = 'Complete';
            // CRM-992 - MySQL can't eat its own dates
            $job->scheduled_date = CRM_Utils_Date::isoToMysql($job->scheduled_date);
            $job->start_date = CRM_Utils_Date::isoToMysql($job->start_date);
            $job->save();
            $mailing->reset();
            $mailing->id = $job->mailing_id;
            $mailing->is_completed = true;
            $mailing->save();
            CRM_Core_DAO::transaction('COMMIT');
        }
    }

    /**
     * Queue recipients of a job.
     *
     * @return void
     * @access public
     */
    public function queue() {
        require_once 'CRM/Mailing/BAO/Mailing.php';
        $mailing =& new CRM_Mailing_BAO_Mailing();
        $mailing->id = $this->mailing_id;
        
        if ($this->is_retry) {
            $recipients =& $mailing->retryRecipients($this->id);
        } else {
            $recipients =& $mailing->getRecipients($this->id);
        }
        
        while ($recipients->fetch()) {
            $params = array(
                'job_id'        => $this->id,
                'email_id'      => $recipients->email_id,
                'contact_id'    => $recipients->contact_id
            );
            CRM_Mailing_Event_BAO_Queue::create($params);
        }
    }

    /**
     * Number of mailings of a job.
     *
     * @return int
     * @access public
     */
    public function getMailingSize() {
        require_once 'CRM/Mailing/BAO/Mailing.php';
        $mailing =& new CRM_Mailing_BAO_Mailing();
        $mailing->id = $this->mailing_id;

        $recipients =& $mailing->getRecipients($this->id, true);
        $mailingSize = 0;
        while ($recipients->fetch()) {
            $mailingSize ++;
        }
        return $mailingSize;
    }

    /**
     * Create a retry job for a mailing
     *
     * @param int $mailing_id           ID of the mailing to retry
     * @param string $start_date        Start date
     * @return object                    The job object
     * @access public
     * @static
     */
    public static function retry($mailing_id, $start_date) {
        $job =& new CRM_Mailing_BAO_Job();
        $job->mailing_id = $mailing_id;
        $job->scheduled_date = $start_date;
        $job->status = 'Scheduled';
        $job->is_retry = true;
        $job->save();
        
        return $job;
    }

    /**
     * Send the mailing
     *
     * @param object $mailer        A Mail object to send the messages
     * @return void
     * @access public
     */
    public function deliver(&$mailer) {
        require_once 'CRM/Mailing/BAO/Mailing.php';
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
                                $eqTable.contact_id,
                                $eqTable.hash
                    FROM        $eqTable
                    INNER JOIN  $emailTable
                            ON  $eqTable.email_id = $emailTable.id
                    LEFT JOIN   $edTable
                            ON  $eqTable.id = $edTable.event_queue_id
                    LEFT JOIN   $ebTable
                            ON  $eqTable.id = $ebTable.event_queue_id
                    WHERE       $eqTable.job_id = " . $this->id . "
                        AND     $edTable.id IS null
                        AND     $ebTable.id IS null";
                    
        $eq->query($query);

        static $config = null;
        static $mailsProcessed = 0;
        if ( $config == null ) {
            $config =& CRM_Core_Config::singleton();
        }

        // make sure that there's no more than $config->mailerBatchLimit mails processed in a run
        while ($eq->fetch()) {
            // if ( ( $mailsProcessed % 100 ) == 0 ) {
            // CRM_Utils_System::xMemory( "$mailsProcessed: " );
            // }

            if ($config->mailerBatchLimit > 0 and $mailsProcessed >= $config->mailerBatchLimit ) {
               return false;
            }
            $mailsProcessed++;
            
            /* Compose the mailing */
            $recipient = null;
            $message =& $mailing->compose(   $this->id, $eq->id, $eq->hash,
                                             $eq->contact_id, $eq->email,
                                             $recipient);
            
            /* Send the mailing */
            $body    =& $message->get();
            $headers =& $message->headers();
            
            /* TODO: when we separate the content generator from the delivery
             * engine, maybe we should dump the messages into a table */
            
            PEAR::setErrorHandling( PEAR_ERROR_CALLBACK,
                                    array('CRM_Mailing_BAO_Mailing', 
                                          'catchSMTP'));
            $result = $mailer->send($recipient, $headers, $body, $this->id);
            CRM_Core_Error::setCallback();
            
            $params = array('event_queue_id' => $eq->id,
                            'job_id' => $this->id,
                            'hash' => $eq->hash);
            
            if (is_a($result, 'PEAR_Error')) {
                /* Register the bounce event */
                require_once 'CRM/Mailing/BAO/BouncePattern.php';
                require_once 'CRM/Mailing/Event/BAO/Bounce.php';
                $params = array_merge($params, 
                CRM_Mailing_BAO_BouncePattern::match($result->getMessage()));
                CRM_Mailing_Event_BAO_Bounce::create($params);
            } else {
                /* Register the delivery event */
                CRM_Mailing_Event_BAO_Delivered::create($params);
            }
            
            // add activity histroy record for every mail that is send
            $jobDate = new CRM_Mailing_BAO_Job();
            $jobDate->mailing_id = $this->mailing_id;
            $jobDate->find();
            while( $jobDate->fetch() ) {
                $job_date =  CRM_Utils_Date::isoToMysql($jobDate->scheduled_date);
                $activityHistory = array('entity_table'     => 'civicrm_contact',
                                         'entity_id'        => $eq->contact_id,
                                         'activity_type'    => 'Email Sent',
                                         'module'           => 'CiviMail',
                                         'callback'         => 'CRM_Mailing_BAO_Mailing::showEmailDetails',
                                         'activity_id'      => $this->mailing_id,
                                         'activity_summary' => $mailing->subject,
                                         'activity_date'    => $job_date
                                         );
            }
            
            if ( is_a( crm_create_activity_history($activityHistory), 'CRM_Core_Error' ) ) {
                return false;
            }
            
            unset( $result );
        }
        return true;
    }
    
    /**
     * cancel a mailing
     *
     * @param int $mailingId  the id of the mailing to be canceled
     * @static
     */
    public static function cancel($mailingId) {
        $job =& new CRM_Mailing_BAO_Job();
        $job->mailing_id = $mailingId;
        if ($job->find(true) and in_array($job->status, array('Scheduled', 'Running', 'Paused'))) {
            // fix MySQL dates...
            $job->scheduled_date = CRM_Utils_Date::isoToMysql($job->scheduled_date);
            $job->start_date     = CRM_Utils_Date::isoToMysql($job->start_date);
            $job->end_date       = CRM_Utils_Date::isoToMysql($job->end_date);
            $job->status         = 'Canceled';
            $job->save();
            CRM_Core_Session::setStatus(ts('The mailing has been canceled.'));
        }
    }


    /**
     * Return a translated status enum string
     *
     * @param string $status        The status enum
     * @return string               The translated version
     * @access public
     * @static
     */
    public static function status($status) {
        static $translation = null;

        if (empty($translation)) {
            $translation = array(
                'Scheduled' =>  ts('Scheduled'),
                'Running'   =>  ts('Running'),
                'Complete'  =>  ts('Complete'),
                'Paused'    =>  ts('Paused'),
                'Canceled'  =>  ts('Canceled'),
            );
        }
        return CRM_Utils_Array::value($status, $translation, ts('Unknown'));
    }
}

?>
