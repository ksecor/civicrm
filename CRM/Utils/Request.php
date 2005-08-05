<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.1                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Social Source Foundation                        |
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
     * get the variable information from the request (GET/POST/SESSION
     *
     * @param $name    name of the variable to be retrieved
     * @param $store   session scope where variable is stored
     * @param $abort   is this variable required
     * @param $default default value of the variable if not present
     * @param $method  where should we look for the variable
     *
     * @return string  the value of the variable
     * @access public
     * @static
     *
     */
    static function retrieve( $name, &$store, $abort = false, $default = null, $method = 'GET' ) {
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
        
        if ( isset( $value ) && $store ) {
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