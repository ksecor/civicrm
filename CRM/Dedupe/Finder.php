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

require_once 'CRM/Core/DAO.php';
require_once 'CRM/Dedupe/Criterion.php';

class CRM_Dedupe_Finder
{
    /**
     * Based on the provided contact_id, an array of criteria, and minimal 
     * threshold, return an array of duplicate contact ids.
     */
    function findDuplicateContacts($cid, $params, $threshold)
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
        foreach ($weights as $cid => $weight) {
            if ($weight >= $threshold) {
                $cids[] = $cid;
            }
        }
        return $cids;
    }
}

?>
