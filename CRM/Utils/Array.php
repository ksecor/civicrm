<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.3                                                |
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
 * Simple wrapper class to add a few essential core functions to
 * arrays in PHP
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

class CRM_Utils_Array {

    /**
     * if the key exists in the list returns the associated value
     *
     * @access public
     *
     * @param array  $list  the array to be searched
     * @param string $key   the key value
     * 
     * @return value if exists else null
     * @static
     * @access public
     *
     */
    static function value( $key, &$list, $default = null ) {
        if ( is_array( $list ) ) {
            return array_key_exists( $key, $list ) ? $list[$key] : $default;
        }
        return $default;
    }

    /**
     * if the value exists in the list returns the associated key
     *
     * @access public
     *
     * @param list  the array to be searched
     * @param value the search value
     * 
     * @return key if exists else null
     * @static
     * @access public
     *
     */
    static function key( $value, &$list ) {
        if ( is_array( $list ) ) {
            $key = array_search( $value, $list );
            return $key ? $key : null;
        }
        return null;
    }

}

?>