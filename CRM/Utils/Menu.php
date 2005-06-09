<?php
/*
 +----------------------------------------------------------------------+
 | CiviCRM version 1.0                                                  |
 +----------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                    |
 +----------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                      |
 |                                                                      |
 | CiviCRM is free software; you can redistribute it and/or modify it   |
 | under the terms of the Affero General Public License Version 1,      |
 | March 2002.                                                          |
 |                                                                      |
 | CiviCRM is distributed in the hope that it will be useful, but       |
 | WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
 | See the Affero General Public License for more details at            |
 | http://www.affero.org/oagpl.html                                     |
 |                                                                      |
 | A copy of the Affero General Public License has been been            |
 | distributed along with this program (affero_gpl.txt)                 |
 +----------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

/**
 *
 */
class CRM_Utils_Menu {
    /**
     * the list of menu items
     * 
     * @var array
     * @static
     */
    static $_items = null;

    /**
     * This is a super super gross hack, please fix sometime soon
     *
     * using constants from DRUPAL/includes/menu.inc, so that we can reuse 
     * the same code in both drupal and mambo
     */
    const
        CALLBACK           =   4,
        NORMAL_ITEM        =  22,
        LOCAL_TASK         = 128,
        DEFAULT_LOCAL_TASK = 640;

    static function &items( ) {
        if ( ! self::$_items ) {
            self::$_items = array( );

            // This is the minimum information you can provide for a menu item.
            self::$_items[] = array(
                             'path'   => 'civicrm/admin',
                             'title'  => ts('Administer CiviCRM'),
                             'qs'     => 'reset=1',
                             'access' => CRM_Utils_System::checkPermission('administer CiviCRM') &&
                                         CRM_Utils_System::checkPermission( 'access CiviCRM' ),
                             'type'   => self::NORMAL_ITEM,
                             'weight' => 40,
                             );
        
            self::$_items[] = array(
                             'path'   => 'civicrm/admin/tag',
                             'title'  => ts('Tags'),
                             'type'   => self::DEFAULT_LOCAL_TASK,
                             'weight' => -10
                             );

            self::$_items[] = array(
                             'path'   => 'civicrm/admin/reltype',
                             'title'  => ts('Relationship Types'),
                             'type'   => self::LOCAL_TASK,
                             'weight' => -8
                             );

            self::$_items[] = array(
                             'path'   => 'civicrm/admin/locationType',
                             'title'  => ts('Location Types'),
                             'type'   => self::LOCAL_TASK,
                             'weight' => -6
                             );
        
            self::$_items[] = array(
                             'path'   => 'civicrm/admin/custom/group',
                             'title'  => ts('Custom Data'),
                             'qs'     => 'reset=1',
                             'type'   => self::LOCAL_TASK,
                             'weight' => -5
                             );

            self::$_items[] = array(
                             'path'   => 'civicrm/admin/custom/group/field',
                             'title'  => ts('Custom Data Fields'),
                             'qs'     => 'reset=1',
                             'type'   => self::CALLBACK,
                             'weight' => 3
                             );
            self::$_items[] = array(
                             'path'   => 'civicrm/admin/IMProvider',
                             'title'  => ts('IM Services'),
                             'type'   => self::LOCAL_TASK,
                             'weight' => -4
                             );

            self::$_items[] = array(
                             'path'   => 'civicrm/admin/mobileProvider',
                             'title'  => ts('Mobile Providers'),
                             'type'   => self::LOCAL_TASK,
                             'weight' => -2
                             );
    
            self::$_items[] = array(
                             'path'     => 'civicrm',
                             'title'    => ts('CiviCRM'),
                             'access'   => CRM_Utils_System::checkPermission( 'access CiviCRM' ),
                             'callback' => 'civicrm_invoke',
                             'type'     => self::CALLBACK,
                             );

            self::$_items[] = array(
                             'path'   => 'civicrm/contact/search',
                             'title'  => ts('Contacts'),
                             'qs'     => 'reset=1',
                             'type'   => self::NORMAL_ITEM,
                             'access'   => CRM_Utils_System::checkPermission( 'access CiviCRM' ),
                             'weight' => 10,
                             );
        
            self::$_items[] = array(
                             'path'   => 'civicrm/contact/search/basic',
                             'title'  => ts('Find Contacts'),
                             'qs'     => 'reset=1',
                             'type'   => self::DEFAULT_LOCAL_TASK,
                             'weight' => 0
                             );

            self::$_items[] = array(
                             'path'   => 'civicrm/contact/search/advanced',
                             'title'  => ts('Advanced Search'),
                             'type'   => self::LOCAL_TASK,
                             'weight' => 1
                             );
            self::$_items[] = array(
                             'path'   => 'civicrm/contact/search/saved',
                             'title'  => ts('Saved Searches'),
                             'qs'     => 'reset=1',
                             'type'   => self::LOCAL_TASK,
                             'weight' => 2
                             );

            self::$_items[] = array(
                             'path'   => 'civicrm/contact/addI',
                             'title'  => ts('New Individual'),
                             'qs'     => 'reset=1',
                             'access' => CRM_Utils_System::checkPermission('add contacts') &&
                                         CRM_Utils_System::checkPermission( 'access CiviCRM' ),
                             'type'   => self::CALLBACK,
                             'weight' => 1
                             );
        
            self::$_items[] = array(
                             'path'   => 'civicrm/contact/addO',
                             'title'  => ts('New Organization'),
                             'qs'     => 'reset=1',
                             'access' => CRM_Utils_System::checkPermission('add contacts') &&
                                         CRM_Utils_System::checkPermission( 'access CiviCRM' ),
                             'type'   => self::CALLBACK,
                             'weight' => 1
                             );
        
            self::$_items[] = array(
                             'path'   => 'civicrm/contact/addH',
                             'title'  => ts('New Household'),
                             'qs'     => 'reset=1',
                             'access' => CRM_Utils_System::checkPermission('add contacts') &&
                                         CRM_Utils_System::checkPermission( 'access CiviCRM' ),
                             'type'   => self::CALLBACK,
                             'weight' => 1
                             );
        
            self::$_items[] = array(
                             'path'   => 'civicrm/contact/edit',
                             'title'  => ts('Edit Contact Name and Location'),
                             'type'   => self::CALLBACK,
                             'weight' => 1
                             );
        
            self::$_items[] = array(
                             'path'   => 'civicrm/contact/view',
                             'title'  => ts('View Contact'),
                             'type'   => self::CALLBACK
                             );

            self::$_items[] = array(
                             'path'   => 'civicrm/contact/view/basic',
                             'title'  => ts('Contact Summary'),
                             'type'   => self::DEFAULT_LOCAL_TASK,
                             'weight' => 0
                             );

            self::$_items[] = array(
                             'path'   => 'civicrm/contact/view/rel',
                             'title'  => ts('Relationships'),
                             'type'   => self::LOCAL_TASK,
                             'weight' => 1
                             );
        
            self::$_items[] = array(
                             'path'   => 'civicrm/contact/view/note',
                             'title'  => ts('Notes'),
                             'type'   => self::LOCAL_TASK,
                             'weight' => 3
                             );

            self::$_items[] = array(
                             'path'   => 'civicrm/contact/view/group',
                             'title'  => ts('Groups'),
                             'type'   => self::LOCAL_TASK,
                             'weight' => 2
                             );
        
            self::$_items[] = array(
                             'path'   => 'civicrm/contact/view/tag',
                             'title'  => ts('Tags'),
                             'type'   => self::LOCAL_TASK,
                             'weight' => 4
                             );

            self::$_items[] = array(
                             'path'   => 'civicrm/contact/view/cd',
                             'title'  => ts('Custom Data'),
                             'type'   => self::LOCAL_TASK,
                             'weight' => 5
                             );

            self::$_items[] = array(
                             'path'   => 'civicrm/contact/view/activity',
                             'title'  => ts('Activity'),
                             'type'   => self::LOCAL_TASK,
                             'weight' => 6
                             );

            self::$_items[] = array(
                             'path'   => 'civicrm/group',
                             'title'  => ts('Manage Groups'),
                             'qs'     => 'reset=1',
                             'type'   => self::NORMAL_ITEM,
                             'access'   => CRM_Utils_System::checkPermission( 'access CiviCRM' ),
                             'weight' => 20,
                             );

            self::$_items[] = array(
                             'path'   => 'civicrm/group/search',
                             'title'  => ts('Group Members'),
                             'type'   => self::CALLBACK,
                             );
        
            self::$_items[] = array(
                             'path'   => 'civicrm/group/add',
                             'title'  => ts('Create New Group'),
                             'type'   => self::CALLBACK,
                             );
        
            self::$_items[] = array(
                             'path'   => 'civicrm/import',
                             'title'  => ts('Import Contacts'),
                             'qs'     => 'reset=1',
                             'access' => CRM_Utils_System::checkPermission('administer CiviCRM') &&
                                         CRM_Utils_System::checkPermission( 'access CiviCRM' ),
                             'type'   => self::NORMAL_ITEM,
                             'weight' => 30,
                             );

            self::$_items[] = array(
                             'path'   => 'civicrm/history/activity/detail',
                             'title'  => ts('Activity Detail'),
                             'type'   => self::CALLBACK,
                             );

            self::$_items[] = array(
                             'path'   => 'civicrm/history/activity/delete',
                             'title'  => ts('Delete Activity'),
                             'type'   => self::CALLBACK,
                             );
        }
        
        return self::$_items;
    }
}

?>
