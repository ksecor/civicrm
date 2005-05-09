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

class CRM_Contact_Page_CustomData {

    /**
     * Browse all custom data.
     *
     * @access public
     *
     * @param object $page - the view page
     *
     * @return none
     *
     * @static
     */
    static function browse($page)
    {
        // get custom data for contact
        $customData = CRM_Contact_BAO_Contact::getCustomData($page->getContactId());
        $basicGroupTree = CRM_Core_BAO_CustomGroup::getBasicTree(CRM_Contact_BAO_Contact::getContactType($page->getContactId()));

        $entityType = CRM_Contact_BAO_Contact::getContactType($page->getContactId());
        $contactID = $page->getContactId();

        $groupTree = CRM_Core_BAO_CustomGroup::getTree($entityType, $contactID);
        $page->assign('groupTree', $groupTree);
    }

    /**
     * edit custom data.
     *
     * editing would involved modifying existing fields + adding data to new fields.
     *
     * @access public
     *
     * @param object $page - view page of contact
     * @param int $action - is it ADD or UPDATE ?
     * @param int $contactId - which contact's custom data are we editing ?
     *
     * @returns none
     *
     * @static
     */
    static function edit($page, $action)
    {
        //CRM_Core_Error::le_method();

        $controller = new CRM_Core_Controller_Simple('CRM_Contact_Form_CustomData', 'Custom Data', $mode);
        $controller->setEmbedded(true);

        // set the userContext stack
        $session = CRM_Core_Session::singleton();
        $session->pushUserContext(CRM_Utils_System::url('civicrm/contact/view/cd', 'action=browse'));
        
        $controller->reset();
        $controller->set('tableName', 'crm_contact');
        $controller->set('tableId'  , $page->getContactId());
        $controller->set('entityType', CRM_Contact_BAO_Contact::getContactType($page->getContactId()));
        $controller->process();
        $controller->run();

        //CRM_Core_Error::ll_method();
    }


    /**
     * Run the page.
     *
     * This method is called after the page is created. It checks for the  
     * type of action and executes that action. 
     *
     * @access public
     * @param object $page - the view page which created this one 
     * @return none
     * @static
     *
     */
    static function run($page)
    {

        //CRM_Core_Error::le_method();

        // get the contact id & requested action
        $contactId = $page->getContactId();
        $action = CRM_Utils_Request::retrieve('action', $page, false, 'browse'); // default to 'browse'

        //CRM_Core_Error::debug_var('action', $action);
        
        // assign vars to templates
        $page->assign('contactId', $contactId);
        $page->assign('action', $action);

        // what action to take ?
        if ($action & (CRM_Core_Action::UPDATE | CRM_Core_Action::ADD)) {

            //CRM_Core_Error::debug_log_message('breakpoint1');
            // both update and add are handled by 'edit'
            self::edit($page, $action, $contactId);
        }
        self::browse($page);

        //CRM_Core_Error::ll_method();
    }
}
?>