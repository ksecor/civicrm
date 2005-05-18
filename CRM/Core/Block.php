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
        SHORTCUTS = 0,
        ADD       = 1,
        SEARCH    = 2,
        MENU      = 4;

    /**
     * template file names for the above blocks
     */
    static $_properties = array(
                                   self::SHORTCUTS   => array( 'template' => 'Shortcuts.tpl',
                                                               'info'     => 'CiviCRM Shortcuts',
                                                               'subject'  => 'CiviCRM Shortcuts',
                                                               'active'   => true ),
                                   self::ADD         => array( 'template' => 'Add.tpl',
                                                               'info'     => 'CiviCRM Quick Add',
                                                               'subject'  => 'New Individual',
                                                               'active'   => true ),
                                   self::SEARCH      => array( 'template' => 'Search.tpl',
                                                               'info'     => 'CiviCRM Search',
                                                               'subject'  => 'Contact Search',
                                                               'active'   => true ),
                                   self::MENU        => array( 'template' => 'Menu.tpl',
                                                               'info'     => 'CiviCRM Menu',
                                                               'subject'  => 'CiviCRM',
                                                               'active'   => true ),
                                   
                                   );

                                                    
    /**
     * class constructor
     *
     */
    function __construct( ) {
    }

    /**
     * Creates the info block for drupal
     *
     * @return array 
     * @access public
     */
    static function getInfo( ) {
        $block = array( );
        foreach ( self::$_properties as $id => $value ) {
            if ( $value['active'] ) {
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
            self::$_properties[self::ADD   ]['templateValues'] =
                array( 'postURL'           => CRM_Utils_System::url( 'civicrm/contact/addI?c_type=Individual' ) );
        } else if ( $id == self::SEARCH ) {
            self::$_properties[self::SEARCH]['templateValues'] =
                array( 'postURL'           => CRM_Utils_System::url( 'civicrm/contact/search' ) ,
                       'advancedSearchURL' => CRM_Utils_System::url( 'civicrm/contact/search/advanced', 'reset=1' ) );
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
        static $shortCuts = array( array( 'path'  => 'civicrm/contact/addI',
                                          'qs'    => 'c_type=Individual&reset=1',
                                          'title' => 'New Individual' ),
                                   array( 'path'  => 'civicrm/contact/addO',
                                          'qs'    => 'c_type=Organization&reset=1',
                                          'title' => 'New Organization' ),
                                   array( 'path'  => 'civicrm/contact/addH',
                                          'qs'    => 'c_type=Household&reset=1',
                                          'title' => 'New Household' ),
                                   array( 'path'  => 'civicrm/group/add',
                                          'qs'    => 'reset=1',
                                          'title' => 'New Group' ) );

        $values = array( );
        foreach ( $shortCuts as &$short ) {
            $value = array( );
            $value['url'  ] = CRM_Utils_System::url( $short['path'], $short['qs'] );
            $value['title'] = $short['title'];
            $values[] = $value;
        }
        self::$_properties[self::SHORTCUTS]['templateValues'] = array( 'shortCuts' => $values );
    }

    /**
     * create the list of shortcuts for the application and format is as a block
     *
     * @return void
     * @access private
     */
    private function setTemplateMenuValues( ) {
        $items = civicrm_menu( true );
        $values = array( );

        foreach ( $items as $item ) {
            if ( $item['type'] == MENU_NORMAL_ITEM ) {
                $value = array( );
                $value['url'  ] = CRM_Utils_System::url( $item['path'], CRM_Utils_Array::value( 'qs', $item ) );
                $value['title'] = $item['title'];
                $value['class'] = 'leaf';
                $values[$item['weight']] = $value;
            }
        }
        ksort($values);
        self::$_properties[self::MENU]['templateValues'] = array( 'menu' => $values );
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
        if ( ! self::$_properties[$id]['active'] ) {
            return null;
        }

        $block['subject'] = self::fetch( $id, 'Subject.tpl',
                                         array( 'subject' => self::$_properties[$id]['subject'] ) );
        $block['content'] = self::fetch( $id, self::$_properties[$id]['template'],
                                         self::$_properties[$id]['templateValues'] );

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
        $template = CRM_Core_Smarty::singleton( );

        if ( $properties ) {
            $template->assign( $properties );
        }

        return $template->fetch( "CRM/Block/" . $fileName );
    }

}

?>