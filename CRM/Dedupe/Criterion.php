<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Utils/Type.php';

/**
 * The CiviCRM duplicate discovery engine is based on an
 * algorithm designed by David Strauss <david@fourkitchens.com>.
 */
class CRM_Dedupe_Criterion
{
    /**
     * The table to match on.
     */
    private $_table;

    /**
     * The field to match on.
     */
    private $_field;

    /**
     * The length of the match (for prefix matches); 
     * if null, match on the whole string.
     */
    private $_length = null;

    /**
     * The weight of the criterion; if 0, disregard this criterion.
     */
    private $_weight;

    /**
     * The supported tables.
     */
    static $supportedTables = array( 'civicrm_address',
                                     'civicrm_contact',
                                     'civicrm_email',
                                     'civicrm_im',
                                     'civicrm_note',
                                     'civicrm_phone');

    /**
     * Construct the criterion based on a hash of criterion data.
     */
    function __construct($params)
    {
        // sanitize the parameters for SQL use
        if (in_array($params['table'], self::$supportedTables)) $this->_table  = $params['table'];
        if (preg_match('/^[a-zA-Z_]+$/', $params['field']))     $this->_field  = $params['field'];
        if ($params['length'] !== null)                         $this->_length = (int) $params['length'];
        $this->_weight = (int) $params['weight'];
    }

    /**
     * Weight getter.
     */
    function getWeight()
    {
        return $this->_weight;
    }

    static function &getSupportedTables( ) {
        return self::$supportedTables;
    }

    /**
     * Return the SQL query for getting the match string.
     */
    function matchQuery($cid)
    {
        // sanitize contact_id
        $cid = (int) $cid;

        // return the query
        // FIXME: add custom fields
        switch ($this->_table) {

        case 'civicrm_contact':
            return "SELECT {$this->_field} AS 'match' FROM {$this->_table} WHERE id = $cid";

        case 'civicrm_address':
        case 'civicrm_email':
        case 'civicrm_im':
        case 'civicrm_phone':
            return "SELECT param.{$this->_field} AS 'match' FROM {$this->_table} param
                WHERE param.contact_id = $cid";

        case 'civicrm_note':
            return "SELECT {$this->_field} AS 'match' FROM {$this->_table} WHERE entity_table = 'civicrm_contact' AND entity_id = $cid";
        }
    }

    /**
     * Return the SQL query for the criterion, based on the match string and 
     * the table being polled.
     */
    function query($match)
    {
        // create the WHERE condition
        if ($this->_length === null) {
            $condition = "= '" . CRM_Utils_Type::escape($match, 'String') . "'";
        } else {
            $substr    = function_exists('mb_substr') ? 'mb_substr' : 'substr';
            $match     = $substr($match, 0, $this->_length);
            $condition = "LIKE '" . CRM_Utils_Type::escape($match, 'String') . "%'";
        }

        // build and return the query
        // FIXME: add custom fields
        switch ($this->_table) {

        case 'civicrm_contact':
            return "SELECT id AS contact_id FROM {$this->_table} WHERE {$this->_field} $condition";

        case 'civicrm_address':
        case 'civicrm_email':
        case 'civicrm_phone':
            return "SELECT param.contact_id AS contact_id FROM {$this->_table} param WHERE param.{$this->_field} $condition";

        case 'civicrm_note':
            return "SELECT entity_id AS contact_id FROM {$this->_table} WHERE entity_table = 'civicrm_contact' AND {$this->_field} $condition";
        }
    }
}

?>
