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

class CRM_Contact_HideShowLinks {
    protected $_hide;

    protected $_show;

    protected $_hideString;
    protected $_showString;

    static $_commMethods = array( 'phone', 'email', 'im' );
    static $_hideShow    = array( 'hide' , 'show' );

    function __construct( ) {
        $this->_show = array(
                             'name'              => 1,
                             'commPrefs'         => 1,
                             'location[1]'       => 1,
                             'location[2][show]' => 1,
                             );
        $this->_hide = array(
                             'notes'            => 1,
                             'demographics'     => 1,
                             );
        foreach ( self::$_commMethods as $item ) {
            $this->_show["location[1][$item][2][show]"] = 1; 
        }

        for ( $i = 1; $i < 4; $i++ ) {
            $this->addHide( "location[$i]" );

            $this->addHide( "location[$i][show]" );
            foreach ( self::$_commMethods as $item ) {
                for ($j = 2; $j < 4; $j++) {
                    $this->addHide( "location[$i][$item][$j]" );
                    $this->addHide( "location[$i][$item][$j][show]" );
                }
            }
        }

        $this->join( );
        $template = SmartyTemplate::singleton($config->templateDir, $config->templateCompileDir);
        $template->assign_by_ref( 'hideBlocks', $this->_hideString );
        $template->assign_by_ref( 'showBlocks', $this->_showString );
    }

    function join( ) {
        $this->_hideString = '';

        $first = true;
        foreach ( array_keys( $this->_hide ) as $h ) {
            if ( ! $first ) {
                $this->_hideString .= ',';
            }
            $this->_hideString .= "'$h'";
            $first = false;
        }

        $first = true;
        foreach ( array_keys( $this->_show ) as $s ) {
            if ( ! $first ) {
                $this->_showString .= ',';
            }
            $this->_showString .= "'$s'";
            $first = false;
        }
    }

    function addHide( $name ) {
        if ( ! array_key_exists( $name, $this->_show ) ) {
            $this->_hide[$name] = 1;
        }
    }

}

?>