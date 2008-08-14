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
 * @copyright CiviCRM LLC (c) 2004-2008
 * $Id$
 *
 */

require_once 'CRM/Core/DAO/Domain.php';
require_once 'CRM/Core/Form.php';

class CRM_Core_I18n_Form extends CRM_Core_Form
{
    function buildQuickForm()
    {
        $config =& CRM_Core_Config::singleton();
        $this->_locales = array_keys($config->languageLimit);

        // get the part of the database we want to edit and validate it
        $this->_table = CRM_Utils_Request::retrieve('table', 'String', $this);
        $this->_field = CRM_Utils_Request::retrieve('field', 'String', $this);
        $this->_id    = CRM_Utils_Request::retrieve('id',    'Int',    $this);
        $structure    = CRM_Core_I18n_SchemaStructure::columns();
        if (!isset($structure[$this->_table][$this->_field])) {
            CRM_Core_Error::fatal("$this->_table.$this->_field is not internationalized.");
        }

        $cols = array();
        foreach ($this->_locales as $locale) {
            $cols[] = "{$this->_field}_{$locale} {$locale}";
        }
        $query = 'SELECT ' . implode(', ', $cols) . " FROM $this->_table WHERE id = $this->_id";

        $dao =& new CRM_Core_DAO();
        $dao->query($query, false);
        $dao->fetch();

        // we want TEXTAREAs for long fields and INPUTs for short ones
        switch ($structure[$this->_table][$this->_field]) {
        case 'text':         $type = 'textarea'; break;
        case 'varchar(255)': $type = 'textarea'; break;
        default:             $type = 'text';     break;
        }
        $languages = CRM_Core_I18n::languages(true);
        foreach ($this->_locales as $locale) {
            $this->addElement($type, $locale, $languages[$locale], array('cols' => 60, 'rows' => 3));
            $this->_defaults[$locale] = $dao->$locale;
        }

        $this->addButtons(array(array('type' => 'next', 'name' => ts('Save'), 'isDefault' => true)));

        $this->assign('locales', $this->_locales);
        $this->assign('field',   $this->_field);
    }

    function setDefaultValues()
    {
        return $this->_defaults;
    }

    function postProcess()
    {
        $values = $this->exportValues();

        $cols   = array();
        $params = array(array($this->_id, 'Int'));
        $i = 1;
        foreach ($this->_locales as $locale) {
            $cols[] = "{$this->_field}_{$locale} = %$i";
            $params[$i] = array($values[$locale], 'String');
            $i++;
        }
        $query = "UPDATE $this->_table SET " . implode(', ', $cols) . " WHERE id = %0";

        $dao =& new CRM_Core_DAO();
        $query = CRM_Core_DAO::composeQuery($query, $params, true, $dao);
        $dao->query($query, false);

        exit;
#       $session =& CRM_Core_Session::singleton();
#       $session->replaceUserContext(CRM_Utils_System::refererPath());
    }
}
