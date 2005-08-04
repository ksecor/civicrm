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
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once '../../modules/config.inc.php';
require_once '../../CRM/Core/Config.php';
require_once 'CRM/Core/Error.php';
require_once 'CRM/Core/I18n.php';
require_once 'CRM/Contact/BAO/Contact.php';

class test_RSTest_PartialNameSearch
{
    private $_partialName;
    
    function __construct()
    {
    }

    function search()
    {
        $arrayForSearch = array ('sort_name' => $this->_partialName);
        $contactBAO = new CRM_Contact_BAO_Contact();
        $result = $contactBAO->searchQuery($arrayForSearch, 0, 0, null, true);
        print_r($result);
    }
    
    function run()
    {
        $name='Zop';
//         echo "\n**********************************************************************************\n";
        // do {
//             fwrite(STDOUT, "Enter Partial Name For Search: \t");
//             $name = fgets(STDIN);
//         } while (trim($name) == '');

//        echo "\n**********************************************************************************\n";
        $this->_partialName = $name;
        
        $this->search();
    }
}
?>