<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.9                                                |
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


class CRM_Mailing_Event_BAO_Resubscribe {

    /**
     * Resubscribe a contact to the groups, he/she was unsubscribed from.
     *
     * @param int $job_id       The job ID
     * @param int $queue_id     The Queue Event ID of the recipient
     * @param string $hash      The hash
     * @return array|null $groups    Array of all groups to which the contact was added, or null if the queue event could not be found.
     * @access public
     * @static
     */
    public static function &resub_to_mailing($job_id, $queue_id, $hash) {
        /* First make sure there's a matching queue event */
        $q =& CRM_Mailing_Event_BAO_Queue::verify($job_id, $queue_id, $hash);
        if (! $q) {
            return null;
        }

        // check if this queue_id was actually unsubscribed 
        $ue =& new CRM_Mailing_Event_BAO_Unsubscribe();
        $ue->event_queue_id = $queue_id;
        $ue->org_unsubscribe = 0;
        if (! $ue->find(true)) {
            return null;
        }
        
        $contact_id = $q->contact_id;
        
        CRM_Core_DAO::transaction('BEGIN');
        
        $do =& new CRM_Core_DAO();
        $mg         = CRM_Mailing_DAO_Group::getTableName();
        $job        = CRM_Mailing_BAO_Job::getTableName();
        $mailing    = CRM_Mailing_BAO_Mailing::getTableName();
        $group      = CRM_Contact_BAO_Group::getTableName();
        $gc         = CRM_Contact_BAO_GroupContact::getTableName();
        
        $do->query("
            SELECT      $mg.entity_table as entity_table,
                        $mg.entity_id as entity_id
            FROM        $mg
            INNER JOIN  $job
                ON      $job.mailing_id = $mg.mailing_id
            WHERE       $job.id = " 
                . CRM_Utils_Type::escape($job_id, 'Integer') . "
                AND     $mg.group_type = 'Include'");
        
        /* Make a list of groups and a list of prior mailings that received 
         * this mailing */
         
        $groups = array();
        $mailings = array();
        
        while ($do->fetch()) {
            if ($do->entity_table == $group) {
                $groups[$do->entity_id] = $do->entity_table;
            } else if ($do->entity_table == $mailing) {
                $mailings[] = $do->entity_id;
            }
        }
        
        /* As long as we have prior mailings, find their groups and add to the
         * list */
        while (! empty($mailings)) {
            $do->query("
                SELECT      $mg.entity_table as entity_table,
                            $mg.entity_id as entity_id
                FROM        $mg
                WHERE       $mg.mailing_id IN (".implode(', ', $mailings).")
                    AND     $mg.group_type = 'Include'");
            
            $mailings = array();
            
            while ($do->fetch()) {
                if ($do->entity_table == $group) {
                    $groups[$do->entity_id] = true;
                } else if ($do->entity_table == $mailing) {
                    $mailings[] = $do->entity_id;
                }
            }
        }

        /* Now we have a complete list of recipient groups.  Filter out all
         * those except smart groups and those that the contact belongs to */
        $do->query("
            SELECT      $group.id as group_id,
                        $group.title as title
            FROM        $group
            LEFT JOIN   $gc
                ON      $gc.group_id = $group.id
            WHERE       $group.id IN (".implode(', ', array_keys($groups)).")
                AND     ($group.saved_search_id is not null
                            OR  ($gc.contact_id = $contact_id
                                AND $gc.status = 'Removed')
                        )");
                        
        while ($do->fetch()) {
            $groups[$do->group_id] = $do->title;
        }

        $contacts = array($contact_id);
        
        foreach ($groups as $group_id => $group_name) {
            
            if ( $group_name == 'civicrm_group' ) {
                list($total, $added, $notadded) = CRM_Contact_BAO_GroupContact::addContactsToGroup( $contacts, $group_id, 'Email', 'Removed');
            } else {
                list($total, $added, $notadded) = CRM_Contact_BAO_GroupContact::addContactsToGroup( $contacts, $group_id, 'Email');
                //CRM_Contact_BAO_GroupContact::removeContactsFromGroup( $contacts, $group_id, 'Email', $queue_id);
            }
            
            if ($notadded) {
                unset($groups[$group_id]);
            }
        }
        
        // remove entry from Unsubscribe table.
        $ue =& new CRM_Mailing_Event_BAO_Unsubscribe();
        $ue->event_queue_id = $queue_id;
        $ue->org_resubscribe = 0;
        if ($ue->find(true)) {
            $ue->delete();
        }

        CRM_Core_DAO::transaction('COMMIT');
        return $groups;
    }


    /**
     * Send a reponse email informing the contact of the groups to which he/she
     * has been resubscribed.
     *
     * @param string $queue_id      The queue event ID
     * @param array $groups         List of group IDs
     * @param bool $is_domain       Is this domain-level?
     * @param int $job              The job ID
     * @return void
     * @access public
     * @static
     */
    public static function send_resub_response($queue_id, $groups, $is_domain = false, $job) {
        // param is_domain is not supported as of now.

        $config =& CRM_Core_Config::singleton();
        $domain =& CRM_Mailing_Event_BAO_Queue::getDomain($queue_id);

        $contacts = CRM_Contact_DAO_Contact::getTableName();
        $email    = CRM_Core_DAO_Email::getTableName();
        $queue    = CRM_Mailing_Event_BAO_Queue::getTableName();
        
        $component =& new CRM_Mailing_BAO_Component();
        $component->component_type = 'Resubscribe';
        $component->find(true);

        $html = $component->body_html;
        if ($component->body_text) {
            $text = $component->body_text;
        } else {
            $text = CRM_Utils_String::htmlToText($component->body_html);
        }

        $eq =& new CRM_Core_DAO();
        $eq->query(
        "SELECT     $contacts.preferred_mail_format as format,
                    $contacts.id as contact_id,
                    $email.email as email,
                    $queue.hash as hash
        FROM        $contacts
        INNER JOIN  $queue ON $queue.contact_id = $contacts.id
        INNER JOIN  $email ON $queue.email_id = $email.id
        WHERE       $queue.id = " 
                    . CRM_Utils_Type::escape($queue_id, 'Integer'));
        $eq->fetch();

        $message =& new Mail_Mime("\n");
        require_once 'CRM/Utils/Token.php';
        if ($eq->format == 'HTML' || $eq->format == 'Both') {
            $html = 
                CRM_Utils_Token::replaceResubscribeTokens($html, $domain, $groups, true, $eq->contact_id, $eq->hash);
            $message->setHTMLBody($html);
        }
        if (!$html || $eq->format == 'Text' || $eq->format == 'Both') {
            $text = 
                CRM_Utils_Token::replaceResubscribeTokens($text, $domain, $groups, false, $eq->contact_id, $eq->hash);
            $message->setTxtBody($text);
        }
        $headers = array(
            'Subject'       => $component->subject,
            'From'          => ts('"%1 Administrator" <%2>',
                array(  1 => $domain->name, 
                        2 => "do-not-reply@{$domain->email_domain}")),
            'To'            => $eq->email,
            'Reply-To'      => "do-not-reply@{$domain->email_domain}",
            'Return-Path'   => "do-not-reply@{$domain->email_domain}"
        );

        $b = $message->get();

        $h = $message->headers($headers);
        $mailer =& $config->getMailer();

        PEAR::setErrorHandling( PEAR_ERROR_CALLBACK,
                                array('CRM_Mailing_BAO_Mailing', 'catchSMTP'));
        $mailer->send($eq->email, $h, $b);
        CRM_Core_Error::setCallback();
    }

}

?>
