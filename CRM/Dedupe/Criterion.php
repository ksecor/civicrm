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
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions      |
 | about the Affero General Public License or the licensing  of       |
 | CiviCRM, see the CiviCRM license FAQ at                            |
 | http://civicrm.org/licensing/                                      |
 +--------------------------------------------------------------------+
*/

/**
 * CiviCRM dedupe criterion for matching contacts
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2007
 * $Id$
 *
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
     * The length of the match (for prefix matches); if -1, match on the whole 
     * string.
     */
    private $_length;

    /**
     * The weight of the criterion; if 0, disregard this criterion.
     */
    private $_weight;

    /**
     * Construct the criterion based on the contact_id and a hash of criterion 
     * data.
     */
    function __construct($params)
    {
        // sanitize the parameters for SQL use
        if (preg_match('/^civicrm_[a-z_]+$/', $params['table'])) $this->_table = $params['table'];
        if (preg_match('/^[a-zA-Z_]+$/',      $params['field'])) $this->_field = $params['field'];
        $this->_length = (int) $params['length'];
        $this->_weight = (int) $params['weight'];
    }

    /**
     * Return the SQL query for getting the match string.
     */
    function matchQuery($cid)
    {
        // sanitize contact_id
        $cid = (int) $cid;

        // return the query
        switch ($this->_table) {
        case 'civicrm_contact':
            return "SELECT {$this->_field} AS 'match' FROM {$this->_table} WHERE id = $cid";

        case 'civicrm_address':
        case 'civicrm_email':
        case 'civicrm_phone':
            return "SELECT param.{$this->_field} AS 'match' FROM {$this->_table} param
                INNER JOIN civicrm_location loc ON param.location_id = loc.id
                WHERE loc.entity_table = 'civicrm_contact' AND loc.entity_id = $cid";
        }
    }

    /**
     * Return the SQL query for the criterion, based on the match string and 
     * which table is being polled.
     */
    function query($match)
    {
        // create the WHERE condition
        if ($this->_length == -1) {
            $condition = "= '$match'";
        } else {
            $substr = function_exists('mb_substr') ? 'mb_substr' : 'substr';
            $match = $substr($match, 0, $this->_length);
            $condition = "LIKE '$match%'";
        }

        // build and return the query
        switch ($this->_table) {

        case 'civicrm_contact':
            return "SELECT id AS contact_id FROM {$this->_table} WHERE {$this->_field} $condition";

        case 'civicrm_address':
        case 'civicrm_email':
        case 'civicrm_phone':
            return "SELECT loc.entity_id AS contact_id FROM civicrm_location loc
                INNER JOIN {$this->_table} param ON param.location_id = loc.id
                WHERE loc.entity_table = 'civicrm_contact' AND param.{$this->_field} $condition";
        }
    }
}

?>
