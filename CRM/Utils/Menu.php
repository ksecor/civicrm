<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.1                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Social Source Foundation                        |
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
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 * This file contains the various menus of the CiviCRM module
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Social Source Foundation (c) 2005
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
        // helper variable for nicer formatting
        $drupalSyncExtra = ts('Synchronize Users to Contacts: CiviCRM will check each user record for a contact record. A new contact record will be created for each user where one does not already exist.\n\nDo you want to continue?');
        $backupDataExtra = ts('Backup Your Data: CiviCRM will create an SQL dump file with all of your existing data, and allow you to download it to your local computer. This process may take a long time and generate a very large file if you have a large number of records. \n\nDo you want to continue?');
 
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
                            'crmType' => self::NORMAL_ITEM,
                            'weight'  => 40,
                            ),
        
                      array(
                            'path'    => 'admin/access',
                            'title'   => ts('Access Control'),
                            'type'    => self::CALLBACK,
                            'adminGroup' => 'Manage',
                            'icon'    => 'admin/03.png',
                            'weight'  => 110
                            ),

                      array(
                            'path'    => 'civicrm/admin/backup',
                            'title'   => ts('Backup Data'),
                            'type'    => self::CALLBACK,
                            'extra' => 'onclick = "if (confirm(\'' . $backupDataExtra . '\')) this.href+=\'&amp;confirmed=1\'; else return false;"',
                            'adminGroup' => 'Manage',
                            'icon'    => 'admin/14.png',
                            'weight'  => 120
                            ),
                      
                      array(
                            'path'    => 'civicrm/admin/synchUser',
                            'title'   => ts('Synchronize Users-to-Contacts'),
                            'type'    => self::CALLBACK,
                            'extra' => 'onclick = "if (confirm(\'' . $drupalSyncExtra . '\')) this.href+=\'&amp;confirmed=1\'; else return false;"',
                            'adminGroup' => 'Manage',
                            'icon'    => 'admin/04.png',
                            'weight'  => 130
                            ),
                      
                      array(
                            'path'    => 'civicrm/admin/activityType',
                            'title'   => ts('Activity Types'),
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'adminGroup' => 'Configure',
                            'icon'    => 'admin/05.png',
                            'weight'  => 210
                            ),
                      
                      array(
                            'path'    => 'civicrm/admin/uf/group',
                            'title'   => ts('CiviCRM Profile'),
                            'qs'      => 'reset=1',
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'adminGroup' => 'Configure',
                            'icon'    => 'admin/02.png',
                            'weight'  => 220
                            ),
                      
                      array(
                            'path'   => 'civicrm/admin/uf/group/field',
                            'title'  => ts('CiviCRM Profile Fields'),
                            'qs'     => 'reset=1',
                            'type'   => self::CALLBACK,
                            'crmType'=> self::CALLBACK,
                            'weight' => 221
                            ),
                      
                      array(
                            'path'    => 'civicrm/admin/custom/group',
                            'title'   => ts('Custom Data'),
                            'qs'      => 'reset=1',
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'adminGroup' => 'Configure',
                            'icon'    => 'admin/12.png',
                            'weight'  => 230
                            ),
                      
                      array(
                            'path'   => 'civicrm/admin/custom/group/field',
                            'title'  => ts('Custom Data Fields'),
                            'qs'     => 'reset=1',
                            'type'   => self::CALLBACK,
                            'crmType'=> self::CALLBACK,
                            'weight' => 231
                            ),
                      
                      array(
                            'path'    => 'civicrm/admin/locationType',
                            'title'   => ts('Location Types (Home, Work...)'),
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'adminGroup' => 'Configure',
                            'icon'    => 'admin/13.png',
                            'weight'  => 240
                            ),
                      
                      array(
                            'path'    => 'civicrm/admin/tag',
                            'title'   => ts('Tags (Categories)'),
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'adminGroup' => 'Configure',
                            'icon'    => 'admin/11.png',
                            'weight'  => 260
                            ),

                      array(
                            'path'    => 'civicrm/admin/reltype',
                            'title'   => ts('Relationship Types'),
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'adminGroup' => 'Configure',
                            'icon'    => 'admin/06.png',
                            'weight'  => 250
                            ),

                      array(
                            'path'    => 'civicrm/admin/gender',
                            'title'   => ts('Gender Options (Male, Female...)'),
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'adminGroup' => 'Setup',
                            'icon'    => 'admin/01.png',
                            'weight'  => 310
                            ),
                      
                      array(
                            'path'    => 'civicrm/admin/IMProvider',
                            'title'   => ts('Instant Messenger Services'),
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'adminGroup' => 'Setup',
                            'icon'    => 'admin/07.png',
                            'weight'  => 320
                            ),

                      array(
                            'path'    => 'civicrm/admin/mobileProvider',
                            'title'   => ts('Mobile Phone Providers'),
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'adminGroup' => 'Setup',
                            'icon'    => 'admin/08.png',
                            'weight'  => 339
                            ),
    
                      array(
                            'path'    => 'civicrm/admin/prefix',
                            'title'   => ts('Individual Titles (Ms, Mr...)'),
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'adminGroup' => 'Setup',
                            'icon'    => 'admin/09.png',
                            'weight'  => 340
                            ),
                      
                      array(
                            'path'    => 'civicrm/admin/suffix',
                            'title'   => ts('Individual Suffixes (Jr, Sr...)'),
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'adminGroup' => 'Setup',
                            'icon'    => 'admin/10.png',
                            'weight'  => 350
                            ),
                      
                      array(
                            'path'     => 'civicrm',
                            'title'    => ts('CiviCRM'),
                            'access'   => CRM_Utils_System::checkPermission( 'access CiviCRM' ),
                            'callback' => 'civicrm_invoke',
                            'type'     => self::NORMAL_ITEM,
                            'crmType'  => self::CALLBACK,
                            'weight'   => 0,
                            ),

                      array(
                            'path'    => 'civicrm/contact/search',
                            'title'   => ts('Contacts'),
                            'qs'      => 'reset=1',
                            'type'    => self::CALLBACK,
                            'crmType' => self::ROOT_LOCAL_TASK,
                            'access'  => CRM_Utils_System::checkPermission( 'access CiviCRM' ),
                            'weight'  => 10,
                            ),
        
                      array(
                            'path'    => 'civicrm/contact/search/basic',
                            'title'   => ts('Find Contacts'),
                            'qs'      => 'reset=1',
                            'type'    => self::CALLBACK,
                            'crmType' => self::DEFAULT_LOCAL_TASK | self::NORMAL_ITEM,
                            'access'  => CRM_Utils_System::checkPermission( 'access CiviCRM' ),
                            'weight'  => 0
                            ),

                      array(
                            'path'    => 'civicrm/contact/search/advanced',
                            'qs'      => 'force=1',
                            'title'   => ts('Advanced Search'),
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'weight'  => 1
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
                            'path'    => 'civicrm/contact/view',
                            'qs'      => 'reset=1&cid=%%cid%%',
                            'title'   => ts('View Contact'),
                            'type'    => self::CALLBACK,
                            'crmType' => self::ROOT_LOCAL_TASK,
                            'weight'   => 0,
                            ),

                      array(
                            'path'    => 'civicrm/contact/view/basic',
                            'qs'      => 'reset=1&cid=%%cid%%',
                            'title'   => ts('Contact Summary'),
                            'type'    => self::CALLBACK,
                            'crmType' => self::DEFAULT_LOCAL_TASK,
                            'weight'  => 0
                            ),

                      array(
                            'path'    => 'civicrm/contact/view/activity',
                            'qs'      => 'show=1&reset=1&cid=%%cid%%',
                            'title'   => ts('Activities'),
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'weight'  => 1
                            ),

                      array(
                            'path'    => 'civicrm/contact/view/rel',
                            'qs'      => 'reset=1&cid=%%cid%%',
                            'title'   => ts('Relationships'),
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'weight'  => 2
                            ),
        
                      array(
                            'path'    => 'civicrm/contact/view/group',
                            'qs'      => 'reset=1&cid=%%cid%%',
                            'title'   => ts('Groups'),
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'weight'  => 3
                            ),
                      
                      array(
                            'path'    => 'civicrm/contact/view/note',
                            'qs'      => 'reset=1&cid=%%cid%%',
                            'title'   => ts('Notes'),
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'weight'  => 4
                            ),

                      array(
                            'path'    => 'civicrm/contact/view/tag',
                            'qs'      => 'reset=1&cid=%%cid%%',
                            'title'   => ts('Tags'),
                            'type'    => self::CALLBACK,
                            'crmType' => self::LOCAL_TASK,
                            'weight'  => 5
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
                            'path'    => 'civicrm/group/add',
                            'title'   => ts('Create New Group'),
                            'type'    => self::CALLBACK,
                            'crmType' => self::CALLBACK,
                            'weight'  => 0,
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
                            'title'   => ts( 'Find Contacts' ),
                            'access'  => CRM_Utils_System::checkPermission( 'access CiviCRM Profile Listings'),
                            'type'    => self::CALLBACK, 
                            'crmType' => self::CALLBACK, 
                            'weight'  => 0, 
                            ),

                      array(
                            'path'    => 'civicrm/profile/create',
                            'title'   => ts( 'Add Contact Information' ),
                            'access'  => CRM_Utils_System::checkPermission( 'access CiviCRM Profile Listings'),
                            'type'    => self::CALLBACK, 
                            'crmType' => self::CALLBACK, 
                            'weight'  => 0,
                            ),

                      array(
                            'path'    => 'civicrm/profile/note',
                            'title'   => ts( 'Notes about the Person' ),
                            'access'  => CRM_Utils_System::checkPermission( 'access CiviCRM Profile Listings'),
                            'type'    => self::CALLBACK, 
                            'crmType' => self::CALLBACK, 
                            'weight'  => 0,
                            ),

                      array(
                            'path'    => 'civicrm/mailing/component',
                            'title'   => ts('Mailing Header / Footer'),
                            'type'    => self::CALLBACK,
                            'crmType' => self::CALLBACK,
                            'weight'  => 0,
                            ),

                      array(
                            'path'    => 'civicrm/mailing/send',
                            'title'   => ts('Mailing Message'),
                            'type'    => self::CALLBACK,
                            'crmType' => self::CALLBACK,
                            'weight'  => 0,
                            ),
                      
                      array(
                            'path'    => 'civicrm/mailing/forward',
                            'title'   => ts( 'Forward Mailing' ),
                            'access'  => CRM_Utils_System::checkPermission( 'access CiviCRM Profile Listings'),
                            'type'    => self::CALLBACK, 
                            'crmType' => self::CALLBACK, 
                            'weight'  => 0, 
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

        $config =& CRM_Core_Config::singleton( );
        if ( $config->userFramework == 'Mambo' ) {
            foreach ( self::$_items as $key => $item ) {
                if ( $item['path'] == $path ) {
                    CRM_Utils_System::setTitle( $item['title'] );
                    break;
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
                    $qs  = CRM_Utils_Array::value( 'qs', self::$_items[$index] );
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
