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

/**
 * The CiviCRM duplicate discovery engine is based on an
 * algorithm designed by David Strauss <david@fourkitchens.com>.
 */
class CRM_Dedupe_Finder
{
    /**
     * Return a contact_id-keyed array of arrays of possible dupes 
     * (of the key contact_id) - limited to dupes of $cids if provided.
     */
    function dupes($rgid, $cids = array()) {
        require_once 'CRM/Dedupe/BAO/RuleGroup.php';
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
        }
        $dao->query($rgBao->tableDropQuery());

        return $dupes;
    }

    /**
     * Return a contact_id-keyed array of arrays of possible dupes in the given group.
     */
    function dupesInGroup($rgid, $gid) {
        $cids = array_keys(CRM_Contact_BAO_Group::getMember($gid));
        return self::dupes($rgid, $cids);
    }
}
