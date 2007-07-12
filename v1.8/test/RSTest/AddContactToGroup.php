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
        require_once 'CRM/Contact/DAO/Contact.php';
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
            
            require_once 'CRM/Contact/DAO/GroupContact.php';
            require_once 'CRM/Contact/DAO/SubscriptionHistory.php';
            $groupContactDAO             =& new CRM_Contact_DAO_GroupContact();
            $groupContactDAO->group_id   = test_RSTest_Common::getRandomElement(test_RSTest_Common::getValue('group'), test_RSTest_Common::ARRAY_DIRECT_USE);
            $groupContactDAO->contact_id = $id;
            $groupContactDAO->status     = test_RSTest_Common::getRandomElement(array_values(test_RSTest_Common::$groupStatus), test_RSTest_Common::ARRAY_DIRECT_USE);
            
            $subscriptionHistoryDAO             = new CRM_Contact_DAO_SubscriptionHistory();
            $subscriptionHistoryDAO->contact_id = $groupContactDAO->contact_id;
            $subscriptionHistoryDAO->group_id   = $groupContactDAO->group_id;
            $subscriptionHistoryDAO->status     = $groupContactDAO->status;
            $subscriptionHistoryDAO->method     = test_RSTest_Common::getRandomElement((test_RSTest_Common::$subscriptionHistoryMethod), test_RSTest_Common::ARRAY_DIRECT_USE); // method
            $subscriptionHistoryDAO->date       = test_RSTest_Common::getRandomDate();
            if ($groupContactDAO->status != 'Pending') {
                test_RSTest_Common::_insert($groupContactDAO);
            }
            test_RSTest_Common::_insert($subscriptionHistoryDAO);
        }
    }
    
    
    function run($start, $noOfContact)
    {
        $this->_getContact($start, $noOfContact);
        $this->_addToGroup();
    }
    
}
?>
