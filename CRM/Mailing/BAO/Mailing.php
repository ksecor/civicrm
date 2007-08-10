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


require_once 'Mail/mime.php';

require_once 'CRM/Contact/BAO/SavedSearch.php';
require_once 'CRM/Contact/BAO/Query.php';
require_once 'CRM/Contact/BAO/Group.php';

require_once 'CRM/Mailing/DAO/Mailing.php';
require_once 'CRM/Mailing/DAO/Group.php';
require_once 'CRM/Mailing/Event/BAO/Queue.php';
require_once 'CRM/Mailing/Event/BAO/Delivered.php';
require_once 'CRM/Mailing/Event/BAO/Bounce.php';
require_once 'CRM/Mailing/BAO/TrackableURL.php';
require_once 'CRM/Mailing/BAO/Component.php';

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
     * @return object           A DAO loaded with results of the form
     *                              (email_id, contact_id)
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

        require_once 'CRM/Contact/DAO/Group.php';
        $group      = CRM_Contact_DAO_Group::getTableName();
        $g2contact  = CRM_Contact_DAO_GroupContact::getTableName();
      
        /* Create a temp table for contact exclusion */
        $mailingGroup->query(
            "CREATE TEMPORARY TABLE X_$job_id 
            (contact_id int primary key) 
            ENGINE=HEAP"
        );

        /* Add all the members of groups excluded from this mailing to the temp
         * table */
        $excludeSubGroup =
                    "INSERT INTO        X_$job_id (contact_id)
                    SELECT              $g2contact.contact_id
                    FROM                $g2contact
                    INNER JOIN          $mg
                            ON          $g2contact.group_id = $mg.entity_id AND $mg.entity_table = '$group'
                    WHERE
                                        $mg.mailing_id = {$this->id}
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
                            ON          $job.mailing_id = $mg.entity_id AND $mg.entity_table = '$mailing'
                    WHERE
                                        $mg.mailing_id = {$this->id}
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

        $whereTables = array( );
        while ($ss->fetch()) {
            /* run the saved search query and dump result contacts into the temp
             * table */
            $tables = array($contact => 1);
            $where =
                CRM_Contact_BAO_SavedSearch::whereClause( $ss->saved_search_id,
                                                          $tables,
                                                          $whereTables );
            $from = CRM_Contact_BAO_Query::fromClause($tables);
            $mailingGroup->query(
                    "INSERT IGNORE INTO X_$job_id (contact_id)
                    SELECT              contact_a.id
                                        $from
                    WHERE               $where");
        }

        /* Get all the group contacts we want to include */
        
        $mailingGroup->query(
            "CREATE TEMPORARY TABLE I_$job_id 
            (email_id int, contact_id int primary key)
            ENGINE=HEAP"
        );
        
        /* Get the group contacts, but only those which are not in the
         * exclusion temp table */

        /* Get the emails with no override */
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
                            ON          $job.mailing_id = $mg.entity_id AND $mg.entity_table = '$mailing'
                    LEFT JOIN           X_$job_id
                            ON          $contact.id = X_$job_id.contact_id
                    WHERE
                                        $mg.group_type = 'Include'
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

        $whereTables = array( );
        while ($ss->fetch()) {
            $tables = array($contact => 1, $location => 1, $email => 1);
            $where = CRM_Contact_BAO_SavedSearch::whereClause(
                                                              $ss->saved_search_id,
                                                              $tables,
                                                              $whereTables
                                                              );
            $where = trim( $where );
            if ( $where ) {
                $where = " AND $where ";
            }
            $from = CRM_Contact_BAO_Query::fromClause($tables);
            $ssq = "INSERT IGNORE INTO  I_$job_id (email_id, contact_id)
                    SELECT DISTINCT     $email.id as email_id,
                                        contact_a.id as contact_id 
                    $from
                    LEFT JOIN           X_$job_id
                            ON          contact_a.id = X_$job_id.contact_id
                    WHERE           
                                        contact_a.do_not_email = 0
                        AND             contact_a.is_opt_out = 0
                        AND             $location.is_primary = 1
                        AND             $email.is_primary = 1
                        AND             $email.on_hold = 0
                                        $where
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

        $eq =& new CRM_Mailing_Event_BAO_Queue();
        
        $eq->query("SELECT contact_id, email_id 
                    FROM I_$job_id 
                    ORDER BY contact_id, email_id");
        
        /* Delete the temp table */
        $mailingGroup->reset();
        $mailingGroup->query("DROP TEMPORARY TABLE X_$job_id");
        $mailingGroup->query("DROP TEMPORARY TABLE I_$job_id");

        return $eq;
    }

    /**
     * Generate an event queue for a retry job (ie the contacts who bounced)
     *
     * @param int $job_id       The job marked retry
     * @return object           A DAO loaded with email_id/contact_id results
     * @access public
     */
    public function retryRecipients($job_id) {
        $eq         =& new CRM_Mailing_Event_BAO_Queue();
        $job        = CRM_Mailing_BAO_Job::getTableName();
        $queue      = CRM_Mailing_Event_BAO_Queue::getTableName();
        $bounce     = CRM_Mailing_Event_BAO_Bounce::getTableName();
        $delivered  = CRM_Mailing_Event_BAO_Delivered::getTableName();
        $email      = CRM_Core_BAO_Email::getTableName();
        $contact    = CRM_Contact_BAO_Contact::getTableName();
        
        $query = 
                "SELECT             $queue.email_id as email_id, 
                                    $queue.contact_id as contact_id
                FROM                $queue
                INNER JOIN          $job
                        ON          $queue.job_id = $job.id
                INNER JOIN          $bounce
                        ON          $bounce.event_queue_id = $queue.id
                INNER JOIN          $contact
                        ON          $queue.contact_id = $contact.id
                INNER JOIN          $email
                        ON          $queue.email_id = $email.id
                LEFT JOIN           $queue as queue_d
                        ON          queue_d.contact_id = $queue.contact_id
                LEFT JOIN           $delivered
                        ON          $delivered.event_queue_id = queue_d.id
                LEFT JOIN           $bounce as bounce_d
                        ON          bounce_d.event_queue_id = queue_d.id
                WHERE               
                                    $job.mailing_id = {$this->id}
                    AND             $job.id <> $job_id
                    AND             $contact.do_not_email = 0
                    AND             $contact.is_opt_out = 0
                    AND             $email.on_hold = 0
                    AND             bounce_d.id IS NOT NULL
                GROUP BY            $queue.email_id";

        $eq->query($query);
        return $eq;
    }

    /**
     * Retrieve the header and footer for this mailing
     *
     * @param void
     * @return void
     * @access private
     */
    private function getHeaderFooter() {
        if (!$this->header and $this->header_id) {
            $this->header =& new CRM_Mailing_BAO_Component();
            $this->header->id = $this->header_id;
            $this->header->find(true);
            $this->header->free( );
        }
        
        if (!$this->footer and $this->footer_id) {
            $this->footer =& new CRM_Mailing_BAO_Component();
            $this->footer->id = $this->footer_id;
            $this->footer->find(true);
            $this->footer->free( );
        }
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
            $domain_id = $this->domain_id;
            $job_id = $job_id;
            $event_queue_id = $event_queue_id;
            $hash = $hash;
        } else {
            $domain_id = $this->domain_id;
        }
        if ($this->_domain == null) {
            require_once 'CRM/Core/BAO/Domain.php';
            $this->_domain =& 
                CRM_Core_BAO_Domain::getDomainByID($this->domain_id);
        }

        require_once 'api/Contact.php';
        /**
         * Inbound VERP keys:
         *  reply:          user replied to mailing
         *  bounce:         email address bounced
         *  unsubscribe:    contact opts out of all target lists for the mailing
         *  optOut:         contact unsubscribes from the domain
         */
        $config =& CRM_Core_Config::singleton( );
        
        $verp = array( );
        foreach (array('reply', 'bounce', 'unsubscribe', 'optOut') as $key) {
            $verp[$key] = implode($config->verpSeparator,
                                  array(
                                        $key, 
                                        $domain_id,
                                        $job_id, 
                                        $event_queue_id,
                                        $hash
                                        )
                                  ) . '@' . $this->_domain->email_domain;
        }
                
        $urls = array(
                      'forward'         => CRM_Utils_System::url('civicrm/mailing/forward', 
                                                         "reset=1&jid={$job_id}&qid={$event_queue_id}&h={$hash}",
                                                         true),
                      'unsubscribeUrl' => CRM_Utils_System::url('civicrm/mailing/unsubscribe', 
                                                             "reset=1&jid={$job_id}&qid={$event_queue_id}&h={$hash}",
                                                             true), 
                      'optOutUrl'      => CRM_Utils_System::url('civicrm/mailing/optout', 
                                                        "reset=1&jid={$job_id}&qid={$event_queue_id}&h={$hash}",
                                                        true), 
                      );
        
        $headers = array(
                         'Reply-To'  => CRM_Utils_Verp::encode($verp['reply'], $email),
                         'Return-Path' => CRM_Utils_Verp::encode($verp['bounce'], $email),
                         'From'      => "\"{$this->from_name}\" <{$this->from_email}>",
                         'Subject'   => $this->subject,
                         );

        require_once 'CRM/Utils/Token.php';
        if ($this->html == null || $this->text == null) {
            $this->getHeaderFooter();

            if ($this->body_html) {
                $this->html = null;
                if ( $this->header ) {
                    $this->html = $this->header->body_html . "\n";
                }
                $this->html = $this->html . $this->body_html . "\n";
                if ( $this->footer ) {
                    $this->html = $this->html . $this->footer->body_html;
                }

                $this->html = CRM_Utils_Token::replaceDomainTokens($this->html,
                                $this->_domain, true);
                $this->html = CRM_Utils_Token::replaceMailingTokens($this->html,
                                $this, true);
            }

            if (!$this->body_text) {
                $this->body_text = CRM_Utils_String::htmlToText($this->body_html);
            }

            $this->text = null;
            if ( $this->header ) {
                $this->text = $this->header->body_text . "\n";
            }
            $this->text = $this->text . $this->body_text . "\n";
            if ( $this->footer ) {
                $this->text = $this->text . $this->footer->body_text;
            }

            $this->text = CRM_Utils_Token::replaceDomainTokens($this->text,
                                                               $this->_domain, false);
            $this->text = CRM_Utils_Token::replaceMailingTokens($this->text,
                                                                $this, true);
        }
        
        $html = $this->html;
        $text = $this->text;
        
        $params  = array( 'contact_id' => $contactId );
        $contact =& crm_fetch_contact( $params );
        if ( is_a( $contact, 'CRM_Core_Error' ) ) {
            return null;
        }

        $message =& new Mail_Mime("\n");

        /* Do contact-specific token replacement in text mode, and add to the
         * message if necessary */
        if ($test || !$html || $contact['preferred_mail_format'] == 'Text' ||
            $contact['preferred_mail_format'] == 'Both') 
        {
            $text = CRM_Utils_Token::replaceContactTokens(
                                        $text, $contact, false);
            $text = CRM_Utils_Token::replaceActionTokens( $text,
                                        $verp, $urls, false);
            // render the &amp; entities in text mode, so that the links work
            $text = str_replace('&amp;', '&', $text);
        }



        /* Do contact-specific token replacement in html mode, and add to the
         * message if necessary */
        if ($html && ($test || $contact['preferred_mail_format'] == 'HTML' ||
            $contact['preferred_mail_format'] == 'Both'))
        {
            $html = CRM_Utils_Token::replaceContactTokens(
                                        $html, $contact, true);
            
            $html = CRM_Utils_Token::replaceActionTokens( $html, $verp, $urls, true);
            
            if ($this->open_tracking) {
                $html .= '<img src="' . $config->userFrameworkResourceURL . 
                "extern/open.php?q=$event_queue_id\" width='1' height='1' alt='' border='0'>";
            }
        }
        
        if ($html && !$test && $this->url_tracking) {
            CRM_Mailing_BAO_TrackableURL::scan_and_replace($html,
                                $this->id, $event_queue_id);
            CRM_Mailing_BAO_TrackableURL::scan_and_replace($text,
                                $this->id, $event_queue_id);
        }
        
        if ($test || !$html || $contact['preferred_mail_format'] == 'Text' ||
            $contact['preferred_mail_format'] == 'Both') 
        {
            $message->setTxtBody($text);
            
            unset( $text );
        }

        if ($html && ($test || $contact['preferred_mail_format'] == 'HTML' ||
            $contact['preferred_mail_format'] == 'Both'))
        {
            $message->setHTMLBody($html);

            unset( $html );
        }

        $recipient = "\"{$contact['display_name']}\" <$email>";
        $headers['To'] = $recipient;

        $mailMimeParams = array(
            'text_encoding' => '8bit',
            'html_encoding' => '8bit',
            'head_charset'  => 'utf-8',
            'text_charset'  => 'utf-8',
            'html_charset'  => 'utf-8',
            );
        $message->get($mailMimeParams);
        $message->headers($headers);

        // make sure we unset a lot of stuff
        unset( $verp );
        unset( $urls );
        unset( $params );
        unset( $contact );
        unset( $ids );

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
        
        if ($params['mailing_id']) {
            $mailing->id = $params['mailing_id'];
            if ($mailing->find(true)) {
                $job =& new CRM_Mailing_BAO_Job();
                $job->mailing_id = $mailing->id;
                if ($job->find(true) && ! $mailing->is_template) {
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
                $mailing->save();
            }
            CRM_Core_DAO::transaction('COMMIT');
            return $mailing;
        }
        
        $mailing->domain_id     = $params['domain_id'];
        if ($params['header_id']) $mailing->header_id = $params['header_id'];
        if ($params['footer_id']) $mailing->footer_id = $params['footer_id'];
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
        if (file_exists($params['htmlFile'])) {
            $mailing->body_html = file_get_contents($params['htmlFile']);
        } else {
            $mailing->body_html = $params['htmlFile'];
        }
        if (file_exists($params['textFile'])) {
            $mailing->body_text = file_get_contents($params['textFile']);
        } else if ($params['textFile']) {
            $mailing->body_text = $params['textFile'];
        } else {
            $mailing->body_text = CRM_Utils_String::htmlToText($mailing->body_html);
        }
        
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
            $job->save();
        }
        require_once 'CRM/Contact/BAO/Group.php';
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

        require_once 'CRM/Mailing/Event/BAO/Opened.php';
        require_once 'CRM/Mailing/Event/BAO/Reply.php';
        require_once 'CRM/Mailing/Event/BAO/Unsubscribe.php';
        require_once 'CRM/Mailing/Event/BAO/Forward.php';
        require_once 'CRM/Mailing/Event/BAO/TrackableURLOpen.php';
        $t = array(
                'mailing'   => self::getTableName(),
                'mailing_group'  => CRM_Mailing_DAO_Group::getTableName(),
                'group'     => CRM_Contact_BAO_Group::getTableName(),
                'job'       => CRM_Mailing_BAO_Job::getTableName(),
                'queue'     => CRM_Mailing_Event_BAO_Queue::getTableName(),
                'delivered' => CRM_Mailing_Event_BAO_Delivered::getTableName(),
                'opened'    => CRM_Mailing_Event_BAO_Opened::getTableName(),
                'reply'     => CRM_Mailing_Event_BAO_Reply::getTableName(),
                'unsubscribe'   =>
                            CRM_Mailing_Event_BAO_Unsubscribe::getTableName(),
                'bounce'    => CRM_Mailing_Event_BAO_Bounce::getTableName(),
                'forward'   => CRM_Mailing_Event_BAO_Forward::getTableName(),
                'url'       => CRM_Mailing_BAO_TrackableURL::getTableName(),
                'urlopen'   =>
                    CRM_Mailing_Event_BAO_TrackableURLOpen::getTableName(),
                'component' =>  CRM_Mailing_BAO_Component::getTableName()
            );
        
        
        $report = array();
                
        /* FIXME: put some permissioning in here */
        /* Get the mailing info */
        $mailing->query("
            SELECT          {$t['mailing']}.*
            FROM            {$t['mailing']}
            WHERE           {$t['mailing']}.id = $mailing_id");
            
        $mailing->fetch();
        

        $report['mailing'] = array();
        foreach (array_keys(self::fields()) as $field) {
            $report['mailing'][$field] = $mailing->$field;
        }


        /* Get the component info */
        $query = array();
        
        $components = array(
                        'header'        => ts('Header'),
                        'footer'        => ts('Footer'),
                        'reply'         => ts('Reply'),
                        'unsubscribe'   => ts('Unsubscribe'),
                        'optout'        => ts('Opt-Out')
                    );
        foreach(array_keys($components) as $type) {
            $query[] = "SELECT          {$t['component']}.name as name,
                                        '$type' as type,
                                        {$t['component']}.id as id
                        FROM            {$t['component']}
                        INNER JOIN      {$t['mailing']}
                                ON      {$t['mailing']}.{$type}_id =
                                                {$t['component']}.id
                        WHERE           {$t['mailing']}.id = $mailing_id";
        }
        $q = '(' . implode(') UNION (', $query) . ')';
        $mailing->query($q);

        $report['component'] = array();
        while ($mailing->fetch()) {
            $report['component'][] = array(
                                    'type'  => $components[$mailing->type],
                                    'name'  => $mailing->name,
                                    'link'  =>
                                    CRM_Utils_System::url('civicrm/mailing/component', "reset=1&action=update&id={$mailing->id}"),
                                    );
        }
        
        /* Get the recipient group info */
        $mailing->query("
            SELECT          {$t['mailing_group']}.group_type as group_type,
                            {$t['group']}.id as group_id,
                            {$t['group']}.title as group_title,
                            {$t['mailing']}.id as mailing_id,
                            {$t['mailing']}.name as mailing_name
            FROM            {$t['mailing_group']}
            LEFT JOIN       {$t['group']}
                    ON      {$t['mailing_group']}.entity_id = {$t['group']}.id
                    AND     {$t['mailing_group']}.entity_table =
                                                                '{$t['group']}'
            LEFT JOIN       {$t['mailing']}
                    ON      {$t['mailing_group']}.entity_id =
                                                            {$t['mailing']}.id
                    AND     {$t['mailing_group']}.entity_table =
                                                            '{$t['mailing']}'

            WHERE           {$t['mailing_group']}.mailing_id = $mailing_id
            ");
        
        $report['group'] = array('include' => array(), 'exclude' => array());
        while ($mailing->fetch()) {
            $row = array();
            if (isset($mailing->group_id)) {
                $row['id'] = $mailing->group_id;
                $row['name'] = $mailing->group_title;
                $row['link'] = CRM_Utils_System::url('civicrm/group/search',
                            "reset=1&force=1&context=smog&gid={$row['id']}");
            } else {
                $row['id'] = $mailing->mailing_id;
                $row['name'] = $mailing->mailing_name;
                $row['mailing'] = true;
                $row['link'] = CRM_Utils_System::url('civicrm/mailing/report',
                                                    "mid={$row['id']}");
            }
            
            if ($mailing->group_type == 'Include') {
                $report['group']['include'][] = $row;
            } else {
                $report['group']['exclude'][] = $row;
            }
        }

        /* Get the event totals, grouped by job (retries) */
        $mailing->query("
            SELECT          {$t['job']}.*,
                            COUNT(DISTINCT {$t['queue']}.id) as queue,
                            COUNT(DISTINCT {$t['delivered']}.id) as delivered,
                            COUNT(DISTINCT {$t['reply']}.id) as reply,
                            COUNT(DISTINCT {$t['forward']}.id) as forward,
                            COUNT(DISTINCT {$t['bounce']}.id) as bounce,
                            COUNT(DISTINCT {$t['urlopen']}.id) as url
            FROM            {$t['job']}
            LEFT JOIN       {$t['queue']}
                    ON      {$t['queue']}.job_id = {$t['job']}.id
            LEFT JOIN       {$t['reply']}
                    ON      {$t['reply']}.event_queue_id = {$t['queue']}.id
            LEFT JOIN       {$t['forward']}
                    ON      {$t['forward']}.event_queue_id = {$t['queue']}.id
            LEFT JOIN       {$t['bounce']}
                    ON      {$t['bounce']}.event_queue_id = {$t['queue']}.id
            LEFT JOIN       {$t['delivered']}
                    ON      {$t['delivered']}.event_queue_id = {$t['queue']}.id
                    AND     {$t['bounce']}.id IS null
            LEFT JOIN       {$t['urlopen']}
                    ON      {$t['urlopen']}.event_queue_id = {$t['queue']}.id
                    
            WHERE           {$t['job']}.mailing_id = $mailing_id
            GROUP BY        {$t['job']}.id");
        
        $report['jobs'] = array();
        $report['event_totals'] = array();
        while ($mailing->fetch()) {
            $row = array();
            foreach(array(  'queue', 'delivered', 'url', 'forward',
                            'reply', 'unsubscribe', 'bounce') as $field) {
                if (isset( $mailing->$field )){
                    $row[$field] = $mailing->$field;
                }
                if (isset($report['event_totals'][$field])) {
                    $report['event_totals'][$field] += $mailing->$field;
                }
            }
            
            // compute open total seperately to discount duplicates
            // CRM-1258
            $row['opened'] = CRM_Mailing_Event_BAO_Opened::getTotalCount( $mailing_id, $mailing->id, true );
            if ( isset($report['event_totals']['opened']) ) {
                $report['event_totals']['opened'] += $row['opened'];
            }
            
            // compute unsub total seperately to discount duplicates
            // CRM-1783
            $row['unsubscribe'] = CRM_Mailing_Event_BAO_Unsubscribe::getTotalCount( $mailing_id, $mailing->id, true );
            if (isset($report['event_totals']['unsubscribe'])) {
                $report['event_totals']['unsubscribe'] += $row['unsubscribe'];
            }
            
            foreach(array_keys(CRM_Mailing_BAO_Job::fields()) as $field) {
                $row[$field] = $mailing->$field;
            }
            
            if ($mailing->queue) {
                $row['delivered_rate'] = (100.0 * $mailing->delivered ) /
                    $mailing->queue;
                $row['bounce_rate'] = (100.0 * $mailing->bounce ) /
                    $mailing->queue;
                $row['unsubscribe_rate'] = (100.0 * $row['unsubscribe'] ) /
                    $mailing->queue;
            } else {
                $row['delivered_rate'] = 0;
                $row['bounce_rate'] = 0;
                $row['unsubscribe_rate'] = 0;
            }
            
            $row['links'] = array(
                'clicks' => CRM_Utils_System::url(
                        'civicrm/mailing/report/event',
                        "reset=1&event=click&mid=$mailing_id&jid={$mailing->id}"
                ),
                'queue' =>  CRM_Utils_System::url(
                        'civicrm/mailing/report/event',
                        "reset=1&event=queue&mid=$mailing_id&jid={$mailing->id}"
                ),
                'delivered' => CRM_Utils_System::url(
                        'civicrm/mailing/report/event',
                        "reset=1&event=delivered&mid=$mailing_id&jid={$mailing->id}"
                ),
                'bounce'    => CRM_Utils_System::url(
                        'civicrm/mailing/report/event',
                        "reset=1&event=bounce&mid=$mailing_id&jid={$mailing->id}"
                ),
                'unsubscribe'   => CRM_Utils_System::url(
                        'civicrm/mailing/report/event',
                        "reset=1&event=unsubscribe&mid=$mailing_id&jid={$mailing->id}"
                ),
                'forward'       => CRM_Utils_System::url(
                        'civicrm/mailing/report/event',
                        "reset=1&event=forward&mid=$mailing_id&jid={$mailing->id}"
                ),
                'reply'         => CRM_Utils_System::url(
                        'civicrm/mailing/report/event',
                        "reset=1&event=reply&mid=$mailing_id&jid={$mailing->id}"
                ),
                'opened'        => CRM_Utils_System::url(
                        'civicrm/mailing/report/event',
                        "reset=1&event=opened&mid=$mailing_id&jid={$mailing->id}"
                ),
            );

        foreach (array('scheduled_date', 'start_date', 'end_date') as $key) {
                $row[$key] = CRM_Utils_Date::customFormat($row[$key]);
            }
            $report['jobs'][] = $row;
        }

        if (CRM_Utils_Array::value('queue',$report['event_totals'] )) {
            $report['event_totals']['delivered_rate'] = (100.0 * $report['event_totals']['delivered']) / $report['event_totals']['queue'];
            $report['event_totals']['bounce_rate'] = (100.0 * $report['event_totals']['bounce']) / $report['event_totals']['queue'];
            $report['event_totals']['unsubscribe_rate'] = (100.0 * $report['event_totals']['unsubscribe']) / $report['event_totals']['queue'];
        } else {
            $report['event_totals']['delivered_rate'] = 0;
            $report['event_totals']['bounce_rate'] = 0;
            $report['event_totals']['unsubscribe_rate'] = 0;
        }

        /* Get the click-through totals, grouped by URL */
        $mailing->query("
            SELECT      {$t['url']}.url,
                        {$t['url']}.id,
                        COUNT({$t['urlopen']}.id) as clicks,
                        COUNT(DISTINCT {$t['queue']}.id) as unique_clicks
            FROM        {$t['url']}
            LEFT JOIN   {$t['urlopen']}
                    ON  {$t['urlopen']}.trackable_url_id = {$t['url']}.id
            LEFT JOIN  {$t['queue']}
                    ON  {$t['urlopen']}.event_queue_id = {$t['queue']}.id
            LEFT JOIN  {$t['job']}
                    ON  {$t['queue']}.job_id = {$t['job']}.id
            WHERE       {$t['url']}.mailing_id = $mailing_id
            GROUP BY    {$t['url']}.id");
       
        $report['click_through'] = array();

        while ($mailing->fetch()) {
            $report['click_through'][] = array(
                                    'url' => $mailing->url,
                                    'link' =>
                                    CRM_Utils_System::url(
                    'civicrm/mailing/event',
                    "reset=1&event=click&mid=$mailing_id&uid={$mailing->id}"),
                                    'link_unique' =>
                                    CRM_Utils_System::url(
                    'civicrm/mailing/event',
                    "reset=1&event=click&mid=$mailing_id&uid={$mailing->id}&distinct=1"),
                                    'clicks' => $mailing->clicks,
                                    'unique' => $mailing->unique_clicks,
                                    'rate'   => CRM_Utils_Array::value('delivered',$report['event_totals']) ? (100.0 * $mailing->unique_clicks) / $report['event_totals']['delivered'] : 0
                                );
        }

        $report['event_totals']['links'] = array(
            'clicks' => CRM_Utils_System::url(
                            'civicrm/mailing/report/event',
                            "reset=1&event=click&mid=$mailing_id"
            ),
            'clicks_unique' => CRM_Utils_System::url(
                            'civicrm/mailing/report/event',
                            "reset=1&event=click&mid=$mailing_id&distinct=1"
            ),
            'queue' =>  CRM_Utils_System::url(
                            'civicrm/mailing/report/event',
                            "reset=1&event=queue&mid=$mailing_id"
            ),
            'delivered' => CRM_Utils_System::url(
                            'civicrm/mailing/report/event',
                            "reset=1&event=delivered&mid=$mailing_id"
            ),
            'bounce'    => CRM_Utils_System::url(
                            'civicrm/mailing/report/event',
                            "reset=1&event=bounce&mid=$mailing_id"
            ),
            'unsubscribe'   => CRM_Utils_System::url(
                            'civicrm/mailing/report/event',
                            "reset=1&event=unsubscribe&mid=$mailing_id"
            ),
            'forward'         => CRM_Utils_System::url(
                            'civicrm/mailing/report/event',
                            "reset=1&event=forward&mid=$mailing_id"
            ),
            'reply'         => CRM_Utils_System::url(
                            'civicrm/mailing/report/event',
                            "reset=1&event=reply&mid=$mailing_id"
            ),
            'opened'        => CRM_Utils_System::url(
                            'civicrm/mailing/report/event',
                            "reset=1&event=opened&mid=$mailing_id"
            ),
        );

        $report['retry'] = CRM_Utils_System::url(
                            'civicrm/mailing/retry',
                            "reset=1&mid=$mailing_id");

        return $report;
    }

    /**
     * Get the count of mailings 
     *
     * @param
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
                        MIN($job.scheduled_date) as scheduled_date, 
                        MIN($job.start_date) as start_date,
                        MAX($job.end_date) as end_date
            FROM        $mailing
            INNER JOIN  $job
                    ON  $job.mailing_id = $mailing.id
            WHERE       $mailing.domain_id = $domain_id
            GROUP BY    $mailing.id ";
        //ORDER BY    $mailing.id DESC, $job.end_date DESC";
        
        if ($sort) {
            $orderBy = trim( $sort->orderBy() );
            if ( ! empty( $orderBy ) ) {
                $query .= " ORDER BY $orderBy";
            }
        }

        if ($rowCount) {
            $query .= " LIMIT $offset, $rowCount ";
        }
    
        $this->query($query);

        $rows = array();

        while ($this->fetch()) {
            $rows[] = array(
                'id'            =>  $this->id,
                'name'          =>  $this->name, 
                'status'        => CRM_Mailing_BAO_Job::status($this->status), 
                'scheduled'     => CRM_Utils_Date::customFormat($this->scheduled_date),
                'scheduled_iso' => $this->scheduled_date,
                'start'         => CRM_Utils_Date::customFormat($this->start_date), 
                'end'           => CRM_Utils_Date::customFormat($this->end_date)
            );
        }
        return $rows;
    }


    /**
     * compose the url to show details of activityHistory for CiviMail
     *
     * @param int $id
     *
     * @static
     * @access public
     */

    static function showEmailDetails( $id )
    {
        return CRM_Utils_System::url('civicrm/mailing/report', "mid=$id");
        
    }

     /**
     * Delete Mails and all its associated records
     * 
     * @param  int  $id id of the mail to delete
     *
     * @return void
     * @access public
     * @static
     */
    public static function del($id) {
        
        $dependencies = array( 'CRM_Mailing_DAO_Group', 'CRM_Mailing_DAO_Job', 'CRM_Mailing_DAO_TrackableURL');
        
        foreach ($dependencies as $className) {
            eval('$dao = & new ' . $className . '();');
            $dao->mailing_id = $id;
            
            if ( $className == 'CRM_Mailing_DAO_Job' ) {
                $dao->find(true);
                    $daoQueue = new CRM_Mailing_Event_BAO_Queue();
                    $daoQueue->deleteEventQueue( $dao->id, 'job');
            }
            
            $dao->delete();
        }
        
        $dao = & new CRM_Mailing_DAO_Mailing();
        $dao->id = $id;
        $dao->delete();
        
        CRM_Core_Session::setStatus(ts('Selected mailing has been deleted.'));
   }
}

?>
