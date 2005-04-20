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

class CRM_Contact_Page_SavedSearch extends CRM_Page {

    /**
     * constants for various modes that the page can operate as
     *
     * @var const int
     */
    const MODE_NONE = 0;

    /**
     * class constructor
     *
     * @param string $name  name of the page
     * @param string $title title of the page
     * @param int    $mode  mode of the page
     *
     * @return CRM_Page
     */
    function __construct( $name, $title = null, $mode = null ) {
        parent::__construct($name, $title, $mode);
    }

    function run() {
        CRM_Error::le_method();
        if ($this->_mode == self::MODE_NONE) {
            $this->runModeNone();
        }
        CRM_Error::ll_method();        
        return parent::run();
    }

    function runModeNone() {
        $rows = array();
        $ssDAO = new CRM_Contact_DAO_SavedSearch();
        $ssDAO->find();
        while ($ssDAO->fetch()) {
            $row = array();
            CRM_Error::debug_log_message("fetching a saved search");
            $properties = array('name', 'description', 'query', 'form_values', 'qill');
            foreach ($properties as $property) {
                $row[$property] = $ssDAO->$property;
            }
            $rows[] = $row;
        }
        CRM_Error::debug_var('rows', $rows);
        $this->assign('rows', $rows);
    }
}
?>