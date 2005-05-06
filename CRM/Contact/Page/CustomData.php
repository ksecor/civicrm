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
     * View custom data.
     *
     * @access public
     *
     * @param object $page - the view page
     *
     * @return none
     *
     * @static
     */
    static function view($page)
    {

        CRM_Core_Error::le_method();

        // get contact
        $params   = array();
        $defaults = array();
        $ids      = array();

        $params['id'] = $this->_contactId;
        $contact = CRM_Contact_BAO_Contact::retrieve($params, $defaults, $ids);

        CRM_Core_Error::debug_var('params', $params);
        CRM_Core_Error::debug_var('defaults', $defaults);
        CRM_Core_Error::debug_var('ids', $ids);

        //CRM_Contact_BAO_Contact::resolveDefaults( $defaults );

        // $contactType = CRM_Contact_BAO_Contact::getContactType($page->getContactId());

        // get groups, fields for contact type & id
        //$customData = CRM_Core_BAO_CustomGroup::getCustomData($page->getContactID());

        CRM_Core_Error::ll_method();
    }

    static function browse( $page )
    {
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
        $controller = new CRM_Core_Controller_Simple('CRM_Contact_Form_CustomData', 'Custom Data', $mode);
        $controller->setEmbedded( true );

        // set the userContext stack
        $session = CRM_Core_Session::singleton();
        $session->pushUserContext(CRM_Utils_System::url('civicrm/contact/view/cd', 'action=browse'));

        $controller->run();
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

        CRM_Core_Error::le_method();

        // get the contact id & requested action
        $contactId = $page->getContactId();
        $action = CRM_Utils_Request::retrieve('action', $page, false, 'browse'); // default to 'browse'
        
        // assign vars to templates
        $page->assign('contactId', $contactId);
        $page->assign('action', $action);

        // what action to take ?
        if ($action & CRM_Core_Action::VIEW) {
            self::view($page);
        } else if ($action & (CRM_Core_Action::UPDATE | CRM_Core_Action::ADD)) {
            // both update and add are handled by 'edit'
            self::edit($page, $action, $contactId);
        }
        self::browse($page);

        CRM_Core_Error::ll_method();
    }
}

?>