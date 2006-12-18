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

require_once 'CRM/Core/Page/Basic.php';

/**
 * Page for displaying list of eventship types
 */
class CRM_Event_Page_ManageEvent extends CRM_Core_Page
{
    /**
     * The action links that we need to display for the browse screen
     *
     * @var array
     * @static
     */
    static $_actionLinks = null;

    /**
     * Get action Links
     *
     * @return array (reference) of action links
     */
    function &links()
    {
        if (!(self::$_actionLinks)) {
            // helper variable for nicer formatting
            $disableExtra = ts('Are you sure you want to disable this eventship type?');

            self::$_actionLinks = array(
                                  CRM_Core_Action::UPDATE  => array(
                                                                    'name'  => ts('Edit'),
                                                                    'url'   => 'civicrm/admin/event',
                                                                    'qs'    => 'action=update&id=%%id%%&reset=1',
                                                                    'title' => ts('Edit Eventship Status') 
                                                                   ),
                                  CRM_Core_Action::DISABLE => array(
                                                                    'name'  => ts('Disable'),
                                                                    'url'   => 'civicrm/admin/event',
                                                                    'qs'    => 'action=disable&id=%%id%%',
                                                                    'extra' => 'onclick = "return confirm(\'' . $disableExtra . '\');"',
                                                                    'title' => ts('Disable Eventship Status') 
                                                                   ),
                                  CRM_Core_Action::ENABLE  => array(
                                                                    'name'  => ts('Enable'),
                                                                    'url'   => 'civicrm/admin/event',
                                                                    'qs'    => 'action=enable&id=%%id%%',
                                                                    'title' => ts('Enable Eventship Status') 
                                                                    ),
                                  CRM_Core_Action::DELETE  => array(
                                                                    'name'  => ts('Delete'),
                                                                    'url'   => 'civicrm/admin/event',
                                                                    'qs'    => 'action=delete&id=%%id%%',
                                                                    'title' => ts('Delete Eventship Status') 
                                                                   )
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
        $this->assign( 'dojoIncludes', "dojo.require('dojo.widget.SortableTable');" );        
        
        // get the requested action
        $action = CRM_Utils_Request::retrieve('action', 'String',
                                              $this, false, 'browse'); // default to 'browse'
        
        // assign vars to templates
        $this->assign('action', $action);
        $id = CRM_Utils_Request::retrieve('id', 'Positive',
                                          $this, false, 0);
        
        // set breadcrumb to append to 2nd layer pages
        $breadCrumbPath = CRM_Utils_System::url( 'civicrm/admin/event', 'reset=1' );
        $additionalBreadCrumb = "<a href=\"$breadCrumbPath\">" . ts('Manage Events') . '</a>';

        // what action to take ?
        if ( $action & CRM_Core_Action::ADD ) {
            $session =& CRM_Core_Session::singleton( ); 
            $session->pushUserContext( CRM_Utils_System::url('civicrm/admin/event', 'reset=1' ) );

            require_once 'CRM/Event/Controller/ManageEvent.php';
            $controller =& new CRM_Event_Controller_ManageEvent( );
            CRM_Utils_System::appendBreadCrumb( $additionalBreadCrumb );
            CRM_Utils_System::setTitle( ts('New Event Wizard') );
            return $controller->run( );
        } else if ($action & CRM_Core_Action::UPDATE ) {
            require_once 'CRM/Event/Page/ManageEventEdit.php';
            $page =& new CRM_Event_Page_ManageEventEdit( );
            CRM_Utils_System::appendBreadCrumb( $additionalBreadCrumb );
            CRM_Utils_System::setTitle( ts('Edit Event') );
            return $page->run( );
        }

        // finally browse the custom groups
        $this->browse();
        
        // parent run 
        parent::run();
    }

    /**
     * Browse all custom data groups.
     *  
     * 
     * @return void
     * @access public
     * @static
     */
    function browse()
    {
        // get all custom groups sorted by weight
        $manageEvent = array();
        require_once 'CRM/Event/DAO/Event.php';
        $dao =& new CRM_Event_DAO_Event();

        //$dao->orderBy('weight');
        $dao->find();

        while ($dao->fetch()) {
            $manageEvent[$dao->id] = array();
            CRM_Core_DAO::storeValues( $dao, $manageEvent[$dao->id]);
            // form all action links
            $action = array_sum(array_keys($this->links()));

            // update enable/disable links depending on if it is is_reserved or is_active
            if ($dao->is_reserved) {
                continue;
            } else {
                if ($dao->is_active) {
                    $action -= CRM_Core_Action::ENABLE;
                } else {
                    $action -= CRM_Core_Action::DISABLE;
                }
            }
            
            $manageEvent[$dao->id]['action'] = CRM_Core_Action::formLink(self::links(), $action, 
                                                                              array('id' => $dao->id));
            $manageEvent[$dao->id]['start_event'] = str_replace("_", " ", $manageEvent[$dao->id]['start_event']);
            $manageEvent[$dao->id]['end_event']   = str_replace("_", " ", $manageEvent[$dao->id]['end_event']);
        }
        $this->assign('rows', $manageEvent);
    }
}

?>
