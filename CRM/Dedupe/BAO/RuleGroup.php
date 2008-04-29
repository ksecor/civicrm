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
     * supported tables
     */
    static $supportedTables = array('civicrm_address',
                                    'civicrm_contact',
                                    'civicrm_email',
                                    'civicrm_im',
                                    'civicrm_note',
                                    'civicrm_phone');

    static function &getSupportedTables() {
        return self::$supportedTables;
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
        $bao->dedupe_rule_group_id = $self->id;
        $bao->find();
        $queries = array();
        while ($bao->fetch()) {
            $bao->contactIds = $this->contactIds;
            $queries[] = $bao->sql();
        }
        return 'CREATE TEMPORARY TABLE dedupe ENGINE MEMORY ' . implode(' UNION ', $queries);
    }

    /**
     * Return the SQL query for getting only the interesting results out of the dedupe table.
     */
    function thresholdQuery() {
        return "SELECT id1, id2, SUM(weight) threshold FROM dedupe GROUP BY id1, id2 HAVING threshold >= {$this->threshold}";
    }

}
