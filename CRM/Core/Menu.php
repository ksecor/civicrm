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
     * The list of dynamic params
     *
     * @var array
     * @static
     */
    static $_params = null;

    static $_serializedElements = array( 'access_arguments',
                                         'access_callback' ,
                                         'page_arguments'  ,
                                         'page_callback'   ,
                                         'breadcrumb'      );

    static $_menuCache = null;

    const
        MENU_ITEM  = 1;

    static function &xmlItems( ) {
        if ( ! self::$_items ) {
            $files = array( 'Menu/Permissioned.xml',
                            'Menu/Admin.xml',
                            'Menu/Contact.xml',
                            'Menu/Group.xml',
                            'Menu/Import.xml',
                            'Menu/Profile.xml',
                            'Menu/Custom.xml',
                            'Menu/Misc.xml'
                            );

            $files = array_merge( $files,
                                  CRM_Core_Component::xmlMenu( ) );

            self::$_items = array( );
            foreach ( $files as $file ) {
                self::read( $file, self::$_items );
            }
        }

        return self::$_items;
    }
    
    static function read( $name, &$menu ) {

        $config =& CRM_Core_Config::singleton( );
        $fileName =
            $config->templateDir .
            $name;

        $xml = simplexml_load_file( $fileName );
        foreach ( $xml->item as $item ) {
            if ( ! (string ) $item->path ) {
                CRM_Core_Error::debug( 'i', $item );
                CRM_Core_Error::fatal( );
            }
            $path = (string ) $item->path;
            $menu[$path] = array( );
            unset( $item->path );
            foreach ( $item as $key => $value ) {
                $key   = (string ) $key;
                $value = (string ) $value;
                if ( strpos( $key, '_callback' ) &&
                     strpos( $value, '::' ) ) {
                    $value = explode( '::', $value );
                } else if ( $key == 'access_arguments' ) {
                    if ( strpos( $value, ',' ) ||
                         strpos( $value, ';' ) ) {
                        if ( strpos( $value, ',' ) ) {
                            $elements = explode( ',', $value );
                            $op = 'and';
                        } else {
                            $elements = explode( ';', $element );
                            $op = 'or';
                        }
                        $items = array( );
                        foreach ( $elements as $element ) {
                            $items[] = $element;
                        }
                        $value = array( $items, $op );
                    } else {
                        $value = array( array( $value ), 'and' );
                    }
                }
                $menu[$path][$key] = $value;
            }
        }
    }

    /**
     * This function defines information for various menu items
     *
     * @static
     * @access public
     */
    static function &items( ) 
    {
        return self::xmlItems( );

        if ( ! self::$_items ) {
            require_once 'CRM/Core/Permission.php';

            // This is the minimum information you can provide for a menu item.
            self::$_items = self::permissionedItems( );
            $config =& CRM_Core_Config::singleton( );

            self::$_items = array_merge( self::$_items,
                                         self::adminItems( ) );

            self::$_items = array_merge( self::$_items,
                                         self::contactItems( ) );

            self::$_items = array_merge( self::$_items,
                                         self::groupItems( ) );
            
            self::$_items = array_merge( self::$_items,
                                         self::importItems( ) );

            self::$_items = array_merge( self::$_items,
                                         self::profileItems( ) );

            self::$_items = array_merge( self::$_items,
                                         self::miscItems( ) );

            // merge component menu items
            self::$_items = array_merge( self::$_items,
                                         CRM_Core_Component::menu( ) );
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
                                         'access_callback'  => array( 'CRM_Core_Permission', 'checkMenu' ),
                                         'access_arguments' => array( array( 'access CiviCRM' ) ),
                                         'page_callback'    => 'CRM_Contact_Page_View_DashBoard',
                                         'page_arguments'   => 'null',
                                         'weight'           => 0,
                                         ),

                      'civicrm/dashboard' => array(
                                                   'title'  => ts('CiviCRM Home'),
                                                   'query'  => array('reset' => 1),
                                                   'page_type'=> self::MENU_ITEM,
                                                   'page_callback'=> 'CRM_Contact_Page_View_DashBoard',
                                                   'access_arguments' => array( array( 'access CiviCRM' ) ),
                                                   'weight' => 0,
                                                   ),

                      'civicrm/ajax' => array(
                                              'title'  => null,
                                              'page_callback' => 'CRM_Core_Page_AJAX',
                                              'access_arguments' => array( array( 'access CiviCRM' ) ),
                                              'weight' => 0,
                                              ),

                      'civicrm/contact/map/event' => array(
                                                           'title'   => ts('Map Event Location'),
                                                           'query'   => array('reset' => 1),
                                                           'page_callback' => 'CRM_Contact_Form_Task_Map_Event',
                                                           'access_callback'  => true,
                                                           'weight'  => 1
                                                           ),

                      'civicrm/group' => array(
                                               'title'  => ts('Manage Groups'),
                                               'query'  => array('reset' => 1),
                                               'page_type'=> self::MENU_ITEM,
                                               'page_callback' => 'CRM_Group_Page_Group',
                                               'access_arguments' => array( array( 'access CiviCRM' ) ),
                                               'weight' => 30,
                                               ),

                      'civicrm/import' => array(
                                                'title'  => ts( 'Import' ),
                                                'query'  => array('reset' => 1),
                                                'access_arguments' => array( array( 'import contacts', 'access CiviCRM' ) ),
                                                'page_type'=>  CRM_Core_Menu::MENU_ITEM,
                                                'page_callback' => 'CRM_Import_Controller',
                                                'weight' =>  400,
                                                ),

                      'civicrm/admin' => array(
                                               'title'   => ts('Administer CiviCRM'),
                                               'query'   => array('reset' => 1),
                                               'access_arguments'  => array( array( 'administer CiviCRM', 'access CiviCRM' ) ),
                                               'page_type' => self::MENU_ITEM,
                                               'page_callback' => 'CRM_Admin_Page_Admin',
                                               'weight'  => 9000,
                                               ),

                      'civicrm/file' => array( 
                                              'title'   => ts( 'Browse Uploaded files' ), 
                                              'access_arguments'  => array( array( 'access uploaded files' ) ),
                                              'page_callback' => 'CRM_Core_Page_File',
                                              'weight'  => 0,  
                                               ),

                      'civicrm/profile' => array(
                                                 'title'   => ts( 'Contact Information' ),
                                                 'access_callback'  => true,
                                                 'page_callback' => 'CRM_Profile_Page_Listings',
                                                 'weight'  => 0, 
                                                 ),

                      'civicrm/user' => array(
                                              'title'   => ts( 'Contact Dashboard' ),
                                              'saccess_arguments'  => array( array( 'access Contact Dashboard' ) ),
                                              'page_callback' => 'CRM_Contact_Page_View_UserDashBoard',
                                              'weight'  => 0, 
                                              ),

                      'civicrm/friend' => array(
                                                'title'   => ts( 'Tell a Friend' ),
                                                'access_arguments'  => array( array( 'make online contributions', 'register for events' ), 'or' ),
                                                'page_callback' => 'CRM_Friend_Form',
                                                'weight'  => 0, 
                                                ),
                      
                      'civicrm/logout' => array(
                                                'title'   => ts('Log out'),
                                                'query'   => array('reset' => 1),
                                                'page_callback' => array( 'CRM_Core_Invoke', 'logout' ),
                                                'access_arguments'  => array( array( 'access CiviCRM' ) ),
                                                'weight'  => 9999,
                                                )
                      
                      );                     

            require_once 'CRM/Core/Component.php';
            $permissionedItems =& CRM_Core_Component::menu( true );
            self::$_permissionedItems = array_merge( self::$_permissionedItems, $permissionedItems );
            
        }
        return self::$_permissionedItems;
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
                             'query'   => array('reset' => 1),
                             'page_callback'=> 'CRM_Custom_Page_Group',
                             'adminGroup' => ts('Customize'),
                             'icon'    => 'admin/small/custm_data.png',
                             'weight'  => 10
                             ),
                       
                       'civicrm/admin/custom/group/field' =>
                       array(
                             'title'  => ts('Custom Data Fields'),
                             'query'  => array('reset' => 1),
                             'page_callback'=> 'CRM_Custom_Page_Field',
                             'weight' => 11
                             ),
                       
                       'civicrm/admin/uf/group' => array(
                             'title'   => ts('CiviCRM Profile'),
                             'desc'    => ts('Profiles allow you to aggregate groups of fields and include them in your site as input forms, contact display pages, and search and listings features.'), 
                             'query'   => array('reset' => 1),
                             'page_callback' => 'CRM_UF_Page_Group',
                             'adminGroup' => ts('Customize'),
                             'icon'    => 'admin/small/Profile.png',
                             'weight'  => 20
                             ),
                       
                       'civicrm/admin/uf/group/field' =>
                       array(
                             'title'  => ts('CiviCRM Profile Fields'),
                             'query'  => array('reset' => 1),
                             'page_callback' => 'CRM_UF_Page_Field',
                             'weight' => 21
                             ),
                       
                       'civicrm/admin/options/custom_search' => 
                       array(
                             'title'   => ts('Register Custom Searches'),
                             'desc'    => ts("Developers and accidental techies with a bit of PHP and SQL knowledge can create new search forms to handle specific search and reporting needs which aren't covered by the built-in Advanced Search and Search Builder features."), 
                             'query'   => array('reset' => 1, 'group' => 'custom_search'),
                             'page_callback' => 'CRM_Admin_Page_Options',
                             'adminGroup' => ts('Customize'),
                             'icon'       => 'admin/small/template.png',
                             'weight'  => 30
                             ),
                       
                       'civicrm/admin/dedupefind' => array(
                           'title'      => ts('Find Duplicate Contacts'),
                           'desc'    => ts('Use configured duplicate matching rules to identify suspected duplicate contact records in your database.'),
                           'query'      => array('reset' => 1),
                           'page_callback' => 'CRM_Admin_Page_DedupeFind',
                           'adminGroup' => ts('Manage'),
                           'icon'       => 'admin/small/duplicate_matching.png',
                           'weight'     => 130
                           ),

                       'civicrm/admin/synchUser' => array(
                             'title'   => ts('Synchronize Users to Contacts'),
                             'desc'    => ts('Automatically create a CiviCRM contact record for each CMS user record.'),
                             'page_callback' => 'CRM_Admin_Page_CMSUser',
                             'extra' => 'onclick = "if (confirm(\'' . $drupalSyncExtra . '\')) this.href+=\'&amp;confirmed=1\'; else return false;"',
                             'adminGroup' => ts('Manage'),
                             'icon'    => 'admin/small/Synch_user.png',
                             'weight'  => 140
                             ),
                       
                       'civicrm/admin/setting' =>
                       array(
                             'title'   => ts('Global Settings'),
                             'desc'    => ts('Configure Global Settings for your site, including: Enabled Components, Site Preferences for screens and forms, Directory Paths and Resource URLs, Address formats, Localization, Payment Processor, Outbound Email, Mapping, and Debugging.'), 
                             'query'  => array('reset' => 1),
                             'page_callback' => 'CRM_Admin_Page_Setting',
                             'adminGroup' => ts('Configure'),
                             'icon'    => 'admin/small/36.png',
                             'weight'  => 200
                             ),

                       'civicrm/admin/setting/component' =>
                       array(
                             'title'   => ts('Components'),
                             'query'   => array('reset' => 1),
                             'page_callback' => 'CRM_Admin_Form_Setting_Component',
                             ),

                       'civicrm/admin/setting/preferences/display' =>
                       array(
                             'title'   => ts('System Preferences'),
                             'query'   => array('reset' => 1),
                             'page_callback' => 'CRM_Admin_Form_Preferences_Display',
                             ),

                       'civicrm/admin/setting/preferences/address' =>
                       array(
                             'title'   => ts('Address Preferences'),
                             'query'   => array('reset' => 1),
                             'page_callback' => 'CRM_Admin_Form_Preferences_Address',
                             ),

                       'civicrm/admin/setting/preferences/date' =>
                       array(
                             'title'   => ts('View Date Prefences'),
                             'query'   => array('reset' => 1),
                             'page_callback' => 'CRM_Admin_Page_PreferencesDate',
                             ),

                       'civicrm/admin/setting/path' =>
                       array(
                             'title'   => ts('File System Paths'),
                             'query'   => array('reset' => 1),
                             'page_callback' => 'CRM_Admin_Form_Setting_Path',
                             ),

                       'civicrm/admin/setting/url' =>
                       array(
                             'title'   => ts('Site URLs'),
                             'query'   => array('reset' => 1),
                             'page_callback' => 'CRM_Admin_Form_Setting_Url',
                             ),

                       'civicrm/admin/setting/smtp' =>
                       array(
                             'title'   => ts('Smtp Server'),
                             'query'   => array('reset' => 1),
                             'page_callback' => 'CRM_Admin_Form_Setting_Smtp',
                             ),

                       'civicrm/admin/setting/uf' =>
                       array(
                             'title'   => ts('User Framework Settings'),
                             'query'   => array('reset' => 1),
                             'page_callback' => 'CRM_Admin_Form_Setting_UF',
                             ),

                       'civicrm/admin/setting/mapping' =>
                       array(
                             'title'   => ts('Mapping and Geocoding'),
                             'query'   => array('reset' => 1),
                             'page_callback' => 'CRM_Admin_Form_Setting_Mapping',
                             ),

                       'civicrm/admin/setting/localization' =>
                       array(
                             'title'   => ts('Localization'),
                             'query'   => array('reset' => 1),
                             'page_callback' => 'CRM_Admin_Form_Setting_Localization',
                             ),

                       'civicrm/admin/setting/date' =>
                       array(
                             'title'   => ts('Date Formatting'),
                             'query'   => array('reset' => 1),
                             'page_callback' => 'CRM_Admin_Form_Setting_Date',
                             ),

                       'civicrm/admin/setting/misc' =>
                       array(
                             'title'   => ts('Miscellaneous'),
                             'query'   => array('reset' => 1),
                             'page_callback' => 'CRM_Admin_Form_Setting_Miscellaneous',
                             ),

                       'civicrm/admin/setting/debug' =>
                       array(
                             'title'   => ts('Debugging'),
                             'query'   => array('reset' => 1),
                             'page_callback' => 'CRM_Admin_Form_Setting_Debugging',
                             ),

                       'civicrm/admin/setting/updateConfigBackend' =>
                       array(
                             'title'   => ts('Update Config Backend'),
                             'query'   => array('reset' => 1),
                             'page_callback' => 'CRM_Admin_Form_Setting_UpdateConfigBackend',
                             ),

                       'civicrm/admin/dupematch' => 
                       array(
                           'title'   => ts('Contact Matching'),
                           'desc'    => ts('Rules used to identify potentially duplicate contact records, and to match imported data to existing contact records.'), 
                           'query'  => array('reset' => 1),
                           'page_callback' => 'CRM_Admin_Page_DupeMatch',
                           'adminGroup' => ts('Configure'),
                           'icon'    => 'admin/small/duplicate_matching.png',
                           'weight'  => 240
                           ),

                       'civicrm/admin/deduperules' =>
                       array(
                           'title'   => ts('Duplicate Contact Rules'),
                           'desc'    => ts('Rules used to identify potentially duplicate contact records, and to match imported data to existing contact records.'), 
                           'query'  => array('reset' => 1),
                           'page_callback' => 'CRM_Admin_Page_DedupeRules',
                           'adminGroup' => ts('Configure'),
                           'icon'    => 'admin/small/duplicate_matching.png',
                           'weight'  => 245
                           ),

                       'civicrm/admin/mapping' =>
                       array(
                             'title'      => ts('Import/Export Mappings'),
                             'desc'    => ts('Import and Export mappings allow you to easily run the same job multiple times. This option allows you to rename or delete existing mappings.'), 
                             'query'      => array('reset' => 1),
                             'page_callback' => 'CRM_Admin_Page_Mapping',
                             'adminGroup' => ts('Configure'),
                             'icon'       => 'admin/small/import_export_map.png',
                             'weight'     => 250
                             ),
                       
                       'civicrm/admin/messageTemplates' => array(
                             'title'      => ts('Message Templates'),
                             'desc'    => ts('Message templates allow you to save and re-use messages with layouts which you can use when sending email to one or more contacts.'), 
                             'query'      => array('reset' => 1),
                             'page_callback' => 'CRM_Admin_Page_MessageTemplates',
                             'adminGroup' => ts('Configure'),
                             'icon'       => 'admin/small/template.png',
                             'weight'     => 260
                             ),

                       'civicrm/contact/domain' => array(
                             'title'   => ts('Domain Information'),
                             'desc'    => ts('Configure primary contact name, email, return-path and address information. This information is used by CiviMail to identify the sending organization.'), 
                             'query'  => array('reset' => 1, 'action' => 'update'),
                             'page_callback' => 'CRM_Contact_Form_Domain',
                             'adminGroup' => ts('Configure'),
                             'icon'    => 'admin/small/domain.png',
                             'weight'  => 270
                             ),

                       'civicrm/admin/weight' =>
                       array(
                             'title' => ts( 'This should never be seen' ),
                             'page_callback' => array( 'CRM_Utils_Weight', 'fixOrder' ),
                             ),

                       'civicrm/admin/options/activity_type' =>
                       array(
                             'title'   => ts('Activity Types'),
                             'desc'    => ts('CiviCRM has several built-in activity types (meetings, phone calls, emails sent). Track other types of interactions by creating custom activity types here.'), 
                             'query'   => array('reset'=> 1, 'group' => 'activity_type'),
                             'page_callback' => 'CRM_Admin_Page_Options',
                             'adminGroup' => ts('Option Lists'),
                             'icon'    => 'admin/small/05.png',
                             'weight'  => 310
                             ),
                       
                       'civicrm/admin/options/gender' =>
                       array(
                             'title'   => ts('Gender Options'),
                             'desc'    => ts('Options for assigning gender to individual contacts (e.g. Male, Female, Transgender).'), 
                             'query'  => array('reset' => 1, 'group' => 'gender'),
                             'page_callback' => 'CRM_Admin_Page_Options',
                             'adminGroup' => ts('Option Lists'),
                             'icon'    => 'admin/small/01.png',
                             'weight'  => 320
                             ),

                       'civicrm/admin/options/individual_prefix' =>
                       array(
                             'title'   => ts('Individual Prefixes (Ms, Mr...)'),
                             'desc'    => ts('Options for individual contact prefixes (e.g. Ms., Mr., Dr. etc.).'), 
                             'query'  => array('reset' => 1, 'group' => 'individual_prefix'),
                             'page_callback' => 'CRM_Admin_Page_Options',
                             'adminGroup' => ts('Option Lists'),
                             'icon'    => 'admin/small/title.png',
                             'weight'  => 330
                             ),

                       'civicrm/admin/options/individual_suffix' =>
                       array(
                             'title'   => ts('Individual Suffixes (Jr, Sr...)'),
                             'desc'    => ts('Options for individual contact suffixes (e.g. Jr., Sr. etc.).'), 
                             'query'  => array('reset' => 1, 'group' => 'individual_suffix'),
                             'page_callback' => 'CRM_Admin_Page_Options',
                             'adminGroup' => ts('Option Lists'),
                             'icon'    => 'admin/small/10.png',
                             'weight'  => 340
                             ),

                       'civicrm/admin/options/instant_messenger_service' =>
                       array(
                             'title'   => ts('Instant Messenger Services'),
                             'desc'    => ts('List of IM services which can be used when recording screen-names for contacts.'), 
                             'query'  => array('reset' => 1, 'group' => 'instant_messenger_service'),
                             'page_callback' => 'CRM_Admin_Page_Options',
                             'adminGroup' => ts('Option Lists'),
                             'icon'    => 'admin/small/07.png',
                             'weight'  => 350
                             ),

                       'civicrm/admin/options/mobile_provider' =>
                       array(
                             'title'   => ts('Mobile Phone Providers'),
                             'desc'    => ts('List of mobile phone providers which can be assigned when recording contact phone numbers.'), 
                             'query'  => array('reset' => 1, 'group' => 'mobile_provider'),
                             'page_callback' => 'CRM_Admin_Page_Options',
                             'adminGroup' => ts('Option Lists'),
                             'icon'    => 'admin/small/08.png',
                             'weight'  => 365
                             ),

                       'civicrm/admin/options/preferred_communication_method' =>
                       array(
                             'title'   => ts('Preferred Communication Methods'),
                             'desc'    => ts('One or more preferred methods of communication can be assigned to each contact. Customize the available options here.'), 
                             'query'  => array('reset' => 1, 'group' => 'preferred_communication_method'),
                             'page_callback' => 'CRM_Admin_Page_Options',
                             'adminGroup' => ts('Option Lists'),
                             'icon'    => 'admin/small/communication.png',
                             'weight'  => 370
                             ),

                       'civicrm/admin/locationType' =>
                       array(
                             'title'   => ts('Location Types (Home, Work...)'),
                             'desc'    => ts('Options for categorizing contact addresses and phone numbers (e.g. Home, Work, Billing, etc.).'), 
                             'query'  => array('reset' => 1),
                             'page_callback' => 'CRM_Admin_Page_LocationType',
                             'adminGroup' => ts('Option Lists'),
                             'icon'    => 'admin/small/13.png',
                             'weight'  => 360
                             ),
                       
                       'civicrm/admin/reltype' =>
                       array(
                             'title'   => ts('Relationship Types'),
                             'desc'    => ts('Contacts can be linked to each other through Relationships (e.g. Spouse, Employer, etc.). Define the types of relationships you want to record here.'), 
                             'query'  => array('reset' => 1),
                             'page_callback' => 'CRM_Admin_Page_RelationshipType',
                             'adminGroup' => ts('Option Lists'),
                             'icon'    => 'admin/small/rela_type.png',
                             'weight'  => 375
                             ),
                       
                       'civicrm/admin/tag' =>
                       array(
                             'title'   => ts('Tags (Categories)'),
                             'desc'    => ts('Tags are useful for segmenting the contacts in your database into categories (e.g. Staff Member, Donor, Volunteer, etc.). Create and edit available tags here.'), 
                             'query'  => array('reset' => 1),
                             'page_callback' => 'CRM_Admin_Page_Tag',
                             'adminGroup' => ts('Option Lists'),
                             'icon'    => 'admin/small/11.png',
                             'weight'  => 380
                             ),
                       
                       'civicrm/admin/paymentProcessor' =>
                       array(
                             'title'   => ts('Payment Processor'),
                             'desc'    => ts('Payment Processor setup for CiviCRM transactions'),
                             'query'  => array('reset' => 1),
                             'page_callback' => 'CRM_Admin_Page_PaymentProcessor',
                             'adminGroup' => null,
                             'weight'  => 390
                             ),
                       
                       'civicrm/admin/paymentProcessorType' =>
                       array(
                             'title'   => ts('Payment Processor Type'),
                             'desc'    => ts('Payment Processor type information'),
                             'query'  => array('reset' => 1),
                             'page_callback' => 'CRM_Admin_Page_PaymentProcessorType',
                             'adminGroup' => null,
                             'weight'  => 390
                             ),
                       
                       );

        $config = CRM_Core_Config::singleton( );
        if ( $config->userFramework != 'Joomla' ) {
            $items['civicrm/admin/access'] = array(
                             'title'   => ts('Access Control'),
                             'desc'    => ts('Grant or deny access to actions (view, edit...), features and components.'), 
                             'query'   => array('reset' => 1),
                             'page_callback' => 'CRM_Admin_Page_Access',
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
                       'civicrm/export/contact' => array(
                                                         'title'  => ts('Export Contacts'),
                                                         'weight'  => 0,
                                                         ),

                       'civicrm/acl' => array(
                                              'title'   => ts( 'Manage ACLs' ),
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
                             'query'   => array('reset' => 1),
                             'page_callback' => array( 'CRM_Core_Invoke', 'search' ),
                             'weight'  => 10,
                             ),

                       /* Repeat this here for local nav bar, remove it when we switch *
                        * to using Tab Container                                       */
                       'civicrm/contact/search/basic' =>
                       array(
                             'title'   => ts('Find Contacts'),
                             'query'   => array('reset' => 1),
                             'page_type' => self::MENU_ITEM,
                             'page_callback' => array( 'CRM_Core_Invoke', 'search' ),
                             'access_arguments'  => array( array( 'access CiviCRM' )),
                             'weight'  => 1
                             ),
                       
                       'civicrm/contact/search/advanced' =>
                       array(
                             'query'   => array('reset' => 1),
                             'title'   => ts('Advanced Search'),
                             'page_callback' => array( 'CRM_Core_Invoke', 'search' ),
                             'weight'  => 2
                             ),

                       'civicrm/contact/search/builder' =>
                       array(
                             'title'   => ts('Search Builder'),
                             'query'  => array('reset' => 1),
                             'page_callback' => array( 'CRM_Core_Invoke', 'search' ),
                             'weight'  => 3
                             ),


                       'civicrm/contact/add' =>
                       array(
                             'title'  => ts('New Contact'),
                             'query'  => array('reset' => 1),
                             'page_callback' => 'CRM_Contact_Form_Edit',
                             'weight' => 1
                             ),
                
                       'civicrm/contact/view/basic' =>
                       array(
                             'query'   => array('reset' => 1, 'cid' => '%%cid%%'),
                             'title'   => ts('Contact Summary'),
                             'page_callback' => 'CRM_Contact_Page_View_Basic',
                             'weight'  => 0
                             ),

                       'civicrm/contact/view/activity' =>
                       array(
                             'query'   => array('reset' => 1, 'show' => 1, 'cid' => '%%cid%%'),
                             'title'   => ts('Activities'),
                             'page_callback' => 'CRM_Contact_Page_View_Activity',
                             'weight'  => 3
                             ),

                       'civicrm/contact/view/rel' =>
                       array(
                             'query'   => array('reset' => 1, 'cid' => '%%cid%%'),
                             'title'   => ts('Relationships'),
                             'page_callback' => 'CRM_Contact_Page_View_Relationship',
                             'weight'  => 4
                             ),
        
                       'civicrm/contact/view/group' =>
                       array(
                             'query'   => array('reset' => 1, 'cid' => '%%cid%%'),
                             'title'   => ts('Groups'),
                             'page_callback' => 'CRM_Contact_Page_View_GroupContact',
                             'weight'  => 5
                             ),
                      
                       'civicrm/contact/view/note' =>
                       array(
                             'query'   => array('reset' => 1, 'cid' => '%%cid%%'),
                             'title'   => ts('Notes'),
                             'page_callback' => 'CRM_Contact_Page_View_Note',
                             'weight'  => 6
                             ),

                       'civicrm/contact/view/tag' =>
                       array(
                             'query'   => array('reset' => 1, 'cid' => '%%cid%%'),
                             'title'   => ts('Tags'),
                             'page_callback' => 'CRM_Contact_Page_View_Tag',
                             'weight'  => 7
                             ),
                       
                       'civicrm/contact/view/case' =>
                       array(
                             'query'   => array('reset' => 1, 'cid' => '%%cid%%'),
                             'title'   => ts('Case'),
                             'page_callback' => 'CRM_Contact_Page_View_Case',
                             'weight'  => 8
                             ),
                       
                       'civicrm/contact/view/cd' =>
                       array(
                             'page_callback' => 'CRM_Contact_Page_View_CustomData',
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
                             'page_callback' => 'CRM_Group_Page_Group',
                             ),

                       'civicrm/group/search' =>
                       array(
                             'title'  => ts('Group Members'),
                             'page_callback' => array( 'CRM_Core_Invoke', 'search' ),
                             ),
        
                       'civicrm/group/add' =>
                       array(
                             'title'   => ts('Create New Group'),
                             'page_callback' => 'CRM_Group_Controller',
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
                             'query'   => array('reset' => 1),
                             'title'   => ts( 'Contacts' ), 
                             'access_arguments'  => array( array('import contacts', 'access CiviCRM') ), 
                             'page_type' => CRM_Core_Menu::MENU_ITEM,  
                             'page_callback' => 'CRM_Import_Controller',
                             'weight'  => 410,
                             ),
                       
                       'civicrm/import/activity' =>
                       array( 
                             'query'   => array('reset' => 1),
                             'title'   => ts( 'Activity' ), 
                             'access_arguments'  =>  array( array('import contacts', 'access CiviCRM') ), 
                             'page_type' => CRM_Core_Menu::MENU_ITEM,
                             'page_callback' => 'CRM_Activity_Import_Controller',
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
                             'page_callback' => array( 'CRM_Core_Invoke', 'profile' ),
                             'weight'  => 0,
                             ),
                       );
        return $items;
    }

    static function isArrayTrue( &$values ) {
        foreach ( $values as $name => $value ) {
            if ( ! $value ) {
                return false;
            }
        }
        return true;
    }

    static function fillMenuValues( &$menu, $path ) {
        $fieldsToPropagate = array( 'access_callback',
                                    'access_arguments',
                                    'page_callback',
                                    'page_arguments' );
        $fieldsPresent = array( );
        foreach ( $fieldsToPropagate as $field ) {
            $fieldsPresent[$field] = CRM_Utils_Array::value( $field, $menu[$path] ) ?
                true : false;
        }

        $args = explode( '/', $path );
        while ( ! self::isArrayTrue( $fieldsPresent ) &&
                ! empty( $args ) ) {

            array_pop( $args );
            $parentPath = implode( '/', $args );

            foreach ( $fieldsToPropagate as $field ) {
                if ( ! $fieldsPresent[$field] ) {
                    if ( CRM_Utils_Array::value( $field, $menu[$parentPath] ) ) {
                        $fieldsPresent[$field] = true;
                        $menu[$path][$field] = $menu[$parentPath][$field];
                    }
                }
            }
        }

        if ( self::isArrayTrue( $fieldsPresent ) ) {
            return;
        }

        $messages = array( );
        foreach ( $fieldsToPropagate as $field ) {
            if ( ! $fieldsPresent[$field] ) {
                $messages[] = ts( "Could not find %1 in path tree",
                                  array( 1 => $field ) );
            }
        }
        CRM_Core_Error::fatal( "'$path': " . implode( ', ', $messages ) );
    }

    /**
     * We use this function to
     * 
     * 1. Compute the breadcrumb
     * 2. Compute local tasks value if any
     * 3. Propagate access argument, access callback, page callback to the menu item
     * 4. Build the global navigation block
     * 
     */
    static function build( &$menu ) {
        foreach ( $menu as $path => $menuItems ) {
            self::buildBreadcrumb ( $menu, $path );
            self::fillMenuValues  ( $menu, $path );
            self::fillComponentIds( $menu, $path );

            // add add page_type if not present
            if ( ! isset( $path['page_type'] ) ) {
                $path['page_type'] = 0;
            }

        }
        
        self::buildNavigation( $menu );

        self::buildAdminLinks( $menu );
    }

    static function store( ) {
        // first clean up the db
        $query = 'TRUNCATE civicrm_menu';
        CRM_Core_DAO::executeQuery( $query,
                                    CRM_Core_DAO::$_nullArray );

        $menu =& self::items( );

        self::build( $menu );

        require_once "CRM/Core/DAO/Menu.php";

        foreach ( $menu as $path => $item ) {
            $menu  =& new CRM_Core_DAO_Menu( );
            $menu->domain_id = CRM_Core_Config::domainID( );
            $menu->path      = $path;

            $menu->find( true );
            
            $menu->copyValues( $item );

            foreach ( self::$_serializedElements as $element ) {
                $menu->$element = serialize( $item[$element] );
            }

            $menu->save( );
        }
    }

    static function buildNavigation( &$menu ) {

        $components = array( ts( 'CiviContribute' ) => 1,
                             ts( 'CiviEvent'      ) => 1,
                             ts( 'CiviMember'     ) => 1,
                             ts( 'CiviMail'       ) => 1,
                             ts( 'Import'         ) => 1,
                             ts( 'CiviGrant'      ) => 1,
                             ts( 'Logout'         ) => 1);

        $values = array( );
        foreach ( $menu as $path => $item ) {
            if ( ! CRM_Utils_Array::value( 'page_type', $item ) ) {
                continue;
            }

            if ( $item['page_type'] ==  CRM_Core_Menu::MENU_ITEM ) {
                $query = CRM_Utils_Array::value( 'path_arguments', $item ) 
                    ? str_replace(',', '&', $item['path_arguments']) . '&reset=1' : 'reset=1';
                
                $value = array( );
                $value['url'  ]  = CRM_Utils_System::url( $path, $query );
                $value['title']  = $item['title'];
                $value['path']   = $path;
                $value['access_callback' ] = $item['access_callback' ];
                $value['access_arguments'] = $item['access_arguments'];
                $value['component_id'    ] = $item['component_id'    ];
                
                if ( array_key_exists( $item['title'], $components ) ) {
                    $value['class']  = 'collapsed';
                } else {
                    $value['class']  = 'leaf';
                }
                $value['parent'] = null;
                $value['start']  = $value['end'] = null;
                $value['active'] = '';

                // check if there is a parent
                foreach ( $values as $weight => $v ) {
                    if ( strpos( $path, $v['path'] ) !== false) {
                        $value['parent'] = $weight;

                        // only reset if still a leaf
                        if ( $values[$weight]['class'] == 'leaf' ) {
                            $values[$weight]['class'] = 'collapsed';
                        }
                    }
                }
                
                $values[$item['weight'] . '.' . $item['title']] = $value;
            }
        }

        $menu['navigation'] = array( 'breadcrumb' => $values );
    }

    static function buildAdminLinks( &$menu ) {
        $values = array( );

        foreach ( $menu as $path => $item ) {
            if ( ! CRM_Utils_Array::value( 'adminGroup', $item ) ) {
                continue;
            }

            $query = CRM_Utils_Array::value( 'path_arguments', $item ) 
                ? str_replace(',', '&', $item['path_arguments']) . '&reset=1' : 'reset=1';
            
            $value = array( 'title' => $item['title'],
                            'desc'  => $item['desc'],
                            'id'    => strtr($item['title'], array('('=>'_', ')'=>'', ' '=>'',
                                                                   ','=>'_', '/'=>'_' 
                                                                   )
                                             ),
                            'url'   => CRM_Utils_System::url( $path, $query ), 
                            'icon'  => $item['icon'],
                            'extra' => CRM_Utils_Array::value( 'extra', $item ) );
            if ( ! array_key_exists( $item['adminGroup'], $values ) ) {
                $values[$item['adminGroup']] = array( );
                $values[$item['adminGroup']]['fields'] = array( );
            }
            $values[$item['adminGroup']]['fields'][$item['weight'] . '.' . $item['title']] = $value;
            $values[$item['adminGroup']]['component_id'] = $item['component_id'];
        }

        foreach( $values as $group => $dontCare ) {
            $values[$group]['perColumn'] = round( count( $values[$group]['fields'] ) / 2 );
            ksort( $values[$group] );
        }

        // CRM_Core_Error::debug( 'v', $values );
        $menu['admin'] = array( 'breadcrumb' => $values );
    }

    static function &getNavigation( ) {
        if ( ! self::$_menuCache ) {
            self::get( 'navigation' );
        }

        if ( ! array_key_exists( 'navigation', self::$_menuCache ) ) {
            CRM_Core_Error::fatal( );
        }
        $nav =& self::$_menuCache['navigation'];

        if ( ! $nav ||
             ! isset( $nav['breadcrumb'] ) ) {
            return null;
        }

        $values =& $nav['breadcrumb'];

        $config =& CRM_Core_Config::singleton( );
        foreach ( $values as $index => $item ) {
            if ( strpos( CRM_Utils_Array::value( $config->userFrameworkURLVar, $_REQUEST ),
                         $item['path'] ) === 0 ) {
                $values[$index]['active'] = 'class="active"';
            } else {
                $values[$index]['active'] = '';
            }

            if ( $values[$index]['parent'] ) {
                $parent = $values[$index]['parent'];

                // only reset if still a leaf
                if ( $values[$parent]['class'] == 'leaf' ) {
                    $values[$parent]['class'] = 'collapsed';
                }

                // if a child or the parent is active, expand the menu
                if ( $values[$index ]['active'] ||
                     $values[$parent]['active'] ) {
                    $values[$parent]['class'] = 'expanded';
                }
                    
                // make the parent inactive if the child is active
                if ( $values[$index ]['active'] &&
                     $values[$parent]['active'] ) { 
                    $values[$parent]['active'] = '';
                }
            }
        }

        // remove all collapsed menu items from the array
        foreach ( $values as $weight => $v ) {
            if ( $v['parent'] &&
                 $values[$v['parent']]['class'] == 'collapsed' ) {
                unset( $values[$weight] );
            }
        }

        // check permissions for the rest
        $activeChildren = array( );
        foreach ( $values as $weight => $v ) {
            if ( CRM_Core_Permission::checkMenuItem( $v ) ) {
                if ( $v['parent'] ) {
                    $activeChildren[] = $weight;
                }
            } else {
                unset( $values[$weight] );
            }
        }

        // add the start / end tags
        $len = count($activeChildren) - 1;
        if ( $len >= 0 ) {
            $values[$activeChildren[0   ]]['start'] = true;
            $values[$activeChildren[$len]]['end'  ] = true;
        }

        ksort($values, SORT_NUMERIC );
        // CRM_Core_Error::debug( 'v', $values );
        return $values;
    }

    static function &getAdminLinks( ) {
        $links =& self::get( 'admin' );

        if ( ! $links ||
             ! isset( $links['breadcrumb'] ) ) {
            return null;
        }

        $values =& $links['breadcrumb'];
        return $values;
    }

    /**
     * Get the breadcrumb for a given path.
     *
     * @param  array   $menu   An array of all the menu items.
     * @param  string  $path   Path for which breadcrumb is to be build.
     *
     * @return array  The breadcrumb for this path
     *
     * @static
     * @access public
     */
    static function buildBreadcrumb( &$menu, $path ) {
        $crumbs       = array( );

        $pathElements = explode('/', $path);
        array_pop( $pathElements );
        
        while ( $newPath = array_shift($pathElements) ) {
            $currentPath = $currentPath ? ($currentPath . '/' . $newPath) : $newPath;
            
            // add to crumb, if current-path exists in params.
            if ( array_key_exists($currentPath, $menu) && isset($menu[$currentPath]['title']) ) {
                $crumbs[] = array('title' => $menu[$currentPath]['title'], 
                                  'url'   => CRM_Utils_System::url( $currentPath ));
            }
        }
        $menu[$path]['breadcrumb'] = $crumbs;

        return $crumbs;
    }

    static function fillComponentIds( &$menu, $path ) {
        static $cache = array( );

        if (array_key_exists('component_id', $menu[$path])) {
            return;
        }
        
        $args = explode('/', $path);

        if ( count($args) > 1 ) {
            $compPath  = $args[0] . '/' . $args[1];
        } else {
            $compPath  = $args[0];
        }    
        
        $componentId = null;

        if ( array_key_exists($compPath, $cache) ) {
            $menu[$path]['component_id'] = $cache[$compPath];
        } else {
            if ( $menu[$compPath]['component'] ) {
                $componentId = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_Component', 
                                                            $menu[$compPath]['component'], 
                                                            'id', 'name' );
            }
            $menu[$path]['component_id'] = $componentId ? $componentId : null;
            $cache[$compPath] = $menu[$path]['component_id'];
        }
    }

    static function get( $path )
    {
        // return null if menu rebuild
        $config =& CRM_Core_Config::singleton( );
        if ( strpos( CRM_Utils_Array::value( $config->userFrameworkURLVar, $_REQUEST ),
                     'civicrm/menu/rebuild' ) !== false ) {
            return null;
        }

        $params = array( );

        $args = explode( '/', $path );

        $elements = array( );
        while ( ! empty( $args ) ) {
            $elements[] = "'" . implode( '/', $args ) . "'";
            array_pop( $args );
        }

        $queryString = implode( ', ', $elements );
        
        $query = "
( 
  SELECT * 
  FROM     civicrm_menu 
  WHERE    path in ( $queryString )
  ORDER BY length(path) DESC
  LIMIT    1 
)
";

        if ( substr( $path, 0, 7 ) == 'civicrm' ) {
            $query .= "
UNION ( 
  SELECT *
  FROM   civicrm_menu 
  WHERE   path IN ( 'navigation' )
)
";
        }
        
        require_once "CRM/Core/DAO/Menu.php";
        $menu  =& new CRM_Core_DAO_Menu( );
        $menu->query( $query );

        self::$_menuCache = array( );
        while ( $menu->fetch( ) ) {
            self::$_menuCache[$menu->path] = array( );
            CRM_Core_DAO::storeValues( $menu, self::$_menuCache[$menu->path] );

            foreach ( self::$_serializedElements as $element ) {
                self::$_menuCache[$menu->path][$element] = unserialize( $menu->$element );
                
                if ( strpos( $path, $menu->path ) !== false ) {
                    $menuPath =& self::$_menuCache[$menu->path];
                }
            }
        }
        
        return $menuPath;
    }

    static function getArrayForPathArgs( $pathArgs )
    {
        if (! is_string($pathArgs)) {
            return;
        }
        $args = array();

        $elements = explode( ',', $pathArgs );
        foreach ( $elements as $keyVal ) {
            list($key, $val) = explode( '=', $keyVal );
            $arr[$key] = $val;
        }

        if (array_key_exists('urlToSession', $arr)) {
            $urlToSession = array( );

            $params = explode( ';', $arr['urlToSession'] );
            $count  = 0;
            foreach ( $params as $keyVal ) {
                list($urlToSession[$count]['urlVar'], 
                     $urlToSession[$count]['sessionVar'], 
                     $urlToSession[$count]['type'], 
                     $urlToSession[$count]['default'] ) = explode( ':', $keyVal );
                $count++;
            }
            $arr['urlToSession'] = $urlToSession; 
        }
        return $arr;
    }
}


