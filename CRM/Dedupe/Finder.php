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
     * @return array  array of (cid1, cid2, weight) dupe triples
     */
    function dupes($rgid, $cids = array()) {
        $rgBao =& new CRM_Dedupe_BAO_RuleGroup();
        $rgBao->id = $rgid;
        $rgBao->contactIds = $cids;
        $rgBao->find(true);

        $dao =& new CRM_Core_DAO();
        $dao->query($rgBao->tableQuery());
        $dao->query($rgBao->thresholdQuery());
        $dupes = array();
        while ($dao->fetch()) {
            $dupes[] = array( $dao->id1, $dao->id2, $dao->weight );
        }
        $dao->query($rgBao->tableDropQuery());

        return $dupes;
    }

    /**
     * Return an array of possible dupes, based on the provided array of 
     * params, using the default rule group for the given contact type and 
     * level.
     *
     * @param array  $params  array of params of the form $params[$table][$field] == $value
     * @param string $ctype   contact type to match against
     * @param string $level   dedupe rule group level ('Fuzzy' or 'Strict')
     * @param array  $except  array of contacts that shouldn't be considered dupes
     *
     * @return array  matching contact ids
     */
    function dupesByParams($params, $ctype, $level = 'Strict', $except = array()) {
        $rgBao =& new CRM_Dedupe_BAO_RuleGroup();
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
            $dupes[] = $dao->id;
        }
        $dao->query($rgBao->tableDropQuery());

        return array_diff($dupes, $except);
    }

    /**
     * Return a contact_id-keyed array of arrays of possible dupes in the given group.
     *
     * @param int $rgid  rule group id
     * @param int $gid   contact group id (currently, works only with non-smart groups)
     *
     * @return array  array of (cid1, cid2, weight) dupe triples
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
     * @return array  array of dupe contact_ids
     */
    function dupesOfContact($cid, $level = 'Strict', $ctype = null) {
        // if not provided, fetch the contact type from the database
        if (!$ctype) {
            $dao =& new CRM_Contact_DAO_Contact();
            $dao->id = $cid;
            $dao->find(true);
            $ctype = $dao->contact_type;
        }
        $rgBao =& new CRM_Dedupe_BAO_RuleGroup();
        $rgBao->level = $level;
        $rgBao->contact_type = $ctype;
        $rgBao->is_default = 1;
        $rgBao->find(true);
        $dupes = self::dupes($rgBao->id, array($cid));
        
        // get the dupes for this cid
        $result = array( );
        foreach ( $dupes as $dupe ) {
            if ( $dupe[0] == $cid ) {
                $result[] = $dupe[1];
            } elseif ( $dupe[1] == $cid ) {
                $result[] = $dupe[0];
            }
        }
        return $result;
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

        // handle {birth,deceased}_date
        foreach(array('birth_date', 'deceased_date') as $date) {
            if ($fields[$date]) {
                $flat[$date] = $fields[$date];
                if (is_array($flat[$date])) $flat[$date] = CRM_Utils_Date::format($flat[$date]);
            }
        }

        // handle preferred_communication_method
        if (array_key_exists('preferred_communication_method', $fields)) {
            $methods = array_intersect($fields['preferred_communication_method'], array('1'));
            $methods = array_keys($methods);
            sort($methods);
            if ($methods) {
                $flat['preferred_communication_method'] = CRM_Core_DAO::VALUE_SEPARATOR . implode(CRM_Core_DAO::VALUE_SEPARATOR, $methods) . CRM_Core_DAO::VALUE_SEPARATOR;
            }
        }

        // handle custom data
        require_once 'CRM/Core/BAO/CustomGroup.php';
        $tree =& CRM_Core_BAO_CustomGroup::getTree($ctype, null, -1);
        CRM_Core_BAO_CustomGroup::postProcess($tree, $fields, true);
        foreach($tree as $key => $cg) {
            if (!is_int($key)) continue;
            foreach($cg['fields'] as $cf) {
                $flat[$cf['column_name']] = $cf['customValue']['data'];
            }
        }

        // if the key is dotted, keep just the last part of it
        foreach($flat as $key => $value) {
            if (substr_count($key, '.')) {
                $last = array_pop(explode('.', $key));
                // make sure the first occurence is kept, not the last
                if (!isset($flat[$last])) $flat[$last] = $value;
                unset($flat[$key]);
            }
        }

        // drop the -digit postfixes (so event registration's $flat['email-5'] becomes $flat['email'])
        foreach ($flat as $key => $value) {
            $matches = array();
            if (preg_match('/(.*)-\d+$/', $key, $matches)) {
                $flat[$matches[1]] = $value;
                unset($flat[$key]);
            }
        }

        $params = array();
        foreach(CRM_Dedupe_BAO_RuleGroup::supportedFields($ctype) as $table => $fields) {
            // for matching on civicrm_address fields, we also need the location_type_id
            if ($table == 'civicrm_address') $fields['location_type_id'] = '';
            foreach($fields as $field => $title) {
                if ( CRM_Utils_Array::value( $field, $flat ) ) {
                    $params[$table][$field] = $flat[$field];
                }
            }
        }
        return $params;
    }
}
