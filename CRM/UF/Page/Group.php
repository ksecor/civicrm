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
 * Create a page for displaying UF Groups.
 *
 * Heart of this class is the run method which checks
 * for action type and then displays the appropriate
 * page.
 *
 */
class CRM_UF_Page_Group extends CRM_Core_Page {

    /**
     * The action links that we need to display for the browse screen
     *
     * @var array
     */
    private static $_actionLinks = null;

    /**
     * Get the action links for this page.
     *
     * @param none
     * @return array $_actionLinks
     *
     */
    function &actionLinks()
    {
        // check if variable _actionsLinks is populated
        if ( ! self::$_actionLinks ) {
            // helper variable for nicer formatting
            $disableExtra = ts('Are you sure you want to disable this CiviCRM Profile group?');
            self::$_actionLinks = array(
                                        CRM_Core_Action::BROWSE  => array(
                                                                          'name'  => ts('View and Edit Fields'),
                                                                          'url'   => 'civicrm/admin/uf/group/field',
                                                                          'qs'    => 'reset=1&action=browse&gid=%%id%%',
                                                                          'title' => ts('List CiviCRM Profile Group Fields'),
                                                                          ),
                                        CRM_Core_Action::UPDATE  => array(
                                                                          'name'  => ts('Edit Profile Name'),
                                                                          'url'   => 'civicrm/admin/uf/group',
                                                                          'qs'    => 'action=update&id=%%id%%',
                                                                          'title' => ts('Edit CiviCRM Profile Group') 
                                                                          ),
                                        CRM_Core_Action::DISABLE => array(
                                                                          'name'  => ts('Disable'),
                                                                          'url'   => 'civicrm/admin/uf/group',
                                                                          'qs'    => 'action=disable&id=%%id%%',
                                                                          'title' => ts('Disable CiviCRM Profile Group'),
                                                                          'extra' => 'onclick = "return confirm(\'' . $disableExtra . '\');"',
                                                                          ),
                                        CRM_Core_Action::ENABLE  => array(
                                                                          'name'  => ts('Enable'),
                                                                          'url'   => 'civicrm/admin/uf/group',
                                                                          'qs'    => 'action=enable&id=%%id%%',
                                                                          'title' => ts('Enable CiviCRM Profile Group'),
                                                                          ),
                                        );
        }
        return self::$_actionLinks;
    }

    /**
     * Run the page.
     *
     * This method is called after the page is created. It checks for the  
     * type of action and executes that action.
     * Finally it calls the parent's run method.
     *
     * @param none
     * @return none
     * @access public
     *
     */
    function run()
    {
        // get the requested action
        $action = CRM_Utils_Request::retrieve('action', $this, false, 'browse'); // default to 'browse'

        // assign vars to templates
        $this->assign('action', $action);
        $id = CRM_Utils_Request::retrieve('id', $this, false, 0);
        
        // what action to take ?
        if ($action & (CRM_Core_Action::UPDATE | CRM_Core_Action::ADD)) {
            $this->edit($id, $action) ;
        } else {
            // if action is enable or disable to the needful.
            if ($action & CRM_Core_Action::DISABLE) {
                CRM_Core_BAO_UFGroup::setIsActive($id, 0);
            } else if ($action & CRM_Core_Action::ENABLE) {
                CRM_Core_BAO_UFGroup::setIsActive($id, 1);
            }

            // finally browse the uf groups
            $this->browse();
        }
        // parent run 
        parent::run();
    }


    /**
     * edit uf group
     *
     * @param int $id uf group id
     * @param string $action the action to be invoked
     * @return none
     * @access public
     */
    function edit($id, $action)
    {
        // create a simple controller for editing uf data
        $controller =& new CRM_Core_Controller_Simple('CRM_UF_Form_Group', ts('CiviCRM Profile Group'), $action);

        // set the userContext stack
        $session =& CRM_Core_Session::singleton();
        $session->pushUserContext(CRM_Utils_System::url('civicrm/admin/uf/group/', 'action=browse'));
        $controller->set('id', $id);
        $controller->setEmbedded(true);
        $controller->process();
        $controller->run();
    }


    /**
     * Browse all uf data groups.
     *
     * @param none
     * @return none
     * @access public
     * @static
     */
    function browse($action=null)
    {
        
        $ufGroup = array();
        $dao =& new CRM_Core_DAO_UFGroup();

        // set the domain_id parameter
        $config =& CRM_Core_Config::singleton( );
        $dao->domain_id = $config->domainID( );

        $dao->orderBy('title');
        $dao->find();

        while ($dao->fetch()) {
            $ufGroup[$dao->id] = array();
            CRM_Core_DAO::storeValues( $dao, $ufGroup[$dao->id]);
            // form all action links
            $action = array_sum(array_keys($this->actionLinks()));
            
            // update enable/disable links depending on uf_group properties.
            if ($dao->is_active) {
                $action -= CRM_Core_Action::ENABLE;
            } else {
                $action -= CRM_Core_Action::DISABLE;
            }
            
            $ufGroup[$dao->id]['action'] = CRM_Core_Action::formLink(self::actionLinks(), $action, 
                                                                                    array('id' => $dao->id));
        }
        $this->assign('rows', $ufGroup);
    }
}
?>