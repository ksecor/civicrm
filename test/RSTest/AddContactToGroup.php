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

class test_RSTest_AddContactToGroup
{
    private $_contactArray = array();

    function __construct()
    {

    }
    
    /**
     * Getter for the contacts.
     *
     * This method is used for getting the neccessary contacts 
     * for the particular operation.
     *
     * @param   start        int    gives the id of the first contact from the group of contact 
     * @param   noOfContact  int    gives the no of contacts required for the operations.
     * 
     * @access  private
     * @return  contactArray array  gives the array of contacts on which the operations needs to be carried out. 
     *
     */
    private function _getContact($start, $noOfContact)
    {
        $contactDAO = new CRM_Contact_DAO_Contact();
        $contactDAO->selectAdd();
        $contactDAO->selectAdd('id');
        $contactDAO->limit($start, $noOfContact);
        $contactDAO->find();
        
        while ($contactDAO->fetch()) {
            $this->_contactArray[]  = $contactDAO->id;
        }
    }
    
    /**
     * Adding Group Contact Status Details. 
     *
     * This helper method is used to add details for group contact details.
     * This method can not be called statically.
     *
     * @param   groupContactDAO    group Contact DAO object
     *
     * @return  none
     *
     * @access  private
     *//*
    private function _setGroupContactStatus($groupContactDAO)
    {
        switch ($groupContactDAO->status) {
        case 'Pending':
            $groupContactDAO->pending_date   = test_RSTest_Common::getRandomDate();
            $groupContactDAO->pending_method = test_RSTest_Common::getRandomElement(array_values(test_RSTest_Common::$groupMethod), test_RSTest_Common::ARRAY_DIRECT_USE);
            break;
        case 'In':
            $groupContactDAO->in_date        = test_RSTest_Common::getRandomDate();
            $groupContactDAO->in_method      = test_RSTest_Common::getRandomElement(array_values(test_RSTest_Common::$groupMethod), test_RSTest_Common::ARRAY_DIRECT_USE);
            break;
        case 'Out':
            $groupContactDAO->out_date       = test_RSTest_Common::getRandomDate();
            $groupContactDAO->in_date        = test_RSTest_Common::getRandomDate(0, strtotime($groupContactDAO->out_date));
            $groupContactDAO->out_method     = test_RSTest_Common::getRandomElement(array_values(test_RSTest_Common::$groupMethod), test_RSTest_Common::ARRAY_DIRECT_USE);
            break;
        } 
    }
       */
    /** 
     * Add contact to Group.
     *
     * This function is used for adding Contacts to a Group.
     * 
     * @access private
     * @return none
     *     
     */
    private function _addToGroup()
    {
        foreach ($this->_contactArray as $id) {
            echo ".";
            ob_flush();
            flush();
            
            $groupContactDAO             =& new CRM_Contact_DAO_GroupContact();
            $groupContactDAO->group_id   = test_RSTest_Common::getRandomElement(test_RSTest_Common::getValue('group'), test_RSTest_Common::ARRAY_DIRECT_USE);
            $groupContactDAO->contact_id = $id;
            $groupContactDAO->status     = test_RSTest_Common::getRandomElement(array_values(test_RSTest_Common::$groupStatus), test_RSTest_Common::ARRAY_DIRECT_USE);
            
            if ($groupContactDAO->status != 'Pending') {
                test_RSTest_Common::_insert($groupContactDAO);
            }
        }
    }
    
    
    function run($start, $noOfContact)
    {
        $this->_getContact($start, $noOfContact);
        $this->_addToGroup();
    }
    
}
?>