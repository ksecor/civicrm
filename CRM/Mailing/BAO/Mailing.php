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
        
        $mailing    = CRM_Mailing_BAO_Mailing::getTableName();
        $job        = CRM_Mailing_BAO_Job::getTableName();
        $mg         = CRM_Mailing_DAO_Group::getTableName();
        $eq         = CRM_Mailing_Event_DAO_Queue::getTableName();
        $ed         = CRM_Mailing_Event_DAO_Delivered::getTableName();
        $eb         = CRM_Mailing_Event_DAO_Bounce::getTableName();
        
        $email      = CRM_Core_DAO_Email::getTableName();
        $contact    = CRM_Contact_DAO_Contact::getTableName();
        $location   = CRM_Core_DAO_Location::getTableName();
        $group      = CRM_Contact_DAO_Group::getTableName();
        $g2contact  = CRM_Contact_DAO_GroupContact::getTableName();
      
        /* Create a temp table for contact exclusion */
        $mailingGroup->query(
            "CREATE TEMPORARY TABLE X_$job_id 
            (contact_id int primary key) 
            TYPE=HEAP"
        );

        /* Add all the members of groups excluded from this mailing to the temp
         * table */
        $excludeSubGroup =
                    "INSERT INTO        X_$job_id (contact_id)
                    SELECT              $g2contact.contact_id
                    FROM                $g2contact
                    INNER JOIN          $mg
                            ON          $g2contact.group_id = $mg.entity_id
                    WHERE
                                        $mg.mailing_id = {$this->id}
                        AND             $mg.entity_table = '$group'
                        AND             $g2contact.status = 'Added'
                        AND             $mg.group_type = 'Exclude'";
        $mailingGroup->query($excludeSubGroup);
        
        /* Add all the (intended) recipients of an excluded prior mailing to
         * the temp table */
        $excludeSubMailing = 
                    "INSERT IGNORE INTO X_$job_id (contact_id)
                    SELECT              $eq.contact_id
                    FROM                $eq
                    INNER JOIN          $job
                            ON          $eq.job_id = $job.id
                    INNER JOIN          $mg
                            ON          $job.mailing_id = $mg.entity_id
                    WHERE
                                        $mg.mailing_id = {$this->id}
                        AND             $mg.entity_table = '$mailing'
                        AND             $mg.group_type = 'Exclude'";
        $mailingGroup->query($excludeSubMailing);
        
        /* Add all the succesful deliveries of this mailing (but any job/retry)
         * to the exclude temp table */
        $excludeRetry =
                    "INSERT IGNORE INTO X_$job_id (contact_id)
                    SELECT              $eq.contact_id
                    FROM                $eq
                    INNER JOIN          $job
                            ON          $eq.job_id = $job.id
                    INNER JOIN          $ed
                            ON          $eq.id = $ed.event_queue_id
                    LEFT JOIN           $eb
                            ON          $eq.id = $eb.event_queue_id
                    WHERE
                                        $job.mailing_id = {$this->id}
                        AND             $eb.id IS null";
        $mailingGroup->query($excludeRetry);
        
        $ss =& new CRM_Core_DAO();
        $ss->query(
                "SELECT             $group.saved_search_id as saved_search_id
                FROM                $group
                INNER JOIN          $mg
                        ON          $mg.entity_id = $group.id
                WHERE               $mg.entity_table = '$group'
                    AND             $mg.group_type = 'Exclude'
                    AND             $mg.mailing_id = {$this->id}
                    AND             $group.saved_search_id IS NOT null");
        
        while ($ss->fetch()) {
            /* run the saved search query and dump result contacts into the temp
             * table */
            $tables = array($contact => 1);
            $where = CRM_Contact_BAO_SavedSearch::whereClause(
                $ss->saved_search_id, $tables);
            $from = CRM_Contact_BAO_Contact::fromClause($tables);
            $mailingGroup->query(
                    "INSERT IGNORE INTO X_$job_id (contact id)
                    SELECT              $contact.id
                                    $from
                    WHERE               $where");
        }

        /* Get all the group contacts we want to include */
        
        $mailingGroup->query(
            "CREATE TEMPORARY TABLE I_$job_id 
            (email_id int, contact_id int primary key)
            TYPE=HEAP"
        );
        
        /* Get the group contacts, but only those which are not in the
         * exclusion temp table */

        /* Get the emails with no override */
        $mailingGroup->query(
                    "INSERT INTO        I_$job_id (email_id, contact_id)
                    SELECT DISTINCT     $email.id as email_id,
                                        $contact.id as contact_id
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
                                AND     $mg.entity_table = '$group'
                    LEFT JOIN           X_$job_id
                            ON          $contact.id = X_$job_id.contact_id
                    WHERE           
                                        $mg.group_type = 'Include'
                        AND             $g2contact.status = 'Added'
                        AND             $g2contact.location_id IS null
                        AND             $g2contact.email_id IS null
                        AND             $contact.do_not_email = 0
                        AND             $contact.is_opt_out = 0
                        AND             $location.is_primary = 1
                        AND             $email.is_primary = 1
                        AND             $email.on_hold = 0
                        AND             $mg.mailing_id = {$this->id}
                        AND             X_$job_id.contact_id IS null");

        /* Query prior mailings */
        $mailingGroup->query(
                    "REPLACE INTO       I_$job_id (email_id, contact_id)
                    SELECT DISTINCT     $email.id as email_id,
                                        $contact.id as contact_id
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
                        AND             $contact.is_opt_out = 0
                        AND             $location.is_primary = 1
                        AND             $email.is_primary = 1
                        AND             $email.on_hold = 0
                        AND             $mg.mailing_id = {$this->id}
                        AND             X_$job_id.contact_id IS null");

        /* Construct the saved-search queries */
        $ss->query("SELECT          $group.saved_search_id as saved_search_id
                    FROM            $group
                    INNER JOIN      $mg
                            ON      $mg.entity_id = $group.id
                                AND $mg.entity_table = '$group'
                    WHERE               
                                    $mg.group_type = 'Include'
                        AND         $mg.mailing_id = {$this->id}
                        AND         $group.saved_search_id IS NOT null");
        while ($ss->fetch()) {
            $tables = array($contact => 1, $location => 1, $email => 1);
            $where = CRM_Contact_BAO_SavedSearch::whereClause(
                                    $ss->saved_search_id, $tables);
            $from = CRM_Contact_BAO_Contact::fromClause($tables);
            $ssq = "INSERT IGNORE INTO  I_$job_id (email_id, contact_id)
                    SELECT DISTINCT     $email.id as email_id,
                                        $contact.id as contact_id 
                    $from
                    LEFT JOIN           X_$job_id
                            ON          $contact.id = X_$job_id.contact_id
                    WHERE           
                                        $contact.do_not_email = 0
                        AND             $contact.is_opt_out = 0
                        AND             $location.is_primary = 1
                        AND             $email.is_primary = 1
                        AND             $email.on_hold = 0
                        AND             $where
                        AND             X_$job_id.contact_id IS null ";
            $mailingGroup->query($ssq);
        }
        
        /* Get the emails with only location override */
        $mailingGroup->query(
                    "REPLACE INTO       I_$job_id (email_id, contact_id)
                    SELECT DISTINCT     $email.id as local_email_id,
                                        $contact.id as contact_id
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
                        AND             $g2contact.status = 'Added'
                        AND             $g2contact.location_id IS NOT null
                        AND             $g2contact.email_id is null
                        AND             $contact.do_not_email = 0
                        AND             $contact.is_opt_out = 0
                        AND             $email.is_primary = 1
                        AND             $email.on_hold = 0
                        AND             $mg.mailing_id = {$this->id}
                        AND             X_$job_id.contact_id IS null");
                    
        /* Get the emails with full override */
        $mailingGroup->query(
                    "REPLACE INTO       I_$job_id (email_id, contact_id)
                    SELECT DISTINCT     $email.id as email_id,
                                        $contact.id as contact_id
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
                        AND             $g2contact.status = 'Added'
                        AND             $g2contact.location_id IS NOT null
                        AND             $g2contact.email_id IS NOT null
                        AND             $contact.do_not_email = 0
                        AND             $contact.is_opt_out = 0
                        AND             $email.on_hold = 0
                        AND             $mg.mailing_id = {$this->id}
                        AND             X_$job_id.contact_id IS null");
                        
        $results = array();

        $mailingGroup->reset();
        $mailingGroup->query("  SELECT * 
                                FROM I_$job_id 
                                ORDER BY contact_id, email_id");
        
        while ($mailingGroup->fetch()) {
            $results[] =    
                array(  'email_id'  => $mailingGroup->email_id,
                        'contact_id'=> $mailingGroup->contact_id
                );
        }
        
        /* Delete the temp table */
        $mailingGroup->query("DROP TEMPORARY TABLE X_$job_id");
        $mailingGroup->query("DROP TEMPORARY TABLE I_$job_id");

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
        $eq         =& new CRM_Mailing_Event_BAO_Queue();
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
                                    $job.mailing_id = {$this->id}
                    AND             $job.id <> $job_id
                    AND             $contact.do_not_email = 0
                    AND             $contact.is_opt_out = 0
                    AND             $email.on_hold = 0
                GROUP BY            $queue.email_id";

        $eq->query($query);
        
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
         *  bounce:         email address bounced
         *  unsubscribe:    contact opts out of all target lists for the mailing
         *  opt-out:        contact unsubscribes from the domain
         */
        foreach (array('reply', 'bounce', 'unsubscribe', 'optOut') as $key) 
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
            'From'      => "\"{$this->from_name}\" <{$this->from_email}>",
            'Reply-To'  => CRM_Utils_Verp::encode($verp['reply'], $email),
            'Return-path' => CRM_Utils_Verp::encode($verp['bounce'], $email),
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
        
        if (!$test && $this->url_tracking) {
            CRM_Mailing_BAO_TrackableURL::scan_and_replace($this->html,
                                $this->id, $event_queue_id);
            CRM_Mailing_BAO_TrackableURL::scan_and_replace($this->text,
                                $this->id, $event_queue_id);
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
            $text = CRM_Utils_Token::replaceActionTokens( $text,
                                        $verp, false);
                                        
            $message->setTxtBody($text);
        }



        /* Do contact-specific token replacement in html mode, and add to the
         * message if necessary */
        if ($test || $contact['preferred_mail_format'] == 'HTML' ||
            $contact['preferred_mail_format'] == 'Both')
        {
            $html = CRM_Utils_Token::replaceContactTokens(
                                        $this->html, $contact, true);
            $html = CRM_Utils_Token::replaceActionTokens( $html, $verp, true);
            
            if ($this->open_tracking) {
                $html .= '<img src="' . CRM_Utils_System::baseURL() .
                "/modules/civicrm/extern/open.php?q=$event_queue_id\" width='1' height='1' alt='' border='0'>";
            }
            $message->setHTMLBody($html);
        }
        
        $recipient = "\"{$contact['display_name']}\" <$email>";
        $headers['To'] = $recipient;

        $message->get();
        $message->headers($headers);
        
        return $message;
    }

    /**
     * Return a list of group names for this mailing.  Does not work with
     * prior-mailing targets.
     *
     * @return array        Names of groups receiving this mailing
     * @access public
     */
    public function &getGroupNames() {
        if (! isset($this->id)) {
            return array();
        }
        $mg =& new CRM_Mailing_DAO_Group();
        $mgtable = CRM_Mailing_DAO_Group::getTableName();
        $group = CRM_Contact_BAO_Group::getTableName();

        $mg->query("SELECT      $group.name as name FROM $mgtable 
                    INNER JOIN  $group ON $mgtable.entity_id = $group.id
                    WHERE       $mgtable.mailing_id = {$this->id}
                        AND     $mgtable.entity_table = '$group'
                        AND     $mgtable.group_type = 'Include'
                    ORDER BY    $group.name");

        $groups = array();
        while ($mg->fetch()) {
            $groups[] = $mg->name;
        }
        return $groups;
    }
    
    /**
     * Error handler to quietly catch otherwise fatal smtp transport errors.
     *
     * @param object $obj       The PEAR_ERROR object
     * @return object $obj
     * @access public
     * @static
     */
    public static function catchSMTP($obj) {
        return $obj;
    }


    /**
     * Construct a new mailing object, along with job and mailing_group
     * objects, from the form values of the create mailing wizard.
     *
     * @params array $params        Form values
     * @return object $mailing      The new mailing object
     * @access public
     * @static
     */
    public static function create(&$params) {
        CRM_Core_DAO::transaction('BEGIN');
        $mailing =& new CRM_Mailing_BAO_Mailing();       

        $mailing->domain_id     = $params['domain_id'];
        $mailing->header_id     = $params['header_id'];
        $mailing->footer_id     = $params['footer_id'];
        $mailing->reply_id      = $params['reply_id'];
        $mailing->unsubscribe_id= $params['unsubscribe_id'];
        $mailing->optout_id     = $params['optout_id'];
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
        $mailing->is_template   = $params['template'] ? true : false;
        $mailing->auto_responder= $params['auto_responder'] ? true : false;
        $mailing->url_tracking  = $params['track_urls'] ? true : false;
        $mailing->open_tracking = $params['track_opens'] ? true : false;
        $mailing->forward_replies = $params['forward_reply'] ? true : false;
        $mailing->is_completed  = false;
        $mailing->save();
        
        if (! $mailing->is_template) {
            /* Create the job record */
            $job =& new CRM_Mailing_BAO_Job();
            $job->mailing_id = $mailing->id;
            $job->status = 'Scheduled';
            $job->is_retry = false;
            if ($params['now']) {
                $job->scheduled_date = date('YmdHis');
            } else {
                $job->scheduled_date =
                    CRM_Utils_Date::format($params['start_date']);
            }
            $job->save();
        }
        
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


    /**
     * Generate a report.  Fetch event count information, mailing data, and job
     * status.
     *
     * @param int $id       The mailing id to report
     * @return array        Associative array of reporting data
     * @access public
     * @static
     */
    public static function &report($id) {
        $mailing_id = CRM_Utils_Type::escape($id, 'Integer');
        
        $mailing =& new CRM_Mailing_BAO_Mailing();
        
        $t = array(
                'mailing'   => self::getTableName(),
                'job'       => CRM_Mailing_BAO_Job::getTableName(),
                'queue'     => CRM_Mailing_Event_BAO_Queue::getTableName(),
                'delivered' => CRM_Mailing_Event_BAO_Delivered::getTableName(),
                'opened'    => CRM_Mailing_Event_BAO_Opened::getTableName(),
                'reply'     => CRM_Mailing_Event_BAO_Reply::getTableName(),
                'unsubscribe'   =>
                            CRM_Mailing_Event_BAO_Unsubscribe::getTableName(),
                'bounce'    => CRM_Mailing_Event_BAO_Bounce::getTableName(),
            );
                
        /* FIXME: put some permissioning in here */
        $mailing->query("
            SELECT          {$t['mailing']}.*,
                            COUNT({$t['queue']}.id) as queue,
                            COUNT({$t['delivered']}.id) as delivered,
                            COUNT({$t['opened']}.id) as opened,
                            COUNT({$t['reply']}.id) as reply,
                            COUNT({$t['unsubscribe']}.id) as unsubscribe,
                            COUNT({$t['bounce']}.id) as bounce
            FROM            {$t['mailing']}
            INNER JOIN      {$t['job']}
                    ON      {$t['job']}.mailing_id = {$t['mailing']}.id
            LEFT JOIN       {$t['queue']}
                    ON      {$t['queue']}.job_id = {$t['job']}.id
            LEFT JOIN       {$t['delivered']}
                    ON      {$t['delivered']}.event_queue_id = {$t['queue']}.id
            LEFT JOIN       {$t['opened']}
                    ON      {$t['opened']}.event_queue_id = {$t['queue']}.id
            LEFT JOIN       {$t['reply']}
                    ON      {$t['reply']}.event_queue_id = {$t['queue']}.id
            LEFT JOIN       {$t['unsubscribe']}
                    ON      {$t['unsubscribe']}.event_queue_id = {$t['queue']}.id
            LEFT JOIN       {$t['bounce']}
                    ON      {$t['bounce']}.event_queue_id = {$t['queue']}.id

            WHERE           {$t['mailing']}.id = $mailing_id
            GROUP BY        {$t['mailing']}.id");
        $mailing->fetch();
        
        $values = array();
        foreach(array('queue', 'delivered', 'opened', 'reply', 'unsubscribe',
        'bounce') + array_keys(self::fields()) as $field) {
            $values[$field] = $mailing->$field;
        }

        return $values;
    }

    /**
     * Get the count of mailings 
     *
     * @param none
     * @return int              Count
     * @access public
     */
    public function getCount() {
        $this->selectAdd();
        $this->selectAdd('COUNT(id) as count');
        
        $session =& CRM_Core_Session::singleton();
        $this->domain_id = $session->get('domainID');
        
        $this->find(true);
        
        return $this->count;
    }


    /**
     * Get the rows for a browse operation
     *
     * @param int $offset       The row number to start from
     * @param int $rowCount     The nmber of rows to return
     * @param string $sort      The sql string that describes the sort order
     * 
     * @return array            The rows
     * @access public
     */
    public function &getRows($offset, $rowCount, $sort) {
        $mailing    = self::getTableName();
        $job        = CRM_Mailing_BAO_Job::getTableName();

        $session    =& CRM_Core_Session::singleton();
        $domain_id  = $session->get('domainID');

        $query = "
            SELECT      $mailing.id,
                        $mailing.name, 
                        $job.status, 
                        $job.scheduled_date, 
                        $job.start_date,
                        $job.end_date
            FROM        $mailing
            INNER JOIN  $job
                    ON  $job.mailing_id = $mailing.id
            WHERE       $mailing.domain_id = $domain_id
            GROUP BY    $job.id
            ORDER BY    $mailing.id, $job.end_date";

        if ($rowCount) {
            $query .= " LIMIT $offset, $rowCount ";
        }
    
        $this->query($query);

        $rows = array();

        while ($this->fetch()) {
            $rows[] = array(
                'id'        =>  $this->id,
                'name'      =>  $this->name, 
                'status'    => CRM_Mailing_BAO_Job::status($this->status), 
                'scheduled' => CRM_Utils_Date::customFormat($this->scheduled_date),
                'start'     => CRM_Utils_Date::customFormat($this->start_date), 
                'end'       => CRM_Utils_Date::customFormat($this->end_date)
            );
        }
        return $rows;
    }
}

?>
