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

require_once 'CRM/Core/Page.php';

class CRM_Admin_Page_View extends CRM_Page {

    /**
     * constants for various modes that the page can operate as
     *
     * @var const int
     */
    const
        MODE_NONE                  =   0,
        MODE_LTYPE                 =   1,
        MODE_IM_PROVIDER           =   2,
        MODE_MOB_PROVIDER          =   4;

    /**
     * class constructor
     *
     * @param string $name  name of the page
     * @param string $title title of the page
     * @param int    $mode  mode of the page
     *
     * @return CRM_Page
     */
    function __construct( $name, $title = null, $mode = null ) {
        parent::__construct($name, $title, $mode);
    }

    function run( ) {

        if ( $this->_mode == self::MODE_LTYPE ) {
            CRM_Admin_Page_LocationType::run( $this );
        } else if ( $this->_mode == self::MODE_IM_PROVIDER ) {
            CRM_Admin_Page_IMProvider::run( $this );
        } else if ( $this->_mode == self::MODE_MOB_PROVIDER ) {
            CRM_Admin_Page_MobileProvider::run( $this );
        } 

        return parent::run( );
    }
    
}

?>