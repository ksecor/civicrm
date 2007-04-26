<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
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
        $ids = array();
        require_once 'CRM/Event/BAO/Event.php';
        CRM_Event_BAO_Event::retrieve($params, $values['event']);
        if (! $values['event']['is_active']){
            // form is inactive, die a fatal death
            CRM_Core_Error::fatal( ts( 'The page you requested is currently unavailable.' ) );
        }          

        // get the eventPageID
        $eventPageID = CRM_Core_DAO::getFieldValue( 'CRM_Event_DAO_EventPage',
                                                    $id,
                                                    'id',
                                                    'event_id' );

        //retrieve custom information
        require_once 'CRM/Core/BAO/CustomOption.php'; 
        CRM_Core_BAO_CustomOption::getAssoc( 'civicrm_event_page', $eventPageID, $values['custom'] );
     
        $params = array( 'entity_id' => $id ,'entity_table' => 'civicrm_event');
        require_once 'CRM/Core/BAO/Location.php';
        CRM_Core_BAO_Location::getValues($params, $values, $ids, 1);
        
        //retrieve custom field information
        require_once 'CRM/Core/BAO/CustomGroup.php';
        $groupTree =& CRM_Core_BAO_CustomGroup::getTree("Event", $id, 0, $values['event']['event_type_id'] );
        CRM_Core_BAO_CustomGroup::buildViewHTML( $this, $groupTree );
        $this->assign( 'action', CRM_Core_Action::VIEW);
        
        require_once 'CRM/Event/BAO/Participant.php';
        $eventFullMessage = CRM_Event_BAO_Participant::eventFull( $id );
        if( $eventFullMessage ) {
            CRM_Core_Session::setStatus( $eventFullMessage );
        } else {
            if ( $values['event']['is_online_registration'] ) {
                $registerText = ts('Register Now');
                if ( $values['event']['registration_link_text'] ) {
                    $registerText = $values['event']['registration_link_text'];
                }
                
                $this->assign( 'registerText', $registerText );
                $this->assign( 'is_online_registration', $values['event']['is_online_registration'] );
                
                if ( $action ==  CRM_Core_Action::PREVIEW ) {
                    $url = CRM_Utils_System::url("civicrm/event/register", "id={$id}&reset=1&action=preview" );
                } else {
                    $url = CRM_Utils_System::url("civicrm/event/register", "id={$id}&reset=1" );
                }
                $this->assign( 'registerURL', $url );
            }
            
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
