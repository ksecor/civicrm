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
     * Cache the outgoing email domain
     */
    private $email_domain = null;

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
        $mailingGroup =& new CRM_Mailing_DAO_MailingGroup();
        
        $mailing    = CRM_Mailing_DAO_Mailing::tableName();
        $mg         = CRM_Mailing_DAO_MailingGroup::tableName();
        $eq         = CRM_Mailing_DAO_MailingEventQueue::tableName();
        $ed         = CRM_Mailing_DAO_MailingEventDelivered::tableName();
        $eb         = CRM_Mailing_DAO_MailingEventBounce::tableName();
        $job        = CRM_Mailing_DAO_Job::tableName();
        
        $email      = CRM_Core_DAO_Email::tableName();
        $contact    = CRM_Contact_DAO_Contact::tableName();
        $location   = CRM_Core_DAO_Location::tableName();
        $group      = CRM_Contact_DAO_Group::tableName();
        $g2contact  = CRM_Contact_DAO_GroupContact::tableName();
      
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
                            ON          $location.contact_id = $contact.id
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
                            ON          $location.contact_id = $contact.id
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
                            ON          $location.contact_id = $contact.id
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
                                ON      $location.contact_id = $contact.id
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
        $eq =& new CRM_Mailing_BAO_MailingEventQueue();
        $job        = CRM_Mailing_BAO_Job::tableName();
        $queue      = CRM_Mailing_BAO_MailingEventQueue::tableName();
        $bounce     = CRM_Mailing_BAO_MailingEventBounce::tableName();
        $email      = CRM_Core_BAO_Email::tableName();
        $contact    = CRM_Contact_BAO_Contact::tableName();
        
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
    
        $domain = $this->getDomain();
        
        /**
         * Inbound VERP keys:
         *  reply:          user replied to mailing
         *  owner:          bounce
         *  unsubscribe:    contact opts out of all target lists for the mailing
         *  opt-out:        contact unsubscribes from the domain
         */
        foreach (array('reply', 'owner', 'unsubscribe', 'optOut') as $key) 
        {
            $address[$key] = implode('.', 
                        array(
                            $key, 
                            $job_id, 
                            $event_queue_id,
                            $hash
                        )
                    ) . "@$domain";
        }
        $headers = array(
            'Subject'   => $this->subject,
            'From'      => $this->from_name . ' <' . $this->from_email . '>',
            'Reply-To'  => CRM_Utils_Verp::encode($address['reply'], $email),
            'Return-path' => CRM_Utils_Verp::encode($address['owner'], $email),
        );

        if ($this->html == null || $this->text == null) {
            /* TODO get the domain object */
            $domain = null;
            $this->getHeaderFooter();
        
            $this->html = $this->header->body_html 
                        . $this->body_html 
                        . $this->footer->body_html;
            
            $this->html = CRM_Utils_Token::replaceDomainTokens($this->html,
                            $domain, true);
            
            $this->text = $this->header->body_text
                        . $this->body_text
                        . $this->footer->body_text;
            
            $this->text = CRM_Utils_Token::replaceDomainTokens($this->text,
                            $domain, false);
            
        }

        
        $params = array('id' => $contactId);
        $contact = array();
        $ids    = array();
        CRM_Contact_BAO_Contact::retrieve($params, $contact, $ids);

        $message =& new Mail_Mime("\n");


        /* Do contact-specific token replacement in text mode, and add to the
         * message if necessary */
        if ($values['preferred_mail_format'] == 'Text' ||
            $values['preferred_mail_format'] == 'Both') 
        {
            $text = CRM_Utils_Token::replaceContactTokens(
                                        $this->text, $contact, false);
            $text = CRM_Utils_token::replaceActionTokens( $this->text,
                                        $address, false);
                                        
            /* TODO: trackable URL construction */
            
            $message->setTxtBody($text);
        }


        /* Do contact-specific token replacement in html mode, and add to the
         * message if necessary */
        if ($values['preferred_mail_format'] == 'HTML' ||
            $values['preferred_mail_format'] == 'Both')
        {
            $text = CRM_Utils_Token::replaceContactTokens(
                                        $this->html, $contact, true);
            $text = CRM_Utils_token::replaceActionTokens( $this->text,
                                        $address, true);
            
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
     * Retrieve the outgoing email domain of this mailing
     *
     * @param void
     * @return string   The email domain (pulled from civicrm_domain)
     * @access public
     */
    public function getDomain() {
        if ($this->email_domain != null) {
            return $this->email_domain;
        }

        $domain =& new CRM_Core_DAO_Domain();
        $domain->id = $this->domain_id;
        $domain->find(true);
        $this->email_domain = $domain->email_domain;
        return $this->email_domain;
    }

}

?>
