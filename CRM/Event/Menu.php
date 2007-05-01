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
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
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
                       array( 
                             'path'    => 'civicrm/event',
                             'query'   => 'reset=1',
                             'title'   => ts('CiviEvent'), 
                             'access'  => CRM_Core_Permission::check( 'access CiviEvent'), 
                             'type'    => CRM_Core_Menu::CALLBACK,  
                             'crmType' => CRM_Core_Menu::NORMAL_ITEM,
                             'weight'  => 800,
                             ),
                       array( 
                              'path'    => 'civicrm/event/info', 
                              'query'   => 'reset=1',
                              'title'   => ts( 'Event Information' ), 
                              'access'  => CRM_Core_Permission::check( 'register for events'), 
                              'type'    => CRM_Core_Menu::CALLBACK,  
                              'crmType' => CRM_Core_Menu::CALLBACK,
                              'weight'  => 0, 
                              ),
                       array( 
                              'path'    => 'civicrm/event/register', 
                              'query'   => 'reset=1',
                              'title'   => ts( 'Event Registration' ), 
                              'access'  => CRM_Core_Permission::check( 'register for events'), 
                              'type'    => CRM_Core_Menu::CALLBACK,  
                              'crmType' => CRM_Core_Menu::CALLBACK,
                              'weight'  => 0, 
                              ),
                       );
        return $items;
    }

    static function &main( $task ) {
        $items = array( );
        switch ( $task ) {
        case 'admin':
            $items = array(
                           array(
                                 'path'    => 'civicrm/admin/event',
                                 'title'   => ts('Manage Events'),
                                 'query'  => 'reset=1',
                                 'access'  => CRM_Core_Permission::check('administer CiviCRM') &&
                                 CRM_Core_Permission::check( 'access CiviEvent' ),
                                 'type'    => CRM_Core_Menu::CALLBACK,
                                 'crmType' => CRM_Core_Menu::LOCAL_TASK,
                                 'adminGroup' => 'CiviEvent',
                                 'icon'    => 'admin/event_manage.png',
                                 'weight'  => 370
                                 ),
                      
                           array(
                                 'path'    => 'civicrm/admin/options',
                                 'title'   => ts('Event Types'),
                                 'query'  => 'group=event_type&reset=1',
                                 'access'  => CRM_Core_Permission::check('administer CiviCRM') &&
                                 CRM_Core_Permission::check( 'access CiviEvent' ),
                                 'type'    => CRM_Core_Menu::CALLBACK,
                                 'crmType' => CRM_Core_Menu::LOCAL_TASK,
                                 'adminGroup' => 'CiviEvent',
                                 'icon'    => 'admin/event_type.png',
                                 'weight'  => 375
                                 ),
                       
                           array(
                                 'path'    => 'civicrm/admin/options',
                                 'title'   => ts('Participant Status'),
                                 'query'  => 'group=participant_status&reset=1',
                                 'access'  => CRM_Core_Permission::check('administer CiviCRM') &&
                                 CRM_Core_Permission::check( 'access CiviEvent' ),
                                 'type'    => CRM_Core_Menu::CALLBACK,
                                 'crmType' => CRM_Core_Menu::LOCAL_TASK,
                                 'adminGroup' => 'CiviEvent',
                                 'icon'    => 'admin/parti_status.png',
                                 'weight'  => 380
                                 ),

                           array(
                                 'path'    => 'civicrm/admin/options',
                                 'title'   => ts('Participant Role'),
                                 'query'  => 'group=participant_role&reset=1',
                                 'access'  => CRM_Core_Permission::check('administer CiviCRM') &&
                                 CRM_Core_Permission::check( 'access CiviEvent' ),
                                 'type'    => CRM_Core_Menu::CALLBACK,
                                 'crmType' => CRM_Core_Menu::LOCAL_TASK,
                                 'adminGroup' => 'CiviEvent',
                                 'icon'    => 'admin/parti_role.png',
                                 'weight'  => 385
                                 ),

                           array(
                                 'path'    => 'civicrm/admin/price',
                                 'title'   => ts('Price Sets'),
                                 'query'   => 'reset=1',
                                 'access'  => CRM_Core_Permission::check('administer CiviCRM') &&
                                 CRM_Core_Permission::check( 'access CiviEvent' ),
                                 'type'    => CRM_Core_Menu::CALLBACK,
                                 'crmType' => CRM_Core_Menu::LOCAL_TASK,
                                 'adminGroup' => 'CiviEvent',
                                 'icon'    => 'admin/price_sets.png',
                                 'weight'  => 386
                                 ),
                                  
                           );
            break;

        case 'event':
            $items = array(
                           array( 
                                 'path'    => 'civicrm/event/search',
                                 'query'   => 'reset=1',
                                 'title'   => ts( 'Find Participants' ),
                                 'access'  => CRM_Core_Permission::check( 'access CiviEvent' ), 
                                 'type'    => CRM_Core_Menu::CALLBACK,  
                                 'crmType' => CRM_Core_Menu::NORMAL_ITEM,  
                                 'weight'  => 810,  
                                 ),
                       
                           array(
                                 'path'    => 'civicrm/event/import', 
                                 'query'   => 'reset=1',
                                 'title'   => ts( 'Import Participants' ),
                                 'access'  => CRM_Core_Permission::check('administer CiviCRM') &&
                                              CRM_Core_Permission::check( 'access CiviEvent' ),
                                 'type'    => CRM_Core_Menu::CALLBACK,  
                                 'crmType' => CRM_Core_Menu::NORMAL_ITEM,  
                                 'weight'  => 820,  
                                 ),

                             array(
                                 'path'    => 'civicrm/event/manage', 
                                 'query'   => 'reset=1',
                                 'title'   => ts( 'Manage Events' ),
                                 'access'  => CRM_Core_Permission::check( 'administer CiviCRM' ) &&
                                              CRM_Core_Permission::check( 'access CiviEvent' ),
                                 'type'    => CRM_Core_Menu::CALLBACK,  
                                 'crmType' => CRM_Core_Menu::NORMAL_ITEM,  
                                 'weight'  => 830,  
                                 )
                           );
            break;
        }
        return $items;
    }

}

?>
