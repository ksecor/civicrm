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


define( 'CRM_UTILS_STRING_COMMA',",");
define( 'CRM_UTILS_STRING_SEMICOLON',";");
define( 'CRM_UTILS_STRING_SPACE'," ");
define( 'CRM_UTILS_STRING_TAB',"\t");
define( 'CRM_UTILS_STRING_LINEFEED',"\n");
define( 'CRM_UTILS_STRING_CARRIAGELINE',"\r\n");
define( 'CRM_UTILS_STRING_LINECARRIAGE',"\n\r");
define( 'CRM_UTILS_STRING_CARRIAGERETURN',"\r");

require_once 'CRM/Utils/Rule.php';
require_once 'HTML/QuickForm/Rule/Email.php';

class CRM_Utils_String {
  
    
                  
                  
                      
                       
                  
            
            
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
     function titleToVar( $title ) {
        if ( ! CRM_Utils_Rule::title( $title ) ) {
            return null;
        }

        $variable = CRM_Utils_String::munge( $title );

        if ( CRM_Utils_Rule::variable( $variable ) ) {
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
     function munge( $name, $char = '_', $len = 31 ) {
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
     function rename( $name, $len = 4 ) {
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
     function getClassName( $string, $char = '_' ) {
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
     function append( &$str, $delim, $name ) {
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
     function isAscii( $str ) {
        $str = preg_replace( '/\s+/', '', $str ); // eliminate all white space from the string

        if ( preg_match( '/[\x00-\x20]/', $str ) || // low ascii characters
             preg_match( '/[\x7F-\xFF]/', $str ) ) {   // high ascii characters
            return false;
        }
        return true;
    }

    /**
     * determine if two href's are equivalent (fuzzy match)
     *
     * @param string $url1 the first url to be matched
     * @param string $url2 the second url to be matched against
     *
     * @return boolean true if the urls match, else false
     * @access public
     * @static
     */
    function match( $url1, $url2 ) {
        $url1Str = parse_url( $url1 );
        $url2Str = parse_url( $url2 );

        // CRM_Core_Error::debug( $url1Str['path'], $url2Str['path'] );
        return ( strtolower( $url1Str['path'] ) == strtolower( $url2Str['path'] ) ) ? true : false;
    }

}

?>