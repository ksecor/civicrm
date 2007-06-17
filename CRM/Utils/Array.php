<?php


/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.8                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
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
            return $key ? $key : null;
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
     * emulated version of array_combine
     *
     * As array_combine is PHP5 only method, need to have emulated
     * version for PHP4.
     * 
     * @params  array  $keyList
     * @params  array  $valueList
     * 
     * @return  array  combined array.
     * 
     * @static
     * @access public
     */
    static function combine( &$keyList, &$valueList ) {
        $keys = array_values( (array) $keyList );
        $vals = array_values( (array) $valueList );
        
        $n = max( count( $keys ), count( $vals ) );
        $r = array();
        for( $i=0; $i<$n; $i++ ) {
            $r[ $keys[ $i ] ] = $vals[ $i ];
        }
        return $r;
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
    static function array_deep_copy( &$array, $maxdepth=50, $depth=0 ) {
        if( $depth > $maxdepth ) { 
            return $array; 
        }
        $copy = array();
        foreach( $array as $key => &$value ) {
            if( is_array( $value ) ) {
                array_deep_copy( $value, $copy[$key], $maxdepth, ++$depth);
            } else {
                $copy[$key] = $value;
            }
        }
        return $copy;
    }



}

?>
