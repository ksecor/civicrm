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

class CRM_Contact_Page_Activity {

    /**
     * Browse all activities for a particular contact
     *
     * @param object $page - CRM_Contact_Page_View - main view page object
     * @param boolean $history - true if we want to browse activity history, false otherwise.
     * @return none
     *
     * @access public
     * @static
     */
    static function browse($page, $history)
    {
        
        $page->assign( 'totalCountOpenActivity',
                       CRM_Contact_BAO_Contact::getNumOpenActivity( $page->getContactId( ) ) );
        $page->assign( 'totalCountActivity',
                       CRM_Core_BAO_History::getNumHistory( $page->getContactId( ),
                                                            'Activity' ) );

        if ($history) {
  
            $page->assign('history', true);

            // create the selector, controller and run - store results in session
            $output = CRM_Core_Selector_Controller::SESSION;
            $selector   =& new CRM_History_Selector_Activity($page->getContactId());
            $sortID     = null;
            if ( $page->get( CRM_Utils_Sort::SORT_ID  ) ) {
                $sortID = CRM_Utils_Sort::sortIDValue( $page->get( CRM_Utils_Sort::SORT_ID  ),
                                                       $page->get( CRM_Utils_Sort::SORT_DIRECTION ) );
            }
            $controller =& new CRM_Core_Selector_Controller($selector, $page->get(CRM_Utils_Pager::PAGE_ID),
                                                            $sortID, CRM_Core_Action::VIEW, $page, $output);
            $controller->setEmbedded(true);
            $controller->run();
            $controller->moveFromSessionToTemplate( );
        } else {
  
            $page->assign('history', false);

            // create the selector, controller and run - store results in session
            $output = CRM_Core_Selector_Controller::SESSION;
            $selector   =& new CRM_Contact_Selector_Activity($page->getContactId());
            $sortID     = null;
            if ( $page->get( CRM_Utils_Sort::SORT_ID  ) ) {
                $sortID = CRM_Utils_Sort::sortIDValue( $page->get( CRM_Utils_Sort::SORT_ID  ),
                                                       $page->get( CRM_Utils_Sort::SORT_DIRECTION ) );
            }
            $controller =& new CRM_Core_Selector_Controller($selector, $page->get(CRM_Utils_Pager::PAGE_ID),
                                                            $sortID, CRM_Core_Action::VIEW, $page, $output);
            $controller->setEmbedded(true);
            $controller->run();
            $controller->moveFromSessionToTemplate( );
        }
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

        // get selector type ? open or closed activities ?
        $history = CRM_Utils_Request::retrieve('history', $page, false, null, 'GET');

        // used for delete
        $activityHistoryId = CRM_Utils_Request::retrieve('activity_history_id', $page, false, 0);

        if ($action & CRM_Core_Action::DELETE) {
            $url   = 'civicrm/contact/view/activity';
            $session =& CRM_Core_Session::singleton();
            $session->pushUserContext(CRM_Utils_System::url($url, 'action=browse&history=1'));
            $controller =& new CRM_Core_Controller_Simple('CRM_History_Form_Activity', ts('Delete Activity History'), $action);
            $controller->set('activityHistoryId', $activityHistoryId);
            $controller->setEmbedded(true);
            $controller->process();
            $controller->run();
        }

        self::browse($page, $history);
    }
}
?>