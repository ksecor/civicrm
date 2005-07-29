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

require_once 'Mail/mime.php';

class CRM_Mailing_BAO_Mailing extends CRM_Mailing_DAO_Mailing {

    /**
     * The header associated with this mailing
     */
    private $header = null;

    /**
     * The footer associated with this mailing
     */
    private $footer = null;


    /**
     * The HTML content of the message
     */
    private $html = null;

    /**
     * The text content of the message
     */
    private $text = null;

    /**
     * Cached BAO for the domain
     */
    private $_domain = null;


    /**
     * class constructor
     */
    function __construct( ) {
        parent::__construct( );
    }

    /**
     * Find all intended recipients of a mailing
     *
     * @param int $job_id       Job ID
     * @return array            Tuples of Contact IDs and Email IDs
     */
    function &getRecipients($job_id) {
        $mailingGroup =& new CRM_Mailing_DAO_Group();
        
        $mailing    = CRM_Mailing_DAO_Mailing::getTableName();
        $mg         = CRM_Mailing_DAO_Group::getTableName();
        $eq         = CRM_Mailing_Event_DAO_Queue::getTableName();
        $ed         = CRM_Mailing_Event_DAO_Delivered::getTableName();
        $eb         = CRM_Mailing_Event_DAO_Bounce::getTableName();
        $job        = CRM_Mailing_DAO_Job::getTableName();
        
        $email      = CRM_Core_DAO_Email::getTableName();
        $contact    = CRM_Contact_DAO_Contact::getTableName();
        $location   = CRM_Core_DAO_Location::getTableName();
        $group      = CRM_Contact_DAO_Group::getTableName();
        $g2contact  = CRM_Contact_DAO_GroupContact::getTableName();
      
        /* Create a temp table for contact exclusion */
        $mailingGroup->query(
            "CREATE TEMPORARY TABLE X_$job_id (contact_id int) TYPE=HEAP"
        );
        $mailingGroup->find();

        /* Add all the members of groups excluded from this mailing to the temp
         * table */
        $excludeSubGroup =
                    "INSERT INTO        X_$job_id (contact_id)
                    SELECT DISTINCT     $g2contact.contact_id
                    FROM                $g2contact
                    INNER JOIN          $mg
                            ON          $g2contact.group_id = $mg.entity_id
                    WHERE
                                        $mg.mailing_id = " . $this->id . "
                        AND             $mg.entity_table = '$group'
                        AND             $g2contact.status = 'In'
                        AND             $mg.group_type = 'Exclude'";
        $mailingGroup->query($excludeSubGroup);
        $mailingGroup->find();
        
        /* Add all the (intended) recipients of an excluded prior mailing to
         * the temp table */
        $excludeSubMailing = 
                    "INSERT INTO        X_$job_id (contact_id)
                    SELECT DISTINCT     $eq.contact_id
                    FROM                $eq
                    INNER JOIN          $job
                            ON          $eq.job_id = $job.id
                    INNER JOIN          $mg
                            ON          $job.mailing_id = $mg.entity_id
                    WHERE
                                        $mg.mailing_id = " . $this->id . "
                        AND             $mg.entity_table = '$mailing'
                        AND             $mg.group_type = 'Exclude'";
        $mailingGroup->query($excludeSubMailing);
        $mailingGroup->find();
        
        /* Add all the succesful deliveries of this mailing (but any job/retry)
         * to the exclude temp table */
        $excludeRetry =
                    "INSERT INTO        X_$job_id (contact_id)
                    SELECT DISTINCT     $eq.contact_id
                    FROM                $eq
                    INNER JOIN          $job
                            ON          $eq.job_id = $job.id
                    INNER JOIN          $ed
                            ON          $eq.id = $ed.event_queue_id
                    LEFT JOIN           $eb
                            ON          $eq.id = $eb.event_queue_id
                    WHERE
                                        $job.mailing_id = " . $this->id . "
                    HAVING              $eb.id IS null";
        $mailingGroup->query($excludeRetry);
        $mailingGroup->find();

        $mailingGroup->query(
                    "SELECT             $group.saved_search_id as saved_search_id
                    FROM                $group
                    INNER JOIN          $mg
                            ON          $mg.entity_id = $group.id
                    WHERE               $mg.entity_table = '$group'
                        AND             $mg.group_type = 'Exclude'
                        AND             $mg.mailing_id = " . $this->id . "
                        AND             $group.saved_search_id <> null");
        $mailingGroup->find();
        $ss =& new CRM_Contact_BAO_SavedSearch();
        
        while ($mailingGroup->fetch()) {
            /* run the saved search query and dump result contacts into the temp
             * table */
            $tables = array($contact);
            $from = CRM_Contact_BAO_Contact::fromClause($tables);
            $where =
            CRM_Contact_BAO_SavedSearch::whereClause(
                $mailingGroup->saved_search_id, $tables);
            $ss->query(
                    "INSERT INTO        X_$job_id (contact id)
                    SELECT              $contact.id
                    FROM                $from
                    WHERE               $where");
            $ss->find();
            $ss->reset();
        }
        
        /* Get all the group contacts we want to include */
        
        /* Get the group contacts, but only those which are not in the temp
         * table */
         /* Get the emails with no override */
        $queryGroupPrimary = 
                    "SELECT DISTINCT    $email.id as email_id,
                                        $contact.id as contact_id,
                    FROM                $email
                    INNER JOIN          $location
                            ON          $email.location_id = $location.id
                    INNER JOIN          $contact
                            ON          $location.entity_id = $contact.id
                                AND     $location.entity_table = '$contact'
                    INNER JOIN          $g2contact
                            ON          $contact.id = $g2contact.contact_id
                    INNER JOIN          $mg
                            ON          $g2contact.group_id = $mg.entity_id
                    LEFT JOIN           X_$job_id
                            ON          $contact.id = X_$job_id.contact_id
                    WHERE           
                                        $mg.entity_table = '$group'
                        AND             $mg.group_type = 'Include'
                        AND             $g2contact.status = 'In'
                        AND             $g2contact.location_id IS null
                        AND             $g2contact.email_id IS null
                        AND             $contact.do_not_email = 0
                        AND             $contact.is_subscribed = 1
                        AND             $location.is_primary = 1
                        AND             $email.is_primary = 1
                        AND             $email.bounce_hold = 0
                        AND             $mg.mailing_id = " . $this->id . "
                    HAVING              X_$job_id.contact_id IS null";
                    
        /* Get the emails with only location override */
        $queryGroupLocation = 
                    "SELECT DISTINCT    $email.id as email_id,
                                        $contact.id as contact_id,
                    FROM                $email
                    INNER JOIN          $location
                            ON          $email.location_id = $location.id
                    INNER JOIN          $contact
                            ON          $location.entity_id = $contact.id
                                AND     $location.entity_table = '$contact'
                    INNER JOIN          $g2contact
                            ON          $contact.id = $g2contact.contact_id
                                AND     $location.id = $g2contact.location_id
                    INNER JOIN          $mg
                            ON          $g2contact.group_id = $mg.entity_id
                    LEFT JOIN           X_$job_id
                            ON          $contact.id = X_$job_id.contact_id
                    WHERE           
                                        $mg.entity_table = '$group'
                        AND             $mg.group_type = 'Include'
                        AND             $g2contact.status = 'In'
                        AND             $g2contact.location_id <> null
                        AND             $g2contact.email_id is null
                        AND             $contact.do_not_email = 0
                        AND             $contact.is_subscribed = 1
                        AND             $email.is_primary = 1
                        AND             $email.bounce_hold = 0
                        AND             $mg.mailing_id = " . $this->id . "
                    HAVING              X_$job_id.contact_id IS null";
                    
        /* Get the emails with full override */
        $queryGroupEmail = 
                    "SELECT DISTINCT    $email.id as email_id,
                                        $contact.id as contact_id,
                    FROM                $email
                    INNER JOIN          $g2contact
                            ON          $email.id = $g2contact.email_id
                    INNER JOIN          $contact
                            ON          $contact.id = $g2contact.contact_id
                    INNER JOIN          $mg
                            ON          $g2contact.group_id = $mg.entity_id
                    LEFT JOIN           X_$job_id
                            ON          $contact.id = X_$job_id.contact_id
                    WHERE           
                                        $mg.entity_table = '$group'
                        AND             $mg.group_type = 'Include'
                        AND             $g2contact.status = 'In'
                        AND             $g2contact.location_id <> null
                        AND             $g2contact.email_id <> null
                        AND             $contact.do_not_email = 0
                        AND             $contact.is_subscribed = 1
                        AND             $email.bounce_hold = 0
                        AND             $mg.mailing_id = " . $this->id . "
                    HAVING              X_$job_id.contact_id IS null";
                        
        $queryGroup =   "($queryGroupPrimary) 
                        UNION DISTINCT ($queryGroupLocation) 
                        UNION DISTINCT ($queryGroupEmail)";
                        
        $queryMailing =
                    "SELECT DISTINCT    $email.id as email_id,
                                        $contact.id as contact_id,
                    FROM                $email
                    INNER JOIN          $location
                            ON          $email.location_id = $location.id
                    INNER JOIN          $contact
                            ON          $location.entity_id = $contact.id
                                AND     $location.entity_table = '$contact'
                    INNER JOIN          $eq
                            ON          $eq.contact_id = $contact.id
                    INNER JOIN          $job
                            ON          $eq.job_id = $job.id
                    INNER JOIN          $mg
                            ON          $job.mailing_id = $mg.mailing_id
                    LEFT JOIN           X_$job_id
                            ON          $contact.id = X_$job_id.contact_id
                    WHERE
                                        $mg.entity_table = '$mailing'
                        AND             $mg.group_type = 'Include'
                        
                        AND             $contact.do_not_email = 0
                        AND             $contact.is_subscribed = 1
                        AND             $location.is_primary = 1
                        AND             $email.is_primary = 1
                        AND             $email.bounce_hold = 0
                        AND             $mg.mailing_id = " . $this->id . "
                    HAVING              X_$job_id IS null";

        $query = "($queryGroup) UNION DISTINCT ($queryMailing)";

        /* Construct the saved-search queries */
        $mailingGroup->query(
                    "SELECT             $group.saved_search_id as saved_search_id
                    FROM                $group
                    INNER JOIN          $mg
                            ON          $mg.entity_id = $group.id
                    WHERE               $mg.entity_table = '$group'
                        AND             $mg.group_type = 'Include'
                        AND             $mg.mailing_id = " . $this->id . "
                        AND             $group.saved_search_id <> null");
        $mailingGroup->find();
        /* FIXME: is it kosher to possibly multiple-inner-join? */
        while ($mailingGroup->fetch()) {
            $tables = array($contact);
            $from = CRM_Contact_BAO_Contact::fromClause($tables);
            $where = CRM_Contact_BAO_SavedSearch::whereClause(
                        $mailingGroup->saved_search_id, $tables);

            $query .=   " 
                        UNION DISTINCT
                        (SELECT         $email.id as email_id,
                                        $contact.id as contact_id 
                        FROM            $from
                        INNER JOIN      $location
                                ON      $location.entity_id = $contact.id
                                    AND $location.entity_table = '$contact'
                        INNER JOIN      $email
                                ON      $email.location_id = $location.id
                        LEFT JOIN       X_$job_id
                                ON      $contact.id = X_$job_id.contact_id
                        WHERE           
                                        $contact.do_not_email = 0
                            AND         $contact.is_subscribed = 1
                            AND         $location.is_primary = 1
                            AND         $email.is_primary = 1
                            AND         $email.bounce_hold = 0
                            AND         $where
                        HAVING          X_$job_id IS null) ";
        }
        
        $results = array();

        $mailingGroup->query($query);
        $mailingGroup->find();
    
        while ($mailingGroup->fetch()) {
            $results[] =    
                array(  'email_id'  => $mailingGroup->email_id,
                        'contact_id'=> $mailingGroup->contact_id
                );
        }
        
        /* Delete the temp table */
        $mailingGroup->query("DROP TEMPORARY TABLE X_$job_id");
        
        return $results;
    }

    /**
     * Generate an event queue for a retry job (ie the contacts who bounced)
     *
     * @param int $job_id       The job marked retry
     * @return array            Tuples of Email ID and Contact ID
     * @access public
     */
    public function retryRecipients($job_id) {
        $eq =& new CRM_Mailing_Event_BAO_Queue();
        $job        = CRM_Mailing_BAO_Job::getTableName();
        $queue      = CRM_Mailing_Event_BAO_Queue::getTableName();
        $bounce     = CRM_Mailing_Event_BAO_Bounce::getTableName();
        $email      = CRM_Core_BAO_Email::getTableName();
        $contact    = CRM_Contact_BAO_Contact::getTableName();
        
        $query = 
                "SELECT             email_id, contact_id
                FROM                $queue
                INNER JOIN          $job
                        ON          $queue.job_id = $job.id
                INNER JOIN          $bounce
                        ON          $bounce.event_queue_id = $queue.id
                INNER JOIN          $contact
                        ON          $queue.contact_id = $contact.id
                INNER JOIN          $email
                        ON          $queue.email_id = $email.id
                WHERE               
                                    $job.mailing_id = " . $this->id . "
                    AND             $job.id <> $job_id
                    AND             $contact.do_not_email = 0
                    AND             $contact.is_subscribed = 1
                    AND             $email.bounce_hold = 0
                GROUP BY            $queue.email_id";

        $eq->query();
        $eq->find();
        
        $results = array();
        while ($eq->fetch()) {
            $results[] = array(
                'email_id' => $eq.email_id,
                'contact_id' => $eq.contact_id,
            );
        }

        return $results;
    }

    /**
     * Retrieve the header and footer for this mailing
     *
     * @param void
     * @return void
     * @access private
     */
    private function getHeaderFooter() {
        $this->header =& new CRM_Mailing_BAO_Component();
        $this->header->id = $this->header_id;
        $this->header->find(true);
        
        $this->footer =& new CRM_Mailing_BAO_Component();
        $this->footer->id = $this->footer_id;
        $this->footer->find(true);
    }


    /**
     * Compose a message
     *
     * @param int $job_id           ID of the Job associated with this message
     * @param int $event_queue_id   ID of the EventQueue
     * @param string $hash          Hash of the EventQueue
     * @param string $contactId     ID of the Contact
     * @param string $email         Destination address
     * @param string $recipient     To: of the recipient
     * @param boolean $test         Is this mailing a test?
     * @return object               The mail object
     * @access public
     */
    public function &compose($job_id, $event_queue_id, $hash, $contactId, 
                                $email, &$recipient, $test = false) 
    {
        if ($test) {
            $job_id = 'JOB';
            $event_queue_id = 'QUEUE';
            $hash = 'HASH';
        }
        if ($this->_domain == null) {
            $this->_domain =& 
                CRM_Core_BAO_Domain::getDomainByID($this->domain_id);
        }

        /**
         * Inbound VERP keys:
         *  reply:          user replied to mailing
         *  owner:          bounce
         *  unsubscribe:    contact opts out of all target lists for the mailing
         *  opt-out:        contact unsubscribes from the domain
         */
        foreach (array('reply', 'owner', 'unsubscribe', 'optOut') as $key) 
        {
            $verp[$key] = implode('.', 
                        array(
                            $key, 
                            $job_id, 
                            $event_queue_id,
                            $hash
                        )
                    ) . '@' . $this->_domain->email_domain;
        }
        $headers = array(
            'Subject'   => $this->subject,
            'From'      => $this->from_name . ' <' . $this->from_email . '>',
            'Reply-To'  => CRM_Utils_Verp::encode($verp['reply'], $email),
            'Return-path' => CRM_Utils_Verp::encode($verp['owner'], $email),
        );

        if ($this->html == null || $this->text == null) {
            $this->getHeaderFooter();
        
            $this->html = $this->header->body_html . '<br />'
                        . $this->body_html . '<br />'
                        . $this->footer->body_html;
            
            $this->html = CRM_Utils_Token::replaceDomainTokens($this->html,
                            $this->_domain, true);
            $this->html = CRM_Utils_Token::replaceMailingTokens($this->html,
                            $this, true);
            
            $this->text = $this->header->body_text . "\n"
                        . $this->body_text . "\n"
                        . $this->footer->body_text;
            
            $this->text = CRM_Utils_Token::replaceDomainTokens($this->text,
                            $this->_domain, false);
            
            $this->text = CRM_Utils_Token::replaceMailingTokens($this->text,
                            $this, true);
        }

        $params = array('contact_id' => $contactId, 'id' => $contactId);
        $contact = array();
        $ids    = array();
        CRM_Contact_BAO_Contact::retrieve($params, $contact, $ids);

        $message =& new Mail_Mime("\n");

        /* Do contact-specific token replacement in text mode, and add to the
         * message if necessary */
        if ($test || $contact['preferred_mail_format'] == 'Text' ||
            $contact['preferred_mail_format'] == 'Both') 
        {
            $text = CRM_Utils_Token::replaceContactTokens(
                                        $this->text, $contact, false);
            $text = CRM_Utils_token::replaceActionTokens( $text,
                                        $verp, false);
                                        
            /* TODO: trackable URL construction */
            
            $message->setTxtBody($text);
        }



        /* Do contact-specific token replacement in html mode, and add to the
         * message if necessary */
        if ($test || $contact['preferred_mail_format'] == 'HTML' ||
            $contact['preferred_mail_format'] == 'Both')
        {
            $html = CRM_Utils_Token::replaceContactTokens(
                                        $this->html, $contact, true);
            $html = CRM_Utils_token::replaceActionTokens( $html, $verp, true);
            
            /* TODO: trackable URL construction */
            /* TODO: insert html for open tracking */
            $message->setHTMLBody($html);
        }
        


        $message->get();
        $message->headers($headers);

        $recipient = $contact['display_name'] . " <$email>";
        
        return $message;
    }

    /**
     * Return a list of group names for this mailing.  Does not work with
     * prior-mailing targets.
     *
     * @param none
     * @return array        Names of groups receiving this mailing
     * @access public
     */
    public function &getGroupNames() {
        if (! isset($this->id)) {
            return array();
        }
        $mg =& new CRM_Mailing_DAO_Group();
        $mgname = CRM_Mailing_DAO_Group::getTableName();
        $group = CRM_Contact_BAO_Group::getTableName();

        $mg->query("SELECT      $group.name as name FROM $mgtable 
                    INNER JOIN  $group ON $mgtable.entity_id = $group.id
                    WHERE       $mgtable.mailing_id = " . $this->id . "
                        AND     $mgtable.entity_table = $group
                        AND     $mgtable.group_type = 'Include'
                    ORDER BY    $group.name");
        $mg->find();

        $groups = array();
        while ($mg->fetch()) {
            $groups[] = $mg->name;
        }
        return $groups;
    }

    public static function catchSMTP($obj) {
        return $obj;
    }

    public static function create(&$params) {
        CRM_Core_DAO::transaction('BEGIN');
        $mailing =& new CRM_Mailing_BAO_Mailing();       

        $mailing->domain_id     = $params['domain_id'];
        $mailing->header_id     = $params['header_id'];
        $mailing->footer_id     = $params['footer_id'];
        $mailing->name          = $params['mailing_name'];
        $mailing->from_name     = $params['from_name'];
        $mailing->from_email    = $params['from_email'];
        if (! isset($params['replyto_email'])) {
            $mailing->replyto_email = $params['from_email'];
        } else  {
            $mailing->replyto_email = $params['replyto_email'];
        }
        $mailing->subject       = $params['subject'];
        $mailing->body_text     = file_get_contents($params['textFile']);
        $mailing->body_html     = file_get_contents($params['htmlFile']);
        $mailing->is_template   = $params['template'];
        $mailing->is_completed  = false;
        $mailing->save();

        /* Create the job record */
        $job =& new CRM_Mailing_BAO_Job();
        $job->mailing_id = $mailing->id;
        $job->status = 'Scheduled';
        $job->is_retry = false;
        if ($params['now']) {
            $job->scheduled_date = date('Ymd H:i:s');
        } else {
            $job->scheduled_date =
                CRM_Utils_Date::format($params['start_date']);
        }
        $job->save();
        
        /* Create the mailing group record */
        $mg =& new CRM_Mailing_DAO_Group();
        foreach (array('groups', 'mailings') as $entity) {
            foreach (array('include', 'exclude') as $type) {
                if (is_array($params[$entity][$type])) {
                    foreach ($params[$entity][$type] as $entityId) {
                        $mg->reset();
                        $mg->mailing_id = $mailing->id;
                        $mg->entity_table   = ($entity == 'groups') 
                                            ? CRM_Contact_BAO_Group::getTableName()
                                            : CRM_Mailing_BAO_Mailing::getTableName();
                        $mg->entity_id = $entityId;
                        $mg->group_type = $type;
                        $mg->save();
                    }
                }
            }
        }
        CRM_Core_DAO::transaction('COMMIT');
        return $mailing;
    }
}

?>
