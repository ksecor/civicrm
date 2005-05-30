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

/**
 * Main page for viewing Activities.
 *
 */
class CRM_Contact_Page_Activity {


    /**
     * View a single activity
     *
     * @param object $page - CRM_Contact_Page_View - main view page object
     * @param int $activityTableId - id of the activity table
     * @return none
     *
     * @access public
     * @static
     */
    static function view($page, $activityTableId)
    {
        $activityDAO = new CRM_Core_DAO_Activity();
        $activityDAO->id = $activityTableId;
        if ($activityDAO->find(true)) {
            $values = array();
            $activityDAO->storeValues($values);
            $page->assign('activity', $values);
        }
        self::browse($page);
    }

    /**
     * Browse all activities for a particular contact
     *
     * @param object $page - CRM_Contact_Page_View - main view page object
     * @return none
     *
     * @access public
     * @static
     */
    static function browse($page)
    {
        $values = CRM_Core_BAO_Activity::getActivity($page->getContactId());
        $page->assign('activity', $values);
    }


    /**
     * Add, Update or View a single activity
     *
     * @param object $page - CRM_Contact_Page_View - main view page object
     * @param int $mode - CRM_Core_Action::ADD | CRM_Core_Action::UPDATE | CRM_Core_Action::VIEW  
     * @param int $activityTableId - optional - id for update and view mode
     * @return none
     *
     * @access public
     * @static
     */
    static function edit($page, $mode, $activityTableId = null)
    {
        $controller = new CRM_Core_Controller_Simple('CRM_Activity_Form_Activity', 'Contact Activity', $mode);
        $controller->setEmbedded(true);

        // set the userContext stack
        $session = CRM_Core_Session::singleton();
        $session->pushUserContext(CRM_Utils_System::url('civicrm/contact/view/activity', 'action=browse'));

        $controller->reset();
        $controller->set('tableName', 'crm_contact');
        $controller->set('tableId', $page->getContactId());
        $controller->set('activityTableId', $activityTableId);

        $controller->process();
        $controller->run();
    }


    /**
     * perform actions and display for activities.
     *
     * @param object CRM_Contact_Page_View
     * @return none
     *
     * @access public
     * @static
     */
    static function run($page)
    {
        // get contactid and action for current page
        $contactId = $page->getContactId();
        $page->assign('contactId', $contactId);
        $action = CRM_Utils_Request::retrieve('action', $page, false, 'browse');
        $page->assign('action', $action);
        
        // used for edit, view purpose
        $activityTableId = CRM_Utils_Request::retrieve('activityTableId', $page, false, 0);

        if ($action & CRM_Core_Action::VIEW) {
            self::view($page, $activityTableId); // view activity
        } else if ($action & (CRM_Core_Action::UPDATE | CRM_Core_Action::ADD)) {
            self::edit($page, $action, $activityTableId); // add / update
        } else if ($action & CRM_Core_Action::DELETE) {
            CRM_Core_BAO_Activity::delete($activityTableId);
        }
        self::browse($page);
    }
}
?>