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
require_once 'test/RSTest/Common.php';

class test_RSTest_InsertRel
{
    private $_contactArray = array();

    function __construct()
    {
        
    }
    
    /**
     * Getter for the Contacts.
     *
     * This method is used for getting the neccessary contacts 
     * for the particular operation.
     *
     * @param   start        int    gives the id of the first contact from the group of contact.
     * @param   noOfContact  int    gives the no of contacts required for the operations.
     * 
     * @access  private
     * @return  none
     */
    
    private function _getContact($start, $noOfContact)
    {
        $contactDAO = new CRM_Contact_DAO_Contact();
        $contactDAO->selectAdd();
        $contactDAO->selectAdd('id,contact_type');
        $contactDAO->limit($start, ($start + $noOfContact));
        $contactDAO->find();
        
        while ($contactDAO->fetch()) {
            $this->_contactArray[] = array($contactDAO->id,
                                           $contactDAO->contact_type
                                           );
        }
    }
    
    private function _insertRel()
    {
        $individualArray   = array();
        $householdArray    = array();
        $organizationArray = array();

        foreach ($this->_contactArray as $value) {
            if ($value[1] == 'Individual') {
                $individualArray[]   = $value[0];
            } else if ($value[1] == 'Household') {
                $householdArray[]    = $value[0];
            } else {
                $organizationArray[] = $value[0];
            }
        }
        
        $relationshipDAO            =& new CRM_Contact_DAO_Relationship();
        $relationshipDAO->is_active = 1; // all active for now.
        
        foreach ($householdArray as $id) {
            $relationshipDAO->relationship_type_id = mt_rand(6,7);
            $relationshipDAO->contact_id_b         = $id;
            $relationshipDAO->contact_id_a         = test_RSTest_Common::getRandomElement($individualArray, test_RSTest_Common::ARRAY_DIRECT_USE);
            test_RSTest_Common::_insert($relationshipDAO);
        }
        
        foreach ($organizationArray as $id) {
            $relationshipDAO->relationship_type_id = mt_rand(4,5);
            $relationshipDAO->contact_id_b         = $id;
            $relationshipDAO->contact_id_a         = test_RSTest_Common::getRandomElement($individualArray, test_RSTest_Common::ARRAY_DIRECT_USE);
            test_RSTest_Common::_insert($relationshipDAO);
        }
        
        foreach ($individualArray as $id) {
            $secondContact = test_RSTest_Common::getRandomElement($individualArray, test_RSTest_Common::ARRAY_DIRECT_USE);
            if ($id == $secondContact) {
                $secondContact = test_RSTest_Common::getRandomElement($individualArray, test_RSTest_Common::ARRAY_DIRECT_USE);
            }
            $relationshipDAO->relationship_type_id = mt_rand(1,3);
            $relationshipDAO->contact_id_b         = $id;
            $relationshipDAO->contact_id_a         = $secondContact;
            test_RSTest_Common::_insert($relationshipDAO);
        }

    }
    
    function run($start, $noOfContact) 
    {
        //parent::addRelationship();
        $this->_getContact($start, $noOfContact);
        //print_r($this->_contactArray);
        $this->_insertRel();
    }
}
?>