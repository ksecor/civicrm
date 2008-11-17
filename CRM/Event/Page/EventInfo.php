<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
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
        $this->_id      = CRM_Utils_Request::retrieve( 'id'    , 'Positive', $this, true );

        // ensure that the user has permission to see this page
        if ( ! CRM_Core_Permission::event( CRM_Core_Permission::VIEW,
                                           $this->_id ) ) {
            CRM_Core_Error::fatal( ts( 'You do not have permission to view this event' ) );
        }

        $action  = CRM_Utils_Request::retrieve( 'action', 'String'  , $this, false );
        $context = CRM_Utils_Request::retrieve( 'context', 'String'  , $this, false, 'register' );
        $this->assign( 'context', $context );

        // set breadcrumb to append to 2nd layer pages
        $breadCrumbPath = CRM_Utils_System::url( "civicrm/event/info", "id={$this->_id}&reset=1" );
        $additionalBreadCrumb = "<a href=\"$breadCrumbPath\">" . ts('Events') . '</a>';
       
        //retrieve event information
        $params = array( 'id' => $this->_id );
        $ids = array();

        require_once 'CRM/Event/BAO/Event.php';
        CRM_Event_BAO_Event::retrieve($params, $values['event']);

        if (! $values['event']['is_active']){
            // form is inactive, die a fatal death
            CRM_Core_Error::fatal( ts( 'The page you requested is currently unavailable.' ) );
        }          

        // get the eventPageID
        $eventPageId = CRM_Core_DAO::getFieldValue( 'CRM_Event_DAO_EventPage',
                                                    $this->_id,
                                                    'id',
                                                    'event_id' );
       $isShowLocation = CRM_Core_DAO::getFieldValue( 'CRM_Event_DAO_Event',
                                                     $this->_id,
                                                     'is_show_location',
                                                     'id' );
     
        $this->assign( 'isShowLocation',$isShowLocation );
        
        // do not bother with price information if price fields are used
        require_once 'CRM/Core/BAO/PriceSet.php';
        if ( isset ($eventPageId ) ) {
            if ( ! CRM_Core_BAO_PriceSet::getFor( 'civicrm_event_page', $eventPageId ) ) {
                //retrieve event fees.
                require_once 'CRM/Core/OptionGroup.php';
		require_once 'CRM/Core/BAO/Discount.php';
		$discountId = CRM_Core_BAO_Discount::findSet( $this->_id, 'civicrm_event' );
		if ( $discountId ) {
		  CRM_Core_OptionGroup::getAssoc( 
						 CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_Discount',
									      $discountId,
									      'option_group_id' ),
						 $values['feeBlock'], 'id' );
		} else {
		  CRM_Core_OptionGroup::getAssoc( "civicrm_event_page.amount.{$eventPageId}", $values['feeBlock'] );
		}
	    }
        }				
	
	$params = array( 'entity_id' => $this->_id ,'entity_table' => 'civicrm_event');
        require_once 'CRM/Core/BAO/Location.php';
        CRM_Core_BAO_Location::getValues( $params, $values, true );
        
        //retrieve custom field information
        require_once 'CRM/Core/BAO/CustomGroup.php';
        $groupTree =& CRM_Core_BAO_CustomGroup::getTree("Event", $this->_id, 0, $values['event']['event_type_id'] );
        CRM_Core_BAO_CustomGroup::buildViewHTML( $this, $groupTree );
        $this->assign( 'action', CRM_Core_Action::VIEW);
        
        require_once 'CRM/Event/BAO/Participant.php';
        $eventFullMessage = CRM_Event_BAO_Participant::eventFull( $this->_id );
        if( $eventFullMessage ) {
            CRM_Core_Session::setStatus( $eventFullMessage );
        }
        
        if ( isset($values['event']['is_online_registration']) ) {
            // make sure that we are between  registration start date and registration end date
            $startDate = CRM_Utils_Date::unixTime( CRM_Utils_Array::value( 'registration_start_date',
                                                                           $values['event'] ) );
            $endDate = CRM_Utils_Date::unixTime( CRM_Utils_Array::value( 'registration_end_date',
                                                                         $values['event'] ) );
            $now = time( );
            $validDate = true;
            if ( $startDate && $startDate >= $now ) {
                $validDate = false;
                
            }
            if ( $endDate && $endDate < $now ) {
                $validDate = false;
            }

            // also check that the user has permission to register for this event
            $hasPermission = CRM_Core_Permission::event( CRM_Core_Permission::EDIT,
                                                         $this->_id );
            if ( $validDate && $hasPermission ) {
                if ( ! $eventFullMessage ) {
                    $registerText = ts('Register Now');
                    if ( CRM_Utils_Array::value('registration_link_text',$values['event']) ) {
                        $registerText = $values['event']['registration_link_text'];
                    }
                    
                    $this->assign( 'registerText', $registerText );
                    $this->assign( 'is_online_registration', $values['event']['is_online_registration'] );
                }

                // we always generate urls for the front end in joomla
                if ( $action ==  CRM_Core_Action::PREVIEW ) {
                    $url    = CRM_Utils_System::url( 'civicrm/event/register',
                                                     "id={$this->_id}&reset=1&action=preview",
                                                     true, null, true,
                                                     true );
                } else {
                    $url = CRM_Utils_System::url( 'civicrm/event/register',
                                                  "id={$this->_id}&reset=1",
                                                  true, null, true,
                                                  true );
                }
                if ( ! $eventFullMessage ) {
                    $this->assign( 'registerURL', $url    );
                }
            }

            if ( $action ==  CRM_Core_Action::PREVIEW ) {
                $mapURL = CRM_Utils_System::url( 'civicrm/contact/map/event',
                                                 "eid={$this->_id}&reset=1&action=preview",
                                                 true, null, true,
                                                 true );
            } else {
                $mapURL = CRM_Utils_System::url( 'civicrm/contact/map/event',
                                                 "eid={$this->_id}&reset=1",
                                                 true, null, true,
                                                 true );
            }
            $this->assign( 'mapURL'     , $mapURL );
        }

        // we do not want to display recently viewed items, so turn off
        $this->assign('displayRecent' , false );
        
        $this->assign('event',   $values['event']);
        if ( isset( $values['feeBlock'] ) ) {
            $this->assign( 'feeBlock', $values['feeBlock'] );
        }
        $this->assign('location',$values['location']);
        
        parent::run();
        
    }

    function getTemplateFileName() 
    {
        if ( $this->_id ) {
            $templateFile = "CRM/Event/Page/{$this->_id}/EventInfo.tpl";
            $template     =& CRM_Core_Page::getTemplate( );

            if ( $template->template_exists( $templateFile ) ) {
                return $templateFile;
            }
        }
        return parent::getTemplateFileName( );
    }


}

