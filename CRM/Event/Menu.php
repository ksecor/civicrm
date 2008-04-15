<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                  |
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
 * Menu for the civievent module
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Core/Menu.php';

class CRM_Event_Menu {

    static function permissioned( ) {
        $items = array(
                       'civicrm/event' => 
                       array( 
                             'query'   => array('reset' => 1),
                             'title'   => ts('CiviEvent'), 
                             'access_arguments'  => array( array( 'access CiviEvent') ), 
                             'page_type' => CRM_Core_Menu::MENU_ITEM,
                             'weight'    => 800,
                             'component' => 'CiviEvent',
                             ),
                       
                       'civicrm/event/info' =>
                       array( 
                             'path'    => 'civicrm/event/info', 
                             'query'   => array('reset' => 1),
                             'access_arguments'  => 1,
                             'weight'  => 0, 
                             ),
                       
                       'civicrm/event/register' =>
                       array( 
                             'query'   => array('reset' => 1),
                             'title'   => ts( 'Event Registration' ), 
                             'access_arguments'  => 1,
                             'weight'  => 0, 
                             ),

                       'civicrm/event/ical' =>
                       array( 
                             'query'   => array('reset' => 1),
                             'title'   => ts( 'Event Listings' ), 
                             'access_arguments'  => array( array( 'register for events') ), 
                             'weight'  => 0, 
                             ),

                       'civicrm/event/participant' =>
                       array( 
                             'path'    => 'civicrm/event/participant', 
                             'query'   => array('reset' => 1),
                             'title'   => ts( 'Event Participants List' ), 
                             'access_arguments'  => array( array( 'view event participants' ) ),
                              'weight'  => 0, 
                             ),
                       );
        return $items;
    }

    static function &main( ) {
        $items = array(
                       'civicrm/admin/event' =>
                       array(
                             'title'   => ts('Manage Events'),
                             'desc'    => ts('Create and edit event configuration including times, locations, online registration forms, and fees. Links for iCal and RSS syndication.'), 
                             'query'  => array('reset' => 1),
                             'access_arguments'  => array( array('administer CiviCRM', 'access CiviEvent' ) ),
                             'adminGroup' => 'CiviEvent',
                             'icon'    => 'admin/small/event_manage.png',
                             'weight'  => 370
                             ),
                       
                       'civicrm/admin/options/event_type' =>
                       array(
                             'title'   => ts('Event Types'),
                             'desc'    => ts('Use Event Types to categorize your events. Event feeds can be filtered by Event Type and participant searches can use Event Type as a criteria.'), 
                             'query'  => array('reset' => 1, 'group' => 'event_type'),
                             'access_arguments'  => array( array('administer CiviCRM', 'access CiviEvent' )),
                             'adminGroup' => 'CiviEvent',
                             'icon'    => 'admin/small/event_type.png',
                             'weight'  => 375
                             ),
                       
                       'civicrm/admin/options/participant_status' => 
                       array(
                             'title'   => ts('Participant Status'),
                             'desc'    => ts('Define statuses for event participants here (e.g. Registered, Attended, Cancelled...). You can then assign statuses and search for participants by status.'), 
                             'query'  => array('reset' => 1, 'group' => 'participant_status'),
                             'access_arguments'  => array( array('administer CiviCRM', 'access CiviEvent') ),
                             'adminGroup' => 'CiviEvent',
                             'icon'    => 'admin/small/parti_status.png',
                             'weight'  => 380
                             ),
                       
                       'civicrm/admin/options/participant_role' => 
                       array(
                             'title'   => ts('Participant Role'),
                             'desc'    => ts('Define participant roles for events here (e.g. Attendee, Host, Speaker...). You can then assign roles and search for participants by role.'), 
                             'query'  => array('reset' => 1, 'group' => 'participant_role'),
                             'access_arguments'  => array( array('administer CiviCRM', 'access CiviEvent') ),
                             'adminGroup' => 'CiviEvent',
                             'icon'    => 'admin/small/parti_role.png',
                             'weight'  => 385
                             ),
                       
                       'civicrm/admin/price' => 
                       array(
                             'title'   => ts('Price Sets'),
                             'desc'    => ts('Price sets allow you to offer multiple options with associated fees (e.g. pre-conference workshops, additional meals, etc.). Configure Price Sets for events which need more than a single set of fee levels.'), 
                             'query'   => array('reset' => 1),
                             'access_arguments'  => array( array('administer CiviCRM', 'access CiviEvent') ),
                             'adminGroup' => 'CiviEvent',
                             'icon'    => 'admin/small/price_sets.png',
                             'weight'  => 386
                             ),
                       
                       'civicrm/event/search' => 
                       array( 
                             'query'   => array('reset' => 1),
                             'title'   => ts( 'Find Participants' ),
                             'access_arguments'  => array( array( 'access CiviEvent' ) ), 
                             'page_type' => CRM_Core_Menu::MENU_ITEM,  
                             'weight'  => 810,  
                             ),
                       
                       'civicrm/event/manage' => 
                       array(
                             'query'   => array('reset' => 1),
                             'title'   => ts( 'Manage Events' ),
                             'access_arguments'  => array( array( 'administer CiviCRM', 'access CiviEvent') ),
                             'page_type' => CRM_Core_Menu::MENU_ITEM,  
                             'weight'  => 820,  
                             ),
                       
                       'civicrm/event/add' => 
                       array(
                             'query'   => array('reset' => 1, 'action' => 'add'),
                             'title'   => ts( 'New Event' ),
                             'access_arguments'  => array( array( 'administer CiviCRM', 'access CiviEvent') ),
                             'page_type' => CRM_Core_Menu::MENU_ITEM,  
                             'weight'  => 830,  
                             ),
                       
                       'civicrm/event/import' => 
                       array(
                             'query'   => array('reset' => 1),
                             'title'   => ts( 'Import Participants' ),
                             'access_arguments'  => array( array('administer CiviCRM', 'access CiviEvent') ),
                             'page_type' => CRM_Core_Menu::MENU_ITEM,  
                             'weight'  => 840,  
                             ),
                       
                       );

        return $items;
    }
    
}


