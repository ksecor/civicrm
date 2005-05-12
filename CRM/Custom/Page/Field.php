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

require_once 'CRM/Core/Page/Basic.php';

class CRM_Custom_Page_Field extends CRM_Core_Page {
    
    /**
     * The group id of the field
     *
     * @var int
     */
    protected $_groupId;

    /**
     * The action links that we need to display for the browse screen
     *
     * @var array
     */
    static $_links = array(
                           CRM_Core_Action::VIEW    => array(
                                                        'name'  => 'View',
                                                        'url'   => 'civicrm/admin/custom/group/field',
                                                        'qs'    => 'action=view&id=%%id%%',
                                                        'title' => 'View Extended Property Group',
                                                        ),
                           CRM_Core_Action::UPDATE  => array(
                                                        'name'  => 'Edit',
                                                        'url'   => 'civicrm/admin/custom/group/field',
                                                        'qs'    => 'action=update&id=%%id%%',
                                                        'title' => 'Edit Extended Property Group'),
                           CRM_Core_Action::DISABLE => array(
                                                        'name'  => 'Disable',
                                                        'url'   => 'civicrm/admin/custom/group/field',
                                                        'qs'    => 'action=disable&id=%%id%%',
                                                        'title' => 'Disable Extended Property Group',
                                                        ),
                           CRM_Core_Action::ENABLE  => array(
                                                        'name'  => 'Enable',
                                                        'url'   => 'civicrm/admin/custom/group/field',
                                                        'qs'    => 'action=enable&id=%%id%%',
                                                        'title' => 'Enable Extended Property Group',
                                                        ),
                           );



    /**
     * Browse all custom data.
     *
     * @param none
     * @return none
     * @access public
     * @static
     */
    function browse()
    {
        CRM_Core_Error::le_method();

        $customField = array();
        $customFieldDAO = new CRM_Core_DAO_CustomField();
        $fields = $customFieldDAO->fields();
        
        CRM_Core_Error::debug_var('fields', $fields);
        
        $customFieldDAO->custom_group_id = $this->_groupId;
        $customFieldDAO->find();
        
        while ($customFieldDAO->fetch()) {
            $fieldId = $customFieldDAO->id;
            $customField[$fieldId] = array();
            foreach (array_keys($fields) as $fieldName) {
                $customField[$fieldId][$fieldName] = $customFieldDAO->$fieldName;
            }
        }

        $this->assign('customField', $customField);
        CRM_Core_Error::ll_method();
    }


    /**
     * edit custom data.
     *
     * editing would involved modifying existing fields + adding data to new fields.
     *
     * @param none
     * @returns none
     * @access public
     * @static
     */
    function edit()
    {
        // create a simple controller for editing custom data
        $controller = new CRM_Core_Controller_Simple('CRM_Custom_Form_Field', 'Custom Field', CRM_Core_Form::MODE_UPDATE);
        $controller->setEmbedded(true);

        // set the userContext stack
        $session = CRM_Core_Session::singleton();
        $session->pushUserContext(CRM_Utils_System::url('civicrm/admin/group/', 'action=browse'));
        
        $controller->reset();
        $controller->set('groupId' , $this->_groupId);
        $controller->process();
        $controller->run();
    }


    /**
     * Run the page.
     *
     * This method is called after the page is created. It checks for the  
     * type of action and executes that action. 
     *
     * @param none
     * @return none
     * @access public
     *
     */
    function run()
    {
        // get the group id
        $this->_groupId = CRM_Utils_Request::retrieve('gid', $this);
        if ($this->_groupId) {
            $this->assign('gid', $this->_groupId);
        }

        // get the requested action
        $action = CRM_Utils_Request::retrieve('action', $this, false, 'browse'); // default to 'browse'

        // assign vars to templates
        $this->assign('action', $action);
        
        // what action to take ?
        if ($action & (CRM_Core_Action::UPDATE)) {
            // both update and add are handled by 'edit'
            self::edit();
        } else {
            self::browse();
        }
        
        // call the parents run method
        parent::run();
    }
}

?>