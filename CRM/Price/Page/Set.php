<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

require_once 'CRM/Core/Page.php';

/**
 * Create a page for displaying Price Sets.
 *
 * Heart of this class is the run method which checks
 * for action type and then displays the appropriate
 * page.
 *
 */
class CRM_Price_Page_Set extends CRM_Core_Page {

    /**
     * The action links that we need to display for the browse screen
     *
     * @var array
     */
    private static $_actionLinks;


    /**
     * Get the action links for this page.
     * 
     * @param null
     * 
     * @return  array   array of action links that we need to display for the browse screen
     * @access public
     */
    function &actionLinks()
    {
        // check if variable _actionsLinks is populated
        if (!isset(self::$_actionLinks)) {
            // helper variable for nicer formatting
            $deleteExtra = ts('Are you sure you want to delete this price set?');
            self::$_actionLinks = array(
                                        CRM_Core_Action::BROWSE  => array(
                                                                          'name'  => ts('View and Edit Price Fields'),
                                                                          'url'   => 'civicrm/admin/price/field',
                                                                          'qs'    => 'reset=1&action=browse&sid=%%sid%%',
                                                                          'title' => ts('View and Edit Price Fields'),
                                                                          ),
                                        CRM_Core_Action::PREVIEW => array(
                                                                          'name'  => ts('Preview'),
                                                                          'url'   => 'civicrm/admin/price',
                                                                          'qs'    => 'action=preview&reset=1&sid=%%sid%%',
                                                                          'title' => ts('Preview Price Set'),
                                                                          ),
                                        CRM_Core_Action::UPDATE  => array(
                                                                          'name'  => ts('Settings'),
                                                                          'url'   => 'civicrm/admin/price',
                                                                          'qs'    => 'action=update&reset=1&sid=%%sid%%',
                                                                          'title' => ts('Edit Price Set') 
                                                                          ),
                                        CRM_Core_Action::DISABLE => array(
                                                                          'name'  => ts('Disable'),
                                                                          'url'   => 'civicrm/admin/price',
                                                                          'qs'    => 'action=disable&reset=1&sid=%%sid%%',
                                                                          'title' => ts('Disable Price Set'),
                                                                          ),
                                        CRM_Core_Action::ENABLE  => array(
                                                                          'name'  => ts('Enable'),
                                                                          'url'   => 'civicrm/admin/price',
                                                                          'qs'    => 'action=enable&reset=1&sid=%%sid%%',
                                                                          'title' => ts('Enable Price Set'),
                                                                          ),
                                        CRM_Core_Action::DELETE  => array(
                                                                          'name'  => ts('Delete'),
                                                                          'url'   => 'civicrm/admin/price',
                                                                          'qs'    => 'action=delete&reset=1&sid=%%sid%%',
                                                                          'title' => ts('Delete Price Set'),
                                                                          'extra' => 'onclick = "return confirm(\'' . $deleteExtra . '\');"'
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
     * @param null
     * 
     * @return void
     * @access public
     *
     */
    function run()
    {
        // get the requested action
        $action = CRM_Utils_Request::retrieve('action', 'String',
                                              $this, false, 'browse'); // default to 'browse'
        
        // assign vars to templates
        $this->assign('action', $action);
        $sid = CRM_Utils_Request::retrieve('sid', 'Positive',
                                          $this, false, 0);
        
        // what action to take ?
        if ($action & (CRM_Core_Action::UPDATE | CRM_Core_Action::ADD)) {
            $this->edit($sid, $action) ;
        } else if ($action & CRM_Core_Action::PREVIEW) {
            $this->preview($sid) ;
        } else {
            require_once 'CRM/Core/BAO/PriceSet.php';
            require_once 'CRM/Core/BAO/PriceField.php';

            // if action is enable or disable to the needful.
            if ($action & (CRM_Core_Action::DISABLE | CRM_Core_Action::DELETE)) {
                $usedBy =& CRM_Core_BAO_PriceSet::getUsedBy( $sid );
                if ( empty( $usedBy ) ) {
                    if ( $action & CRM_Core_Action::DISABLE) {
                        // disable price set
                        CRM_Core_BAO_PriceSet::setIsActive( $sid, 0 );
                    } elseif ( $action & CRM_Core_Action::DELETE) {
                        // prompt to delete
                        $session = & CRM_Core_Session::singleton();
                        $session->pushUserContext(CRM_Utils_System::url('civicrm/admin/price', 'action=browse'));
                        $controller =& new CRM_Core_Controller_Simple( 'CRM_Price_Form_DeleteSet','Delete Price Set', null );
                        // $id = CRM_Utils_Request::retrieve('sid', 'Positive',
//                                                           $this, false, 0);
                        $controller->set('sid', $sid);
                        $controller->setEmbedded( true );
                        $controller->process( );
                        $controller->run( );
                    }
                } else {
                    // add breadcrumb
                    $url = CRM_Utils_System::url( 'civicrm/admin/price', 'reset=1' );
                    CRM_Utils_System::appendBreadCrumb( ts('Price Sets'),
                                                        $url );
                    $this->assign( 'usedPriceSetTitle', CRM_Core_BAO_PriceSet::getTitle( $sid ) );
                    $this->assign( 'usedBy', $usedBy );
                }
            } else if ($action & CRM_Core_Action::ENABLE) {
                CRM_Core_BAO_PriceSet::setIsActive($sid, 1);
            }

            // finally browse the price sets 
            $this->browse();
        }
        // parent run 
        parent::run();
    }


    /**
     * edit price set
     *
     * @param int    $id       price set id
     * @param string $action   the action to be invoked
     * 
     * @return void
     * @access public
     */
    function edit($sid, $action)
    {
        // create a simple controller for editing price sets
        $controller =& new CRM_Core_Controller_Simple('CRM_Price_Form_Set', ts('Price Set'), $action);

        // set the userContext stack
        $session =& CRM_Core_Session::singleton();
        $session->pushUserContext(CRM_Utils_System::url('civicrm/admin/price', 'action=browse'));
        $controller->set('sid', $sid);
        $controller->setEmbedded(true);
        $controller->process();
        $controller->run();
    }
    
    /**
     * Preview price set
     *
     * @param int $id price set id
     * @return void
     * @access public
     */
    function preview($sid)
    {
        $controller =& new CRM_Core_Controller_Simple('CRM_Price_Form_Preview', ts('Preview Price Set'), null);
        $session =& CRM_Core_Session::singleton();
        $session->pushUserContext(CRM_Utils_System::url('civicrm/admin/price', 'action=browse'));
        $controller->set('groupId', $sid);
        $controller->setEmbedded(true);
        $controller->process();
        $controller->run();
    }


    /**
     * Browse all price sets
     * 
     * @param string $action   the action to be invoked
     * 
     * @return void
     * @access public
     */
    function browse($action=null)
    {
        // get all price sets
        $priceSet = array();
        $dao =& new CRM_Core_DAO_PriceSet();

        $dao->find();

        while ($dao->fetch()) {
            $priceSet[$dao->id] = array();
            CRM_Core_DAO::storeValues( $dao, $priceSet[$dao->id] );
            // form all action links
            $action = array_sum(array_keys($this->actionLinks()));
            
            // update enable/disable links depending on price_set properties.
            if ($dao->is_active) {
                $action -= CRM_Core_Action::ENABLE;
            } else {
                $action -= CRM_Core_Action::DISABLE;
            }

            $priceSet[$dao->id]['action'] = CRM_Core_Action::formLink(self::actionLinks(), $action, 
                                                                      array('sid' => $dao->id) );
        }
        $this->assign('rows', $priceSet);
    }
}

