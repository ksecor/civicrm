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
    // FIXME: this should be auto-generated from the schema
    static $validFields = array(
        'Household'    => array('household_name'),
        'Organization' => array('organization_name', 'legal_name', 'sic_code'),
        'Individual'   => array('prefix_id', 'first_name', 'middle_name',
            'last_name', 'suffix_id', 'greeting_type', 'custom_greeting',
            'job_title', 'gender_id', 'birth_date', 'is_deceased', 'deceased_date'),
        'Contact'      => array('nick_name', 'legal_identifier', 'home_URL',
            'image_URL', 'source', 'external_identifier', 'do_not_phone', 'do_not_email',
            'do_not_mail', 'do_not_trade', 'preferred_communication_method',
            'preferred_mail_format', 'is_opt_out'),
    );

    // FIXME: consider creating a common structure with cidRefs() and eidRefs()
    // FIXME: the sub-pages references by the URLs should
    // be loaded dynamically on the merge form instead
    static function &relTables()
    {
        static $relTables;
        if (!$relTables) {
            $relTables = array(
                'rel_table_contributions' => array(
                    'title'  => ts('Contributions'),
                    'tables' => array('civicrm_contribution', 'civicrm_contribution_recur', 'civicrm_financial_trxn'),
                    'url'    => CRM_Utils_System::url('civicrm/contact/view', 'reset=1&force=1&cid=$cid&selectedChild=contribute'),
                ),
                'rel_table_memberships' => array(
                    'title'  => ts('Memberships'),
                    'tables' => array('civicrm_membership', 'civicrm_membership_log', 'civicrm_membership_type'),
                    'url'    => CRM_Utils_System::url('civicrm/contact/view', 'reset=1&force=1&cid=$cid&selectedChild=member'),
                ),
                'rel_table_events' => array(
                    'title'  => ts('Events'),
                    'tables' => array('civicrm_participant'),
                    'url'    => CRM_Utils_System::url('civicrm/contact/view', 'reset=1&force=1&cid=$cid&selectedChild=participant'),
                ),
                'rel_table_activities' => array(
                    'title'  => ts('Activities'),
                    'tables' => array('civicrm_activity', 'civicrm_activity_history', 'civicrm_email_history', 'civicrm_meeting', 'civicrm_phonecall', 'civicrm_sms_history'),
                    'url'    => CRM_Utils_System::url('civicrm/contact/view', 'reset=1&force=1&cid=$cid&selectedChild=activity'),
                ),
                'rel_table_relationships' => array(
                    'title'  => ts('Relationships'),
                    'tables' => array('civicrm_relationship'),
                    'url'    => CRM_Utils_System::url('civicrm/contact/view', 'reset=1&force=1&cid=$cid&selectedChild=rel'),
                ),
                'rel_table_groups' => array(
                    'title'  => ts('Groups'),
                    'tables' => array('civicrm_group_contact'),
                    'url'    => CRM_Utils_System::url('civicrm/contact/view', 'reset=1&force=1&cid=$cid&selectedChild=group'),
                ),
                'rel_table_notes' => array(
                    'title'  => ts('Notes'),
                    'tables' => array('civicrm_note'),
                    'url'    => CRM_Utils_System::url('civicrm/contact/view', 'reset=1&force=1&cid=$cid&selectedChild=note'),
                ),
                'rel_table_tags' => array(
                    'title'  => ts('Tags'),
                    'tables' => array('civicrm_entity_tag'),
                    'url'    => CRM_Utils_System::url('civicrm/contact/view', 'reset=1&force=1&cid=$cid&selectedChild=tag'),
                ),
            );
        }
        return $relTables;
    }

    /**
     * Returns the related tables groups for which a contact has any info entered
     */
    static function getActiveRelTables($cid)
    {
        $cid = (int) $cid;
        $groups = array();
        $dao =& new CRM_Core_DAO();

        $relTables =& self::relTables();
        $cidRefs   =& self::cidRefs();
        $eidRefs   =& self::eidRefs();
        foreach ($relTables as $group => $params) {
            $sqls = array();
            foreach ($params['tables'] as $table) {
                if (isset($cidRefs[$table])) {
                    foreach ($cidRefs[$table] as $field) {
                        $sqls[] = "SELECT COUNT(*) AS count FROM $table WHERE $field = $cid";
                    }
                }
                if (isset($eidRefs[$table])) {
                    foreach ($eidRefs[$table] as $entityTable => $entityId) {
                        $sqls[] = "SELECT COUNT(*) AS count FROM $table WHERE $entityId = $cid AND $entityTable = 'civicrm_contact'";
                    }
                }
                foreach ($sqls as $sql) {
                    $dao->query($sql);
                    $dao->fetch();
                    if ($dao->count > 0) $groups[] = $group;
                }
            }
        }
        return array_unique($groups);
    }

    /**
     * Return tables and their fields referencing civicrm_contact.contact_id explicitely
     */
    //
    // FIXME: this should be generated dynamically from the schema's 
    // foreign keys referencing civicrm_contact(id)
    static function &cidRefs()
    {
        static $cidRefs;
        if (!$cidRefs) {
            $cidRefs = array(
                'civicrm_acl_cache'               => array('contact_id'),
                'civicrm_activity'                => array('source_contact_id'),
                'civicrm_contribution'            => array('contact_id', 'solicitor_id', 'honor_contact_id'),
                'civicrm_contribution_recur'      => array('contact_id'),
                'civicrm_email_history'           => array('contact_id'),
                'civicrm_group_contact'           => array('contact_id'),
                'civicrm_household'               => array('primary_contact_id'),
                'civicrm_log'                     => array('modified_id'),
                'civicrm_mailing_event_queue'     => array('contact_id'),
                'civicrm_mailing_event_subscribe' => array('contact_id'),
                'civicrm_meeting'                 => array('source_contact_id'),
                'civicrm_membership'              => array('contact_id'),
                'civicrm_membership_log'          => array('modified_id'),
                'civicrm_membership_type'         => array('member_of_contact_id'),
                'civicrm_note'                    => array('contact_id'),
                'civicrm_organization'            => array('primary_contact_id'),
                'civicrm_participant'             => array('contact_id'),
                'civicrm_phonecall'               => array('source_contact_id'),
                'civicrm_relationship'            => array('contact_id_a', 'contact_id_b'),
                'civicrm_sms_history'             => array('contact_id'),
                'civicrm_subscription_history'    => array('contact_id'),
                'civicrm_uf_match'                => array('contact_id'),
            );
        }
        return $cidRefs;
    }

    /**
     * Return tables and their fields referencing civicrm_contact.contact_id with entity_id
     */
    // FIXME: this should be generated dynamically from the schema
    // tables that reference contacts with entity_{id,table}
    static function &eidRefs()
    {
        static $eidRefs;
        if (!$eidRefs) {
            $eidRefs = array(
                'civicrm_acl'              => array('entity_table'             => 'entity_id'),
                'civicrm_acl_entity_role'  => array('entity_table'             => 'entity_id'),
                'civicrm_activity'         => array('target_entity_table'      => 'target_entity_id'),
                'civicrm_activity_history' => array('entity_table'             => 'entity_id'),
            //  'civicrm_custom_value'     => array('entity_table'             => 'entity_id'),
            //  'civicrm_entity_file'      => array('entity_table'             => 'entity_id'),
                'civicrm_entity_tag'       => array('entity_table'             => 'entity_id'),
                'civicrm_financial_trxn'   => array('entity_table'             => 'entity_id'),
                'civicrm_log'              => array('entity_table'             => 'entity_id'),
                'civicrm_mailing_group'    => array('entity_table'             => 'entity_id'),
                'civicrm_meeting'          => array('target_entity_table'      => 'target_entity_id'),
                'civicrm_note'             => array('entity_table'             => 'entity_id'),
                'civicrm_phonecall'        => array('target_entity_table'      => 'target_entity_id'),
                'civicrm_project'          => array('owner_entity_table'       => 'owner_entity_id'),
                'civicrm_task'             => array('owner_entity_table'       => 'owner_entity_id'),
                'civicrm_task_status'      => array('responsible_entity_table' => 'responsible_entity_id', 'target_entity_table' => 'target_entity_id'),
            );
        }
        return $eidRefs;
    }

    /**
     * Based on the provided two contact_ids and a set of tables, move the 
     * belongings of the other contact to the main one.
     */
    function moveContactBelongings($mainId, $otherId, $tables = false)
    {
        $cidRefs =& self::cidRefs();
        $eidRefs =& self::eidRefs();
        $affected = array_merge(array_keys($cidRefs), array_keys($eidRefs));
        if ($tables) {
            // if there are specific tables, sanitize the list
            $affected = array_unique(array_intersect($affected, $tables));
        } else {
            // if there aren't any specific tables, don't affect the ones handled by relTables()
            $relTables =& self::relTables();
            $handled = array();
            foreach ($relTables as $params) {
                $handled = array_merge($handled, $params['tables']);
            }
            $affected = array_diff($affected, $handled);
        }

        $mainId  = (int) $mainId;
        $otherId = (int) $otherId;

        foreach ($affected as $table) {
            if (isset($cidRefs[$table])) {
                foreach ($cidRefs[$table] as $field) {
                    $sqls[] = "UPDATE $table SET $field = $mainId WHERE $field = $otherId";
                }
            }
            if (isset($eidRefs[$table])) {
                foreach ($eidRefs[$table] as $entityTable => $entityId) {
                    $sqls[] = "UPDATE $table SET $entityId = $mainId WHERE $entityId = $otherId AND $entityTable = 'civicrm_contact'";
                }
            }
        }

        // call the SQL queries in one transaction
        $dao =& new CRM_Core_DAO();
        $dao->transaction('BEGIN');
        foreach ($sqls as $sql) {
            $dao->query($sql);
            $dao->fetch();
        }
        $dao->transaction('COMMIT');
    }

    /**
     * Find differences between contacts.
     */
    function findDifferences($mainId, $otherId)
    {
        require_once 'api/Contact.php';
        $main  = crm_get_contact(array('contact_id' => (int) $mainId));
        $other = crm_get_contact(array('contact_id' => (int) $otherId));
        if ($main->contact_type != $other->contact_type) {
            return false;
        }
        $cType = $main->contact_type;

        $diffs = array();
        foreach (self::$validFields['Contact'] as $validField) {
            if ($main->$validField != $other->$validField) {
                $diffs['Contact'][] = $validField;
            }
        }
        foreach (self::$validFields[$cType] as $validField) {
            if ($main->contact_type_object->$validField != $other->contact_type_object->$validField) {
                $diffs[$cType][] = $validField;
            }
        }

        $customIds = array();
        foreach ($main->custom_values as $cv) {
            $customIds[$cv['custom_field_id']] = $cv['value'];
        }
        foreach ($other->custom_values as $cv) {
            if ($customIds[$cv['custom_field_id']] == $cv['value']) {
                unset($customIds[$cv['custom_field_id']]);
            } else {
                $customIds[$cv['custom_field_id']] = $cv['value'];
            }
        }
        foreach (array_keys($customIds) as $customId) {
            $diffs['custom'][] = $customId;
        }

        return $diffs;
    }
}

?>
