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

require_once 'CRM/Dedupe/DAO/Rule.php';

/**
 * The CiviCRM duplicate discovery engine is based on an
 * algorithm designed by David Strauss <david@fourkitchens.com>.
 */
class CRM_Dedupe_BAO_Rule extends CRM_Dedupe_DAO_Rule
{

    /**
     * ids of the contacts to limit the SQL queries (whole-database queries otherwise)
     */
    var $contactIds = array();
    
    /**
     * Return the SQL query for the given criterion.
     */
    function sql() {

        // we need to initialise WHERE here, as some table types extend it
        // $where is an array of required conditions
        $where = array();

        switch ($this->rule_table) {
        case 'civicrm_contact':
            $id = 'id';
            break;
        case 'civicrm_address':
        case 'civicrm_email':
        case 'civicrm_phone':
            $id = 'contact_id';
            break;
        case 'civicrm_note':
            $id = 'entity_id';
            $where[] = "t1.entity_table = 'civicrm_contact'";
            $where[] = "t2.entity_table = 'civicrm_contact'";
            break;
        }

        // build SELECT based on the field names containing contact ids
        $select = "t1.$id id1, t2.$id id2, {$this->rule_weight} weight";

        // build FROM based on whether the rule is about substrings or not
        if ($this->rule_length) {
            $from = "{$this->rule_table} t1 JOIN {$this->rule_table} t2 ON SUBSTR(t1.{$this->rule_field}, 1, {$this->rule_length}) = SUBSTR(t2.{$this->rule_field}, 1, {$this->rule_length})";
        } else {
            $from = "{$this->rule_table} t1 JOIN {$this->rule_table} t2 USING ({$this->rule_field})";
        }

        // finish building WHERE, also limit the results if requested
        $where[] = "t1.$id != t2.$id";
        if ($this->contactIds) {
            $cids = array();
            foreach ($this->contactIds as $cid) {
                $cids[] = CRM_Utils_Type::escape($cid, 'Integer');
            }
            if (count($cids) == 1) {
                $where[] = "t1.$id = {$cids[0]}";
            } else {
                $where[] = "t1.$id IN (" . implode(',', $cids) . ")";
            }
        }

        return "SELECT $select FROM $from WHERE " . implode(' AND ', $where);
    }

}
