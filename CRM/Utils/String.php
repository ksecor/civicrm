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


require_once 'HTML/QuickForm/Rule/Email.php';

class CRM_Utils_String {
  
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
        if ( ! CRM_Utils_Rule::title( $title ) ) {
            return null;
        }

        $variable = self::munge( $title );

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
     * @param boolean $utf8 attempt utf8 match on failure (default yes)
     *
     * @return boolean    true if string is ascii
     * @access public
     * @static
     */
    static function isAscii( $str, $utf8 = true ) {
        $str = preg_replace( '/\s+/', '', $str ); // eliminate all white space from the string
        /* FIXME:  This is a pretty brutal hack to make utf8 and 8859-1 work.
         */
        
        /* match low- or high-ascii characters */
        if ( preg_match( '/[\x00-\x20]|[\x7F-\xFF]/', $str ) )  {
//         || // low ascii characters
//              preg_match( '/[\x7F-\xFF]/', $str ) ) {   // high ascii characters
            if ($utf8) {
                /* if we did match, try for utf-8, or iso8859-1 */
                return self::isUtf8( $str );
            } else {
                return false;
            }
        }
        return true;
    }

    /**
     * Determine if a string is composed only of utf8 characters
     *
     * @param string $str  input string
     * @access public
     * @static
     * @return boolean
     */
    static function isUtf8( $str ) {
        $str = preg_replace( '/\s+/', '', $str ); // eliminate all white space from the string
        
        /* pattern stolen from the php.net function documentation for
         * utf8decode();
         * comment by JF Sebastian, 30-Mar-2005
         */
        return  preg_match( '/^([\x00-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xec][\x80-\xbf]{2}|\xed[\x80-\x9f][\x80-\xbf]|[\xee-\xef][\x80-\xbf]{2}|f0[\x90-\xbf][\x80-\xbf]{2}|[\xf1-\xf3][\x80-\xbf]{3}|\xf4[\x80-\x8f][\x80-\xbf]{2})*$/' , $str );
//             || 
//                 iconv('ISO-8859-1', 'UTF-8', $str);
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
        $url1 = strtolower( $url1 );
        $url2 = strtolower( $url2 );

        $url1Str = parse_url( $url1 );
        $url2Str = parse_url( $url2 );

        if ( $url1Str['path'] == $url2Str['path'] && 
             self::extractURLVarValue( $url1Str['query'] ) == self::extractURLVarValue( $url2Str['query'] ) ) {
            return true;
        }
        return false;
    }

    function extractURLVarValue( $query ) {
        $config =& CRM_Core_Config::singleton( );
        $urlVar =  $config->userFrameworkURLVar;

        $params = explode( '&', $query );
        foreach ( $params as $p ) {
            list( $k, $v ) = explode( '=', $p );
            if ( $k == $urlVar ) {
                return $v;
            }
        }
        return null;
    }

    /**
     * translate a true/false/yes/no string to a 0 or 1 value
     *
     * @param string $str  the string to be translated
     * @return boolean
     * @access public
     * @static
     */
    static function strtobool($str) {
        if (preg_match('/^(y(es)?|t(rue)?|1)$/i', $str)) {
            return true;
        }
        return false;
    }

}

?>
