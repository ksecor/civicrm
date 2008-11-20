<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2008
 * $Id$
 *
 */

require_once 'CRM/Dedupe/DAO/RuleGroup.php';

/**
 * The CiviCRM duplicate discovery engine is based on an
 * algorithm designed by David Strauss <david@fourkitchens.com>.
 */
class CRM_Dedupe_BAO_RuleGroup extends CRM_Dedupe_DAO_RuleGroup
{

    /**
     * ids of the contacts to limit the SQL queries (whole-database queries otherwise)
     */
    var $contactIds = array();

    /**
     * params to dedupe against (queries against the whole contact set otherwise)
     */
    var $params = array();

    /**
     * Return a structure holding the supported tables, fields and their titles
     *
     * @param string $requestedType  the requested contact type
     *
     * @return array  a table-keyed array of field-keyed arrays holding supported fields' titles
     */
    function &supportedFields($requestedType) {
        static $fields = null;
        if (!$fields) {
            // this is needed, as we're piggy-backing importableFields() below
            $replacements = array(
                'civicrm_country.name'        => 'civicrm_address.country_id',
                'civicrm_county.name'         => 'civicrm_address.county_id',
                'civicrm_state_province.name' => 'civicrm_address.state_province_id',
                'gender.label'                => 'civicrm_contact.gender_id',
                'individual_prefix.label'     => 'civicrm_contact.prefix_id',
                'individual_suffix.label'     => 'civicrm_contact.suffix_id',
            );
            // the table names we support in dedupe rules - a filter for importableFields()
            $supportedTables = array('civicrm_address', 'civicrm_contact', 'civicrm_email',
                'civicrm_im', 'civicrm_note', 'civicrm_openid', 'civicrm_phone');

            require_once 'CRM/Contact/BAO/Contact.php';
            require_once 'CRM/Core/BAO/CustomGroup.php';
            foreach(array('Individual', 'Organization', 'Household') as $ctype) {
                // take the table.field pairs and their titles from importableFields() if the table is supported
                foreach(CRM_Contact_BAO_Contact::importableFields($ctype) as $iField) {
                    if (isset($iField['where'])) {
                        $where = $iField['where'];
                        if (isset($replacements[$where])) $where = $replacements[$where];
                        list($table, $field) = explode('.', $where);
                        if (!in_array($table, $supportedTables)) continue;
                        $fields[$ctype][$table][$field] = $iField['title'];
                    }
                }
                // add custom data fields
                foreach(CRM_Core_BAO_CustomGroup::getTree($ctype, CRM_Core_DAO::$_nullObject, null, -1) as $key => $cg) {
                    if (!is_int($key)) continue;
                    foreach($cg['fields'] as $cf) {
                        $fields[$ctype][$cg['table_name']][$cf['column_name']] = $cf['label'];
                    }
                }
            }
        }
        return $fields[$requestedType];
    }

    /**
     * Return the SQL query for dropping the temporary table.
     */
    function tableDropQuery() {
        return 'DROP TEMPORARY TABLE IF EXISTS dedupe';
    }
    
    /**
     * Return the SQL query for creating the temporary table.
     */
    function tableQuery() {
        require_once 'CRM/Dedupe/BAO/Rule.php';
        $bao =& new CRM_Dedupe_BAO_Rule();
        $bao->dedupe_rule_group_id = $this->id;
        $bao->find();
        $queries = array();
        while ($bao->fetch()) {
            $bao->contactIds = $this->contactIds;
            $bao->params = $this->params;
            $queries[] = $bao->sql();
        }

        // if there are no rules in this rule group, add an empty query fulfilling the pattern
        if (!$queries) $queries = array('SELECT 0 id1, 0 id2, 0 weight LIMIT 0');

        return 'CREATE TEMPORARY TABLE dedupe ' . implode(' UNION ALL ', $queries);
    }

    /**
     * Return the SQL query for getting only the interesting results out of the dedupe table.
     */
    function thresholdQuery() {
        if ($this->params) {
            return "SELECT id
                FROM dedupe JOIN civicrm_contact USING (id)
                WHERE contact_type = '{$this->contact_type}'
                GROUP BY id HAVING SUM(weight) >= {$this->threshold}
                ORDER BY SUM(weight) desc";
        } else {
            return "SELECT id1, id2, SUM(weight) as weight
                FROM dedupe JOIN civicrm_contact c1 ON id1 = c1.id JOIN civicrm_contact c2 ON id2 = c2.id
                WHERE c1.contact_type = '{$this->contact_type}' AND c2.contact_type = '{$this->contact_type}'
                GROUP BY id1, id2 HAVING SUM(weight) >= {$this->threshold}
                ORDER BY SUM(weight) desc";
        }
    }
    
    /**
     * To find fields related to a rule group.
     * @param array contains the rule group property to identify rule group
     *
     * @return (rule field => weight) array and threshold associated to rule group 
     * @access public
     */
    function dedupeRuleFieldsWeight( $params)
    {
        require_once 'CRM/Dedupe/BAO/Rule.php';
        $rgBao =& new CRM_Dedupe_BAO_RuleGroup();
        $rgBao->level = $params['level'];
        $rgBao->contact_type = $params['contact_type'];
        $rgBao->is_default = 1;
        $rgBao->find(true);
        
        $ruleBao =& new CRM_Dedupe_BAO_Rule();
        $ruleBao->dedupe_rule_group_id = $rgBao->id;
        $ruleBao->find();
        $ruleFields = array();
        while ($ruleBao->fetch()) {
            $ruleFields[$ruleBao->rule_field] = $ruleBao->rule_weight;
        }
        
        return array($ruleFields, $rgBao->threshold);
    }
}
