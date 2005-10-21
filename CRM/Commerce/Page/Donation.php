<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.1                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Social Source Foundation                        |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the Affero General Public License Version 1,    |
 | March 2002.                                                        |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the Affero General Public License for more details.            |
 |                                                                    |
 | You should have received a copy of the Affero General Public       |
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Social Source Foundation (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Core/Page.php';

/**
 * Create a page for displaying Donation Pages
 * Donation Pages are pages that are used to display
 * donations of different types. Pages consist
 * of many customizable sections which can be
 * accessed.
 *
 * This page provides a top level browse view
 * of all the donation pages in the system.
 *
 */
class CRM_Commerce_Page_Donation extends CRM_Core_Page {

    /**
     * The action links that we need to display for the browse screen
     *
     * @var array
     */
    private static $_actionLinks;


    /**
     * Get the action links for this page.
     *
     * @return array $_actionLinks
     *
     */
    function &actionLinks()
    {
        // check if variable _actionsLinks is populated
        if (!isset(self::$_actionLinks)) {
            // helper variable for nicer formatting
            self::$_actionLinks = array(
                                        CRM_Core_Action::PREVIEW => array(
                                                                          'name'  => ts('Preview Donation Page'),
                                                                          'url'   => 'civicrm/commerce/donation',
                                                                          'qs'    => 'reset=1&action=preview&id=%%id%%',
                                                                          'title' => ts('Preview Donation Page'),
                                                                          ),
                                        CRM_Core_Action::BROWSE  => array(
                                                                          'name'  => ts('Browse Pages'),
                                                                          'url'   => 'civicrm/commerce/donation',
                                                                          'qs'    => 'reset=1&action=browse&id=%%id%%',
                                                                          'title' => ts('List Page Sections'),
                                                                          ),
                                        CRM_Core_Action::UPDATE  => array(
                                                                          'name'  => ts('Edit Donation Page'),
                                                                          'url'   => 'civicrm/commerce/donation',
                                                                          'qs'    => 'reset=1&action=update&id=%%id%%',
                                                                          'title' => ts('Edit Donation Page') 
                                                                          ),
                                        CRM_Core_Action::DISABLE => array(
                                                                          'name'  => ts('Disable'),
                                                                          'url'   => 'civicrm/commerce/donation',
                                                                          'qs'    => 'action=disable&id=%%id%%',
                                                                          'title' => ts('Disable Donation Page'),
                                                                          'extra' => 'onclick = "return confirm(\'' . $disableExtra . '\');"',
                                                                          ),
                                        CRM_Core_Action::ENABLE  => array(
                                                                          'name'  => ts('Enable'),
                                                                          'url'   => 'civicrm/commerce/donation',
                                                                          'qs'    => 'action=enable&id=%%id%%',
                                                                          'title' => ts('Enable Donation Page'),
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
     * @return void
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
        } else if ($action & CRM_Core_Action::PREVIEW) {
            $this->preview($id) ;
        } else {
            // if action is enable or disable to the needful.
            if ($action & CRM_Core_Action::DISABLE) {
                CRM_Commerce_BAO_Donation::setIsActive($id, 0);
            } else if ($action & CRM_Core_Action::ENABLE) {
                CRM_Commerce_BAO_Donation::setIsActive($id, 1);
            }

            // finally browse the custom groups
            $this->browse();
        }

        return parent::run();
    }


    /**
     * edit donation page
     *
     * @param int $id donation page id
     * @param string $action the action to be invoked
     * @return void
     * @access public
     */
    function edit($id, $action)
    {
        // create a simple controller for editing custom data
        $controller =& new CRM_Core_Controller_Simple('CRM_Commerce_Form_Donation', ts('Donation Page'), $action);

        // set the userContext stack
        $session =& CRM_Core_Session::singleton();
        $session->pushUserContext(CRM_Utils_System::url('civicrm/admin/commerce/donation', 'action=browse'));
        $controller->set('id', $id);
        $controller->setEmbedded(true);
        $controller->process();
        $controller->run();
    }


    /**
     * Preview custom group
     *
     * @param int $id custom group id
     * @return void
     * @access public
     */
    function preview($id)
    {
        $controller =& new CRM_Core_Controller_Simple('CRM_Commerce_Donation_Form_Preview', ts('Preview Donation Page'), $action);
        $session =& CRM_Core_Session::singleton();
        $session->pushUserContext(CRM_Utils_System::url('civicrm/commerce/donation', 'action=browse'));
        $controller->set('id', $id);
        $controller->process();
        $controller->run();
    }


    /**
     * Browse all custom data groups.
     *
     * @return void
     * @access public
     * @static
     */
    function browse($action=null)
    {
        
        // get all custom groups sorted by weight
        $donation =  array();
        $dao      =& new CRM_Commerce_DAO_Donation();

        // set the domain_id parameter
        $config =& CRM_Core_Config::singleton( );
        $dao->domain_id = $config->domainID( );

        $dao->orderBy('name');
        $dao->find();

        while ($dao->fetch()) {
            $donation[$dao->id] = array();
            CRM_Core_DAO::storeValues($dao, $donation[$dao->id]);
            // form all action links
            $action = array_sum(array_keys($this->actionLinks()));
            
            // update enable/disable links depending on custom_group properties.
            if ($dao->is_active) {
                $action -= CRM_Core_Action::ENABLE;
            } else {
                $action -= CRM_Core_Action::DISABLE;
            }
            
            $donation[$dao->id]['action'] = CRM_Core_Action::formLink(self::actionLinks(), $action, 
                                                                          array('id' => $dao->id));
        }
        $this->assign('rows', $donation);
    }
}
?>
