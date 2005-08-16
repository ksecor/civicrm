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

class CRM_Mailing_Event_BAO_Unsubscribe extends CRM_Mailing_Event_DAO_Unsubscribe {

    /**
     * class constructor
     */
    function __construct( ) {
        parent::__construct( );
    }

    /**
     * Unsubscribe a contact from the domain
     *
     * @param int $job_id       The job ID
     * @param int $queue_id     The Queue Event ID of the recipient
     * @param string $hash      The hash
     * @return boolean          Was the contact succesfully unsubscribed?
     * @access public
     * @static
     */
    public static function unsub_from_domain($job_id, $queue_id, $hash) {
        $q =& CRM_Mailing_Event_BAO_Queue::verify($job_id, $queue_id, $hash);
        if (! $q) {
            return false;
        }
        CRM_Core_DAO::transaction('BEGIN');
        $contact =& new CRM_Contact_BAO_Contact();
        $contact->id = $q->contact_id;
        $contact->is_opt_out = true;
        $contact->save();
        
        $ue =& new CRM_Mailing_Event_BAO_Unsubscribe();
        $ue->event_queue_id = $queue_id;
        $ue->org_unsubscribe = 1;
        $ue->time_stamp = date('YmdHis');
        $ue->save();

        $shParams = array(
            'contact_id'    => $q->contact_id,
            'group_id'      => null,
            'status'        => 'Removed',
            'method'        => 'Email',
            'tracking'      => $ue->id
        );
        CRM_Contact_BAO_SubscriptionHistory::create($shParams);
        
        CRM_Core_DAO::transaction('COMMIT');
        
        return true;
    }

    /**
     * Unsubscribe a contact from all groups that received this mailing
     *
     * @param int $job_id       The job ID
     * @param int $queue_id     The Queue Event ID of the recipient
     * @param string $hash      The hash
     * @return array|null $groups    Array of all groups from which the contact was removed, or null if the queue event could not be found.
     * @access public
     * @static
     */
    public static function &unsub_from_mailing($job_id, $queue_id, $hash) {
        /* First make sure there's a matching queue event */
        $q =& CRM_Mailing_Event_BAO_Queue::verify($job_id, $queue_id, $hash);
        if (! $q) {
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
            WHERE       $job.id = $job_id
                AND     $mg.group_type = 'Include'");
        
        /* Make a list of groups and a list of prior mailings that received 
         * this mailing */
         
        $groups = array();
        $mailings = array();

        while ($do->fetch()) {
            if ($do->entity_table == $group) {
                $groups[$do->entity_id] = true;
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
                        $group.name as name
            FROM        $group
            LEFT JOIN   $gc
                ON      $gc.group_id = $group.id
            WHERE       $group.id IN (".implode(', ', array_keys($groups)).")
                AND     ($group.saved_search_id is not null
                            OR  ($gc.contact_id = $contact_id
                                AND $gc.status = 'Added')
                        )");
                        
        $groups = array();
        
        while ($do->fetch()) {
            $groups[$do->group_id] = $do->name;
        }

        $contacts = array($contact_id);

        foreach ($groups as $group_id => $group_name) {
            list($total, $removed, $notremoved) = 
                CRM_Contact_BAO_GroupContact::removeContactsFromGroup(
                    $contacts, $group_id, 'Email', $queue_id);
            if ($notremoved) {
                unset($groups[$group_id]);
            }
        }
        
        $ue =& new CRM_Mailing_Event_BAO_Unsubscribe();
        $ue->event_queue_id = $queue_id;
        $ue->org_unsubscribe = 0;
        $ue->time_stamp = date('YmdHis');
        $ue->save();
        
        CRM_Core_DAO::transaction('COMMIT');
        return $groups;
    }

    /**
     * Send a reponse email informing the contact of the groups from which he
     * has been unsubscribed.
     *
     * @param string $email         The email address of the contact
     * @param array $groups         List of group IDs
     * @param bool $is_domain       Is this domain-level?
     * @return void
     * @access public
     * @static
     */
    public static function send_unsub_response($email, $groups, $is_domain = false) {
        $config =& CRM_Core_Config::singleton();
        $domain =& CRM_Core_BAO_Domain::getCurrentDomain();

        if ($is_domain) {
            $body = ts('You have been unsubscribed from %1.', 
                        array(1 => $domain->name));
        } else if (count($groups) > 1) {
            $body = ts('You have been removed from the following groups: %1.', 
                        array(1 => implode(', ', $groups)));
        } else {    
            $body = ts('You have been removed from %1.',
                        array(1 => array_shift($groups)));
        }
        /* TODO: add links to resubscribe */
        /* TODO: use autoresponder template? */
        /* TODO: include domain contact information */

        $headers = array(
            'Subject'       => ts('Unsubscribe request completed'),
            'From'          => ts('"%1 Administrator" <%2>',
                array(  1 => $domain->name, 
                        2 => "do-not-reply@{$domain->email_domain}")),
            'Reply-To'      => "do-not-reply@{$domain->email_domain}",
            'Return-path'   => "do-not-reply@{$domain->email_domain}"
        );

        $message =& new Mail_Mime("\n");
        $message->setTxtBody($body);
        $b = $message->get();
        $h = $message->headers($headers);
        $mailer =& $config->getMailer();

        PEAR::setErrorHandling( PEAR_ERROR_CALLBACK,
                                array('CRM_Mailing_BAO_Mailing', 'catchSMTP'));
        $mailer->send($email, $h, $b);
        CRM_Core_Error::setCallback();
    }
}

?>
