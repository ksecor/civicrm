<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                  |
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
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Core/Page.php';

/**
 * Page for displaying list of events
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
            $disableExtra = ts('Are you sure you want to disable this Event?');
            $deleteExtra = ts('Are you sure you want to delete this Event?');

            self::$_actionLinks = array(
                                        CRM_Core_Action::UPDATE  => array(
                                                                          'name'  => ts('Configure'),
                                                                          'url'   => 'civicrm/admin/event',
                                                                          'qs'    => 'action=update&id=%%id%%&reset=1',
                                                                          'title' => ts('Configure Event') 
                                                                          ),
                                        CRM_Core_Action::PREVIEW => array(
                                                                          'name'  => ts('Test-drive'),
                                                                          'url'   => 'civicrm/event/info',
                                                                          'qs'    => 'reset=1&action=preview&id=%%id%%',
                                                                          'title' => ts('Preview') 
                                                                          ),
                                        CRM_Core_Action::FOLLOWUP    => array(
                                                                          'name'  => ts('Live Page'),
                                                                          'url'   => 'civicrm/event/info',
                                                                          'qs'    => 'reset=1&id=%%id%%',
                                                                          'title' => ts('FollowUp'),
                                                                          ),
                                        CRM_Core_Action::DISABLE => array(
                                                                          'name'  => ts('Disable'),
                                                                          'url'   => 'civicrm/admin/event',
                                                                          'qs'    => 'action=disable&id=%%id%%',
                                                                          'extra' => 'onclick = "return confirm(\'' . $disableExtra . '\');"',
                                                                          'title' => ts('Disable Event') 
                                                                          ),
                                        CRM_Core_Action::ENABLE  => array(
                                                                          'name'  => ts('Enable'),
                                                                          'url'   => 'civicrm/admin/event',
                                                                          'qs'    => 'action=enable&id=%%id%%',
                                                                          'title' => ts('Enable Event') 
                                                                          ),
                                        CRM_Core_Action::DELETE  => array(
                                                                          'name'  => ts('Delete'),
                                                                          'url'   => 'civicrm/admin/event',
                                                                          'qs'    => 'action=delete&id=%%id%%',
                                                                          'extra' => 'onclick = "return confirm(\'' . $deleteExtra . '\');"',
                                                                          'title' => ts('Delete Event') 
                                                                          ),
                                        CRM_Core_Action::COPY     => array(
                                                                           'name'  => ts('Copy'),
                                                                          'url'   => 'civicrm/admin/event',
                                                                          'qs'    => 'reset=1&action=copy&id=%%id%%',
                                                                          'title' => ts('Copy Event') 
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
            
            $title = "New Event Wizard";
            $session->pushUserContext( CRM_Utils_System::url('civicrm/admin/event', 'reset=1' ) );
            CRM_Utils_System::appendBreadCrumb( $additionalBreadCrumb );
            CRM_Utils_System::setTitle( $title );
            
            require_once 'CRM/Event/Controller/ManageEvent.php';
            $controller =& new CRM_Event_Controller_ManageEvent( );
            return $controller->run( );
        } else if ($action & CRM_Core_Action::UPDATE ) {
            CRM_Utils_System::appendBreadCrumb( $additionalBreadCrumb );

            require_once 'CRM/Event/Page/ManageEventEdit.php';
            $page =& new CRM_Event_Page_ManageEventEdit( );
            return $page->run( );
        } else if ($action & CRM_Core_Action::DISABLE ) {
            require_once 'CRM/Event/BAO/Event.php';
            CRM_Event_BAO_Event::setIsActive($id ,0);
        } else if ($action & CRM_Core_Action::ENABLE ) {
            require_once 'CRM/Event/BAO/Event.php';
            CRM_Event_BAO_Event::setIsActive($id ,1); 
        } else if ($action & CRM_Core_Action::DELETE ) {
            $session =& CRM_Core_Session::singleton();
            $session->pushUserContext( CRM_Utils_System::url('civicrm/admin/event', 'reset=1&action=browse' ) );
            $controller =& new CRM_Core_Controller_Simple( 'CRM_Event_Form_ManageEvent_Delete',
                                                           'Delete Event',
                                                           $action );
            $id = CRM_Utils_Request::retrieve('id', 'Positive',
                                              $this, false, 0);
            $controller->set( 'id', $id );
            $controller->process( );
            return $controller->run( );
        } else if ($action & CRM_Core_Action::COPY ) {
            $this->copy( );
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
        
        $past = false;
                
        require_once 'CRM/Event/DAO/Event.php';
        $dao =& new CRM_Event_DAO_Event();
        
        if ( ! CRM_Utils_Request::retrieve( 'past', 'Boolean', $this ) ) {
            $past = true;
            $dao->whereAdd( 'end_date >= ' . date( 'YmdHis' ) );
        } else {
            $dao->whereAdd( 'end_date < ' . date( 'YmdHis' ) );
        }
        
        $dao->find( );
        
        $this->assign( 'past', $past );
        
        while ($dao->fetch()) {
            $manageEvent[$dao->id] = array();
            CRM_Core_DAO::storeValues( $dao, $manageEvent[$dao->id]);
            
            // form all action links
            $action = array_sum(array_keys($this->links()));
            
            if ($dao->is_active) {
                $action -= CRM_Core_Action::ENABLE;
            } else {
                $action -= CRM_Core_Action::DISABLE;
            }
            
            $manageEvent[$dao->id]['action'] = CRM_Core_Action::formLink(self::links(), $action, 
                                                                         array('id' => $dao->id));

            $params = array( 'entity_id' => $dao->id, 'entity_table' => 'civicrm_event');
            require_once 'CRM/Core/BAO/Location.php';
            $location = CRM_Core_BAO_Location::getValues($params, $defaults, $id, 1);
            
            if( $manageEvent[$dao->id]['id'] == $defaults['location'][1]['entity_id'] ) {
                if ( $defaults['location'][1]['address']['city'] ) {
                    $manageEvent[$dao->id]['city'] = $defaults['location'][1]['address']['city'];
                }
                if ( $defaults['location'][1]['address']['state_province_id'] ) {
                    $manageEvent[$dao->id]['state_province'] = CRM_Core_PseudoConstant::stateProvince($defaults['location'][1]['address']['state_province_id']);
                }
            }
        }
        
        $this->assign('rows', $manageEvent);
    }
    
    /**
     * This function is to make a copy of a Event, including
     * all the fields in the event wizard
     *
     * @return void
     * @access public
     */
    function copy( )
    {
        $id = CRM_Utils_Request::retrieve('id', 'Positive', $this, true, 0, 'GET');
        
        require_once 'CRM/Event/BAO/Event.php';
        CRM_Event_BAO_Event::copy( $id );
    }
}
?>
