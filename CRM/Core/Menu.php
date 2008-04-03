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
 * This file contains the various menus of the CiviCRM module
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Core/I18n.php';

class CRM_Core_Menu 
{
    const
        IS_FORM   = 1,
        IS_WIZARD = 2,
        IS_PAGE   = 3;

    /**
     * the list of menu items
     * 
     * @var array
     * @static
     */
    static $_items = null;

    /**
     * the list of permissioned menu items
     * 
     * @var array
     * @static
     */
    static $_permissionedItems = null;

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
    static function &items( ) 
    {
        if ( ! self::$_items ) {
            require_once 'CRM/Core/Permission.php';

            // This is the minimum information you can provide for a menu item.
            self::$_items = self::permissionedItems( );
            $config =& CRM_Core_Config::singleton( );

            $args     = explode( '/', CRM_Utils_Array::value( $config->userFrameworkURLVar,
                                                              $_GET ) );
            $firstArg = CRM_Utils_Array::value( 1, $args );

            self::initialize( );

            switch ( $firstArg ) {
            case 'admin':
                $items =& self::adminItems( );
                break;
                
            case 'contact':
                // unset the search item
                unset( self::$_items[3] );
                $items =& self::contactItems( );
                break;

            case 'group':
                $items =& self::groupItems( );
                break;

            case 'import':
                $items =& self::importItems( );
                break;

            case 'profile':
                $items =& self::profileItems( );
                break;

            default:
                $items =& self::miscItems( );
                break;
            }

            self::$_items = array_merge( self::$_items, $items );

            if ( $firstArg && false ) { // FIXME when we fix menu structure
                require_once 'CRM/Core/Component.php';
                $items =& CRM_Core_Component::menu( false, $args[1] );
                self::$_items = array_merge( self::$_items, $items );
            }
            
            self::initialize( );
        }
        
        return self::$_items;
    }

    /**
     * This function defines information for various menu items
     * that are permissioned and part of the CMS permissioning system
     * Most permission have now been moved to invoke
     *
     * @static
     * @access public
     */
    static function &permissionedItems( ) 
    {
        if ( ! self::$_permissionedItems ) {
            require_once 'CRM/Core/Permission.php';
            $config = CRM_Core_Config::singleton( );
            
            self::$_permissionedItems =
                array(
                      'civicrm' => array(
                                         'title'            => ts('CiviCRM'),
                                         'access callback'  => 'civicrm_hack_access',
                                         'access arguments' => array( array( 'access CiviCRM' ) ),
                                         'page callback'    => 'civicrm_invoke',
                                         'type'             => self::NORMAL_ITEM,
                                         'crmType'          => self::CALLBACK,
                                         'weight'           => 0,
                                         ),

                      'civicrm/dashboard' => array(
                                                   'title'  => ts('CiviCRM Home'),
                                                   'query'  => 'reset=1',
                                                   'type'   => self::CALLBACK,
                                                   'crmType'=> self::NORMAL_ITEM,
                                                   'crmCallback'=> 'CRM_Contact_Page_View_DashBoard',
                                                   'access arguments' => array( array( 'access CiviCRM' ) ),
                                                   'weight' => 0,
                                                   ),

                      'civicrm/ajax' => array(
                                              'title'  => null,
                                              'type'   => self::CALLBACK,
                                              'crmType'=> self::CALLBACK,
                                              'crmCallback' => 'CRM_Core_Page_AJAX',
                                              'access arguments' => array( array( 'access CiviCRM' ) ),
                                              'weight' => 0,
                                              ),

                      'civicrm/contact/search/basic' => array(
                                                              'title'   => ts('Find Contacts'),
                                                              'query'   => 'reset=1',
                                                              'type'    => self::CALLBACK,
                                                              'crmType' => self::DEFAULT_LOCAL_TASK | self::NORMAL_ITEM,
                                                              'crmCallback' => array( 'CRM_Core_Invoke', 'search' ),
                                                              'access arguments'  => array( array( 'access CiviCRM' ) ),
                                                              'weight'  => 1
                                                              ),

                      'civicrm/contact/map/event' => array(
                                                           'title'   => ts('Map Event Location'),
                                                           'query'   => 'reset=1',
                                                           'type'    => self::CALLBACK,
                                                           'crmType' => self::CALLBACK,
                                                           'crmCallback' => 'CRM_Contact_Form_Task_Map_Event',
                                                           'access callback'  => true,
                                                           'weight'  => 1
                                                           ),

                      'civicrm/group' => array(
                                               'title'  => ts('Manage Groups'),
                                               'query'  => 'reset=1',
                                               'type'   => self::CALLBACK,
                                               'crmType'=> self::NORMAL_ITEM,
                                               'crmCallback' => 'CRM_Group_Page_Group',
                                               'access arguments' => array( array( 'access CiviCRM' ) ),
                                               'weight' => 30,
                                               ),

                      'civicrm/import' => array(
                                                'title'  => ts( 'Import' ),
                                                'query'  => 'reset=1',
                                                'access arguments' => array( array( 'import contacts', 'access CiviCRM' ) ),
                                                'type'   =>  CRM_Core_Menu::CALLBACK,
                                                'crmType'=>  CRM_Core_Menu::NORMAL_ITEM,
                                                'crmCallback' => array( 'CRM_Core_Invoke', 'import' ),
                                                'weight' =>  400,
                                                ),

                      'civicrm/admin' => array(
                                               'title'   => ts('Administer CiviCRM'),
                                               'query'   => 'reset=1',
                                               'access arguments'  => array( array( 'administer CiviCRM', 'access CiviCRM' ) ),
                                               'type'    => self::CALLBACK,
                                               'crmType' => self::NORMAL_ITEM,
                                               'crmCallback' => 'CRM_Admin_Page_Admin',
                                               'weight'  => 9000,
                                               ),

                      'civicrm/file' => array( 
                                              'title'   => ts( 'Browse Uploaded files' ), 
                                              'access arguments'  => array( array( 'access uploaded files' ) ),
                                              'type'    => self::CALLBACK,  
                                              'crmType' => self::CALLBACK,  
                                              'crmCallback' => 'CRM_Core_Page_File',
                                              'weight'  => 0,  
                                               ),

                      'civicrm/profile' => array(
                                                 'title'   => ts( 'Contact Information' ),
                                                 'access callback'  => true,
                                                 'type'    => self::CALLBACK, 
                                                 'crmType' => self::CALLBACK,
                                                 'crmCallback' => 'CRM_Profile_Page_Listings',
                                                 'weight'  => 0, 
                                                 ),

                      'civicrm/user' => array(
                                              'title'   => ts( 'Contact Dashboard' ),
                                              'access arguments'  => array( array( 'access Contact Dashboard' ) ),
                                              'type'    => self::CALLBACK, 
                                              'crmType' => self::CALLBACK, 
                                              'crmCallback' => 'CRM_Contact_Page_View_UserDashBoard',
                                              'weight'  => 0, 
                                              ),

                      'civicrm/friend' => array(
                                                'title'   => ts( 'Tell a Friend' ),
                                                'access arguments'  => array( array( 'make online contributions', 'register for events' ), 'or' ),
                                                'type'    => self::CALLBACK, 
                                                'crmType' => self::CALLBACK, 
                                                'crmCallback' => 'CRM_Friend_Form',
                                                'crmForm'     => CRM_Core_MENU::IS_FORM,
                                                'weight'  => 0, 
                                                ),
                      
                      'civicrm/logout' => array(
                                                'title'   => ts('Log out'),
                                                'query'   => 'reset=1',
                                                'type'    => self::CALLBACK,
                                                'crmType' => self::DEFAULT_LOCAL_TASK | self::NORMAL_ITEM,
                                                'crmCallback' => array( 'CRM_Core_Invoke', 'logout' ),
                                                'access arguments'  => array( array( 'access CiviCRM' ) ),
                                                'weight'  => 9999,
                                                )
                      
                      );                     

//            require_once 'CRM/Core/Component.php';
//             $permissionedItems =& CRM_Core_Component::menu( true );
//             self::$_permissionedItems = array_merge( self::$_permissionedItems, $permissionedItems );
            
        }
        return self::$_permissionedItems;
    }

    /**
     * create the local tasks array based on current url
     *
     * @param string $path current url path
     * 
     * @return void
     * @access static
     */
    static function createLocalTasks( $path ) 
    {
        if ( $path == 'civicrm/contact/view/tabbed' ) {
            return;
        }

        if ( self::$_localTasks ) {
            return;
        }

        self::items( );

        $config =& CRM_Core_Config::singleton( );
        if ( $config->userFramework == 'Joomla'  || $config->userFramework == 'Standalone') {
            static $processed = false;
            if ( ! $processed ) {                
                $processed = true;
                foreach ( self::$_items as $key => $item ) {
                    if ( $key == $path && isset( $item['title']) ) {
                        CRM_Utils_System::setTitle( $item['title'] );
                        break;
                    }
                }
            }
        }

        foreach ( self::$_rootLocalTasks as $rootPath => $dontCare ) {
            if ( strpos( $path, $rootPath ) !== false ) {
                self::$_localTasks = array( );
                foreach ( self::$_rootLocalTasks[$rootPath]['children'] as $dontCare => $item ) {
                    $childPath = $item['path'];
                    $klass = '';
                    if ( strpos( $path, $childPath ) !== false ||
                         ( $rootPath == $path &&
                           CRM_Utils_Array::value( 'isDefault', $item ) ) ) {
                        $extra = CRM_Utils_Array::value( 'extra', self::$_items[$childPath] );
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
                    $qs  = CRM_Utils_Array::value( 'query', self::$_items[$childPath] );
                    if ( self::$_params ) {
                        foreach ( self::$_params as $n => $v ) {
                            $qs = str_replace( "%%$n%%", $v, $qs );
                        }
                    }
                    $url = CRM_Utils_System::url( $childPath, $qs );
                    self::$_localTasks[self::$_items[$childPath]['weight']] =
                        array(
                              'url'    => $url, 
                              'title'  => self::$_items[$childPath]['title'],
                              'class'  => $klass
                              );
                }
                ksort( self::$_localTasks );
                $template =& CRM_Core_Smarty::singleton( );
                $template->assign_by_ref( 'localTasks', self::$_localTasks );
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
    static function add( &$item ) 
    {
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
    static function addParam( $key, $value ) 
    {
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
    static function initialize( ) 
    {
        self::$_rootLocalTasks = array( );

        $i = -1;
        foreach ( self::$_items as $path => $item ) {
            $i++;
            // this item is a root_local_task and potentially more
            if ( ( CRM_Utils_Array::value( 'crmType', $item ) & self::ROOT_LOCAL_TASK ) &&
                 ( CRM_Utils_Array::value( 'crmType', $item ) >= self::ROOT_LOCAL_TASK ) ) {
                self::$_rootLocalTasks[$path] = array(
                                                      'root'     => $path,
                                                      'children' => array( )
                                                      );
            } else if ( ( CRM_Utils_Array::value( 'crmType', $item ) &  self::LOCAL_TASK ) &&
                        ( CRM_Utils_Array::value( 'crmType', $item ) >= self::LOCAL_TASK ) ) {
                // find parent of the local task
                foreach ( self::$_rootLocalTasks as $path => $dontCare ) {
                    if ( strpos( $item['path'], $path ) !== false &&
                         CRM_Utils_Array::value( 'access callback', $item, true ) ) {
                        $isDefault =
                            ( CRM_Utils_Array::value( 'crmType', $item ) == self::DEFAULT_LOCAL_TASK ) ? true : false;
                        self::$_rootLocalTasks[$path]['children'][] = array( 'path'      => $path,
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
    public static function &breadcrumb( $args ) 
    {
        // we dont care about the current menu item
        array_pop( $args );

        $menus =& self::items( );

        $crumbs      = array( );
        $currentPath = null;
        foreach ( $args as $arg ) {
            $currentPath = $currentPath ? "{$currentPath}/{$arg}" : $arg;

            foreach ( $menus as $path => $menu ) {
                if ( $path == $currentPath ) {
                    $crumbs[] = array('title' => $menu['title'], 
                                      'url'   => CRM_Utils_System::url( $path ) );
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

   
    static function &adminItems( ) 
    {
        // helper variable for nicer formatting
        $drupalSyncExtra = ts('Synchronize Users to Contacts:') . ' ' . ts('CiviCRM will check each user record for a contact record. A new contact record will be created for each user where one does not already exist.') . '\n\n' . ts('Do you want to continue?');


        $items = array(
                       'civicrm/admin/custom/group' => 
                       array(
                             'title'   => ts('Custom Data'),
                             'desc'    => ts('Configure custom fields to collect and store custom data which is not included in the standard CiviCRM forms.'), 
                             'query'   => 'reset=1',
                             'type'    => self::CALLBACK,
                             'crmType' => self::LOCAL_TASK,
                             'crmCallback'=> 'CRM_Custom_Page_Group',
                             'adminGroup' => ts('Customize'),
                             'icon'    => 'admin/small/custm_data.png',
                             'weight'  => 10
                             ),
                       
                       'civicrm/admin/custom/group/field' =>
                       array(
                             'title'  => ts('Custom Data Fields'),
                             'query'  => 'reset=1',
                             'type'   => self::CALLBACK,
                             'crmType'=> self::CALLBACK,
                             'crmCallback'=> 'CRM_Custom_Page_Field',
                             'weight' => 11
                             ),
                       
                       'civicrm/admin/uf/group' => array(
                             'title'   => ts('CiviCRM Profile'),
                             'desc'    => ts('Profiles allow you to aggregate groups of fields and include them in your site as input forms, contact display pages, and search and listings features.'), 
                             'query'   => 'reset=1',
                             'type'    => self::CALLBACK,
                             'crmType' => self::LOCAL_TASK,
                             'crmCallback' => 'CRM_UF_Page_Group',
                             'adminGroup' => ts('Customize'),
                             'icon'    => 'admin/small/Profile.png',
                             'weight'  => 20
                             ),
                       
                       'civicrm/admin/uf/group/field' =>
                       array(
                             'title'  => ts('CiviCRM Profile Fields'),
                             'query'  => 'reset=1',
                             'type'   => self::CALLBACK,
                             'crmType'=> self::CALLBACK,
                             'crmCallback' => 'CRM_UF_Page_Field',
                             'weight' => 21
                             ),
                       
                       'civicrm/admin/dedupefind' => array(
                           'title'      => ts('Find Duplicate Contacts'),
                           'desc'    => ts('Use configured duplicate matching rules to identify suspected duplicate contact records in your database.'),
                           'query'      => 'reset=1',
                           'type'       => self::CALLBACK,
                           'crmType'    => self::LOCAL_TASK,
                           'crmCallback' => 'CRM_Admin_Page_DedupeFind',
                           'adminGroup' => ts('Manage'),
                           'icon'       => 'admin/small/duplicate_matching.png',
                           'weight'     => 130
                           ),

                       'civicrm/admin/synchUser' => array(
                             'title'   => ts('Synchronize Users to Contacts'),
                             'desc'    => ts('Automatically create a CiviCRM contact record for each CMS user record.'),
                             'type'    => self::CALLBACK,
                             'crmCallback' => 'CRM_Admin_Page_CMSUser',
                             'extra' => 'onclick = "if (confirm(\'' . $drupalSyncExtra . '\')) this.href+=\'&amp;confirmed=1\'; else return false;"',
                             'adminGroup' => ts('Manage'),
                             'icon'    => 'admin/small/Synch_user.png',
                             'weight'  => 140
                             ),
                       
                       'civicrm/admin/setting' =>
                       array(
                             'title'   => ts('Global Settings'),
                             'desc'    => ts('Configure Global Settings for your site, including: Enabled Components, Site Preferences for screens and forms, Directory Paths and Resource URLs, Address formats, Localization, Payment Processor, Outbound Email, Mapping, and Debugging.'), 
                             'query'  => 'reset=1',
                             'type'    => self::CALLBACK,
                             'crmType' => self::LOCAL_TASK,
                             'crmCallback' => array( 'CRM_Core_Invoke', 'setting' ),
                             'adminGroup' => ts('Configure'),
                             'icon'    => 'admin/small/36.png',
                             'weight'  => 200
                             ),
                       
                       'civicrm/admin/dupematch' => 
                       array(
                           'title'   => ts('Contact Matching'),
                           'desc'    => ts('Rules used to identify potentially duplicate contact records, and to match imported data to existing contact records.'), 
                           'query'  => 'reset=1',
                           'type'    => self::CALLBACK,
                           'crmType' => self::LOCAL_TASK,
                           'crmCallback' => 'CRM_Admin_Page_DupeMatch',
                           'adminGroup' => ts('Configure'),
                           'icon'    => 'admin/small/duplicate_matching.png',
                           'weight'  => 240
                           ),

                       'civicrm/admin/deduperules' =>
                       array(
                           'title'   => ts('Duplicate Contact Rules'),
                           'desc'    => ts('Rules used to identify potentially duplicate contact records, and to match imported data to existing contact records.'), 
                           'query'  => 'reset=1',
                           'type'    => self::CALLBACK,
                           'crmType' => self::LOCAL_TASK,
                           'crmCallback' => 'CRM_Admin_Page_DedupeRules',
                           'adminGroup' => ts('Configure'),
                           'icon'    => 'admin/small/duplicate_matching.png',
                           'weight'  => 245
                           ),

                       'civicrm/admin/mapping' =>
                       array(
                             'title'      => ts('Import/Export Mappings'),
                             'desc'    => ts('Import and Export mappings allow you to easily run the same job multiple times. This option allows you to rename or delete existing mappings.'), 
                             'query'      => 'reset=1',
                             'type'       => self::CALLBACK,
                             'crmType'    => self::LOCAL_TASK,
                             'crmCallback' => 'CRM_Admin_Page_Mapping',
                             'adminGroup' => ts('Configure'),
                             'icon'       => 'admin/small/import_export_map.png',
                             'weight'     => 250
                             ),
                       
                       'civicrm/admin/messageTemplates' => array(
                             'title'      => ts('Message Templates'),
                             'desc'    => ts('Message templates allow you to save and re-use messages with layouts which you can use when sending email to one or more contacts.'), 
                             'query'      => 'reset=1',
                             'type'       => self::CALLBACK,
                             'crmType'    => self::LOCAL_TASK,
                             'crmCallback' => 'CRM_Admin_Page_MessageTemplates',
                             'adminGroup' => ts('Configure'),
                             'icon'       => 'admin/small/template.png',
                             'weight'     => 260
                             ),

                       'civicrm/contact/domain' => array(
                             'title'   => ts('Domain Information'),
                             'desc'    => ts('Configure primary contact name, email, return-path and address information. This information is used by CiviMail to identify the sending organization.'), 
                             'query'  => 'reset=1&action=update',
                             'type'    => CRM_Core_Menu::CALLBACK,
                             'crmType' => CRM_Core_Menu::LOCAL_TASK,  
                             'crmCallback' => 'CRM_Contact_Form_Domain',
                             'adminGroup' => ts('Configure'),
                             'icon'    => 'admin/small/domain.png',
                             'weight'  => 270
                             ),

                       'civicrm/admin/options' =>
                       array(
                             'title'   => ts('CiviCRM Option Value Pairs'),
                             'desc'    => ts('Configure CiviCRM option values.'), 
                             'query'   => 'reset=1',
                             'type'    => self::CALLBACK,
                             'crmType' => self::LOCAL_TASK,
                             'crmCallback' => 'CRM_Admin_Page_Options',
                             'adminGroup' => ts('Option Lists'),
                             'icon'    => 'admin/small/05.png',
                             'weight'  => 310
                             ),
                       
                       'civicrm/admin/locationType' =>
                       array(
                             'title'   => ts('Location Types (Home, Work...)'),
                             'desc'    => ts('Options for categorizing contact addresses and phone numbers (e.g. Home, Work, Billing, etc.).'), 
                             'query'  => 'reset=1',
                             'type'    => self::CALLBACK,
                             'crmCallback' => 'CRM_Admin_Page_LocationType',
                             'crmType' => self::LOCAL_TASK,
                             'adminGroup' => ts('Option Lists'),
                             'icon'    => 'admin/small/13.png',
                             'weight'  => 360
                             ),
                       
                       'civicrm/admin/reltype' =>
                       array(
                             'title'   => ts('Relationship Types'),
                             'desc'    => ts('Contacts can be linked to each other through Relationships (e.g. Spouse, Employer, etc.). Define the types of relationships you want to record here.'), 
                             'query'  => 'reset=1',
                             'type'    => self::CALLBACK,
                             'crmType' => self::LOCAL_TASK,
                             'crmCallback' => 'CRM_Admin_Page_RelationshipType',
                             'adminGroup' => ts('Option Lists'),
                             'icon'    => 'admin/small/rela_type.png',
                             'weight'  => 375
                             ),
                       
                       'civicrm/admin/tag' =>
                       array(
                             'title'   => ts('Tags (Categories)'),
                             'desc'    => ts('Tags are useful for segmenting the contacts in your database into categories (e.g. Staff Member, Donor, Volunteer, etc.). Create and edit available tags here.'), 
                             'query'  => 'reset=1',
                             'type'    => self::CALLBACK,
                             'crmType' => self::LOCAL_TASK,
                             'crmCallback' => 'CRM_Admin_Page_Tag',
                             'adminGroup' => ts('Option Lists'),
                             'icon'    => 'admin/small/11.png',
                             'weight'  => 380
                             ),
                       
                       'civicrm/admin/paymentProcessor' =>
                       array(
                             'title'   => ts('Payment Processor'),
                             'desc'    => ts('Payment Processor setup for CiviCRM transactions'),
                             'query'  => 'reset=1',
                             'type'    => self::CALLBACK,
                             'crmType' => self::CALLBACK,
                             'crmCallback' => 'CRM_Admin_Page_PaymentProcessor',
                             'adminGroup' => null,
                             'weight'  => 390
                             ),
                       
                       'civicrm/admin/paymentProcessorType' =>
                       array(
                             'title'   => ts('Payment Processor Type'),
                             'desc'    => ts('Payment Processor type information'),
                             'query'  => 'reset=1',
                             'type'    => self::CALLBACK,
                             'crmType' => self::CALLBACK,
                             'crmCallback' => 'CRM_Admin_Page_PaymentProcessorType',
                             'adminGroup' => null,
                             'weight'  => 390
                             ),
                       
                       );

        $config = CRM_Core_Config::singleton( );
        if ( $config->userFramework != 'Joomla' ) {
            $items['civicrm/admin/access'] = array(
                             'title'   => ts('Access Control'),
                             'desc'    => ts('Grant or deny access to actions (view, edit...), features and components.'), 
                             'query'   => 'reset=1',
                             'type'    => self::CALLBACK,
                             'crmType' => self::LOCAL_TASK,
                             'crmCallback' => 'CRM_Admin_Page_Access',
                             'adminGroup' => ts('Manage'),
                             'icon'    => 'admin/small/03.png',
                             'weight'  => 110
                             );
        }
        
        return $items;
    }

    static function &miscItems( ) 
    {

        $items = array(
                       'civicrm/quickreg' => array( 
                                                   'title'   => ts( 'Quick Registration' ), 
                                                   'type'    => self::CALLBACK,  
                                                   'crmType' => self::CALLBACK,  
                                                   'weight'  => 0,  
                                                    ),

                       'civicrm/export/contact' => array(
                                                         'title'  => ts('Export Contacts'),
                                                         'type'   => self::CALLBACK,
                                                         'crmType' => self::CALLBACK,
                                                         'weight'  => 0,
                                                         ),

                       'civicrm/acl' => array(
                                              'title'   => ts( 'Manage ACLs' ),
                                              'type'    => self::CALLBACK, 
                                              'crmType' => self::CALLBACK, 
                                              'weight'  => 0,
                                              ),
                       );
        return $items;
    }

    static function &contactItems( ) 
    {
        $items = array(
                       'civicrm/contact/search' =>
                       array(
                             'title'   => ts('Contacts'),
                             'query'   => 'reset=1',
                             'type'    => self::CALLBACK,
                             'crmType' => self::ROOT_LOCAL_TASK,
                             'crmCallback' => array( 'CRM_Core_Invoke', 'search' ),
                             'weight'  => 10,
                             ),

                       /* Repeat this here for local nav bar, remove it when we switch *
                        * to using Tab Container                                       */
                       'civicrm/contact/search/basic' =>
                       array(
                             'title'   => ts('Find Contacts'),
                             'query'   => 'reset=1',
                             'type'    => self::CALLBACK,
                             'crmType' => self::DEFAULT_LOCAL_TASK | self::NORMAL_ITEM,
                             'crmCallback' => array( 'CRM_Core_Invoke', 'search' ),
                             'access callback'  => 'civicrm_hack_access',
                             'access arguments'  => CRM_Core_Permission::check( 'access CiviCRM' ),
                             'weight'  => 1
                             ),
                       
                       'civicrm/contact/search/advanced' =>
                       array(
                             'query'   => 'reset=1',
                             'title'   => ts('Advanced Search'),
                             'type'    => self::CALLBACK,
                             'crmType' => self::LOCAL_TASK,
                             'crmCallback' => array( 'CRM_Core_Invoke', 'search' ),
                             'weight'  => 2
                             ),

                       'civicrm/contact/search/builder' =>
                       array(
                             'title'   => ts('Search Builder'),
                             'query'  => 'reset=1',
                             'type'    => self::CALLBACK,
                             'crmType' => self::LOCAL_TASK,
                             'crmCallback' => array( 'CRM_Core_Invoke', 'search' ),
                             'weight'  => 3
                             ),


                       'civicrm/contact/add' =>
                       array(
                             'title'  => ts('New Contact'),
                             'query'  => 'reset=1',
                             'type'   => self::CALLBACK,
                             'crmType'=> self::CALLBACK,
                             'crmCallback' => 'CRM_Contact_Form_Edit',
                             'weight' => 1
                             ),
                
                       'civicrm/contact/view/basic' =>
                       array(
                             'query'   => 'reset=1&cid=%%cid%%',
                             'title'   => ts('Contact Summary'),
                             'type'    => self::CALLBACK,
                             'crmType' => self::CALLBACK,
                             'crmCallback' => 'CRM_Contact_Page_View_Basic',
                             'weight'  => 0
                             ),

                       'civicrm/contact/view/activity' =>
                       array(
                             'query'   => 'show=1&reset=1&cid=%%cid%%',
                             'title'   => ts('Activities'),
                             'type'    => self::CALLBACK, 
                             'crmType' => self::CALLBACK,
                             'crmCallback' => 'CRM_Contact_Page_View_Activity',
                             'weight'  => 3
                             ),

                       'civicrm/contact/view/rel' =>
                       array(
                             'query'   => 'reset=1&cid=%%cid%%',
                             'title'   => ts('Relationships'),
                             'type'    => self::CALLBACK,
                             'crmType' => self::CALLBACK,
                             'crmCallback' => 'CRM_Contact_Page_View_Relationship',
                             'weight'  => 4
                             ),
        
                       'civicrm/contact/view/group' =>
                       array(
                             'query'   => 'reset=1&cid=%%cid%%',
                             'title'   => ts('Groups'),
                             'type'    => self::CALLBACK,
                             'crmType' => self::CALLBACK,
                             'crmCallback' => 'CRM_Contact_Page_View_GroupContact',
                             'weight'  => 5
                             ),
                      
                       'civicrm/contact/view/note' =>
                       array(
                             'query'   => 'reset=1&cid=%%cid%%',
                             'title'   => ts('Notes'),
                             'type'    => self::CALLBACK,
                             'crmType' => self::CALLBACK,
                             'crmCallback' => 'CRM_Contact_Page_View_Note',
                             'weight'  => 6
                             ),

                       'civicrm/contact/view/tag' =>
                       array(
                             'query'   => 'reset=1&cid=%%cid%%',
                             'title'   => ts('Tags'),
                             'type'    => self::CALLBACK,
                             'crmType' => self::CALLBACK,
                             'crmCallback' => 'CRM_Contact_Page_View_Tag',
                             'weight'  => 7
                             ),
                       
                       'civicrm/contact/view/case' =>
                       array(
                             'query'   => 'reset=1&cid=%%cid%%',
                             'title'   => ts('Case'),
                             'type'    => self::CALLBACK,
                             'crmType' => self::CALLBACK,
                             'crmCallback' => 'CRM_Contact_Page_View_Case',
                             'weight'  => 8
                             ),
                       
                       'civicrm/contact/view/cd' =>
                       array(
                             'type'    => self::CALLBACK,
                             'crmType' => self::CALLBACK,
                             'crmCallback' => 'CRM_Contact_Page_View_CustomData',
                             'weight'  => 0,
                             ),
                       );                     
        return $items;
    }

    static function &groupItems( ) 
    {
        $items = array(
                       'civicrm/group' =>
                       array(
                             'title'  => ts('View Groups'),
                             'type'   => self::CALLBACK,
                             'crmType'=> self::CALLBACK,
                             'crmCallback' => 'CRM_Group_Page_Group',
                             ),

                       'civicrm/group/search' =>
                       array(
                             'title'  => ts('Group Members'),
                             'type'   => self::CALLBACK,
                             'crmType'=> self::CALLBACK,
                             'crmCallback' => array( 'CRM_Core_Invoke', 'search' ),
                             ),
        
                       'civicrm/group/add' =>
                       array(
                             'title'   => ts('Create New Group'),
                             'type'    => self::CALLBACK,
                             'crmType' => self::CALLBACK,
                             'crmCallback' => 'CRM_Group_Controller',
                             'weight'  => 0,
                             ),
                       );
        return $items;
    }

    static function &importItems( ) 
    {
        $items = array(
                       'civicrm/import/contact' =>
                       array( 
                             'query'   => 'reset=1',
                             'title'   => ts( 'Contacts' ), 
                             'access callback'  => 'civicrm_hack_access',
                             'access arguments'  => CRM_Core_Permission::check( 'import contacts' ) &&
                             CRM_Core_Permission::check( 'access CiviCRM' ), 
                             'type'    => CRM_Core_Menu::CALLBACK,  
                             'crmType' => CRM_Core_Menu::NORMAL_ITEM,  
                             'crmCallback' => 'CRM_Import_Controller',
                             'weight'  => 410,
                             ),
                       
                       'civicrm/import/activity' =>
                       array( 
                             'query'   => 'reset=1',
                             'title'   => ts( 'Activity' ), 
                             'access callback'  => 'civicrm_hack_access',
                             'access arguments'  => CRM_Core_Permission::check( 'import contacts' ) &&
                             CRM_Core_Permission::check( 'access CiviCRM' ), 
                             'type'    => CRM_Core_Menu::CALLBACK,  
                             'crmType' => CRM_Core_Menu::NORMAL_ITEM,
                             'crmCallback' => 'CRM_Activity_Import_Controller',
                             'weight'  => 420,  
                             ),
                       );                     
        return $items;
    }

    static function &profileItems( ) 
    {
        $items = array(
                       'civicrm/profile' =>
                       array(
                             'title'   => ts( 'CiviCRM Profile' ),
                             'type'    => self::CALLBACK, 
                             'crmType' => self::CALLBACK, 
                             'crmCallback' => array( 'CRM_Core_Invoke', 'profile' ),
                             'weight'  => 0,
                             ),
                       );
        return $items;
    }

    static function storeInDB( &$params ) 
    {
        require_once "CRM/Core/DAO/Menu.php";

        foreach ( $params as $path => $menuItems ) {
            $menu  =& new CRM_Core_DAO_Menu( );
            $menu->domain_id = CRM_Core_Config::domainID( );
            $menu->path      = $path;
            $menu->copyValues( $menuItems );
            $menu->access_arguments = serialize(CRM_Utils_Array::value( 'access_arguments', $menuItems ));
            $menu->save( );
        }

        return $params;
    }
}


