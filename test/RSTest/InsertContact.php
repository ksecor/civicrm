<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 * Insertion of the contacts is done here 
 *
 * This class is implements the functionality for 
 * insertion of the contacts in the database.
 * 
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
require_once 'test/RSTest/Common.php';
require_once 'test/RSTest/GenDataset.php';

class test_RSTest_InsertContact extends test_RSTest_GenDataset
{
    private $_startID;
    
    function __construct($record=10)
    {
        parent::__construct($record);
    }

    function run($recordSize=0, $ID=0)
    {
        
        $this->_startID = $recordSize + $ID;
        //$this->_startID = $recordSize;
        //echo "Hello I 1 \n";
        parent::initID($this->_startID, $setDomain);
        //echo "Hello I 2 \n";
        parent::parseDataFile();
        //echo "Hello I 3 \n";
        parent::initDB();
        //echo "Hello I 4 \n";
        parent::addContact();
        //echo "Hello I 5 \n";
        parent::addIndividual();
        //echo "Hello I 6 \n";
        parent::addHousehold();
        //echo "Hello I 7 \n";
        parent::addOrganization();
        //echo "Hello I 8 \n";
        parent::addRelationship();
        //echo "Hello I 9 \n";
        parent::addLocation(1);
        //echo "Hello I 10 \n";
        parent::addEntityTag();
        //echo "Hello I 11 \n";
        parent::addGroup(false);
        //echo "Hello I 12 \n";
        parent::addNote();
        //echo "Hello I 13 \n";
        parent::addActivityHistory();
    }
}

?>
