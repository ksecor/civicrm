<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
 +--------------------------------------------------------------------+
 | copyright CiviCRM LLC (c) 2004-2006                        |
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
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 * 
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */
require_once '../../civicrm.config.php';
require_once 'CRM/Core/Config.php';
require_once 'CRM/Core/Error.php';
require_once 'CRM/Core/I18n.php';
require_once 'test/RSTest/Common.php';

class test_RSTest_GroupSearch
{
    
    private $_groupName;
    private $_groupNo;
    private $_group = array();
    function __construct()
    {
    }

    private function _search()
    {
        $arrayForSearch = array (
                                 'contact_type' => '',
                                 'tag'          => '',
                                 'sort_name'    => '',
                                 'task'         => '',
                                 'radio_ts'     => 'ts_sel',
                                 'group'        => array(
                                                         $this->_groupNo => 1
                                                         )
                                 );
        require_once 'CRM/Contact/BAO/Query.php';
        $contactBAO     =& new CRM_Contact_BAO_Query($arrayForSearch);
        $count          = $contactBAO->searchQuery(0, 0, null, true);
        return $count;
    }
    
    private function _parseForGroups()
    {
        $sampleData         = simplexml_load_file(test_RSTest_Common::DATA_FILENAME);
        
        // group
        foreach ($sampleData->groups->group as $group) {
            $this->_group[] = trim($group);
        }
    }
    
    private function _display()
    {
        echo "\n**********************************************************************************\n";
        echo "Searching for Finding the Members of a particular Group: \n";
        echo "The Groups are : \n" ;
        echo "--------------------------------------------\n";
        foreach ($this->_group as $groupID => $groupName) {
            echo "". ($groupID + 1) . " : {$groupName} \n";
        }
        echo "--------------------------------------------\n";
    }

    function run()
    {
        $this->_parseForGroups();
        $this->_display();
        fwrite(STDOUT, "Enter the Group Number for which Searching needs to be done : \t");
        while (trim($groupNO) == '') {
            $groupNO = fgetc(STDIN);
        }
        echo "\n**********************************************************************************\n";
        $this->_groupNo         = $groupNO;
        $this->_groupName       = $this->_group[$groupNO-1];
        $result                 = array();
        $result['criteria']     = array('group' => $this->_groupName);
        $startTimeGS            = microtime(true);
        $result['count']        = $this->_search();
        $endTimeGS              = microtime(true);
        $this->_groupSearchTime = $endTimeGS - $startTimeGS;
        $result['time']         = $this->_groupSearchTime;
        return $result;
    }
}
?>