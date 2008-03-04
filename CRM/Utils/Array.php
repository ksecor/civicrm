<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 * @copyright CiviCRM LLC (c) 2004-2007
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
            
            // array_search returns key if found, false otherwise 
            // it may return values like 0 or empty string which
            // evaluates to false
            // hence we must use identical comparison operator
            return ($key === false) ? null : $key;
        }
        return null;
    }

    static function &xml( &$list, $depth = 1, $seperator = "\n" ) {
        $xml = '';
        foreach( $list as $name => $value ) {
            $xml .= str_repeat( ' ', $depth * 4 );
            if ( is_array( $value ) ) {
                $xml .= "<{$name}>{$seperator}";
                $xml .= self::xml( $value, $depth + 1, $seperator );
                $xml .= str_repeat( ' ', $depth * 4 );
                $xml .= "</{$name}>{$seperator}";
            } else {
                // make sure we escape value
                $value = self::escapeXML( $value );
                $xml .= "<{$name}>$value</{$name}>{$seperator}";
            }
        }
        return $xml;
    }

    static function escapeXML( $value ) {
        static $src = null;
        static $dst = null;

        if ( ! $src ) {
            $src = array( '&'    , '<'   , '>'   , '' );
            $dst = array( '&amp;', '&lt;', '&gt;', ','  );
        }

        return str_replace( $src, $dst, $value );
    }

    static function &flatten( &$list, &$flat, $prefix = '', $seperator = "." ) {
        foreach( $list as $name => $value ) {
            $newPrefix = ( $prefix ) ? $prefix . $seperator . $name : $name;
            if ( is_array( $value ) ) {
                self::flatten( $value, $flat, $newPrefix, $seperator );
            } else {
                if ( ! empty( $value ) ) {
                    $flat[$newPrefix] = $value;
                }
            }
        }
    }

    /**
     * Funtion to merge to two arrays recursively
     * 
     * @param array $a1 
     * @param array $a2
     *
     * @return  $a3
     * @static
     */
    static function crmArrayMerge( $a1, $a2 ) 
    {
        if ( empty($a1) ) {
            return $a2;
        }

        if ( empty( $a2 ) ) {
            return $a1;
        }

        $a3 = array( );
        foreach ( $a1 as $key => $value) {
            if ( array_key_exists($key, $a2) ) {
                $a3[$key] = array_merge($a1[$key], $a2[$key]);
            } else {
                $a3[$key] = $a1[$key];
            }
        }
       
        foreach ( $a2 as $key => $value) {
            if ( array_key_exists($key, $a1) ) {
                // already handled in above loop
                continue;
            }
            $a3[$key] = $a2[$key];
        }

        foreach ( $a2 as $key => $value) {
            if ( array_key_exists($key, $a1) ) {
                // already handled in above loop
                continue;
            }
            $a3[$key] = $a2[$key];
        }

        return $a3;
    }

    static function isHierarchical( &$list ) {
        foreach ( $list as $n => $v ) {
            if ( is_array( $v ) ) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Array deep copy
     *
     * @params  array  $array
     * @params  int    $maxdepth
     * @params  int    $depth
     * 
     * @return  array  copy of the array
     * 
     * @static
     * @access public
     */
    static function array_deep_copy( &$array, $maxdepth=50, $depth=0 ) 
    {
        if( $depth > $maxdepth ) { 
            return $array; 
        }
        $copy = array();
        foreach( $array as $key => $value ) {
            if( is_array( $value ) ) {
                array_deep_copy( $value, $copy[$key], $maxdepth, ++$depth);
            } else {
                $copy[$key] = $value;
            }
        }
        return $copy;
    }

    /**
     * Array splice function that preserves associative keys
     * defauly php array_splice function doesnot preserve keys
     * So specify start and end of the array that you want to remove
     *
     * @param  array    $params  array to slice
     * @param  Integer  $start   
     * @param  Integer  $end
     *
     * @return  void
     * @static
     */
    static function crmArraySplice( &$params, $start, $end ) 
    {
        // verify start and end date
        if ( $start < 0 ) $start = 0;
        if ( $end > count( $params ) ) $end = count( $params );

        $i = 0;
        
        // procees unset operation
        foreach ( $params as $key => $value ) {
            if ( $i >= $start && $i < $end ) {
                unset( $params[$key] );
            }
            $i++;
        }
    }

    /**
     * Function for case insensitive in_array search
     *
     * @param $value             value or search string
     * @param $params            array that need to be searched
     * @param $caseInsensitive   boolean true or false
     *
     * @static
     */
    static function crmInArray( $value, $params, $caseInsensitive = true )
    {
        foreach ( $params as $item) {
            if ( is_array($item) ) {
                $ret = crmInArray( $value, $item, $caseInsensitive );
            } else {
                $ret = ($caseInsensitive) ? strtolower($item) == strtolower($value) : $item == $value;
                if ( $ret ) {
                    return $ret; 
                }
            }
        }
        return false;
    }

}


