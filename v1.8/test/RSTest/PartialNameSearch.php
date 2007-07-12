<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.8                                                |
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

require_once '../../civicrm.config.php';
require_once 'CRM/Core/Config.php';
require_once 'CRM/Core/Error.php';
require_once 'CRM/Core/I18n.php';
require_once 'CRM/Contact/BAO/Contact.php';

class test_RSTest_PartialNameSearch
{
    private $_partialName;
    
    function __construct()
    {
    }

    private function _search()
    {
        $arrayForSearch = array (
                                 'contact_type' => '',
                                 'group'        => '',
                                 'tag'          => '',
                                 'sort_name'    => $this->_partialName
                                 );
        
        require_once 'CRM/Contact/BAO/Query.php';
        $contactBAO     =& new CRM_Contact_BAO_Query($arrayForSearch);
        $count          = $contactBAO->searchQuery(0, 0, null, true);
        return $count;
    }
    
    function run()
    {
        echo "\n**********************************************************************************\n";
        fwrite(STDOUT, "Enter the Partial Name of the Contact for which Searching needs to be done : \t");
        do {
            $name = fgets(STDIN);
        } while (trim($name) == '');
        echo "\n**********************************************************************************\n";
        $this->_partialName = $name;
        $result             = array();
        $result['criteria']           = array('name' => $this->_partialName);
        $startTimePNS                 = microtime(true);        
        $result['count']              = $this->_search();
        $endTimePNS                   = microtime(true);
        $this->_partialNameSearchTime = $endTimePNS - $startTimePNS;
        $result['time']               = $this->_partialNameSearchTime;        
        return $result;
    }
}
?>
