<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.4                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                  |
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
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
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
        MENU       =  1,
        SHORTCUTS  =  2,
        SEARCH     =  4,
        ADD        =  8;

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
        if (!(self::$_properties)) {
            self::$_properties = array(
                                       self::SHORTCUTS   => array( 'template' => 'Shortcuts.tpl',
                                                                   'info'     => ts('CiviCRM Shortcuts'),
                                                                   'subject'  => ts('CiviCRM Shortcuts'),
                                                                   'active'   => true ),
                                       self::ADD         => array( 'template' => 'Add.tpl',
                                                                   'info'     => ts('CiviCRM Quick Add'),
                                                                   'subject'  => ts('New Individual'),
                                                                   'active'   => true ),
                                       self::SEARCH      => array( 'template' => 'Search.tpl',
                                                                   'info'     => ts('CiviCRM Search'),
                                                                   'subject'  => ts('Contact Search'),
                                                                   'active'   => true ),
                                       self::MENU        => array( 'template' => 'Menu.tpl',
                                                                   'info'     => ts('CiviCRM Menu'),
                                                                   'subject'  => ts('CiviCRM'),
                                                                   'active'   => true )
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
        $block = array( );
        foreach ( self::properties() as $id => $value ) {
             if ( $value['active'] ) {
                 if ( ( $id == self::ADD || $id == self::SHORTCUTS ) &&
                      ( ! CRM_Core_Permission::check('add contacts') ) && ( ! CRM_Core_Permission::check('edit groups') ) ) {
                     continue;
                 }
                $block[$id]['info'] = $value['info'];
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
        if ( $id == self::SHORTCUTS ) {
            self::setTemplateShortcutValues( );
        } else if ( $id == self::ADD ) {
            require_once 'CRM/Core/BAO/LocationType.php';
            $defaultLocation = CRM_Core_BAO_LocationType::getDefault( );
            $values = array( 'postURL' => CRM_Utils_System::url( 'civicrm/contact/add', 'reset=1&amp;ct=Individual' ), 
                             'primaryLocationType' => $defaultLocation->id ); 
            self::setProperty( self::ADD,
                               'templateValues',
                               $values );
        } else if ( $id == self::SEARCH ) {
            $urlArray = array(
                'postURL'           => CRM_Utils_System::url( 'civicrm/contact/search/basic',
                                                              'reset=1' ) ,
                'advancedSearchURL' => CRM_Utils_System::url( 'civicrm/contact/search/advanced',
                                                              'reset=1' )
            );
            self::setProperty( self::SEARCH, 'templateValues', $urlArray );
        } else if ( $id == self::MENU ) {
            self::setTemplateMenuValues( );
        }
    }

    /**
     * create the list of shortcuts for the application and format is as a block
     *
     * @return void
     * @access private
     */
    private function setTemplateShortcutValues( ) {
        static $shortCuts = array( );
        
        if (!($shortCuts)) {
            if (CRM_Core_Permission::check('add contacts')) {
                $shortCuts = array( array( 'path'  => 'civicrm/contact/add',
                                           'qs'    => 'ct=Individual&reset=1',
                                           'title' => ts('New Individual') ),
                                    array( 'path'  => 'civicrm/contact/add',
                                           'qs'    => 'ct=Organization&reset=1',
                                           'title' => ts('New Organization') ),
                                    array( 'path'  => 'civicrm/contact/add',
                                           'qs'    => 'ct=Household&reset=1',
                                           'title' => ts('New Household') ),
                                    );
            }

            if( CRM_Core_Permission::check('edit groups')) {
                $shortCuts = array_merge($shortCuts, array( array( 'path'  => 'civicrm/group/add',
                                                                   'qs'    => 'reset=1',
                                                                   'title' => ts('New Group') ) ));
            }

            if (! CRM_Core_Permission::check( 'add contacts' )  &&  ! CRM_Core_Permission::check('edit groups')) {
                return null;
            }

        }

        $values = array( );

        foreach ( $shortCuts as $short ) {
            $value = array( );
            $value['url'  ] = CRM_Utils_System::url( $short['path'], $short['qs'] );
            $value['title'] = $short['title'];
            $values[] = $value;
        }
        self::setProperty( self::SHORTCUTS, 'templateValues', array( 'shortCuts' => $values ) );
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
                                        'qs'    => 'reset=1',
                                        'title' => ts('Send Mailing') ),
                                 array( 'path'  => 'civicrm/mailing/browse',
                                        'qs'    => 'reset=1',
                                        'title' => ts('Browse Sent Mailings') ),
                                 );
        }

        $values = array( );
        foreach ( $shortCuts as $short ) {
            $value = array( );
            $value['url'  ] = CRM_Utils_System::url( $short['path'], $short['qs'] );
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
        $items  =& CRM_Core_Menu::items( );
        $values =  array( );

        foreach ( $items as $item ) {
            if ( ! CRM_Utils_Array::value( 'crmType', $item ) ) {
                continue;
            }

            if ( ( $item['crmType'] &  CRM_Core_Menu::NORMAL_ITEM ) &&
                 ( $item['crmType'] >= CRM_Core_Menu::NORMAL_ITEM ) &&
                 $item['access'] ) {
                $value = array( );
                $value['url'  ]  = CRM_Utils_System::url( $item['path'], CRM_Utils_Array::value( 'qs', $item ) );
                $value['title']  = $item['title'];
                $value['path']   = $item['path'];
                $value['class']  = 'leaf';
                $value['parent'] = null;
                $value['start']  = $value['end'] = null;

                if ( strpos( CRM_Utils_Array::value( $config->userFrameworkURLVar, $_REQUEST ), $item['path'] ) === 0 ) {
                    $value['active'] = 'class="active"';
                } else {
                    $value['active'] = '';
                }
                
                // check if there is a parent
                foreach ( $values as $weight => $v ) {
                    if ( strpos( $item['path'], $v['path'] ) !== false) {
                        $value['parent'] = $weight;

                        // only reset if still a leaf
                        if ( $values[$weight]['class'] == 'leaf' ) {
                            $values[$weight]['class'] = 'collapsed';
                        }

                        // if a child or the parent is active, expand the menu
                        if ( $value['active'] || $values[$weight]['active'] ) {
                            $values[$weight]['class'] = 'expanded';
                        }

                        // make the parent inactive if the child is active
                        if ( $value['active'] && $values[$weight]['active'] ) { 
                            $values[$weight]['active'] = '';
                        }

                    }
                }
                
                $values[$item['weight'] . '.' . $item['title']] = $value;
            }
        }

        // remove all collapsed menu items from the array
        $activeChildren = array( );
        foreach ( $values as $weight => $v ) {
            if ( $v['parent'] ) {
                if ( $values[$v['parent']]['class'] == 'collapsed' ) {
                    unset( $values[$weight] );
                } else {
                    $activeChildren[] = $weight;
                }
            }
        }
        
        // add the start / end tags
        $len = count($activeChildren) - 1;
        if ( $len >= 0 ) {
            $values[$activeChildren[0   ]]['start'] = true;
            $values[$activeChildren[$len]]['end'  ] = true;
        }

        ksort($values);

        self::setProperty( self::MENU, 'templateValues', array( 'menu' => $values ) );
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
        self::setTemplateValues( $id );
        $block = array( );
        if ( ! self::getProperty( $id, 'active' ) ) {
            return null;
        }

         if ( ( $id == self::ADD || $id == self::SHORTCUTS ) &&
              ( ! CRM_Core_Permission::check( 'add contacts' ) ) && ( ! CRM_Core_Permission::check('edit groups') ) ) {
             return null;
         }

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

?>
