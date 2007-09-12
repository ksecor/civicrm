<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.9                                                |
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

require_once 'CRM/Admin/Form.php';
require_once 'CRM/Dedupe/Finder.php';
require_once 'CRM/Dedupe/DAO/Rule.php';
require_once 'CRM/Dedupe/DAO/RuleGroup.php';

class CRM_Admin_Form_DedupeFind extends CRM_Admin_Form
{
    protected $_cid = null;
    protected $_rgid;
    protected $_mainContacts;
    protected $_dupeContacts;

    /**
     * Function to pre processing
     *
     * @return None
     * @access public
     */
    function preProcess()
    {
        // FIXME: move the civicrm_dedupe_rule* operations
        // to CRM_Dedupe_BAO_RuleGroup::getCriteriaArray()
        $cid    = CRM_Utils_Request::retrieve('cid',  'Positive', $this, false, 0);
        $gid    = CRM_Utils_Request::retrieve('gid',  'Positive', $this, false, 0);
        $rgid   = CRM_Utils_Request::retrieve('rgid', 'Positive', $this, false, 0);
        $rgDao =& new CRM_Dedupe_DAO_RuleGroup();
        $rgDao->domain_id = CRM_Core_Config::DomainID();
        $rgDao->id = $rgid;
        $rgDao->find(true);

        $ruleDao =& new CRM_Dedupe_DAO_Rule();
        $ruleDao->dedupe_rule_group_id = $rgid;
        $ruleDao->find();
        $search = array();
        while ($ruleDao->fetch()) {
            $search[] = array(
                'table'  => $ruleDao->rule_table,
                'field'  => $ruleDao->rule_field,
                'length' => $ruleDao->rule_length ? $ruleDao->rule_length : null,
                'weight' => $ruleDao->rule_weight,
            );
        }

        if ($gid) {
            $foundDupes = $this->get("dedupe_dupes_$gid");
            if (!$foundDupes) $foundDupes = CRM_Dedupe_Finder::findDupesInGroup($gid, $search, $rgDao->threshold, $rgDao->contact_type);
            $this->set("dedupe_dupes_$gid", $foundDupes);
        } else {
            $foundDupes = $this->get("dedupe_dupes");
            if (!$foundDupes) $foundDupes = CRM_Dedupe_Finder::findDupes($search, $rgDao->threshold, $rgDao->contact_type);
            $this->set("dedupe_dupes", $foundDupes);
        }
        if (!$foundDupes) {
            $this->assign('no_dupes', true);
            return;
        }

        $cids = array_keys($foundDupes);
        foreach ($foundDupes as $dupeset) {
            $cids = array_merge($cids, $dupeset);
        }
        $cidString = implode(', ', array_unique($cids));
        $domainId  = CRM_Core_Config::domainID();
        $sql = "SELECT id, display_name FROM civicrm_contact WHERE id IN ($cidString) AND domain_id = $domainId ORDER BY sort_name";
        $dao =& new CRM_Core_DAO();
        $dao->query($sql);
        $displayNames = array();
        while ($dao->fetch()) {
            $displayNames[$dao->id] = $dao->display_name;
        }

        // FIXME: sort the contacts; $displayName 
        // is already sort_name-sorted, so use that
        // (also, consider sorting by dupe count first)
        $mainContacts = array();
        $dupeContacts = array();
        foreach ($foundDupes as $mainId => $dupes) {
            $mainContacts[$mainId] = $displayNames[$mainId];
            $localDupes = array();
            foreach ($dupes as $dupeId) {
                $localDupes[$dupeId] = $displayNames[$dupeId];
            }
            $dupeContacts[$mainId] = $localDupes;
        }
        if ($cid) $this->_cid = $cid;
        if ($gid) $this->_gid = $gid;
        $this->_rgid = $rgid;
        $this->_mainContacts = $mainContacts;
        $this->_dupeContacts = $dupeContacts;
    }

    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm()
    {
        $this->assign('main_contacts', $this->_mainContacts);
        $this->assign('dupe_contacts', $this->_dupeContacts);
        if ($this->_cid) $this->assign('cid', $this->_cid);
        if (isset($this->_gid) || $this->_gid) $this->assign('gid', $this->_gid);
        $this->assign('rgid', $this->_rgid);
    }

    function setDefaultValues()
    {
        if ($this->_cid) {
            $session =& CRM_Core_Session::singleton();
            $session->pushUserContext(CRM_Utils_System::url('civicrm/admin/dedupefind', "action=update&rgid={$this->_rgid}&gid={$this->_gid}&cid={$this->_cid}"));
        }
    }

    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        $exportValues = $this->exportValues();
        CRM_Core_Error::debug('$exportValues', $exportValues);
        exit;
    }
}

?>
