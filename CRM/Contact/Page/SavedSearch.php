<?php
/*
 +----------------------------------------------------------------------+
 | CiviCRM version 1.0                                                  |
 +----------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                    |
 +----------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                      |
 |                                                                      |
 | CiviCRM is free software; you can redistribute it and/or modify it   |
 | under the terms of the Affero General Public License Version 1,      |
 | March 2002.                                                          |
 |                                                                      |
 | CiviCRM is distributed in the hope that it will be useful, but       |
 | WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
 | See the Affero General Public License for more details at            |
 | http://www.affero.org/oagpl.html                                     |
 |                                                                      |
 | A copy of the Affero General Public License has been been            |
 | distributed along with this program (affero_gpl.txt)                 |
 +----------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'CRM/Core/Page.php';
require_once 'CRM/Contact/DAO/SavedSearch.php';

class CRM_Contact_Page_SavedSearch extends CRM_Core_Page {

    /**
     * constants for various modes that the page can operate as
     *
     * @var const int
     */
    const MODE_NONE = 0;

    function run() {
        if ($this->_mode == self::MODE_NONE) {
            $this->runModeNone();
        }
        return parent::run();
    }

    function runModeNone() {
        $rows = array();
        $ssDAO = new CRM_Contact_DAO_SavedSearch();
        $ssDAO->selectAdd();
        $ssDAO->selectAdd('id, name, search_type, description, form_values');
        $ssDAO->find();
        while ($ssDAO->fetch()) {
            $row = array();
            $properties = array('id', 'name', 'description');
            foreach ($properties as $property) {
                $row[$property] = $ssDAO->$property;
            }
            $row['query_detail'] = CRM_Contact_Selector::getQILL(unserialize($ssDAO->form_values), $ssDAO->search_type);
            $rows[] = $row;
        }
        $this->assign('rows', $rows);
    }
}
?>