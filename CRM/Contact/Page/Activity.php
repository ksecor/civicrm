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

    static function browse($page)
    {
        $activityDAO = new CRM_Core_DAO_Activity();
        $activityDAO->entity_id = $page->getContactId();
        $activityDAO->orderBy('activity_date desc');

        $values = array();
        $activityDAO->find();
        while ($activityDAO->fetch()) {
            $values[$activityDAO->id] = array();
            $activityDAO->storeValues($values[$activityDAO->id]);
        }
        $page->assign('activity', $values);
    }

    static function edit($page, $mode, $activityTableId = null)
    {
        $controller = new CRM_Core_Controller_Simple('CRM_Note_Form_Note', 'Contact Notes', $mode);
        $controller->setEmbedded(true);

        // set the userContext stack
        $session = CRM_Core_Session::singleton();
        $session->pushUserContext(CRM_Utils_System::url('civicrm/contact/view/activity', 'action=browse'));

        $controller->reset();
        $controller->set('entityId', $page->getContactId());
        $controller->set('activityTableId', $activityTableId);

        $controller->process();
        $controller->run();
    }


    static function run($page)
    {
        $contactId = $page->getContactId();
        $page->assign('contactId', $contactId);

        $action = CRM_Utils_Request::retrieve('action', $page, false, 'browse');
        $page->assign('action', $action);

        $activityTableId = CRM_Utils_Request::retrieve( 'activityTableId', $page, false, 0 );

        if ($action & CRM_Core_Action::VIEW) {
            self::view($page, $activityTableId);
        } else if ($action & (CRM_Core_Action::UPDATE | CRM_Core_Action::ADD)) {
            self::edit($page, $action, $activityTableId);
        } else if ($action & CRM_Core_Action::DELETE) {
            self::delete( $activityTableId );
        }
        self::browse($page);
    }


    static function delete($activityTableId)
    {
        CRM_Core_BAO_Note::del($activityTableId);
    }

}

?>