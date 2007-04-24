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

require_once 'CRM/Core/Form.php';

class CRM_Contact_Form_Merge  extends CRM_Core_Form
{
    function preProcess()
    {
        require_once 'api/Contact.php';
        require_once 'api/Search.php';
        require_once 'CRM/Dedupe/Merger.php';
        $cid = CRM_Utils_Request::retrieve('cid', 'Positive', $this, false);
        $oid = CRM_Utils_Request::retrieve('oid', 'Positive', $this, false);
        $main  = crm_get_contact(array('contact_id' => $cid));
        $other = crm_get_contact(array('contact_id' => $oid));
        $diffs = CRM_Dedupe_Merger::findDifferences($cid, $oid);
        $this->assign('main_name',  $main->display_name);
        $this->assign('other_name', $other->display_name);
        $this->assign('diffs', $diffs);

        foreach ($diffs['contact'] as $field) {
            $group['main']  = HTML_QuickForm::createElement('radio', null, null, $main->$field, 'main');
            $group['other'] = HTML_QuickForm::createElement('radio', null, null, $other->$field, 'other');
            $this->addGroup($group, $field, $field);
        }
    }
    
    function setDefaultValues()
    {
        $defaults = array();
        return $defaults;
    }
    
    function addRules()
    {
    }

    public function buildQuickForm()
    {
        $this->addButtons(array(
            array('type' => 'next',   'name' => ts('Merge'), 'isDefault' => true),
            array('type' => 'cancel', 'name' => ts('Cancel')),
        ));
    }

    public function postProcess()
    {
    }
}

?>
