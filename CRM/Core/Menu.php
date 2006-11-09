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
 | http://www.civicrm.org/licensing/                                 |
 +--------------------------------------------------------------------+
*/

/**
 * This file contains the various menus of the CiviCRM module
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/Core/I18n.php';

class CRM_Core_Menu {
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
     * The list of dynamic params
     *
     * @var array
     * @static
     */
    static $_params = null;

    /**
     * This is a super super gross hack, please fix sometime soon
     *
     * using constants from DRUPAL/includes/menu.inc, so that we can reuse 
     * the same code in both drupal and joomla
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
        // helper variable for nicer formatting
        require_once 'CRM/Core/Permission.php';
        $drupalSyncExtra = ts('Synchronize Users to Contacts:') . ' ' . ts('CiviCRM will check each user record for a contact record. A new contact record will be created for each user where one does not already exist.') . '\n\n' . ts('Do you want to continue?');
        $backupDataExtra = ts('Backup Your Data:') . ' ' . ts('CiviCRM will create an SQL dump file with all of your existing data, and allow you to download it to your local computer. This process may take a long time and generate a very large file if you have a large number of records.') . '\n\n' . ts('Do you want to continue?');
 
        if ( ! self::$_items ) {
            // This is the minimum information you can provide for a menu item.
            self::$_items =
                array(
                      array(
                            'path'    => 'civicrm/admin',
                            'title'   => ts('Administer CiviCRM'),
                            'query'   => 'reset=1',
                            'access'  => CRM_Core_Permission::check('administer CiviCRM') &&
                                         CRM_Core_Permission::check( 'access CiviCRM' ),
                            'type'    => self::CALLBACK,
                            'crmType' => self::NORMAL_ITEM,
                            'weight'  => 9000,
                            ),

                      array(
                            'path'    => 'civicrm/admin/access',
                            'title'   => ts('Access Control'),
                            'query'   => 'reset=1',
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'adminGroup' => ts('Manage'),
                            'icon'    => 'admin/03.png',
                            'weight'  => 110
                            ),

                      array(
                            'path'    => 'civicrm/admin/backup',
                            'title'   => ts('Backup Data'),
                            'type'    => self::CALLBACK,
                            'extra' => 'onclick = "return confirm(\'' . $backupDataExtra . '\');"',
                            'adminGroup' => ts('Manage'),
                            'icon'    => 'admin/14.png',
                            'weight'  => 120
                            ),
                      
                      array(
                            'path'    => 'civicrm/admin/synchUser',
                            'title'   => ts('Synchronize Users-to-Contacts'),
                            'type'    => self::CALLBACK,
                            'extra' => 'onclick = "if (confirm(\'' . $drupalSyncExtra . '\')) this.href+=\'&amp;confirmed=1\'; else return false;"',
                            'adminGroup' => ts('Manage'),
                            'icon'    => 'admin/Synch_user.png',
                            'weight'  => 130
                            ),
                      
                      array(
                            'path'    => 'civicrm/admin/options',
                            'title'   => ts('Activity Types'),
                            'query'   => 'group=activity_type&reset=1',
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'adminGroup' => ts('Configure'),
                            'icon'    => 'admin/05.png',
                            'weight'  => 210
                            ),
                      
                      array(
                            'path'    => 'civicrm/admin/uf/group',
                            'title'   => ts('CiviCRM Profile'),
                            'query'   => 'reset=1',
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'adminGroup' => ts('Configure'),
                            'icon'    => 'admin/Profile.png',
                            'weight'  => 220
                            ),
                      
                      array(
                            'path'   => 'civicrm/admin/uf/group/field',
                            'title'  => ts('CiviCRM Profile Fields'),
                            'query'  => 'reset=1',
                            'type'   => self::CALLBACK,
                            'crmType'=> self::CALLBACK,
                            'weight' => 221
                            ),
                      
                      array(
                            'path'    => 'civicrm/admin/custom/group',
                            'title'   => ts('Custom Data'),
                            'query'   => 'reset=1',
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'adminGroup' => ts('Configure'),
                            'icon'    => 'admin/custm_data.png',
                            'weight'  => 230
                            ),
                      
                      array(
                            'path'   => 'civicrm/admin/custom/group/field',
                            'title'  => ts('Custom Data Fields'),
                            'query'  => 'reset=1',
                            'type'   => self::CALLBACK,
                            'crmType'=> self::CALLBACK,
                            'weight' => 231
                            ),
                      
                      array(
                            'path'    => 'civicrm/admin/locationType',
                            'title'   => ts('Location Types (Home, Work...)'),
                            'query'  => 'reset=1',
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'adminGroup' => ts('Configure'),
                            'icon'    => 'admin/13.png',
                            'weight'  => 240
                            ),
                      
                      array(
                            'path'    => 'civicrm/admin/tag',
                            'title'   => ts('Tags (Categories)'),
                            'query'  => 'reset=1',
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'adminGroup' => ts('Configure'),
                            'icon'    => 'admin/11.png',
                            'weight'  => 260
                            ),
                      
                      array(
                            'path'    => 'civicrm/admin/mapping',
                            'title'   => ts('Import/Export Mappings'),
                            'query'  => 'reset=1',
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'adminGroup' => ts('Configure'),
                            'icon'    => 'admin/import_export_map.png',
                            'weight'  => 290
                            ),

                     array(
                            'path'    => 'civicrm/contact/domain',
                            'title'   => ts('Edit Domain Information'),
                            'query'  => 'reset=1&action=update',
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'adminGroup' => ts('Configure'),
                            'icon'    => 'admin/domain.png',
                            'weight'  => 270
                            ),

                      array(
                            'path'    => 'civicrm/admin/reltype',
                            'title'   => ts('Relationship Types'),
                            'query'  => 'reset=1',
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'adminGroup' => ts('Configure'),
                            'icon'    => 'admin/rela_type.png',
                            'weight'  => 250
                            ),
                      array(
                            'path'    => 'civicrm/admin/optionGroup',
                            'title'   => ts('Options'),
                            'query'  => 'reset=1',
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'adminGroup' => ts('Configure'),
                            'icon'    => 'admin/option.png',
                            'weight'  => 280
                            ),
                      array(
                            'path'    => 'civicrm/admin/dupematch',
                            'title'   => ts('Duplicate Matching'),
                            'query'  => 'reset=1',
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'adminGroup' => ts('Configure'),
                            'icon'    => 'admin/duplicate_matching.png',
                            'weight'  => 239
                            ),

                      array(
                            'path'    => 'civicrm/admin/setting',
                            'title'   => ts('Global Settings'),
                            'query'  => 'reset=1',
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'adminGroup' => ts('Setup'),
                            'icon'    => 'admin/36.png',
                            'weight'  => 300
                            ),

                      array(
                            'path'    => 'civicrm/admin/options',
                            'title'   => ts('Gender Options (Male, Female...)'),
                            'query'  => 'group=gender&reset=1',
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'adminGroup' => ts('Setup'),
                            'icon'    => 'admin/01.png',
                            'weight'  => 310
                            ),
                      
                      array(
                            'path'    => 'civicrm/admin/options',
                            'title'   => ts('Instant Messenger Services'),
                            'query'  => 'group=instant_messenger_service&reset=1',
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'adminGroup' => ts('Setup'),
                            'icon'    => 'admin/07.png',
                            'weight'  => 320
                            ),

                      array(
                            'path'    => 'civicrm/admin/options',
                            'title'   => ts('Mobile Phone Providers'),
                            'query'  => 'group=mobile_provider&reset=1',
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'adminGroup' => ts('Setup'),
                            'icon'    => 'admin/08.png',
                            'weight'  => 339
                            ),
    
                      array(
                            'path'    => 'civicrm/admin/options',
                            'title'   => ts('Individual Prefixes (Ms, Mr...)'),
                            'query'  => 'group=individual_prefix&reset=1',
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'adminGroup' => ts('Setup'),
                            'icon'    => 'admin/title.png',
                            'weight'  => 340
                            ),
                      
                      array(
                            'path'    => 'civicrm/admin/options',
                            'title'   => ts('Individual Suffixes (Jr, Sr...)'),
                            'query'  => 'group=individual_suffix&reset=1',
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'adminGroup' => ts('Setup'),
                            'icon'    => 'admin/10.png',
                            'weight'  => 350
                            ),


                      array(
                            'path'    => 'civicrm/admin/options',
                            'title'   => ts('Preferred Communication Options'),
                            'query'  => 'group=preferred_communication_method&reset=1',
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'adminGroup' => ts('Setup'),
                            'icon'    => 'admin/communication.png',
                            'weight'  => 360
                            ),

                      array(
                            'path'     => 'civicrm',
                            'title'    => ts('CiviCRM'),
                            'access'   => CRM_Core_Permission::check( 'access CiviCRM' ),
                            'callback' => 'civicrm_invoke',
                            'type'     => self::NORMAL_ITEM,
                            'crmType'  => self::CALLBACK,
                            'weight'   => 0,
                            ),

                      array( 
                            'path'    => 'civicrm/quickreg', 
                            'title'   => ts( 'Quick Registration' ), 
                            'access'  => 1,
                            'type'    => self::CALLBACK,  
                            'crmType' => self::CALLBACK,  
                            'weight'  => 0,  
                            ),

                      array( 
                            'path'    => 'civicrm/file', 
                            'title'   => ts( 'Browse Uploaded files' ), 
                            'access'  => CRM_Core_Permission::check( 'access uploaded files' ),
                            'type'    => self::CALLBACK,  
                            'crmType' => self::CALLBACK,  
                            'weight'  => 0,  
                            ),

                      array(
                            'path'   => 'civicrm/dashboard',
                            'title'  => ts('CiviCRM Home'),
                            'query'  => 'reset=1',
                            'type'   => self::CALLBACK,
                            'crmType'=> self::NORMAL_ITEM,
                            'access' => CRM_Core_Permission::check( 'access CiviCRM' ),
                            'weight' => 0,
                            ),

                      array(
                            'path'    => 'civicrm/contact/search',
                            'title'   => ts('Contacts'),
                            'query'   => 'reset=1',
                            'type'    => self::CALLBACK,
                            'crmType' => self::ROOT_LOCAL_TASK,
                            'access'  => CRM_Core_Permission::check( 'access CiviCRM' ),
                            'weight'  => 10,
                            ),
        
                      array(
                            'path'    => 'civicrm/contact/search/basic',
                            'title'   => ts('Find Contacts'),
                            'query'   => 'reset=1',
                            'type'    => self::CALLBACK,
                            'crmType' => self::DEFAULT_LOCAL_TASK | self::NORMAL_ITEM,
                            'access'  => CRM_Core_Permission::check( 'access CiviCRM' ),
                            'weight'  => 1
                            ),

                      array(
                            'path'    => 'civicrm/contact/search/advanced',
                            'query'   => 'reset=1',
                            'title'   => ts('Advanced Search'),
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'weight'  => 2
                            ),

                      array(
                            'path'    => 'civicrm/contact/search/builder',
                            'title'   => ts('Search Builder'),
                            'query'  => 'reset=1',
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'weight'  => 3
                            ),


                      array(
                            'path'   => 'civicrm/contact/add',
                            'title'  => ts('New Contact'),
                            'query'  => 'reset=1',
                            'access' => CRM_Core_Permission::check('add contacts') &&
                                        CRM_Core_Permission::check( 'access CiviCRM' ),
                            'type'   => self::CALLBACK,
                            'crmType'=> self::CALLBACK,
                            'weight' => 1
                            ),
                
                      array(
                            'path'    => 'civicrm/contact/view',
                            'query'   => 'reset=1&cid=%%cid%%',
                            'title'   => ts('View Contact'),
                            'type'    => self::CALLBACK,
                            'crmType' => self::CALLBACK,
                            'weight'   => 0,
                            ),

                      array(
                            'path'    => 'civicrm/contact/view/basic',
                            'query'   => 'reset=1&cid=%%cid%%',
                            'title'   => ts('Contact Summary'),
                            'type'    => self::CALLBACK,
                            'crmType' => self::CALLBACK,
                            'weight'  => 0
                            ),

                      array(
                            'path'    => 'civicrm/contact/view/activity',
                            'query'   => 'show=1&reset=1&cid=%%cid%%',
                            'title'   => ts('Activities'),
                            'type'    => self::CALLBACK, 
                            'crmType' => self::CALLBACK,
                           'weight'  => 3
                            ),

                      array(
                            'path'    => 'civicrm/contact/view/rel',
                            'query'   => 'reset=1&cid=%%cid%%',
                            'title'   => ts('Relationships'),
                            'type'    => self::CALLBACK,
                            'crmType' => self::CALLBACK,
                            'weight'  => 4
                            ),
        
                      array(
                            'path'    => 'civicrm/contact/view/group',
                            'query'   => 'reset=1&cid=%%cid%%',
                            'title'   => ts('Groups'),
                            'type'    => self::CALLBACK,
                            'crmType' => self::CALLBACK,
                            'weight'  => 5
                            ),
                      
                      array(
                            'path'    => 'civicrm/contact/view/note',
                            'query'   => 'reset=1&cid=%%cid%%',
                            'title'   => ts('Notes'),
                            'type'    => self::CALLBACK,
                            'crmType' => self::CALLBACK,
                            'weight'  => 6
                            ),

                      array(
                            'path'    => 'civicrm/contact/view/tag',
                            'query'   => 'reset=1&cid=%%cid%%',
                            'title'   => ts('Tags'),
                            'type'    => self::CALLBACK,
                            'crmType' => self::CALLBACK,
                            'weight'  => 7
                            ),

                      array(
                            'path'    => 'civicrm/contact/view/cd',
                            'type'    => self::CALLBACK,
                            'crmType' => self::CALLBACK,
                            'weight'  => 0,
                            ),
                     
                      array(
                            'path'   => 'civicrm/group',
                            'title'  => ts('Manage Groups'),
                            'query'  => 'reset=1',
                            'type'   => self::CALLBACK,
                            'crmType'=> self::NORMAL_ITEM,
                            'access' => CRM_Core_Permission::check( 'access CiviCRM' ),
                            'weight' => 30,
                            ),

                      array(
                            'path'   => 'civicrm/group/search',
                            'title'  => ts('Group Members'),
                            'type'   => self::CALLBACK,
                            'crmType'=> self::CALLBACK,
                            ),
        
                      array(
                            'path'    => 'civicrm/group/add',
                            'title'   => ts('Create New Group'),
                            'access' => CRM_Core_Permission::check('edit groups') &&
                            CRM_Core_Permission::check( 'access CiviCRM' ),
                            'type'    => self::CALLBACK,
                            'crmType' => self::CALLBACK,
                            'weight'  => 0,
                            ),
        
                      array(
                            'path'   => 'civicrm/import',
                            'title'  => ts( 'Import' ),
                            'query'  => 'reset=1',
                            'access' => CRM_Core_Permission::check( 'import contacts' ) &&
                                        CRM_Core_Permission::check( 'access CiviCRM' ),
                            'type'   =>  CRM_Core_Menu::CALLBACK,
                            'crmType'=>  CRM_Core_Menu::NORMAL_ITEM,
                            'weight' =>  400,
                            ),
                      array( 
                             'path'    => 'civicrm/import/contact',
                             'query'   => 'reset=1',
                             'title'   => ts( 'Contacts' ), 
                             'access' => CRM_Core_Permission::check( 'import contacts' ) &&
                                          CRM_Core_Permission::check( 'access CiviCRM' ), 
                             'type'    => CRM_Core_Menu::CALLBACK,  
                             'crmType' => CRM_Core_Menu::NORMAL_ITEM,  
                             'weight'  => 410,
                             ),
                       array( 
                             'path'    => 'civicrm/import/activityHistory', 
                             'query'   => 'reset=1',
                             'title'   => ts( 'Activity History' ), 
                              'access' => CRM_Core_Permission::check( 'import contacts' ) &&
                                          CRM_Core_Permission::check( 'access CiviCRM' ),
                             'type'    => CRM_Core_Menu::CALLBACK,  
                             'crmType' => CRM_Core_Menu::NORMAL_ITEM,  
                             'weight'  => 420,  
                             ),

                      array(
                            'path'   => 'civicrm/export/contact',
                            'title'  => ts('Export Contacts'),
                            'type'   => self::CALLBACK,
                            'crmType' => self::CALLBACK,
                            'weight'  => 0,
                            ),
                      
                      array(
                            'path'    => 'civicrm/history/activity/detail',
                            'title'   => ts('Activity Detail'),
                            'type'    => self::CALLBACK,
                            'crmType' => self::CALLBACK,
                            'weight'  => 0,
                            ),

                      array(
                            'path'    => 'civicrm/history/activity/delete',
                            'title'   => ts('Delete Activity'),
                            'type'    => self::CALLBACK,
                            'crmType' => self::CALLBACK,
                            'weight'  => 0,
                            ),

                      array(
                            'path'    => 'civicrm/history/email',
                            'title'   => ts('Sent Email Message'),
                            'type'    => self::CALLBACK,
                            'crmType' => self::CALLBACK,
                            'weight'  => 0,
                            ),

                      array(
                            'path'    => 'civicrm/profile',
                            'title'   => ts( 'Contact Information' ),
                            'access'  => CRM_Core_Permission::check( 'profile listings and forms'),
                            'type'    => self::CALLBACK, 
                            'crmType' => self::CALLBACK, 
                            'weight'  => 0, 
                            ),

                      array(
                            'path'    => 'civicrm/profile/create',
                            'title'   => ts( 'Add Contact Information' ),
                            'access'  => CRM_Core_Permission::check( 'profile listings and forms'),
                            'type'    => self::CALLBACK, 
                            'crmType' => self::CALLBACK, 
                            'weight'  => 0,
                            ),

                      array(
                            'path'    => 'civicrm/profile/note',
                            'title'   => ts( 'Notes about the Person' ),
                            'access'  => CRM_Core_Permission::check( 'profile listings and forms'),
                            'type'    => self::CALLBACK, 
                            'crmType' => self::CALLBACK, 
                            'weight'  => 0,
                            ),

                      array(
                            'path'    => 'civicrm/acl',
                            'title'   => ts( 'Manage ACLs' ),
                            'access'  => CRM_Core_Permission::check('administer CiviCRM') &&
                                         CRM_Core_Permission::check( 'access CiviCRM' ),
                            'type'    => self::CALLBACK, 
                            'crmType' => self::CALLBACK, 
                            'weight'  => 0,
                            ),

                      );

            require_once 'CRM/Core/Component.php';
            $items =& CRM_Core_Component::menu( );
            self::$_items = array_merge( self::$_items, $items );
            
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
        if ( $path == 'civicrm/contact/view/tabbed' ) {
            return;
        }

        self::items( );

        $config =& CRM_Core_Config::singleton( );
        if ( $config->userFramework == 'Joomla' ) {
            static $processed = false;
            if ( ! $processed ) {                
                $processed = true;
                foreach ( self::$_items as $key => $item ) {
                    if ( $item['path'] == $path ) {
                        CRM_Utils_System::setTitle( $item['title'] );
                        break;
                    }
                }
            }
        }

        foreach ( self::$_rootLocalTasks as $root => $dontCare ) {
            if ( strpos( $path, self::$_items[$root]['path'] ) !== false ) {
                $localTasks = array( );
                foreach ( self::$_rootLocalTasks[$root]['children'] as $dontCare => $item ) {
                    $index = $item['index'];
                    $klass = '';
                    if ( strpos( $path, self::$_items[$index]['path'] ) !== false ||
                         ( self::$_items[$root ]['path'] == $path && CRM_Utils_Array::value( 'isDefault', $item ) ) ) {
                        $extra = CRM_Utils_Array::value( 'extra', self::$_items[$index] );
                        if ( $extra ) {
                            foreach ( $extra as $k => $v ) {
                                if ( CRM_Utils_Array::value( $k, $_GET ) == $v ) {
                                    $klass = 'active';
                                }
                            }
                        } else {
                            $klass = 'active';
                        }
                    }
                    $qs  = CRM_Utils_Array::value( 'query', self::$_items[$index] );
                    if ( self::$_params ) {
                        foreach ( self::$_params as $n => $v ) {
                            $qs = str_replace( "%%$n%%", $v, $qs );
                        }
                    }
                    $url = CRM_Utils_System::url( self::$_items[$index]['path'], $qs );
                    $localTasks[self::$_items[$index]['weight']] =
                        array(
                              'url'    => $url, 
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
        // make sure the menu system is initialized before we add stuff to it
        self::items( );

        self::$_items[] = $item;
        self::initialize( );
    }

    /**
     * Add a key, value pair to the params array
     *
     * @param string $key  
     * @param string $value
     *
     * @return void
     * @access public
     * @static
     */
    static function addParam( $key, $value ) {
        if ( ! self::$_params ) {
            self::$_params = array( );
        }
        self::$_params[$key] = $value;
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
                    if ( strpos( self::$_items[$i]['path'], self::$_items[$root]['path'] ) !== false &&
                         CRM_Utils_Array::value( 'access', self::$_items[$i], true ) ) {
                        $isDefault =
                            ( CRM_Utils_Array::value( 'crmType', self::$_items[$i] ) == self::DEFAULT_LOCAL_TASK ) ? true : false;
                        self::$_rootLocalTasks[$root]['children'][] = array( 'index'     => $i,
                                                                             'isDefault' => $isDefault );
                    }
                }
            }
        }
    }

    /**
     * Get the breadcrumb for a give menu task
     *
     * @param string $path the current path for which we need the bread crumb
     *
     * @return string       the breadcrumb for this path
     *
     * @static
     * @access public
     */
    public static function &breadcrumb( $args ) {

        // we dont care about the current menu item
        array_pop( $args );

        $menus =& self::items( );

        $crumbs      = array( );
        $currentPath = null;
        foreach ( $args as $arg ) {
            $currentPath = $currentPath ? "{$currentPath}/{$arg}" : $arg;

            foreach ( $menus as $menu ) {
                if ( $menu['path'] == $currentPath ) {
                    $crumbs[] = array('title' => $menu['title'], 
                                      'url'   => CRM_Utils_System::url( $menu['path'] ) );
                }
            }
        }

        return $crumbs;
        // CRM_Core_Error::debug( 'bc', $crumbs );
    }

    /**
     * Get children for a particular menu path sorted by ascending weight
     *
     * @param  string        $path  parent menu path
     * @param  int|array     $type  menu types
     *
     * @return array         $menus
     *
     * @static
     * @access public
     */
    public static function getChildren($path, $type)
    {

        $childMenu = array();

        $path = trim($path, '/');

        // since we need children only
        $path .= '/';
        
        foreach (self::items() as $menu) {
            if (strpos($menu['path'], $path) === 0) {
                // need to add logic for menu types
                $childMenu[] = $menu;
            }
        }
        return $childMenu;
    }


    /**
     * Get max weight for a path
     *
     * @param  string $path  parent menu path
     *
     * @return int    max weight for the path           
     *
     * @static
     * @access public
     */
    public static function getMaxWeight($path)
    {

        $path = trim($path, '/');

        // since we need children only
        $path .= '/';

        $maxWeight  = -1024;   // weights can have -ve numbers hence cant initialize it to 0
        $firstChild = true;

        foreach (self::items() as $menu) {
            if (strpos($menu['path'], $path) === 0) {
                if ($firstChild) {
                    // maxWeight is initialized to the weight of the first child
                    $maxWeight = $menu['weight'];
                    $firstChild = false;
                } else {
                    $maxWeight = ($menu['weight'] > $maxWeight) ? $menu['weight'] : $maxWeight;
                }
            }
        }

        return $maxWeight;
    }


}

?>
