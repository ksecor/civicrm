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
 * Main page for viewing history of activities.
 *
 */
//class CRM_Contact_Page_Activity extends CRM_Core_Page {
class CRM_Contact_Page_Activity {

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
        // create the selector, controller and run - store results in session
        $output = CRM_Core_Selector_Controller::SESSION;
        $selector   = new CRM_History_Selector_Activity($page->getContactId());
        $controller = new CRM_Core_Selector_Controller($selector,
                                                       $page->get(CRM_Utils_Pager::PAGE_ID),
                                                       $page->get(CRM_Utils_Sort::SORT_ID),
                                                       CRM_Core_Action::VIEW, $page, $output);

        $controller->setEmbedded(true);
        $controller->run();
        $controller->moveFromSessionToTemplate( );
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
        $historyId = CRM_Utils_Request::retrieve('historyId', $page, false, 0);
        
        if ($action & CRM_Core_Action::VIEW) {
            //self::view($page, $activityTableId); // view activity
        } else if ($action & CRM_Core_Action::DELETE) {
            CRM_Core_BAO_History::delete($historyId, 'Activity');
        }
        self::browse($page);
    }
}
?>