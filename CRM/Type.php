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


require_once 'CRM/Rule.php';
require_once 'Validate.php';

class CRM_Type {
    const
        T_INT       =     1,
        T_STRING    =     2,
        T_ENUM      =     2,
        T_DATE      =     4,
        T_TIME      =     8,
        T_BOOL      =    16,
        T_BOOLEAN   =    16,
        T_TEXT      =    32,
        T_BLOB      =    64,
        T_TIMESTAMP =   256,
        T_DOUBLE    =   512,
        T_MONEY     =  1024,
        T_DATE      =  2048,
        T_EMAIL     =  4096,
        T_URL       =  8192,
        T_CCNUM     = 16384;

    static $_regex = array(
                           self::T_INT    => '/^-?\d+$/',
                           self::T_BOOL   => '/^[01]$/',
                           self::T_DOUBLE => '/(^-?\d\d*\.\d*$)|(^-?\d\d*$)|(^-?\.\d\d*$)/',
                           self::T_MONEY => '/(^\d\d*\.\d?\d?$)|(^\d\d*$)|(^\.\d?\d?$)/',
                           self::T_STRING => '/^[\w\s\'\&\,\$\#\|\_]+$/',
                           );
                         
    /**
     * given a type and a value, is the value valid for the type
     *
     * @param enum   $type  fixed set of types as defined above
     * @param string $value is value match the particular type
     *
     * @return bool  true if value is valid, else false
     * @access public
     */
    static function valid( $value, $type, $dateFormat = null ) {
        if ( ! isset( $value ) ) {
            return true;
        }

        switch ( $type ) {

        case self::T_INT:
        case self::T_BOOL:
        case self::T_DOUBLE:
        case self::T_MONEY:
        case self::T_STRING:
            if ( ! preg_match( self::$_regex[$type], $value ) ) {
                return false;
            }
            return true;

        case self::T_EMAIL:
            return CRM_Rule::email( $value );

        case self::T_URL:
            return CRM_Rule::uri( $value );

        case self::T_CCNUM:
            return Validate::creditCard( $value );

        default:
            return true;

        }
    }

    /**
     * given a string and a type, eliminate any potential hacks. Primarily used for
     * string and text types
     *
     * @param enum   $type  fixed set of types as defined above
     * @param string $value is value match the particular type
     *
     * @return string the filtered value
     * @access public
     */
    static function filter( $type, $value ) {
        $value = trim( $value );

        switch ( $type ) {

        case self::T_INT:
        case self::T_BOOL:
        case self::T_DOUBLE:
        case self::T_DATE:
            return $value;

        case self::T_STRING:
        case self::T_TEXT:
            return $value;

        default:
            return null;

        }

    }

    /**
     * given a string and a type, format the object based on the type
     * applies to date objects only
     *
     * @param enum   $type  fixed set of types as defined above
     * @param string $value is value match the particular type
     *
     * @return string the formatted value
     * @access public
     */
    static function format( $value, $type ) {
        // string and text values are returned without any checks
        if ( $type == self::T_STRING || $type == self::T_TEXT ) {
            return $value;
        }

        $value = trim( $value );

        if ( ! self::valid( $value, $type ) ) {
            return null;
        }

        switch ( $type ) {

        case self::T_INT:
            return (int ) $value;

        case self::T_BOOL:
            return (boolean ) $value;

        case self::T_DOUBLE:
        case self::T_MONEY:
            return (double ) $value;

        case self::T_EMAIL:
        case self::T_URL:
        case self::T_CCNUM:
            return $value;

        }

        return null;
    }

}

?>