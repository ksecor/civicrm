<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.8                                                |
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

require_once 'CRM/Core/DAO.php';
require_once 'CRM/Contact/BAO/Group.php';
require_once 'CRM/Contact/DAO/Contact.php';
require_once 'CRM/Dedupe/Criterion.php';

class CRM_Dedupe_Finder
{
    /**
     * Based on the provided contact_id, an array of criteria and 
     * minimal threshold, return an array of duplicate contact ids.
     */
    function findDupesOfContact($cid, $params, $threshold, $contactType = null)
    {
        $criteria = array();
        foreach ($params as $param) {
            $criteria[] =& new CRM_Dedupe_Criterion($param);
        }

        $dao =& new CRM_Core_DAO();
        $weights = array();
        foreach ($criteria as $criterion) {
            $weight = $criterion->getWeight();
            $dao->query($criterion->matchQuery($cid));
            $dao->fetch();
            $match = $dao->match;
            $dao->query($criterion->query($match));
            while ($dao->fetch()) {
                $weights[$dao->contact_id] += $weight;
            }
        }

        $cids = array();
        foreach ($weights as $id => $weight) {
            if ($weight >= $threshold) {
                $cids[] = $id;
            }
        }
        // remove $cid from the results
        unset($cids[array_search($cid, $cids)]);
        // screen out contacts from other domains - we can't do that
        // in criteria, as some of the tables might not carry domain_id
        static $validCids = array();
        if (!$validCids) {
            $domainId = CRM_Core_Config::domainID();
            $dao =& new CRM_Core_DAO();
            $sql = "SELECT id FROM civicrm_contact WHERE domain_id = $domainId";
            if ($contactType) {
                $sql .= " AND contact_type = '$contactType'";
            }
            $dao->query($sql);
            while ($dao->fetch()) {
                $validCids[] = $dao->id;
            }
        }
        $cids = array_intersect($cids, $validCids);
        return $cids;
    }

    /**
     * Based on the provided group_id, an array of criteria and minimal
     * threshold, return a contact_id-keyed array of duplicate contact_ids
     * in the given group.
     */
    function findDupesInGroup($gid, $params, $threshold, $contactType = null)
    {
        // get the group's contact_ids
        $members = array_keys(CRM_Contact_BAO_Group::getMember($gid));
        $dupes   = array();
        // for each contact_id find its dupes, but 
        // intersect the result with this group's contacts
        foreach ($members as $cid) {
            $dupes[$cid] = array_intersect(self::findDupesOfContact($cid, $params, $threshold, $contactType), $members);
        }
        // return dropping empty matches
        return array_filter($dupes);
    }

    /**
     * Based on an array of criteria and minimal threshold, return 
     * a contact_id-keyed array of duplicate contact_ids across the 
     * whole database.
     */
    function findDupes($params, $threshold, $contactType = null)
    {
        $contacts = array();
        $dupes    = array();
        $dao =& new CRM_Contact_DAO_Contact();
        $dao->domain_id = CRM_Core_Config::domainID();
        $dao->find();
        while ($dao->fetch()) {
            $dupes[$dao->id] = self::findDupesOfContact($dao->id, $params, $threshold, $contactType);
        }
        // return dropping empty matches
        return array_filter($dupes);
    }
}

?>
