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
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Core/Page.php';

/**
 * Event Info Page - Summmary about the event
 */
class CRM_Event_Page_EventInfo extends CRM_Core_Page
{

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
        $id     = CRM_Utils_Request::retrieve('id', 'Positive', $this, false, 0);
        $action = CRM_Utils_Request::retrieve( 'action', 'String', $this, false );
        
        // set breadcrumb to append to 2nd layer pages
        $breadCrumbPath = CRM_Utils_System::url( "civicrm/event/info", "id={$id}&reset=1" );
        $additionalBreadCrumb = "<a href=\"$breadCrumbPath\">" . ts('Events') . '</a>';
   
        //retrieve event information
        $params = array( 'id' => $id );
        require_once 'CRM/Event/BAO/Event.php';
        CRM_Event_BAO_Event::retrieve($params, $values['event']);

        //retrieve custom information
        require_once 'CRM/Core/BAO/CustomOption.php'; 
        CRM_Core_BAO_CustomOption::getAssoc( 'civicrm_event', $id, $values['custom'] );
     
        $params = array( 'entity_id' => $id ,'entity_table' => 'civicrm_event');
        require_once 'CRM/Core/BAO/Location.php';
        $location = CRM_Core_BAO_Location::getValues($params, $values, $ids, 1);
        
        //retrieve custom field information
        require_once 'CRM/Core/BAO/CustomGroup.php';
        $groupTree =& CRM_Core_BAO_CustomGroup::getTree("Event", $id, 0, $values['event']['event_type_id'] );
        CRM_Core_BAO_CustomGroup::buildViewHTML( $this, $groupTree );
        $this->assign( 'action', CRM_Core_Action::VIEW);
        
        if ( $values['event']['is_online_registration'] ) {
            $registerText = "Register Now";
            if ( $registerText ) {
                $registerText = $values['event']['registration_link_text'];
            }
        
            $this->assign( 'registerText', $registerText );
            $this->assign( 'is_online_registration', $values['event']['is_online_registration'] );

            if ( $action == 1024 ) {
                $url = CRM_Utils_System::url("civicrm/event/register", "id={$id}&reset=1&action=preview" );
            } else {
                $url = CRM_Utils_System::url("civicrm/event/register", "id={$id}&reset=1" );
            }
            $this->assign( 'registerURL', $url );
        }

        // we do not want to display recently viewed items, so turn off
        $this->assign('displayRecent' , false );

        // assigning title to template in case someone wants to use it, also setting CMS page title
        $this->assign( 'title', $values['event']['title'] );
        CRM_Utils_System::setTitle($values['event']['title']);  

        $this->assign('event',   $values['event']);
        $this->assign('custom',  $values['custom']);
        $this->assign('location',$values['location']);
        
        parent::run();
        
    }

}
?>