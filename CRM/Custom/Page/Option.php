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
 * Create a page for displaying Custom Options.
 *
 * Heart of this class is the run method which checks
 * for action type and then displays the appropriate
 * page.
 *
 */
class CRM_Custom_Page_Option extends CRM_Core_Page {
     
    /**
     * The Group id of the option
     *
     * @var int
     * @access protected
     */
    protected $_gid;
    
    /**
     * The field id of the option
     *
     * @var int
     * @access protected
     */
    protected $_fid;

    /**
     * The action links that we need to display for the browse screen
     *
     * @var array
     * @access private
     */
    private static $_actionLinks;


    /**
     * Get the action links for this page.
     *
     * @param none
     * @return array $_actionLinks
     *
     */
    function &actionLinks()
    {
        if (!isset(self::$_actionLinks)) {
            // helper variable for nicer formatting
            $disableExtra = ts('Are you sure you want to disable this custom data option?');
            self::$_actionLinks = array(
                                        CRM_Core_Action::ENABLE  => array(
                                                                          'name'  => ts('Enable'),
                                                                          'url'   => 'civicrm/admin/custom/group/field/option',
                                                                          'qs'    => 'action=enable&id=%%id%%',
                                                                          'title' => ts('Enable Custom Option') 
                                                                          ),
                                        CRM_Core_Action::DISABLE  => array(
                                                                           'name'  => ts('Disable'),
                                                                          'url'   => 'civicrm/admin/custom/group/field/option',
                                                                          'qs'    => 'action=disable&id=%%id%%',
                                                                          'title' => ts('Disable Custom Field'),
                                                                           'extra' => 'onclick = "return confirm(\'' . $disableExtra . '\');"'
                                                                           ),
                                        CRM_Core_Action::UPDATE  => array(
                                                                          'name'  => ts('Edit Option'),
                                                                          'url'   => 'civicrm/admin/custom/group/field/option',
                                                                          'qs'    => 'action=update&id=%%id%%',
                                                                          'title' => ts('Edit Custom Option') 
                                                                          ),
                                        CRM_Core_Action::VIEW    => array(
                                                                          'name'  => ts('View'),
                                                                          'url'   => 'civicrm/admin/custom/group/field/option',
                                                                          'qs'    => 'action=view&id=%%id%%',
                                                                          'title' => ts('View Custom Option'),
                                                                          ),
                                        );
        }
        return self::$_actionLinks;
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
        $customOption = array();
        $customOptionBAO =& new CRM_Core_BAO_CustomOption();
        
        // fkey is fid
        $customOptionBAO->custom_field_id = $this->_fid;
        $customOptionBAO->orderBy('weight');
        $customOptionBAO->find();
       
        while ($customOptionBAO->fetch()) {
            $customOption[$customOptionBAO->id] = array();
            CRM_Core_DAO::storeValues( $customOptionBAO, $customOption[$customOptionBAO->id]);

            $action = array_sum(array_keys($this->actionLinks()));
	    
	    // update enable/disable links depending on custom_field properties.
            if ($customOptionBAO->is_active) {
                $action -= CRM_Core_Action::ENABLE;
            } else {
                $action -= CRM_Core_Action::DISABLE;
            }
            
            $customOption[$customOptionBAO->id]['action'] = CRM_Core_Action::formLink(self::actionLinks(), $action, 
                                                                                    array('id' => $customOptionBAO->id));
        }
        $this->assign('customOption', $customOption);
    }


    /**
     * edit custom Option.
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
        $controller =& new CRM_Core_Controller_Simple('CRM_Custom_Form_Option', ts('Custom Option'), $action);

        // set the userContext stack
        $session =& CRM_Core_Session::singleton();
        $session->pushUserContext(CRM_Utils_System::url('civicrm/admin/custom/group/field/option', 'reset=1&action=browse&fid=' . $this->_fid));
       
        $controller->set('gid', $this->_gid);
        $controller->set('fid', $this->_fid);
        $controller->setEmbedded(true);
        $controller->process();
        $controller->run();
        $this->browse();
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
        
        // get the field id
        $this->_fid = CRM_Utils_Request::retrieve('fid', $this);
        $this->_gid = CRM_Utils_Request::retrieve('gid', $this);
        
        if ($this->_fid) {
	    $fieldTitle = CRM_Core_BAO_CustomField::getTitle($this->_fid);
            $this->assign('fid', $this->_fid);
            $this->assign('fieldTitle', $fieldTitle);
            CRM_Utils_System::setTitle(ts('%1 - Custom Options', array(1 => $fieldTitle)));
        }

        // get the requested action
        $action = CRM_Utils_Request::retrieve('action', $this, false, 'browse'); // default to 'browse'

        // assign vars to templates
        $this->assign('action', $action);

        $id = CRM_Utils_Request::retrieve('id', $this, false, 0);
        
        // what action to take ?
        if ($action & (CRM_Core_Action::UPDATE | CRM_Core_Action::ADD | CRM_Core_Action::VIEW)) {
            $this->edit($action);   // no browse for edit/update/view
        } else {
            if ($action & CRM_Core_Action::DISABLE) {
                CRM_Core_BAO_CustomOption::setIsActive($id, 0);
            } else if ($action & CRM_Core_Action::ENABLE) {
                CRM_Core_BAO_CustomOption::setIsActive($id, 1);
            }
           $this->browse();
        }
        // Call the parents run method
        parent::run();
    }
}

?>
