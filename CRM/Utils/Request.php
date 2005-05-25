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
 * class for managing a http request
 *
 */
class CRM_Utils_Request {
    /**
     * We only need one instance of this object. So we use the singleton
     * pattern and cache the instance in this variable
     *
     * @var object
     * @access private
     * @static
     */
    static private $_singleton = null;

    /**
     * class constructor
     */
    function __construct() {
    }

    /**
     * get the variable information from the request.
     *
     */
    static function retrieve( $name, $store = null, $abort = false, $default = null, $method = 'GET' ) {
        $value = null;
        switch ( $method ) {
        case 'GET':
            $value = CRM_Utils_Array::value( $name, $_GET );
            break;

        case 'POST':
            $value = CRM_Utils_Array::value( $name, $_POST );
            break;
            
        default:
            $value = CRM_Utils_Array::value( $name, $_REQUEST );
            break;
        }

        if ( ! isset( $value ) && $store ) {
            $value = $store->get( $name );
        }

        if ( ! isset( $value ) && $abort ) {
            CRM_Core_Error::fatal( "Could not find valid value for $name" );
        }

        if ( ! isset( $value ) && $default ) {
            $value = $default;
        }

        if ( $value && $store ) {
            // minor hack for action
            if ( $name == 'action' && is_string( $value ) ) {
                $value = CRM_Core_Action::resolve( $value );
            }
            $store->set( $name, $value );
        }

        return $value;
    }

}

?>