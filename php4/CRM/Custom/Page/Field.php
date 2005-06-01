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

$GLOBALS['_CRM_CUSTOM_PAGE_FIELD']['_actionLinks'] = '';

require_once 'CRM/Core/Page.php';
require_once 'CRM/Core/BAO/CustomField.php';
require_once 'CRM/Core/Action.php';
require_once 'CRM/Core/Controller/Simple.php';
require_once 'CRM/Core/Session.php';
require_once 'CRM/Utils/System.php';
require_once 'CRM/Utils/Request.php';
require_once 'CRM/Core/BAO/CustomGroup.php';
require_once 'CRM/Core/Page.php';

/**
 * Create a page for displaying Custom Fields.
 *
 * Heart of this class is the run method which checks
 * for action type and then displays the appropriate
 * page.
 *
 */
class CRM_Custom_Page_Field extends CRM_Core_Page {
    
    /**
     * The group id of the field
     *
     * @var int
     * @access protected
     */
    var $_gid;

    /**
     * The action links that we need to display for the browse screen
     *
     * @var array
     * @access private
     */
    


    /*
     * Get the action links for this page.
     *
     * @param none
     * @return array $_actionLinks
     *
     */
    function &actionLinks()
    {
        if (!isset($GLOBALS['_CRM_CUSTOM_PAGE_FIELD']['_actionLinks'])) {
            // helper variable for nicer formatting
            $disableExtra = ts('Are you sure you want to disable this custom data field?');
            $GLOBALS['_CRM_CUSTOM_PAGE_FIELD']['_actionLinks'] = array(
                                        CRM_CORE_ACTION_UPDATE  => array(
                                                                          'name'  => ts('Edit'),
                                                                          'url'   => 'civicrm/admin/custom/group/field',
                                                                          'qs'    => 'action=update&id=%%id%%',
                                                                          'title' => ts('Edit Custom Field') 
                                                                          ),
                                        CRM_CORE_ACTION_VIEW    => array(
                                                                          'name'  => ts('View'),
                                                                          'url'   => 'civicrm/admin/custom/group/field',
                                                                          'qs'    => 'action=view&id=%%id%%',
                                                                          'title' => ts('View Custom Field'),
                                                                          ),
                                        CRM_CORE_ACTION_DISABLE => array(
                                                                          'name'  => ts('Disable'),
                                                                          'url'   => 'civicrm/admin/custom/group/field',
                                                                          'qs'    => 'action=disable&id=%%id%%',
                                                                          'title' => ts('Disable Custom Field'),
                                                                          'extra' => 'onclick = "return confirm(\'' . $disableExtra . '\');"',
                                                                          ),
                                        CRM_CORE_ACTION_ENABLE  => array(
                                                                          'name'  => ts('Enable'),
                                                                          'url'   => 'civicrm/admin/custom/group/field',
                                                                          'qs'    => 'action=enable&id=%%id%%',
                                                                          'title' => ts('Enable Custom Group'),
                                                                          ),
                                        );
        }
        return $GLOBALS['_CRM_CUSTOM_PAGE_FIELD']['_actionLinks'];
    }

    /**
     * Browse all custom group fields.
     *
     * @param none
     * @return none
     * @access public
     * @static
     */
    function browse()
    {
        $customField = array();
        $customFieldBAO = new CRM_Core_BAO_CustomField();
        
        // fkey is gid
        $customFieldBAO->custom_group_id = $this->_gid;
        $customFieldBAO->orderBy('weight');
        $customFieldBAO->find();
       
        while ($customFieldBAO->fetch()) {
            $customField[$customFieldBAO->id] = array();
            $customFieldBAO->storeValues($customField[$customFieldBAO->id]);

            $action = array_sum(array_keys($this->actionLinks()));
            if ($customFieldBAO->is_active) {
                $action -= CRM_CORE_ACTION_ENABLE;
            } else {
                $action -= CRM_CORE_ACTION_DISABLE;
            }
            $customField[$customFieldBAO->id]['data_type'] =
                //CRM_Core_BAO_CustomField::$_dataType[$customField[$customFieldBAO->id]['data_type']];
                $GLOBALS['_CRM_CORE_BAO_CUSTOMFIELD']['_dataType'][$customField[$customFieldBAO->id]['data_type']];
            //$GLOBALS['_CRM_CORE_BAO_CUSTOMFIELD']['_dataType'] =  array( 'String'  => 'Alphanumeric',                


            $customField[$customFieldBAO->id]['action'] = CRM_Core_Action::formLink(CRM_Custom_Page_Field::actionLinks(), $action, 
                                                                                    array('id' => $customFieldBAO->id));
        }
        $this->assign('customField', $customField);
    }


    /**
     * edit custom data.
     *
     * editing would involved modifying existing fields + adding data to new fields.
     *
     * @param string $action the action to be invoked

     * @return none
     * @access public
     */
    function edit($action)
    {
        // create a simple controller for editing custom data
        $controller = new CRM_Core_Controller_Simple('CRM_Custom_Form_Field', ts('Custom Field'), $action);

        // set the userContext stack
        $session = CRM_Core_Session::singleton();
        $session->pushUserContext(CRM_Utils_System::url('civicrm/admin/custom/group/field', 'reset=1&action=browse&gid=' . $this->_gid));
       
        $controller->set('gid', $this->_gid);
        $controller->setEmbedded(true);
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
        $this->_gid = CRM_Utils_Request::retrieve('gid', $this);
        if ($this->_gid) {
            $groupTitle = CRM_Core_BAO_CustomGroup::getTitle($this->_gid);
            $this->assign('gid', $this->_gid);
            $this->assign('groupTitle', $groupTitle);
            CRM_Utils_System::setTitle("$groupTitle - Custom Fields");
        }

        // get the requested action
        $action = CRM_Utils_Request::retrieve('action', $this, false, 'browse'); // default to 'browse'

        // assign vars to templates
        $this->assign('action', $action);

        $id = CRM_Utils_Request::retrieve('id', $this, false, 0);
        
        // what action to take ?
        if ($action & (CRM_CORE_ACTION_UPDATE | CRM_CORE_ACTION_ADD | CRM_CORE_ACTION_VIEW)) {
            $this->edit($action);   // no browse for edit/update/view
        } else {
            if ($action & CRM_CORE_ACTION_DISABLE) {
                CRM_Core_BAO_CustomField::setIsActive($id, 0);
            } else if ($action & CRM_CORE_ACTION_ENABLE) {
                CRM_Core_BAO_CustomField::setIsActive($id, 1);
            } 
            $this->browse();
        }

        // Call the parents run method
        parent::run();
    }
}

?>