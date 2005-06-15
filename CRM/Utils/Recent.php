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
class CRM_Utils_Recent {
    
    /**
     * max number of items in queue
     *
     * @int
     */
    const
        MAX_ITEMS  = 5,
        STORE_NAME = 'CRM_Utils_Recent';

    /**
     * The list of recently viewed items
     *
     * @var array
     * @static
     */
    static private $_recent = null;

    /**
     * initialize this class and set the static variables
     *
     * @return void
     * @access public
     * @static
     */
    static function initialize( ) {
        if ( ! self::$_recent ) {
            $session =& CRM_Core_Session::singleton( );
            self::$_recent = $session->get( self::STORE_NAME );
            if ( ! self::$_recent ) {
                self::$_recent = array( );
            }
            
            $template =& CRM_Core_Smarty::singleton( );
            $template->assign_by_ref( 'recentlyViewed', self::$_recent );
        }
    }

    /**
     * add an item to the recent stack
     *
     * @param string $title  the title to display
     * @param string $url    the link for the above title
     * @param string $icon   a link to a graphical image
     *
     * @return void
     * @access public
     * @static
     */
    static function add( $title, $url, $icon = null ) {
        self::initialize( );

        // make sure item is not already present in list
        for ( $i = 0; $i < count( self::$_recent ); $i++ ) {
            if ( self::$_recent[$i]['title'] == $title ||
                 self::$_recent[$i]['url'  ] == $url ) {
                return;
            }
        }
        
        self::$_recent[] = array( 'title' => $title, 
                                  'url'   => $url  ,
                                  'icon'  => $icon );
        if ( count( self::$_recent ) > self::MAX_ITEMS ) {
            array_shift( self::$_recent );
        }

        $session =& CRM_Core_Session::singleton( );
        $session->set( self::STORE_NAME, self::$_recent );
        CRM_Core_Error::debug( 'recent', self::$_recent );
    }

}

?>
