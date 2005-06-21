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
 * This file contains the various menus of the CiviCRM module
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
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
     * the list of root local tasks
     *
     * @var array
     * @static
     */
    static $_rootLocalTasks = null;

    /**
     * the list of local tasks
     *
     * @var array
     * @static
     */
    static $_localTasks = null;

    /**
     * This is a super super gross hack, please fix sometime soon
     *
     * using constants from DRUPAL/includes/menu.inc, so that we can reuse 
     * the same code in both drupal and mambo
     */
    const
        CALLBACK           =    4,
        NORMAL_ITEM        =   22,
        LOCAL_TASK         =  128,
        DEFAULT_LOCAL_TASK =  640,
        ROOT_LOCAL_TASK    = 1152;
    
    /**
     * This function defines information for various menu items
     *
     * @static
     * @access public
     */
    static function &items( ) {
        if ( ! self::$_items ) {
            // This is the minimum information you can provide for a menu item.
            self::$_items =
                array(
                      array(
                            'path'    => 'civicrm/admin',
                            'title'   => ts('Administer CiviCRM'),
                            'qs'      => 'reset=1',
                            'access'  => CRM_Utils_System::checkPermission('administer CiviCRM') &&
                                         CRM_Utils_System::checkPermission( 'access CiviCRM' ),
                            'type'    => self::CALLBACK,
                            'crmType' => self::ROOT_LOCAL_TASK | self::NORMAL_ITEM,
                            'weight'  => 40,
                            ),
        
                      array(
                            'path'    => 'civicrm/admin/tag',
                            'title'   => ts('Tags'),
                            'type'    => self::CALLBACK,
                            'crmType' => self::DEFAULT_LOCAL_TASK,
                            'weight'  => -10
                            ),

                      array(
                            'path'    => 'civicrm/admin/reltype',
                            'title'   => ts('Relationship Types'),
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'weight'  => -8
                            ),

                      array(
                            'path'    => 'civicrm/admin/locationType',
                            'title'   => ts('Location Types'),
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'weight'  => -6
                            ),
        
                      array(
                            'path'    => 'civicrm/admin/custom/group',
                            'title'   => ts('Custom Data'),
                            'qs'      => 'reset=1',
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'weight'  => -5
                            ),

                      array(
                            'path'   => 'civicrm/admin/custom/group/field',
                            'title'  => ts('Custom Data Fields'),
                            'qs'     => 'reset=1',
                            'type'   => self::CALLBACK,
                            'crmType'=> self::CALLBACK,
                            'weight' => 3
                            ),

                      array(
                            'path'    => 'civicrm/admin/uf/group',
                            'title'   => ts('User Sharing'),
                            'qs'      => 'reset=1',
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'weight'  => -4
                            ),

                      array(
                            'path'   => 'civicrm/admin/uf/group/field',
                            'title'  => ts('User Sharing Fields'),
                            'qs'     => 'reset=1',
                            'type'   => self::CALLBACK,
                            'crmType'=> self::CALLBACK,
                            'weight' => 3
                            ),

                      array(
                            'path'    => 'civicrm/admin/IMProvider',
                            'title'   => ts('IM Services'),
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'weight'  => -3
                            ),

                      array(
                            'path'    => 'civicrm/admin/mobileProvider',
                            'title'   => ts('Mobile Providers'),
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'weight'  => -2
                            ),
    
                      array(
                            'path'     => 'civicrm',
                            'title'    => ts('CiviCRM'),
                            'access'   => CRM_Utils_System::checkPermission( 'access CiviCRM' ),
                            'callback' => 'civicrm_invoke',
                            'type'     => self::NORMAL_ITEM,
                            'crmType'  => self::CALLBACK,
                            ),

                      array(
                            'path'    => 'civicrm/contact/search',
                            'title'   => ts('Contacts'),
                            'qs'      => 'reset=1',
                            'type'    => self::CALLBACK,
                            'crmType' => self::ROOT_LOCAL_TASK | self::NORMAL_ITEM,
                            'access'  => CRM_Utils_System::checkPermission( 'access CiviCRM' ),
                            'weight'  => 10,
                            ),
        
                      array(
                            'path'    => 'civicrm/contact/search/basic',
                            'title'   => ts('Find Contacts'),
                            'qs'      => 'reset=1',
                            'type'    => self::CALLBACK,
                            'crmType' => self::DEFAULT_LOCAL_TASK,
                            'weight'  => 0
                            ),

                      array(
                            'path'    => 'civicrm/contact/search/advanced',
                            'title'   => ts('Advanced Search'),
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'weight'  => 1
                            ),
                      array(
                            'path'    => 'civicrm/contact/search/saved',
                            'title'   => ts('Saved Searches'),
                            'qs'      => 'reset=1',
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'weight'  => 2
                            ),

                      array(
                            'path'   => 'civicrm/contact/addI',
                            'title'  => ts('New Individual'),
                            'qs'     => 'reset=1',
                            'access' => CRM_Utils_System::checkPermission('add contacts') &&
                            CRM_Utils_System::checkPermission( 'access CiviCRM' ),
                            'type'   => self::CALLBACK,
                            'crmType'=> self::CALLBACK,
                            'weight' => 1
                            ),
        
                      array(
                            'path'   => 'civicrm/contact/addO',
                            'title'  => ts('New Organization'),
                            'qs'     => 'reset=1',
                            'access' => CRM_Utils_System::checkPermission('add contacts') &&
                                        CRM_Utils_System::checkPermission( 'access CiviCRM' ),
                            'type'   => self::CALLBACK,
                            'crmType'=> self::CALLBACK,
                            'weight' => 1
                            ),
        
                      array(
                            'path'   => 'civicrm/contact/addH',
                            'title'  => ts('New Household'),
                            'qs'     => 'reset=1',
                            'access' => CRM_Utils_System::checkPermission('add contacts') &&
                                        CRM_Utils_System::checkPermission( 'access CiviCRM' ),
                            'type'   => self::CALLBACK,
                            'crmType'=> self::CALLBACK,
                            'weight' => 1
                            ),
        
                      array(
                            'path'   => 'civicrm/contact/edit',
                            'title'  => ts('Edit Contact Name and Location'),
                            'type'   => self::CALLBACK,
                            'crmType'=> self::CALLBACK,
                            'weight' => 1
                            ),
        
                      array(
                            'path'    => 'civicrm/contact/view',
                            'title'   => ts('View Contact'),
                            'type'    => self::CALLBACK,
                            'crmType' => self::ROOT_LOCAL_TASK,
                            ),

                      array(
                            'path'    => 'civicrm/contact/view/basic',
                            'title'   => ts('Contact Summary'),
                            'type'    => self::CALLBACK,
                            'crmType' => self::DEFAULT_LOCAL_TASK,
                            'weight'  => 0
                            ),

                      array(
                            'path'    => 'civicrm/contact/view/activity',
                            'title'   => ts('Activities'),
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'weight'  => 1
                            ),

                      array(
                            'path'    => 'civicrm/contact/view/rel',
                            'title'   => ts('Relationships'),
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'weight'  => 2
                            ),
        
                      array(
                            'path'    => 'civicrm/contact/view/group',
                            'title'   => ts('Groups'),
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'weight'  => 3
                            ),
                      
                      array(
                            'path'    => 'civicrm/contact/view/note',
                            'title'   => ts('Notes'),
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'weight'  => 4
                            ),

                      array(
                            'path'    => 'civicrm/contact/view/tag',
                            'title'   => ts('Tags'),
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'weight'  => 5
                            ),

                      array(
                            'path'    => 'civicrm/contact/view/cd',
                            'title'   => ts('Custom Data'),
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'weight'  => 6
                            ),

                      array(
                            'path'   => 'civicrm/group',
                            'title'  => ts('Manage Groups'),
                            'qs'     => 'reset=1',
                            'type'   => self::CALLBACK,
                            'crmType'=> self::NORMAL_ITEM,
                            'access' => CRM_Utils_System::checkPermission( 'access CiviCRM' ),
                            'weight' => 20,
                            ),

                      array(
                            'path'   => 'civicrm/group/search',
                            'title'  => ts('Group Members'),
                            'type'   => self::CALLBACK,
                            'crmType'=> self::CALLBACK,
                            ),
        
                      array(
                            'path'   => 'civicrm/group/add',
                            'title'  => ts('Create New Group'),
                            'type'   => self::CALLBACK,
                            'crmType'=> self::CALLBACK,
                            ),
        
                      array(
                            'path'   => 'civicrm/import',
                            'title'  => ts('Import Contacts'),
                            'qs'     => 'reset=1',
                            'access' => CRM_Utils_System::checkPermission('administer CiviCRM') &&
                                        CRM_Utils_System::checkPermission( 'access CiviCRM' ),
                            'type'   => self::CALLBACK,
                            'crmType' => self::NORMAL_ITEM,
                            'weight' => 30,
                            ),

                      array(
                            'path'   => 'civicrm/history/activity/detail',
                            'title'  => ts('Activity Detail'),
                            'type'   => self::CALLBACK,
                            'crmType'=> self::CALLBACK,
                            ),

                      array(
                            'path'   => 'civicrm/history/activity/delete',
                            'title'  => ts('Delete Activity'),
                            'type'   => self::CALLBACK,
                            'crmType'=> self::CALLBACK,
                            ),

                      array(
                            'path'   => 'civicrm/history/email',
                            'title'  => ts('Email Detail'),
                            'type'   => self::CALLBACK,
                            'crmType'=> self::CALLBACK,
                            ),
                      );
            
            self::initialize( );
        }
        
        return self::$_items;
    }

    /**
     * create the local tasks array based on current url
     *
     * @param string $path current url path
     * 
     * @return void
     * @access static
     */
    static function createLocalTasks( $path ) {
        self::items( );

        foreach ( self::$_rootLocalTasks as $root => $dontCare ) {
            if ( strpos( $path, self::$_items[$root]['path'] ) !== false ) {
                $localTasks = array( );
                foreach ( self::$_rootLocalTasks[$root]['children'] as $dontCare => $item ) {
                    $index = $item['index'];
                    $klass = '';
                    if ( strpos( $path, self::$_items[$index]['path'] ) !== false ||
                         ( self::$_items[$root ]['path'] == $path && $item['isDefault'] ) ) {
                        $klass = 'active';
                    }
                    $localTasks[self::$_items[$index]['weight']] =
                        array(
                              'url'    => CRM_Utils_System::url( self::$_items[$index]['path'],
                                                                 CRM_Utils_Array::value( 'qs', self::$_items[$index] ) ),
                              'title'  => self::$_items[$index]['title'],
                              'class'  => $klass
                              );
                }
                ksort( $localTasks );
                $template =& CRM_Core_Smarty::singleton( );
                $template->assign_by_ref( 'localTasks', $localTasks );
                return;
            }
        }
    }

    /**
     * Add an item to the menu array
     *
     * @param array $item a menu item with the appropriate menu properties
     *
     * @return void
     * @access public
     * @static
     */
    static function add( &$item ) {
        self::$_items[] = $item;
        self::initialize( );
    }

    /**
     * intialize various objects in the meny array to make further processing simpler
     *
     * @return void
     * @static
     * @access private
     */
    static function initialize( ) {
        self::$_rootLocalTasks = array( );
        for ( $i = 0; $i < count( self::$_items ); $i++ ) {
            // this item is a root_local_task and potentially more
            if ( ( CRM_Utils_Array::value( 'crmType', self::$_items[$i] ) & self::ROOT_LOCAL_TASK ) &&
                 ( CRM_Utils_Array::value( 'crmType', self::$_items[$i] ) >= self::ROOT_LOCAL_TASK ) ) {
                self::$_rootLocalTasks[$i] = array(
                                                   'root'     => $i,
                                                   'children' => array( )
                                                   );
            } else if ( ( CRM_Utils_Array::value( 'crmType', self::$_items[$i] ) &  self::LOCAL_TASK ) &&
                        ( CRM_Utils_Array::value( 'crmType', self::$_items[$i] ) >= self::LOCAL_TASK ) ) {
                // find parent of the local task
                foreach ( self::$_rootLocalTasks as $root => $dontCare ) {
                    if ( strpos( self::$_items[$i]['path'], self::$_items[$root]['path'] ) !== false ) {
                        $isDefault =
                            ( CRM_Utils_Array::value( 'crmType', self::$_items[$i] ) == self::DEFAULT_LOCAL_TASK ) ? true : false;
                        self::$_rootLocalTasks[$root]['children'][] = array( 'index'     => $i,
                                                                             'isDefault' => $isDefault );
                    }
                }
            }
        }
    }

}

?>
