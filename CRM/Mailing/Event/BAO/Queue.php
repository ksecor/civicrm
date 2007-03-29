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

require_once 'CRM/Mailing/Event/DAO/Queue.php';
require_once 'CRM/Mailing/BAO/Job.php';
require_once 'CRM/Mailing/BAO/Mailing.php';

require_once 'CRM/Contact/BAO/Contact.php';

class CRM_Mailing_Event_BAO_Queue extends CRM_Mailing_Event_DAO_Queue {

    /**
     * class constructor
     */
    function __construct( ) {
        parent::__construct( );
    }

    /**
     * Queue a new recipient
     *
     * @param array     The values of the new EventQueue
     * @return object   The new EventQueue
     * @access public
     * @static
     */
    public static function &create(&$params) {
        $eq =& new CRM_Mailing_Event_BAO_Queue();
        $eq->copyValues($params);
        $eq->hash = self::hash($params);
        $eq->save();
        return $eq;
    }

    /**
     * Create a security hash from the job, email and contact ids
     *
     * @param array     The ids to be hashed
     * @return int      The hash
     * @access public
     * @static
     */
    public static function hash($params) {
        $jobId      = $params['job_id'];
        $emailId    = $params['email_id'];
        $contactId  = $params['contact_id'];

        return sha1("{$jobId}:{$emailId}:{$contactId}:" . time());
    }


    /**
     * Verify that a queue event exists with the specified id/job id/hash
     *
     * @param int $job_id       The job ID of the event to find
     * @param int $queue_id     The Queue Event ID to find
     * @param string $hash      The hash to validate against
     * @return object|null      The queue event if verified, or null
     * @access public
     * @static
     */
    public static function &verify($job_id, $queue_id, $hash) {
        $q =& new CRM_Mailing_Event_BAO_Queue();
        if (!empty($job_id) && !empty($queue_id) && !empty($hash)) {
            $q->id = $queue_id;
            $q->job_id = $job_id;
            $q->hash = $hash;
            if ($q->find(true)) {
                return $q;
            }
        }
        return null;
    }


    /**
     * Given a queue event ID, find the corresponding email address.
     *
     * @param int $queue_id         The queue event ID
     * @return string               The email address
     * @access public
     * @static
     */
    public static function getEmailAddress($queue_id) {
        $email = CRM_Core_BAO_Email::getTableName();
        $eq = self::getTableName();
        $query = "  SELECT      $email.email as email 
                    FROM        $email 
                    INNER JOIN  $eq 
                    ON          $eq.email_id = $email.id 
                    WHERE       $eq.id = " 
                                . CRM_Utils_Type::rule($queue_id, 'Integer');

        $q =& new CRM_Mailing_Event_BAO_Queue();
        $q->query($query);
        if (! $q->fetch()) {
            return null;
        }

        return $q->email;
    }

    /**
     * Count up events given a mailing id and optional job id
     *
     * @param int $mailing_id       ID of the mailing to count
     * @param int $job_id           Optional ID of a job to limit results
     * @return int                  Number of matching events
     * @access public
     * @static
     */
    public static function getTotalCount($mailing_id, $job_id = null) {
        $dao =& new CRM_Core_DAO();

        $queue      = self::getTableName();
        $mailing    = CRM_Mailing_BAO_Mailing::getTableName();
        $job        = CRM_Mailing_BAO_Job::getTableName();

        $dao->query("
            SELECT      COUNT(*) as queued
            FROM        $queue
            INNER JOIN  $job
                    ON  $queue.job_id = $job.id
            INNER JOIN  $mailing
                    ON  $job.mailing_id = $mailing.id
            WHERE       $mailing.id = " 
            . CRM_Utils_Type::escape($mailing_id, 'Integer') 
            . ($job_id ? " AND $job.id = " . CRM_Utils_Type::escape($job_id,
            'Integer') : ''));

        $dao->fetch();
        return $dao->queued;
    }


    /**
     * Get rows for the event browser
     *
     * @param int $mailing_id       ID of the mailing
     * @param int $job_id           optional ID of the job
     * @param int $offset           Offset
     * @param int $rowCount         Number of rows
     * @param array $sort           sort array
     * @return array                Result set
     * @access public
     * @static
     */
    public static function &getRows($mailing_id, $job_id = null, $offset = null,
                                    $rowCount = null, $sort = null) {
        $dao =& new CRM_Core_Dao();
        
        $queue      = self::getTableName();
        $mailing    = CRM_Mailing_BAO_Mailing::getTableName();
        $job        = CRM_Mailing_BAO_Job::getTableName();
        $contact    = CRM_Contact_BAO_Contact::getTableName();
        $email      = CRM_Core_BAO_Email::getTableName();

        $query =    "
            SELECT      $contact.display_name as display_name,
                        $contact.id as contact_id,
                        $email.email as email,
                        $job.start_date as date
            FROM        $contact
            INNER JOIN  $queue
                    ON  $queue.contact_id = $contact.id
            INNER JOIN  $email
                    ON  $queue.email_id = $email.id
            INNER JOIN  $job
                    ON  $queue.job_id = $job.id
            INNER JOIN  $mailing
                    ON  $job.mailing_id = $mailing.id
            WHERE       $mailing.id = " 
            . CRM_Utils_Type::escape($mailing_id, 'Integer');
    
        if (!empty($job_id)) {
            $query .= " AND $job.id = " 
                    . CRM_Utils_Type::escape($job_id, 'Integer');
        }

        $query .= " ORDER BY $contact.sort_name, $job.start_date DESC ";

        if ($offset) {
            $query .= ' LIMIT ' 
                    . CRM_Utils_Type::escape($offset, 'Integer') . ', ' 
                    . CRM_Utils_Type::escape($rowCount, 'Integer');
        }

        $dao->query($query);
        
        $results = array();

        while ($dao->fetch()) {
            $url = CRM_Utils_System::url('civicrm/contact/view',
                                "reset=1&cid={$dao->contact_id}");
            $results[] = array(
                'name'      => "<a href=\"$url\">{$dao->display_name}</a>",
                'email'     => $dao->email,
                'date'      => CRM_Utils_Date::customFormat($dao->date)
            );
        }
        return $results;
    }


    /**
     * Delete a queue event.
     * 
     * This function will delete entry in civicrm_mailing_event_queue
     * table. (Prior to 1.6 version it was deleting on basis of
     * mail_id only. Now it can be based on any of mail_id, job_id or contact_id).
     *
     * @param int $id        value for the job_id or email_id or contact_id
     * @param int $field     name of the field '$id' belongs to.
     * 
     * @return void
     * @access public
     * @static
     */
    public static function deleteEventQueue( $id , $field='email' ) {
        $dao =& new CRM_Mailing_Event_BAO_Queue();
        eval('$dao->' . $field . '_id = $id;');
        $dao->find();
        
        while ($dao->fetch()) {
            foreach (array('Bounce', 'Delivered', 'Forward', 'Opened', 'Reply',
                'TrackableURLOpen', 'Unsubscribe') as $event) {
                require_once "CRM/Mailing/Event/BAO/{$event}.php";
                eval('$object =& new CRM_Mailing_Event_BAO_' . $event . '();');
                $object->event_queue_id = $dao->id;
                $object->delete();
            }
            $object =& new CRM_Mailing_Event_BAO_Forward();
            $object->dest_queue_id = $dao->id;
            $object->delete();
        }
        
        $dao->reset();
        eval('$dao->' . $field . '_id = $id;');
        $dao->delete( );
    }
    
    /**
     * Get a domain object given a queue event
     * 
     * @param int $queue_id     The ID of the queue event
     * @return object $domain   The domain owning the event
     * @access public
     * @static
     */
    public static function &getDomain($queue_id) {
        $dao =& new CRM_Core_Dao();
        
        $queue      = self::getTableName();
        $job        = CRM_Mailing_BAO_Job::getTableName();
        $mailing    = CRM_Mailing_BAO_Mailing::getTableName();
        
        $dao->query("SELECT         $mailing.domain_id as domain_id
                        FROM        $mailing
                        INNER JOIN  $job 
                                ON  $job.mailing_id = $mailing.id
                        INNER JOIN  $queue
                                ON  $queue.job_id = $job.id
                        WHERE       $queue.id = " 
                                . CRM_Utils_Type::escape($queue_id, 'Integer'));

        $dao->fetch();
        if (empty($dao->domain_id)) {
            return null;
        }
        
        require_once 'CRM/Core/BAO/Domain.php';
        return CRM_Core_BAO_Domain::getDomainById($dao->domain_id);
    }


    /**
     * Get the mailing object for this queue event instance
     * 
     * @param
     * @return object           Mailing BAO
     * @access public
     */
    public function &getMailing() {
        $mailing    =& new CRM_Mailing_BAO_Mailing();
        $jobs       = CRM_Mailing_BAO_Job::getTableName();
        $mailings   = CRM_Mailing_BAO_Mailing::getTableName();
        $queue      = self::getTableName();

        $mailing->query("
                SELECT      $mailings.*
                FROM        $mailings
                INNER JOIN  $jobs
                        ON  $jobs.mailing_id = $mailings.id
                INNER JOIN  $queue
                        ON  $queue.job_id = $jobs.id
                WHERE       $queue.id = {$this->id}");
        $mailing->fetch();
        return $mailing;
    }
}

?>
