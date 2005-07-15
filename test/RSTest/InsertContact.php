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
 * Insertion of the contacts is done here 
 *
 * This class is implements the functionality for 
 * insertion of the contacts in the database.
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
require_once 'test/RSTest/Common.php';
require_once 'test/RSTest/GenDataset.php';

class test_RSTest_InsertContact extends test_RSTest_GenDataset
{
    private $_startID;
    
    function __construct($record=10)
    {
        parent::__construct($record);
    }

    function run($recordSize=0, $ID=0, $setDomain)
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