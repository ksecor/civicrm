<?php
/**
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
 * Redefine the jump action.
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */


require_once 'CRM/Core/QuickForm/Action.php';
require_once 'CRM/Core/QuickForm/Action.php';

class CRM_Core_QuickForm_Action_Jump extends CRM_Core_QuickForm_Action {

    /**
     * class constructor
     *
     * @param object $stateMachine reference to state machine object
     *
     * @return object
     * @access public
     */
    function CRM_Core_QuickForm_Action_Jump( &$stateMachine ) {
        parent::CRM_Core_QuickForm_Action( $stateMachine );
    }

    /**
     * Processes the request. 
     *
     * @param  object    $page       CRM_Core_Form the current form-page
     * @param  string    $actionName Current action name, as one Action object can serve multiple actions
     *
     * @return void
     * @access public
     */
    function perform(&$page, $actionName) {
        // check whether the page is valid before trying to go to it
        if ($page->controller->isModal()) {
            // we check whether *all* pages up to current are valid
            // if there is an invalid page we go to it, instead of the
            // requested one
            $pageName = $page->getAttribute('id');
            if (!$page->controller->isValid($pageName)) {
                $pageName = $page->controller->findInvalid();
            }
            $current =& $page->controller->getPage($pageName);

        } else {
            $current =& $page;
        }
        // generate the URL for the page 'display' event and redirect to it
        $action = $current->getAttribute('action');
        $url    = $action . (false === strpos($action, '?')? '?': '&') .
                  $current->getButtonName('display') . '=true' .
                  ((!defined('SID') || '' == SID)? '': '&' . SID);
        header('Location: ' . $url);
        exit();
    }

}

?>