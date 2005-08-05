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
        $contact->is_subscribed = false;
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
        $mg         = CRM_Mailing_BAO_MailingGroup::getTableName();
        $job        = CRM_Mailing_BAO_Job::getTableName();
        $mailing    = CRM_Mailing_BAO_Mailing::getTableName();
        $group      = CRM_Contact_BAO_Group::getTableName();
        $gc         = CRM_Contact_BAO_GroupContact::getTableName();
        
        $do->query("
            SELECT      $mg.entity_table as entity_table,
                        $mg.entity_id as entity_id,
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
            if ($mg->entity_table == $group) {
                $groups[$mg->entity_id] = true;
            } else if ($mg->entity_table == $mailing) {
                $mailings[] = $mg->entity_id;
            }
        }

        /* As long as we have prior mailings, find their groups and add to the
         * list */
        while (! empty($mailings)) {
            $do->query("
                SELECT      $mg.entity_table as entity_table,
                            $mg.entity_id as entity_id,
                FROM        $mg
                WHERE       $mg.mailing_id IN (".implode(', ', $mailings).")
                    AND     $mg.group_type = 'Include'");
            
            $mailings = array();
            
            while ($do->fetch()) {
                if ($mg->entity_table == $group) {
                    $groups[$mg->entity_id] = true;
                } else if ($mg->entity_table == $mailing) {
                    $mailings[] = $mg->entity_id;
                }
            }
        }

        /* Now we have a complete list of recipient groups.  Filter out all
         * those except smart groups and those that the contact belongs to */
        $do->query("
            SELECT      $group.id as group_id
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
            $groups[] = $do->group_id;
        }

        $contacts = array($contact_id);

        foreach ($groups as $key => $group_id) {
            list($total, $removed, $notremoved) = 
                CRM_Contact_BAO_GroupContact::removeContactsFromGroup(
                    $contacts, $group_id, 'Email', $queue_id);
            if (! $removed) {
                unset($groups[$key]);
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
}

?>
