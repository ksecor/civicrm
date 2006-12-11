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
 * Menu for the civievent module
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/Core/Menu.php';

class CRM_Event_Menu {

    static function &main( ) {
        $items = array(
                       array(
                             'path'    => 'civicrm/admin/event/manageEvent',
                             'title'   => ts('Manage Events'),
                             'query'  => 'reset=1',
                             'access'  => CRM_Core_Permission::check('administer CiviCRM') &&
                             CRM_Core_Permission::check( 'access CiviEvent' ),
                             'type'    => CRM_Core_Menu::CALLBACK,
                             'crmType' => CRM_Core_Menu::LOCAL_TASK,
                             'adminGroup' => 'CiviEvent',
                             'icon'    => 'admin/membership_type.png',
                             'weight'  => 370
                             ),
                      
                       array(
                             'path'    => 'civicrm/admin/event/eventType',
                             'title'   => ts('Event Types'),
                             'query'  => 'reset=1',
                             'access'  => CRM_Core_Permission::check('administer CiviCRM') &&
                             CRM_Core_Permission::check( 'access CiviEvent' ),
                             'type'    => CRM_Core_Menu::CALLBACK,
                             'crmType' => CRM_Core_Menu::LOCAL_TASK,
                             'adminGroup' => 'CiviEvent',
                             'icon'    => 'admin/membership_status.png',
                             'weight'  => 375
                             ),
                       
                       array(
                             'path'    => 'civicrm/admin/event/participantStatus',
                             'title'   => ts('Participant Status'),
                             'query'  => 'reset=1',
                             'access'  => CRM_Core_Permission::check('administer CiviCRM') &&
                             CRM_Core_Permission::check( 'access CiviEvent' ),
                             'type'    => CRM_Core_Menu::CALLBACK,
                             'crmType' => CRM_Core_Menu::LOCAL_TASK,
                             'adminGroup' => 'CiviEvent',
                             'icon'    => 'admin/template.png',
                             'weight'  => 380
                             ),

                       array(
                             'path'    => 'civicrm/admin/event/participantRole',
                             'title'   => ts('Participant Role'),
                             'query'  => 'reset=1',
                             'access'  => CRM_Core_Permission::check('administer CiviCRM') &&
                             CRM_Core_Permission::check( 'access CiviEvent' ),
                             'type'    => CRM_Core_Menu::CALLBACK,
                             'crmType' => CRM_Core_Menu::LOCAL_TASK,
                             'adminGroup' => 'CiviEvent',
                             'icon'    => 'admin/template.png',
                             'weight'  => 385
                             ),

                       array(
                             'path'    => 'civicrm/admin/event',
                             'title'   => ts('Online Event Registration'),
                             'query'  => 'reset=1',
                             'access'  => CRM_Core_Permission::check('administer CiviCRM') &&
                             CRM_Core_Permission::check( 'access CiviEvent' ),
                             'type'    => CRM_Core_Menu::CALLBACK,
                             'crmType' => CRM_Core_Menu::LOCAL_TASK,
                             'adminGroup' => 'CiviEvent',
                             'icon'    => 'admin/online_contribution_pages.png',
                             'weight'  => 390
                             ),
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
                             'path'    => 'civicrm/event/search',
                             'query'   => 'reset=1',
                             'title'   => ts( 'Find Participants' ),
                             'access'  => CRM_Core_Permission::check( 'access CiviEvent'), 
                             'type'    => CRM_Core_Menu::CALLBACK,  
                             'crmType' => CRM_Core_Menu::NORMAL_ITEM,  
                             'weight'  => 810,  
                             ),
                       
                       array(
                             'path'    => 'civicrm/event/import', 
                             'query'   => 'reset=1',
                             'title'   => ts( 'Import Participants' ),
                             'access' => CRM_Core_Permission::check('administer CiviCRM') &&
                             CRM_Core_Permission::check('access CiviEvent'),
                             'type'    => CRM_Core_Menu::CALLBACK,  
                             'crmType' => CRM_Core_Menu::NORMAL_ITEM,  
                             'weight'  => 820,  
                             )
                       );

        return $items;
    }

}

?>
