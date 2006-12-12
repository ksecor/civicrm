<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.6                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                  |
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/Core/Page.php';
require_once 'CRM/Event/DAO/Event.php';

/**
 * Create a page for displaying Event Pages
 * Event Pages are pages that are used to display
 * rRegistrations of different types. Pages consist
 * of many customizable sections which can be
 * accessed.
 *
 * This page provides a top level browse view
 * of all the rRegistration pages in the system.
 *
 */
class CRM_Event_Page_RegistrationPage extends CRM_Core_Page {

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
//         if (!isset(self::$_actionLinks)) {
//             // helper variable for nicer formatting
//             $disableExtra = ts('Are you sure you want to disable this Registration page?');
//             $deleteExtra = ts('Are you sure you want to delete this Registration page?');
//             self::$_actionLinks = array(
//                                         CRM_Core_Action::UPDATE  => array(
//                                                                           'name'  => ts('Configure'),
//                                                                           'url'   => 'civicrm/admin/event',
//                                                                           'qs'    => 'reset=1&action=update&id=%%id%%',
//                                                                           'title' => ts('Configure') 
//                                                                           ),
//                                         CRM_Core_Action::PREVIEW => array(
//                                                                           'name'  => ts('Test-drive'),
//                                                                           'url'   => 'civicrm/event/transact',
//                                                                           'qs'    => 'reset=1&action=preview&id=%%id%%',
//                                                                           'title' => ts('Preview'),
//                                                                           ),
//                                         CRM_Core_Action::FOLLOWUP    => array(
//                                                                           'name'  => ts('Live Page'),
//                                                                           'url'   => 'civicrm/event/transact',
//                                                                           'qs'    => 'reset=1&id=%%id%%',
//                                                                           'title' => ts('FollowUp'),
//                                                                           ),
//                                         CRM_Core_Action::DISABLE => array(
//                                                                           'name'  => ts('Disable'),
//                                                                           'url'   => 'civicrm/admin/event',
//                                                                           'qs'    => 'action=disable&id=%%id%%',
//                                                                           'title' => ts('Disable'),
//                                                                           'extra' => 'onclick = "return confirm(\'' . $disableExtra . '\');"',
//                                                                           ),
//                                         CRM_Core_Action::ENABLE  => array(
//                                                                           'name'  => ts('Enable'),
//                                                                           'url'   => 'civicrm/admin/event',
//                                                                           'qs'    => 'action=enable&id=%%id%%',
//                                                                           'title' => ts('Enable'),
//                                                                           ),
//                                         CRM_Core_Action::DELETE  => array(
//                                                                           'name'  => ts('Delete'),
//                                                                           'url'   => 'civicrm/admin/event',
//                                                                           'qs'    => 'action=delete&reset=1&id=%%id%%',
//                                                                           'title' => ts('Delete Custom Field'),
//                                                                           'extra' => 'onclick = "return confirm(\'' . $deleteExtra . '\');"',
//                                                                           ),
//                                         );
//         }
//         return self::$_actionLinks;
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

        $this->assign( 'dojoIncludes', "dojo.require('dojo.widget.SortableTable');" );
        
        // get the requested action
        $action = CRM_Utils_Request::retrieve('action', 'String',
                                              $this, false, 'browse'); // default to 'browse'

        // assign vars to templates
        $this->assign('action', $action);
        $id = CRM_Utils_Request::retrieve('id', 'Positive',
                                          $this, false, 0);

        // set breadcrumb to append to 2nd layer pages
//         $breadCrumbPath = CRM_Utils_System::url( 'civicrm/admin/event', 'reset=1' );
//         $additionalBreadCrumb = "<a href=\"$breadCrumbPath\">" . ts('Configure Online Registration Pages') . '</a>';
    
        // what action to take ?

        if ( $action & CRM_Core_Action::ADD ) {
            $session =& CRM_Core_Session::singleton( ); 
            $session->pushUserContext( CRM_Utils_System::url('civicrm/admin/event', 'action=browse&reset=1' ) );

            require_once 'CRM/Event/Controller/RegistrationPage.php';
            $controller =& new CRM_Event_Controller_RegistrationPage( );
            //CRM_Utils_System::appendBreadCrumb( $additionalBreadCrumb );
            CRM_Utils_System::setTitle( ts('New Online Registration Page') );
            return $controller->run( );
        } else if ($action & CRM_Core_Action::UPDATE ) {
            require_once 'CRM/Event/Page/RegistrationPageEdit.php';
            $page =& new CRM_Event_Page_RegistrationPageEdit( );
            CRM_Utils_System::appendBreadCrumb( $additionalBreadCrumb );
            return $page->run( );
        } 
        
        return parent::run();
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
//         $rRegistration =  array();
//         $dao      =& new CRM_Event_DAO_Event();

//         // set the domain_id parameter
//         $config =& CRM_Core_Config::singleton( );
//         $dao->domain_id = $config->domainID( );

//         $dao->orderBy('title');
//         $dao->find();

//         while ($dao->fetch()) {
//             $rRegistration[$dao->id] = array();
//             CRM_Core_DAO::storeValues($dao, $rRegistration[$dao->id]);
//             // form all action links
//             $action = array_sum(array_keys($this->actionLinks()));
            
//             // update enable/disable links depending on custom_group properties.
//             if ($dao->is_active) {
//                 $action -= CRM_Core_Action::ENABLE;
//             } else {
//                 $action -= CRM_Core_Action::DISABLE;
//             }
            
//             $rRegistration[$dao->id]['action'] = CRM_Core_Action::formLink(self::actionLinks(), $action, 
//                                                                           array('id' => $dao->id));
//         }
//         $this->assign('rows', $rRegistration);
    }
}
?>
