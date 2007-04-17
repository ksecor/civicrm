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

class CRM_Dedupe_Merger
{
    /**
     * Based on the provided two contact_ids and a set of tables, move the 
     * belongings of the other contact to the main one.
     */
    function moveContactBelongings($mainId, $otherId, $tables = array())
    {
        // FIXME: this should be generated dynamically from the schema's 
        // foreign keys referencing civicrm_contact(id)
        static $cidRefs = array(
            'civicrm_household'               => array('contact_id', 'primary_contact_id'),
            'civicrm_individual'              => array('contact_id'),
            'civicrm_organization'            => array('contact_id', 'primary_contact_id'),
            'civicrm_contribution_recur'      => array('contact_id'),
            'civicrm_sms_history'             => array('contact_id'),
            'civicrm_activity'                => array('source_contact_id'),
            'civicrm_meeting'                 => array('source_contact_id'),
            'civicrm_participant'             => array('contact_id'),
            'civicrm_email_history'           => array('contact_id'),
            'civicrm_note'                    => array('contact_id'),
            'civicrm_uf_match'                => array('contact_id'),
            'civicrm_log'                     => array('modified_id'),
            'civicrm_acl_cache'               => array('contact_id'),
            'civicrm_subscription_history'    => array('contact_id'),
            'civicrm_relationship'            => array('contact_id_a', 'contact_id_b'),
            'civicrm_mailing_event_subscribe' => array('contact_id'),
            'civicrm_membership_type'         => array('member_of_contact_id'),
            'civicrm_membership'              => array('contact_id'),
            'civicrm_membership_log'          => array('modified_id'),
            'civicrm_phonecall'               => array('source_contact_id'),
            'civicrm_group_contact'           => array('contact_id'),
            'civicrm_mailing_event_queue'     => array('contact_id'),
            'civicrm_contribution'            => array('contact_id', 'solicitor_id', 'honor_contact_id'),
        );
        // if we ever referenced civicrm_{household,individual,organization}(id)
        // we should define here further reference arrays

        // get the affected tables and sanitize everything for SQL
        $affected = array_keys($cidRefs);
        if ($tables) $affected = array_intersect($affected, $tables);
        $mainId  = (int) $mainId;
        $otherId = (int) $otherId;

        foreach ($affected as $table) {
            foreach ($cidRefs[$table] as $field) {
                $sqls[] = "UPDATE $table SET $field = $mainId WHERE $field = $otherId";
            }
        }

        // call the SQLs in one transaction
    }
}

?>
