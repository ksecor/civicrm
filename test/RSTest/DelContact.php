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
require_once 'test/RSTest/Common.php';
require_once 'CRM/Core/I18n.php';

class test_RSTest_DelContact
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
     * @param   noOfContact  int    gives the no of contacts required for the operation.
     * 
     * @access  private
     *
     */
    private function _getContact($start, $noOfContact)
    {
        $contactDAO = new CRM_Contact_DAO_Contact();
        $contactDAO->selectAdd();
        $contactDAO->selectAdd('id');
        $contactDAO->whereAdd('id > '. $start . ' AND id <='. ($start + $noOfContact));
        $contactDAO->find();
        
        while ($contactDAO->fetch()) {
            $this->_contactArray[]  = $contactDAO->id;
        }
    }
    
    /**
     * Delete Contact from Group. 
     *
     * Delete Group Contact associated with this Contact.
     *
     * @param   $contactId  int   id of the contact to be deleted
     *
     * @return   void
     * @access   private
     * 
     */
    private function _deleteContactFromGroup($contactId) 
    {
        $groupContactDAO =& new CRM_Contact_DAO_GroupContact();
        $groupContactDAO->contact_id = $contactId;
        $groupContactDAO->delete();
    }
    
    public function _deleteSubscriptionHistory($contactId) {
        $historyDAO =& new CRM_Contact_BAO_SubscriptionHistory();
        $historyDAO->contact_id = $contactId;
        $historyDAO->delete();
    }
    
    /**
     * Delete the Relationship of the Contact. 
     * 
     * This function is used for Deleting the Relationship that are associated with this Contact.
     *
     * @param   int  $contactId   id of the contact to delete
     *
     * @return  void
     * @access  public
     * @static
     */
    private function _deleteRelOfContact($contactId) 
    {
        $relationshipDAO =& new CRM_Contact_DAO_Relationship();
        $relationshipDAO->contact_id_a = $contactId;
        $relationshipDAO->delete();
        
        $relationshipDAO =& new CRM_Contact_DAO_Relationship();
        $relationshipDAO->contact_id_b = $contactId;
        $relationshipDAO->delete();
    }
    
    /**
     * Delete Contct from Note
     * 
     * This method is used for deleting all Records for Contact in context of Notes.  
     *
     * @param   int     $contactId  id of Contact to Delete
     * @access  private
     * @return  void
     * 
     */
    private function _deleteNote($contactId)
    {
        // need to delete for both entity_id
        $noteDAO = new CRM_Core_DAO_Note();
        $noteDAO->entity_table = 'civicrm_contact';
        $noteDAO->entity_id   = $contactId;
        $noteDAO->delete();
        
        // and the creator contact id
        $noteDAO = new CRM_Core_DAO_Note();
        $noteDAO->contact_id = 1;        
        $noteDAO->delete();
    }
    

    private function _deleteActivityHistory($contactId)
    {
        $activityHistoryDAO = new CRM_Core_DAO_ActivityHistory();

        $activityHistoryDAO->entity_table = 'civicrm_contact';
        $activityHistoryDAO->entity_id    = $contactId;
        $activityHistoryDAO->delete();
    }
    
    /**
     * Delete the object records that are associated with this contact
     *
     * @param   int     $contactId   id of the contact to delete
     *
     * @return  void
     * @access  private
     * 
     */
    private function _deleteLocation($contactId)
    {
        $locationDAO =& new CRM_Core_DAO_Location();
        $locationDAO->entity_id = $contactId;
        $locationDAO->entity_table = CRM_Contact_DAO_Contact::getTableName();
        $locationDAO->find();
        while ($locationDAO->fetch()) {
            $this->_deleteLocationBlocks($locationDAO->id);
            $locationDAO->delete();
        }
    }
    
    /**
     * Delete the object records that are associated with this location
     *
     * @param  int  $locationId id of the location to delete
     *
     * @return void
     * @access public
     * @static
     */
    private function _deleteLocationBlocks($locationId) 
    {
        $blocks = array('Address', 'Phone', 'Email', 'IM');
        foreach ($blocks as $name) {
            eval('$object =& new CRM_Core_DAO_' . $name . '();');
            $object->location_id = $locationId;
            $object->delete();
        }
    }
    
    /**
     * Delete the Contacts.
     *
     * This is the main function used for Deleting Contacts. 
     * 
     * @return   void 
     * @access   private
     *
     */
    private function _deleteContact()
    {
        foreach ($this->_contactArray as $id) {
            echo ".";
            ob_flush();
            flush();
            
            CRM_Core_DAO::transaction('BEGIN');
            $this->_deleteContactFromGroup($id);
            $this->_deleteSubscriptionHistory($id);
            $this->_deleteRelOfContact($id);
            $this->_deleteNote($id);
            $this->_deleteActivityHistory($id);
            $this->_deleteLocation($id);
        
            // fix household and org primary contact ids
            $misc = array('Household', 'Organization');
            foreach ($misc as $name) {
                eval( '$object =& new CRM_Contact_DAO_' . $name . '();' );
                $object->primary_contact_id = $id;
                $object->find();
                while ($object->fetch()) {
                    // we need to set this to null explicitly
                    $object->primary_contact_id = 'null';
                    $object->save();
                }
            }

            // get the contact type
            $contactDAO =& new CRM_Contact_DAO_Contact();
            $contactDAO->id = $id;
            if ($contactDAO->find(true)) {
                eval( '$object =& new CRM_Contact_BAO_' . $contactDAO->contact_type . '();' );
                $object->contact_id = $contactDAO->id;
                $object->delete();
                $contactDAO->delete();
            }
            
            CRM_Core_DAO::transaction('COMMIT');
        }
    }
    
    function run($start, $noOfContact)
    {
        $this->_getContact($start, $noOfContact);
        $this->_deleteContact();
    }
    
}
?>