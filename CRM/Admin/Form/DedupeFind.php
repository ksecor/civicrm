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
        $cidstring = implode(', ', array_unique($cids));
        $sql = "SELECT id, display_name FROM civicrm_contact WHERE id IN ($cidstring) AND domain_id = " . CRM_Core_Config::domainID();
        $dao =& new CRM_Core_DAO();
        $dao->query($sql);
        $displayNames = array();
        while ($dao->fetch()) {
            $displayNames[$dao->id] = $dao->display_name;
        }
        $this->_foundDupes   = $foundDupes;
        $this->_displayNames = $displayNames;
    }

    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm()
    {
        $this->assign('found_dupes', $this->_foundDupes);
        $this->assign('display_names', $this->_displayNames);
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
    }
}

?>
