<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.8                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
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
                                                                          'qs'    => 'reset=1&action=browse&gid=%%id%%',
                                                                          'title' => ts('View and Edit Price Fields'),
                                                                          ),
                                        CRM_Core_Action::PREVIEW => array(
                                                                          'name'  => ts('Preview'),
                                                                          'url'   => 'civicrm/admin/price',
                                                                          'qs'    => 'action=preview&reset=1&id=%%id%%',
                                                                          'title' => ts('Preview Price Set'),
                                                                          ),
                                        CRM_Core_Action::UPDATE  => array(
                                                                          'name'  => ts('Settings'),
                                                                          'url'   => 'civicrm/admin/price',
                                                                          'qs'    => 'action=update&reset=1&id=%%id%%',
                                                                          'title' => ts('Edit Price Set') 
                                                                          ),
                                        CRM_Core_Action::DISABLE => array(
                                                                          'name'  => ts('Disable'),
                                                                          'url'   => 'civicrm/admin/price',
                                                                          'qs'    => 'action=disable&reset=1&id=%%id%%',
                                                                          'title' => ts('Disable Price Set'),
                                                                          ),
                                        CRM_Core_Action::ENABLE  => array(
                                                                          'name'  => ts('Enable'),
                                                                          'url'   => 'civicrm/admin/price',
                                                                          'qs'    => 'action=enable&reset=1&id=%%id%%',
                                                                          'title' => ts('Enable Price Set'),
                                                                          ),
                                        CRM_Core_Action::DELETE  => array(
                                                                          'name'  => ts('Delete'),
                                                                          'url'   => 'civicrm/admin/price',
                                                                          'qs'    => 'action=delete&reset=1&id=%%id%%',
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

        $this->assign( 'dojoIncludes', "dojo.require('dojo.widget.SortableTable');" );
        // get the requested action
        $action = CRM_Utils_Request::retrieve('action', 'String',
                                              $this, false, 'browse'); // default to 'browse'
        
        // assign vars to templates
        $this->assign('action', $action);
        $id = CRM_Utils_Request::retrieve('id', 'Positive',
                                          $this, false, 0);
        
        // what action to take ?
        if ($action & (CRM_Core_Action::UPDATE | CRM_Core_Action::ADD)) {
            $this->edit($id, $action) ;
        } else if ($action & CRM_Core_Action::PREVIEW) {
            $this->preview($id) ;
        } else {
            require_once 'CRM/Core/BAO/PriceSet.php';
            require_once 'CRM/Core/BAO/PriceField.php';

            // if action is enable or disable to the needful.
            if ($action & (CRM_Core_Action::DISABLE | CRM_Core_Action::DELETE)) {
                $usedBy =& CRM_Core_BAO_PriceSet::getUsedBy( $id );
                if ( empty( $usedBy ) ) {
                    // remove from all inactive forms
                    $usedBy =& CRM_Core_BAO_PriceSet::getUsedBy( $id, true, true );
                    if ( isset( $usedBy['civicrm_event_page'] ) ) {
                        require_once 'CRM/Event/DAO/EventPage.php';
                        foreach ( $usedBy['civicrm_event_page'] as $eventId => $unused ) {
                            $eventPageDAO =& new CRM_Event_DAO_EventPage( );
                            $eventPageDAO->event_id = $eventId;
                            $eventPageDAO->find();
                            while ( $eventPageDAO->fetch() ) {
                                CRM_Core_BAO_PriceSet::removeFrom( 'civicrm_event_page', $eventPageDAO->id );
                            }
                        }
                    }
                    if ( $action & CRM_Core_Action::DISABLE) {
                        // disable price set
                        CRM_Core_BAO_PriceSet::setIsActive( $id, 0 );
                    } elseif ( $action & CRM_Core_Action::DELETE) {
                        // prompt to delete
                        $session = & CRM_Core_Session::singleton();
                        $session->pushUserContext(CRM_Utils_System::url('civicrm/admin/price', 'action=browse'));
                        $controller =& new CRM_Core_Controller_Simple( 'CRM_Price_Form_DeleteSet','Delete Price Set', null );
                        $id = CRM_Utils_Request::retrieve('id', 'Positive',
                                                          $this, false, 0);
                        $controller->set('id', $id);
                        $controller->setEmbedded( true );
                        $controller->process( );
                        $controller->run( );
                    }
                } else {
                    // add breadcrumb
                    $url = CRM_Utils_System::url( 'civicrm/admin/price', 'reset=1' );
                    $additionalBreadCrumb = '<a href="' . $url . '">' . ts('Price Sets') . '</a>';
                    CRM_Utils_System::appendBreadCrumb( $additionalBreadCrumb );
                    $this->assign( 'usedPriceSetTitle', CRM_Core_BAO_PriceSet::getTitle( $id ) );
                    $this->assign( 'usedBy', $usedBy );
                }
            } else if ($action & CRM_Core_Action::ENABLE) {
                CRM_Core_BAO_PriceSet::setIsActive($id, 1);
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
    function edit($id, $action)
    {
        // create a simple controller for editing price sets
        $controller =& new CRM_Core_Controller_Simple('CRM_Price_Form_Set', ts('Price Set'), $action);

        // set the userContext stack
        $session =& CRM_Core_Session::singleton();
        $session->pushUserContext(CRM_Utils_System::url('civicrm/admin/price', 'action=browse'));
        $controller->set('id', $id);
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
    function preview($id)
    {
        $controller =& new CRM_Core_Controller_Simple('CRM_Price_Form_Preview', ts('Preview Price Set'), null);
        $session =& CRM_Core_Session::singleton();
        $session->pushUserContext(CRM_Utils_System::url('civicrm/admin/price', 'action=browse'));
        $controller->set('groupId', $id);
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

        // set the domain_id parameter
        $config =& CRM_Core_Config::singleton( );
        $dao->domain_id = $config->domainID( );

        //$dao->orderBy('title');
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
                                                                                    array('id' => $dao->id));
        }
        $this->assign('rows', $priceSet);
    }
}
?>
