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
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Core/DAO.php';
require_once 'CRM/Contact/BAO/Group.php';
require_once 'CRM/Dedupe/BAO/RuleGroup.php';

/**
 * The CiviCRM duplicate discovery engine is based on an
 * algorithm designed by David Strauss <david@fourkitchens.com>.
 */
class CRM_Dedupe_Finder
{
    /**
     * Return a contact_id-keyed array of arrays of possible dupes 
     * (of the key contact_id) - limited to dupes of $cids if provided.
     *
     * @param int   $rgid  rule group id
     * @param array $cids  contact ids to limit the search to
     *
     * @return array  id-keyed hash of dupes
     */
    function dupes($rgid, $cids = array()) {
        $rgBao =& new CRM_Dedupe_BAO_RuleGroup();
        $rgBao->domain_id = CRM_Core_Config::DomainID();
        $rgBao->id = $rgid;
        $rgBao->contactIds = $cids;
        $rgBao->find(true);

        $dao =& new CRM_Core_DAO();
        $dao->query($rgBao->tableQuery());
        $dao->query($rgBao->thresholdQuery());
        $dupes = array();
        while ($dao->fetch()) {
            $dupes[$dao->id1][] = $dao->id2;
            $dupes[$dao->id2][] = $dao->id1;
        }
        $dao->query($rgBao->tableDropQuery());

        return $dupes;
    }

    /**
     * Return an array of possible dupes, based on the provided array of 
     * params, using the default rule group for the given contact type and 
     * level.
     *
     * @param string $ctype   contact type to match against
     * @param array  $params  an array of params of the form $params[$table][$field] == $value
     * @param string $level   dedupe rule group level ('Fuzzy' or 'Strict')
     *
     * @return array  matching contact ids
     */
    function dupesByParams($ctype, $params, $level = 'Strict') {
        $rgBao =& new CRM_Dedupe_BAO_RuleGroup();
        $rgBao->domain_id = CRM_Core_Config::DomainID();
        $rgBao->contact_type = $ctype;
        $rgBao->params = $params;
        $rgBao->level = $level;
        $rgBao->is_default = 1;
        $rgBao->find(true);

        $dao =& new CRM_Core_DAO();
        $dao->query($rgBao->tableQuery());
        $dao->query($rgBao->thresholdQuery());
        $dupes = array();
        while ($dao->fetch()) {
            $dupes[] = $dao->id2;
        }
        $dao->query($rgBao->tableDropQuery());

        return $dupes;
    }

    /**
     * Return a contact_id-keyed array of arrays of possible dupes in the given group.
     *
     * @param int $rgid  rule group id
     * @param int $gid   contact group id (currently, works only with non-smart groups)
     *
     * @return array  id-keyed hash of dupes
     */
    function dupesInGroup($rgid, $gid) {
        $cids = array_keys(CRM_Contact_BAO_Group::getMember($gid));
        return self::dupes($rgid, $cids);
    }

    /**
     * Return dupes of a given contact, using the default rule group (of a provided level).
     *
     * @param int    $cid    contact id of the given contact
     * @param string $level  dedupe rule group level ('Fuzzy' or 'Strict')
     * @param string $ctype  contact type of the given contact
     *
     * @return array  id-keyed hash of dupes
     */
    function dupesOfContact($cid, $level = 'Strict', $ctype = null) {
        // if not provided, fetch the contact type from the database
        if (!$ctype) {
            $dao =& new CRM_Contact_DAO_Contact();
            $dao->domain_id = CRM_Core_Config::DomainID();
            $dao->id = $cid;
            $dao->find(true);
            $ctype = $dao->contact_type;
        }
        $rgBao =& new CRM_Dedupe_BAO_RuleGroup();
        $rgBao->domain_id = CRM_Core_Config::DomainID();
        $rgBao->level = $level;
        $rgBao->contact_type = $ctype;
        $rgBao->is_default = 1;
        $rgBao->find(true);
        return self::dupes($rgBao->id, array($cid));
    }

    /**
     * A hackish function needed to massage CRM_Contact_Form_$ctype::formRule() 
     * object into a valid $params array for dedupe
     *
     * @param array $fields  contact structure from formRule()
     * @param string $ctype  contact type of the given contact
     *
     * @return array  valid $params array for dedupe
     */
    function formatParams($fields, $ctype) {
        $flat = array();
        CRM_Utils_Array::flatten($fields, $flat);

        // if the key is dotted, keep just the last part of it
        foreach($flat as $key => $value) {
            if (substr_count($key, '.')) {
                $last = array_pop(explode('.', $key));
                // make sure the first occurence is kept, not the last
                if (!isset($flat[$last])) $flat[$last] = $value;
                unset($flat[$key]);
            }
        }

        $params = array();
        foreach(CRM_Dedupe_BAO_RuleGroup::supportedFields($ctype) as $table => $fields) {
            foreach($fields as $field => $title) {
                if ($flat[$field]) $params[$table][$field] = $flat[$field];
            }
        }
        return $params;
    }
}
