<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
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
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

require_once 'CRM/Core/Menu.php';

/**
 * defines a simple implemenation of a drupal block.
 * blocks definitions and html are in a smarty template file
 *
 */
class CRM_Core_Block {

    /**
     * the following blocks are supported
     *
     * @var int
     */
    const
        SHORTCUTS       = 1,
        RECENTLY_VIEWED = 2,
        DASHBOARD       = 3,
        ADD             = 4,
        LANGSWITCH      = 5,
        EVENT           = 6,
        FULLTEXT_SEARCH = 7;
    
    /**
     * template file names for the above blocks
     */
    static $_properties = null;

    /**
     * class constructor
     *
     */
    function __construct( ) {
    }

    /**
     * initialises the $_properties array
     * @return void
     */
    static function initProperties()
    {
        if ( ! defined( 'BLOCK_CACHE_GLOBAL' ) ) {
            define('BLOCK_CACHE_GLOBAL', 0x0008);
        }

        if ( ! defined( 'BLOCK_CACHE_PER_PAGE' ) ) {
            define('BLOCK_CACHE_PER_PAGE', 0x0004);
        }

        if (!(self::$_properties)) {
            self::$_properties = array(
                                       self::SHORTCUTS   => array( 'template'   => 'Shortcuts.tpl',
                                                                   'info'       => ts('CiviCRM Create New'),
                                                                   'subject'    => ts('Create New'),
                                                                   'active'     => true,
                                                                   'cache'      => BLOCK_CACHE_GLOBAL,
                                                                   'visibility' => 1,
                                                                   'pages'      => 'civicrm*',
                                                                   'region'     => 'left' ),
                                       self::ADD         => array( 'template'   => 'Add.tpl',
                                                                   'info'       => ts('CiviCRM Quick Add'),
                                                                   'subject'    => ts('New Individual'),
                                                                   'active'     => true,
                                                                   'cache'      => BLOCK_CACHE_GLOBAL,
                                                                   'visibility' => 1,
                                                                   'pages'      => 'civicrm*',
                                                                   'region'     => 'left' ),
                                       self::LANGSWITCH  => array( 'template'   => 'LangSwitch.tpl',
                                                                   'info'       => ts('CiviCRM Language Switcher'),
                                                                   'subject'    => '',
                                                                   'templateValues' => array(),
                                                                   'active'     => true,
                                                                   'cache'      => BLOCK_CACHE_GLOBAL,
                                                                   'visibility' => 1,
                                                                   'pages'      => 'civicrm*',
                                                                   'region'     => 'left' ),
                                       self::EVENT      => array( 'template'   => 'Event.tpl',
                                                                   'info'       => ts('CiviCRM Upcoming Events'),
                                                                   'subject'    => ts('Upcoming Events'),
                                                                   'templateValues' => array(),
                                                                   'active'     => true,
                                                                   'cache'      => BLOCK_CACHE_GLOBAL,
                                                                   'visibility' => 1,
                                                                   'pages'      => 'civicrm*',
                                                                   'region'     => 'left' ),
                                       self::FULLTEXT_SEARCH => array(  'template'   => 'FullTextSearch.tpl',
                                                                        'info'       => ts('CiviCRM Full-text Search'),
                                                                        'subject'    => ts('Full-text Search'),
                                                                        'active'     => true,
                                                                        'cache'      => BLOCK_CACHE_GLOBAL,
                                                                        'visibility' => 1,
                                                                        'pages'      => 'civicrm*',
                                                                        'region'     => 'left' ),
                                       self::RECENTLY_VIEWED => array(  'template'   => 'RecentlyViewed.tpl',
                                                                        'info'       => ts('CiviCRM Recently Viewed'),
                                                                        'subject'    => ts('Recently Viewed'),
                                                                        'active'     => true,
                                                                        'cache'      => BLOCK_CACHE_GLOBAL,
                                                                        'visibility' => 1,
                                                                        'pages'      => 'civicrm*',
                                                                        'region'     => 'left' ),
                                       self::DASHBOARD   => array( 'template'   => 'Dashboard.tpl',
                                                                   'info'       => ts('CiviCRM Dashboard'),
                                                                   //'subject'    => ts('CiviCRM Shortcuts'),
                                                                   'active'     => true,
                                                                   'cache'      => BLOCK_CACHE_GLOBAL,
                                                                   'visibility' => 1,
                                                                   'pages'      => 'civicrm*',
                                                                   'region'     => 'left' ),
                                       );

        }
    }

    /**
     * returns the desired property from the $_properties array
     *
     * @params int    $id        one of the class constants (ADD, SEARCH, etc.)
     * @params string $property  the desired property
     *
     * @return string  the value of the desired property
     */
    static function getProperty($id, $property)
    {
        if (!(self::$_properties)) {
            self::initProperties();
        }
        return self::$_properties[$id][$property];
    }

    /**
     * sets the desired property in the $_properties array
     *
     * @params int    $id        one of the class constants (ADD, SEARCH, etc.)
     * @params string $property  the desired property
     * @params string $value     the value of the desired property
     *
     * @return void
     */
    static function setProperty($id, $property, $value)
    {
        if (!(self::$_properties)) {
            self::initProperties();
        }
        self::$_properties[$id][$property] = $value;
    }

    /**
     * returns the whole $_properties array
     * @return array  the $_properties array
     */
    static function properties()
    {
        if (!(self::$_properties)) {
            self::initProperties();
        }
        return self::$_properties;
    }

    /**
     * Creates the info block for drupal
     *
     * @return array 
     * @access public
     */
    static function getInfo( ) {
        require_once 'CRM/Core/Permission.php';

        $block = array( );
        foreach ( self::properties() as $id => $value ) {
             if ( $value['active'] ) {
                 if ( ( $id == self::ADD || $id == self::SHORTCUTS ) &&
                      ( ! CRM_Core_Permission::check('add contacts') ) &&
                      ( ! CRM_Core_Permission::check('edit groups') ) ) {
                     continue;
                 }

                 if ( $id == self::EVENT &&
                      ( ! CRM_Core_Permission::access( 'CiviEvent', false ) ||
                        ! CRM_Core_Permission::check( 'view event info' ) ) ) {
                     continue;
                 }

                 $block[$id] = array(
                                     'info'       => $value['info']      ,
                                     'cache'      => $value['cache']     ,
                                     'status'     => $value['active']    ,
                                     'region'     => $value['region']    ,
                                     'visibility' => $value['visibility'],
                                     'pages'      => $value['pages']     ,
                                     );
            }
        }
        return $block;
    }

    /**
     * set the post action values for the block.
     *
     * php is lame and u cannot call functions from static initializers
     * hence this hack
     *
     * @return void
     * @access private
     */
    private function setTemplateValues( $id ) {
        switch ( $id ) {

        case self::SHORTCUTS:
            self::setTemplateShortcutValues( );
            break;

        case self::DASHBOARD:
            self::setTemplateDashboardValues( );
            break;    

        case self::ADD:
            require_once "CRM/Core/BAO/LocationType.php";
            $defaultLocation =& CRM_Core_BAO_LocationType::getDefault();
            $defaultPrimaryLocationId = $defaultLocation->id;
            
            $values = array( 'postURL' => CRM_Utils_System::url( 'civicrm/contact/add', 'reset=1&ct=Individual' ), 
                             'primaryLocationType' => $defaultPrimaryLocationId );
            self::setProperty( self::ADD,
                               'templateValues',
                               $values );
            break;

        case self::FULLTEXT_SEARCH:
            $urlArray = array( 'fullTextSearchID'  => CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_OptionValue',
                                                    'CRM_Contact_Form_Search_Custom_FullText', 'value', 'name' ) );
            self::setProperty( self::FULLTEXT_SEARCH, 'templateValues', $urlArray );
            break;    

        case self::RECENTLY_VIEWED:
            require_once 'CRM/Utils/Recent.php';
            $recent  =& CRM_Utils_Recent::get( );
            self::setProperty( self::RECENTLY_VIEWED, 'templateValues', array( 'recentlyViewed' => $recent ) );
            break;

        case self::EVENT:
            self::setTemplateEventValues( );
            break;

        }
    }

    /**
     * create the list of shortcuts for the application and format is as a block
     *
     * @return void
     * @access private
     */
    private function setTemplateShortcutValues( ) {
        $config =& CRM_Core_Config::singleton( );

        static $shortCuts = array( );
        
        if (!($shortCuts)) {
            if (CRM_Core_Permission::check('add contacts')) {
                $shortCuts = array( array( 'path'  => 'civicrm/contact/add',
                                           'query' => 'ct=Individual&reset=1',
                                           'key'   => 'I',
                                           'title' => ts('Individual') ),
                                    array( 'path'  => 'civicrm/contact/add',
                                           'query' => 'ct=Organization&reset=1',
                                           'key'   => 'O',
                                           'title' => ts('Organization') ),
                                    array( 'path'  => 'civicrm/contact/add',
                                           'query' => 'ct=Household&reset=1',
                                           'key'   => 'H',
                                           'title' => ts('Household') ),
                                    );
                if ( CRM_Core_Permission::access( 'Quest' ) ) {
                    $shortCuts = array_merge($shortCuts, array( array( 'path'  => 'civicrm/quest/search',
                                                                      'query' => 'reset=1',
                                                                      'title' => ts('Quest Search') ))); 
                }
     
            }

            // add new activity creation link
            $shortCuts = array_merge($shortCuts, array( array( 'path'  => 'civicrm/activity',
                                                              'query' => 'action=add&reset=1&context=standalone',
                                                              'key'   => 'A',
                                                              'title' => ts('Activity') ) ));
            
            
            if ( CRM_Core_Permission::check('edit groups')) {
                $shortCuts = array_merge($shortCuts, array( array( 'path'  => 'civicrm/group/add',
                                                                   'query' => 'reset=1',
                                                                   'key'   => 'G',
                                                                   'title' => ts('Group') ) ));
            }

            if ( CRM_Core_Permission::check('access CiviCase') && 
                 in_array( 'CiviCase', $config->enableComponents ) ) {
                require_once 'CRM/Core/OptionGroup.php';
                $atype = CRM_Core_OptionGroup::getValue('activity_type', 
                                                        'Open Case', 
                                                        'name' );
                if ( $atype ) {
                    $shortCuts = 
                        array_merge($shortCuts, array( array( 'path'  => 'civicrm/contact/view/case',
                                                              'query' => "reset=1&action=add&atype=$atype",
                                                              'title' => ts('Case for New Client') ) ));
                }
            }
            
            if ( CRM_Core_Permission::check('access CiviContribute') ) {
                $shortCuts = 
                    array_merge($shortCuts, array( array( 'path'  => 'civicrm/contact/view/contribution',
                                                          'query' => "reset=1&action=add&context=standalone",
                                                          'title' => ts('Contribution') ) ));
            
            }

            if ( empty( $shortCuts ) ) {
                return null;
            }
        }

        $values = array( );
        foreach ( $shortCuts as $short ) {
            $value = array( );
            if ( isset( $short['url'] ) ) {
                $value['url'] = $short['url'];
            } else {
                $value['url'] = CRM_Utils_System::url( $short['path'], $short['query'], false );
            }
            $value['title'] = $short['title'];
            $value['key'] = CRM_Utils_Array::value( 'key', $short );
            $values[] = $value;
        }
        self::setProperty( self::SHORTCUTS, 'templateValues', array( 'shortCuts' => $values ) );
    }

    /**
     * create the list of dashboard links
     *
     * @return void
     * @access private
     */
    private function setTemplateDashboardValues( ) {
        static $dashboardLinks = array( );
        require_once 'CRM/Core/Permission.php';
        if ( CRM_Core_Permission::check('access Contact Dashboard')) {
            $dashboardLinks = array( array( 'path'  => 'civicrm/user',
                                            'query' => 'reset=1',
                                            'title' => ts('My Contact Dashboard') ) );
        }

        if ( empty( $dashboardLinks ) ) {
            return null;
        }

        $values = array( );
        foreach ( $dashboardLinks as $dash ) {
            $value = array( );
            if ( isset( $dash['url'] ) ) {
                $value['url'] = $dash['url'];
            } else {
                $value['url'] = CRM_Utils_System::url( $dash['path'], $dash['query'], false );
            }
            $value['title'] = $dash['title'];
            $value['key'] = CRM_Utils_Array::value( 'key', $dash );
            $values[] = $value;
        }
        self::setProperty( self::DASHBOARD, 'templateValues', array( 'dashboardLinks' => $values ) );
    }

    /**
     * create the list of mail urls for the application and format is as a block
     *
     * @return void
     * @access private
     */
    private function setTemplateMailValues( ) {
        static $shortCuts = null;
        
        if (!($shortCuts)) {
             $shortCuts = array( array( 'path'  => 'civicrm/mailing/send',
                                        'query' => 'reset=1',
                                        'title' => ts('Send Mailing') ),
                                 array( 'path'  => 'civicrm/mailing/browse',
                                        'query' => 'reset=1',
                                        'title' => ts('Browse Sent Mailings') ),
                                 );
        }

        $values = array( );
        foreach ( $shortCuts as $short ) {
            $value = array( );
            $value['url'  ] = CRM_Utils_System::url( $short['path'], $short['query'] );
            $value['title'] = $short['title'];
            $values[] = $value;
        }
        self::setProperty( self::MAIL, 'templateValues', array( 'shortCuts' => $values ) );
    }

    /**
     * create the list of shortcuts for the application and format is as a block
     *
     * @return void
     * @access private
     */
    private function setTemplateMenuValues( ) {
        $config =& CRM_Core_Config::singleton( );

        $path = 'navigation';
        $values =& CRM_Core_Menu::getNavigation( );
        if ( $values ) {
            self::setProperty( self::MENU, 'templateValues', array( 'menu' => $values ) );
        }
    }

    /**
     * create the event blocks for upcoming events
     *
     * @return void
     * @access private
     */
    private function setTemplateEventValues( ) {
        $config =& CRM_Core_Config::singleton( );
        
        require_once 'CRM/Event/BAO/Event.php';
        $info = CRM_Event_BAO_Event::getCompleteInfo( );

        if ( $info ) {
            $session =& CRM_Core_Session::singleton( );
            // check if registration link should be displayed
            foreach ( $info as $id => $event ) {
                $info[$id]['onlineRegistration'] = CRM_Event_BAO_Event::validRegistrationDate( $event,
                                                                                               $session->get( 'userID' ) );
            }

            self::setProperty( self::EVENT, 'templateValues', array( 'event' => $info ) );
        }

    }

    /**
     * Given an id creates a subject/content array
     *
     * @param int $id id of the block
     *
     * @return array
     * @access public
     */
    static function getContent( $id ) {
        // return if upgrade mode
        $config =& CRM_Core_Config::singleton( );
        if ( CRM_Utils_Array::value( $config->userFrameworkURLVar, $_GET ) == 'civicrm/upgrade' ) {
            return;
        }

        if ( ! self::getProperty( $id, 'active' ) ) {
            return null;
        }

        require_once 'CRM/Core/Permission.php';
        if ( $id == self::EVENT &&
             CRM_Core_Permission::check( 'view event info' ) ) {
            // is CiviEvent enabled?
            if ( ! CRM_Core_Permission::access( 'CiviEvent', false ) ) {
                return null;
            }
            // do nothing
        } else if ( ! CRM_Core_Permission::check( 'access CiviCRM' ) ) {
            return null;
        } else if ( ( $id == self::ADD  ) &&
                    ( ! CRM_Core_Permission::check( 'add contacts' ) ) &&
                    ( ! CRM_Core_Permission::check('edit groups') ) ) {
            return null;
        }


        self::setTemplateValues( $id );

        $block = array( );
        $block['name'   ] = 'block-civicrm';
        $block['id'     ] = $block['name'] . '_' . $id;
        $block['subject'] = self::fetch( $id, 'Subject.tpl',
                                         array( 'subject' => self::getProperty( $id, 'subject' ) ) );
        $block['content'] = self::fetch( $id, self::getProperty( $id, 'template' ),
                                         self::getProperty( $id, 'templateValues' ) );
        
        return $block;
    }

    /**
     * Given an id and a template, fetch the contents
     *
     * @param int    $id         id of the block
     * @param string $fileName   name of the template file
     * @param array  $properties template variables
     *
     * @return array
     * @access public
     */
    static function fetch( $id, $fileName, $properties ) {
        $template =& CRM_Core_Smarty::singleton( );

        if ( $properties ) {
            $template->assign( $properties );
        }

        return $template->fetch( 'CRM/Block/' . $fileName );
    }

}


