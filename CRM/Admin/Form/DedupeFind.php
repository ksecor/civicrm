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

require_once 'CRM/Admin/Form.php';
require_once 'CRM/Dedupe/Finder.php';

/**
 * This class generates form components for DedupeRules
 * 
 */
class CRM_Admin_Form_DedupeFind extends CRM_Admin_Form
{
    protected $_displayNames;

    /**
     * Function to pre processing
     *
     * @return None
     * @access public
     */
    function preProcess()
    {
        $search = array(
            array('table' => 'civicrm_contact',    'field' => 'display_name', 'length' => 5, 'weight' => 10),
            array('table' => 'civicrm_individual', 'field' => 'last_name',                   'weight' =>  7),
            array('table' => 'civicrm_email',      'field' => 'email',        'length' => 6, 'weight' =>  5),
        );
        $foundDupes = CRM_Dedupe_Finder::findDupes($search, 15);
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
        $radios = array();
        foreach ($this->_dupeContacts as $mainId => $dupes) {
            foreach ($dupes as $dupeId => $dupeName) {
                $radios[] = HTML_QuickForm::createElement('radio', null, null, $dupeName, "$mainId|$dupeId");
            }
        }
        $this->addGroup($radios, 'merge_ids');
        $this->assign('main_contacts', $this->_mainContacts);
        $this->assign('dupe_contacts', $this->_dupeContacts);
        $this->addButtons(array(
            array('type' => 'next', 'name' => ts('Merge'), 'isDefault' => true),
        ));
    }

    function setDefaultValues()
    {
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
