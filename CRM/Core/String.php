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


require_once 'HTML/QuickForm/Rule/Email.php';

class CRM_String {
  
    const
    COMMA          = ","   ,
        SEMICOLON      = ";"   ,
        SPACE          = " "   ,
        TAB            = "\t"  ,
        LINEFEED       = "\n"  ,
        CARRIAGELINE   = "\r\n",
        LINECARRIAGE   = "\n\r",
        CARRIAGERETURN = "\r"  ;

    /**
     * Convert a display name into a potential variable
     * name that we could use in forms/code
     * 
     * @param  name    Name of the string
     * @return string  An equivalent variable name
     *
     * @access public
     * @return string (or null)
     * @static
     */
    static function titleToVar( $title ) {
        if ( ! CRM_Rule::title( $title ) ) {
            return null;
        }

        $variable = CRM_String::munge( $title );

        if ( CRM_Rule::variable( $variable ) ) {
            return $variable;
        }
      
        return null;
    }

    /**
     * given a string, replace all non alpha numeric characters and
     * spaces with the replacement character
     *
     * @param string the name to be worked on
     * @param string the character to use for non-valid chars
     * @param int    length of valid variables
     * @access public
     * @return string
     * @static
     */
    static function munge( $name, $char = '_', $len = 31 ) {
        // replace all white space and non-alpha numeric with $char
        $name = preg_replace('/\s+|\W+/', $char, trim($name) );

        // lets keep variable names short
        return substr( $name, 0, $len );
    }


    /* 
     * Takes a variable name and munges it randomly into another variable name
     *  
     * @param  name    Initial Variable Name
     * @return string  Randomized Variable Name
     * 
     * @access public 
     * @return string
     * @static
     */
    static function rename( $name, $len = 4 ) {
        $rand = substr( uniqid(), 0, $len );
        return substr_replace( $name, $rand, -$len, $len );
    }

    /**
     * takes a string and returns the last tuple of the string.
     * useful while converting file names to class names etc
     *
     * @param string the input string
     * @param char   the character used to demarcate the componets
     *
     * @access public
     * @return string the last component
     * @static
     */
    static function getClassName( $string, $char = '_' ) {
        return array_pop( explode( $char, $string ) );
    }

    /**
     * appends a name to a string and seperated by delimiter.
     * does the right thing for an empty string
     *
     * @param string $str   the string to be appended to
     * @param string $delim the delimiter to use
     * @param mixed  $name  the string (or array of strings) to append 
     *
     * @return void
     *
     */
    static function append( &$str, $delim, $name ) {
        if ( empty( $name ) ) {
            return;
        }

        if ( is_array( $name ) ) {
            foreach ( $name as $n ) {
                if ( empty( $n ) ) {
                    continue;
                }
                if ( empty( $str ) ) {
                    $str = $n;
                } else {
                    $str .= $delim . $n;
                }
            }
        } else {
            if ( empty( $str ) ) {
                $str = $name;
            } else {
                $str .= $delim . $name;
            }
        }
    }

    /**
     * determine if the string is composed only of ascii characters
     *
     * @param string $str input string
     *
     * @return boolean    true if string is ascii
     * @access public
     * @static
     */
    static function isAscii( $str ) {
        $str = preg_replace( '/\s+/', '', $str ); // eliminate all white space from the string

        if ( preg_match( '/[\x00-\x20]/', $str ) || // low ascii characters
             preg_match( '/[\x7F-\xFF]/', $str ) ) {   // high ascii characters
            return false;
        }
        return true;
    }
}

?>