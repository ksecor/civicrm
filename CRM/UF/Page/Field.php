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
 * Create a page for displaying User Framework Fields.
 *
 * Heart of this class is the run method which checks
 * for action type and then displays the appropriate
 * page.
 *
 */
class CRM_UF_Page_Field extends CRM_Core_Page {
    
    /**
     * The group id of the field
     *
     * @var int
     * @access protected
     */
    protected $_gid;

    /**
     * The action links that we need to display for the browse screen
     *
     * @var array
     * @access private
     */
    private static $_actionLinks;


    /*
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
            $disableExtra = ts('Are you sure you want to disable this user framework data field?');
            self::$_actionLinks = array(
                                        CRM_Core_Action::UPDATE  => array(
                                                                          'name'  => ts('Edit'),
                                                                          'url'   => 'civicrm/admin/uf/group/field',
                                                                          'qs'    => 'action=update&id=%%id%%',
                                                                          'title' => ts('Edit User Framework Field') 
                                                                          ),
                                        CRM_Core_Action::VIEW    => array(
                                                                          'name'  => ts('View'),
                                                                          'url'   => 'civicrm/admin/uf/group/field',
                                                                          'qs'    => 'action=view&id=%%id%%',
                                                                          'title' => ts('View User Framework Field'),
                                                                          ),
                                        CRM_Core_Action::DISABLE => array(
                                                                          'name'  => ts('Disable'),
                                                                          'url'   => 'civicrm/admin/uf/group/field',
                                                                          'qs'    => 'action=disable&id=%%id%%',
                                                                          'title' => ts('Disable User Framework Field'),
                                                                          'extra' => 'onclick = "return confirm(\'' . $disableExtra . '\');"',
                                                                          ),
                                        CRM_Core_Action::ENABLE  => array(
                                                                          'name'  => ts('Enable'),
                                                                          'url'   => 'civicrm/admin/uf/group/field',
                                                                          'qs'    => 'action=enable&id=%%id%%',
                                                                          'title' => ts('Enable User Framework Group'),
                                                                          ),
                                        );
        }
        return self::$_actionLinks;
    }

    /**
     * Browse all user framework group fields.
     *
     * @param none
     * @return none
     * @access public
     * @static
     */
    function browse()
    {
        $ufField = array();
        $ufFieldBAO =& new CRM_Core_BAO_UFField();
        
        // fkey is gid
        $ufFieldBAO->uf_group_id = $this->_gid;
        $ufFieldBAO->orderBy('field_name');
        $ufFieldBAO->find();

        $fields =& CRM_Contact_BAO_Contact::importableFields( );
        $select = array( );
        foreach ($fields as $name => $field ) {
            if ( $name ) {
                $select[$name] = $field['title'];
            }
        }

        while ($ufFieldBAO->fetch()) {
            $ufField[$ufFieldBAO->id] = array();
            CRM_Core_DAO::storeValues( $ufFieldBAO, $ufField[$ufFieldBAO->id]);

            // fix the field_name value
            $ufField[$ufFieldBAO->id]['field_name'] = $select[$ufField[$ufFieldBAO->id]['field_name']];

            $action = array_sum(array_keys($this->actionLinks()));
            if ($ufFieldBAO->is_active) {
                $action -= CRM_Core_Action::ENABLE;
            } else {
                $action -= CRM_Core_Action::DISABLE;
            }
            
            $ufField[$ufFieldBAO->id]['action'] = CRM_Core_Action::formLink(self::actionLinks(), $action, 
                                                                            array('id' => $ufFieldBAO->id));
        }
        $this->assign('ufField', $ufField);
    }


    /**
     * edit user framework data.
     *
     * editing would involved modifying existing fields + adding data to new fields.
     *
     * @param string $action the action to be invoked

     * @return none
     * @access public
     */
    function edit($action)
    {
        // create a simple controller for editing user framework data
        $controller =& new CRM_Core_Controller_Simple('CRM_UF_Form_Field', ts('User Framework Field'), $action);

        // set the userContext stack
        $session =& CRM_Core_Session::singleton();
        $session->pushUserContext(CRM_Utils_System::url('civicrm/admin/uf/group/field', 'reset=1&action=browse&gid=' . $this->_gid));
       
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
            $groupTitle = CRM_Core_BAO_UFGroup::getTitle($this->_gid);
            $this->assign('gid', $this->_gid);
            $this->assign('groupTitle', $groupTitle);
            CRM_Utils_System::setTitle(ts('%1 - User Framework Fields', array(1 => $groupTitle)));
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
                CRM_Core_BAO_UFField::setIsActive($id, 0);
            } else if ($action & CRM_Core_Action::ENABLE) {
                CRM_Core_BAO_UFField::setIsActive($id, 1);
            } 
            $this->browse();
        }

        // Call the parents run method
        parent::run();
    }
}

?>
