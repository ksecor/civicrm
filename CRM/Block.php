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
 * defines a simple implemenation of a block using smarty
 */
class CRM_Block {

    /**
     * the following blocks are supported
     *
     * @var int
     */
    const
        MENU     = 0,
        ADD      = 1,
        SEARCH   = 2;

    /**
     * template file names for the above blocks
     */
    static $_properties = array(
                                self::MENU   => array( 'template' => 'Menu.tpl',
                                                       'info'     => 'CRM Shortcuts',
                                                       'subject'  => 'CRM Shortcuts',
                                                       'active'   => true ),
                                self::ADD    => array( 'template' => 'Add.tpl',
                                                       'info'     => 'CRM Quick Add Contact Block',
                                                       'subject'  => 'CRM Quick Add Contact Block',
                                                       'active'   => true ),
                                self::SEARCH => array( 'template' => 'Search.tpl',
                                                       'info'     => 'CRM Contact Search',
                                                       'subject'  => 'CRM Contact Search',
                                                       'active'   => true  ),
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
     * Given an id creates a subject/content array
     *
     * @param int $id id of the block
     *
     * @return array
     * @access public
     */
    static function getContent( $id ) {
        $block = array( );
        if ( ! self::$_properties[$id]['active'] ) {
            return null;
        }

        $block['subject'] = self::fetch( $id, 'Subject.tpl', array( 'subject' => self::$_properties[$id]['subject'] ) );
        $block['content'] = self::fetch( $id, self::$_properties[$id]['template'] );

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
        $config  = CRM_Config::singleton ();
        $session = CRM_Session::singleton();

        $template = SmartyTemplate::singleton($config->templateDir, $config->templateCompileDir);
        $template->assign_by_ref( 'config' , $config  );
        $template->assign_by_ref( 'session', $session );

        if ( $properties ) {
            $template->assign( $properties );
        }

        return $template->fetch( "CRM/Block/" . $fileName, $config->templateDir );
    }

}

?>