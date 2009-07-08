<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
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
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */



/**
 * This class contains string functions
 *
 */

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
     * We only need one instance of the default allowed HTML tags.
     *
     * @var array
     * @static
     */
    static private $defaultAllowedTags;

    /**
     * This is the set of HTML tags that we are allowing in this instance.
     *
     * @var array
     * @static
     */
    static private $allowedTags;

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
    static function titleToVar( $title, $maxLength = 31 ) {
        $variable = self::munge( $title );
      
        require_once "CRM/Utils/Rule.php";
        if ( CRM_Utils_Rule::title( $variable, $maxLength ) ) {
            return $variable;
        }
      
        return null;
    }

    /**
     * given a string, replace all non alpha numeric characters and
     * spaces with the replacement character
     *
     * @param string $name the name to be worked on
     * @param string $char the character to use for non-valid chars
     * @param int    $len  length of valid variables
     *
     * @access public
     * @return string returns the manipulated string
     * @static
     */
    static function munge( $name, $char = '_', $len = 63 ) {
        // replace all white space and non-alpha numeric with $char
        $name = preg_replace('/\s+|\W+/', $char, trim($name) );

        if ( $len ) {
            // lets keep variable names short
            return substr( $name, 0, $len );
        } else {
            return $name;
        }
    }


    /* 
     * Takes a variable name and munges it randomly into another variable name
     *  
     * @param  string $name    Initial Variable Name
     * @param int     $len  length of valid variables
     *
     * @return string  Randomized Variable Name
     * @access public 
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
     * @param string $string the input string
     * @param char   $char   the character used to demarcate the componets
     *
     * @access public
     * @return string the last component
     * @static
     */
    static function getClassName( $string, $char = '_' ) {
        $names = explode( $char, $string );
        return array_pop( $names );
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
     * @access public
     * @static
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
     * @param string  $str input string
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

    static function redaction( $str, $stringRules, $regexRules ) {
        //redact the regular expressions
        if (!empty($regexRules)){
            foreach ($regexRules as $pattern => $replacement) {
                $str = preg_replace( $pattern, $replacement, $str );    
            }
        } 
        //redact the strings
        if (!empty($stringRules)){
            foreach ($stringRules as $match => $replace) {
                $str = str_ireplace($match,$replace,$str);
            }
        }
        //return the redacted output
        return $str;
    }
    
    /**
     * Determine if a string is composed only of utf8 characters
     * 
     * All functions designed to filter user input should use isUtf8
     * to ensure they operate on valid UTF-8 strings to prevent bypass of the
     * filter.
     *
     * When text containing an invalid UTF-8 lead byte (0xC0 - 0xFF) is presented
     * as UTF-8 to Internet Explorer 6, the program may misinterpret subsequent
     * bytes. When these subsequent bytes are HTML control characters such as
     * quotes or angle brackets, parts of the text that were deemed safe by filters
     * end up in locations that are potentially unsafe; An onerror attribute that
     * is outside of a tag, and thus deemed safe by a filter, can be interpreted
     * by the browser as if it were inside the tag.
     *
     * This function exploits preg_match behaviour (since PHP 4.3.5) when used
     * with the u modifier, as a fast way to find invalid UTF-8. When the matched
     * string contains an invalid byte sequence, it will fail silently.
     *
     * preg_match may not fail on 4 and 5 octet sequences, even though they
     * are not supported by the specification.
     *
     * The specific preg_match behaviour is present since PHP 4.3.5.
     *
     * @param string $str
     *   The text to check.
     * @access public
     * @static
     * @return boolean
     *   TRUE if the text is valid UTF-8, FALSE if not.
     */
    static function isUtf8( $str ) {
        if ( strlen( $str ) == 0 ) {
          return TRUE;
        }
        return ( preg_match( '/^./us', $str ) == 1 );
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
             self::extractURLVarValue( CRM_Utils_Array::value( 'query', $url1Str) ) == self::extractURLVarValue(  CRM_Utils_Array::value( 'query', $url2Str) ) ) {
            return true;
        }
        return false;
    }

    /**
     * Function to extract variable values
     *
     * @param  mix $query this is basically url
     *
     * @return mix $v  returns civicrm url (eg: civicrm/contact/search/...)
     * @access public
     */
    function extractURLVarValue( $query ) {
        $config =& CRM_Core_Config::singleton( );
        $urlVar =  $config->userFrameworkURLVar;

        $params = explode( '&', $query );
        foreach ( $params as $p ) {
            if ( strpos( $p, '=' ) ) {
                list( $k, $v ) = explode( '=', $p );
                if ( $k == $urlVar ) {
                    return $v;
                }
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
        if ( preg_match('/^(y(es)?|t(rue)?|1)$/i', $str) ) {
            return true;
        }
        return false;
    }

    /**
     * returns string '1' for a true/yes/1 string, and '0' for no/false/0 else returns false
     *
     * @param string $str  the string to be translated
     * @return boolean
     * @access public
     * @static
     */
    static function strtoboolstr($str) {
        if ( preg_match('/^(y(es)?|t(rue)?|1)$/i', $str) ) {
            return '1';
        } else if ( preg_match('/^(n(o)?|f(alse)?|0)$/i', $str) ) {
            return '0';
        }else {            
            return false;
        }
    }

    /**
     * Convert a HTML string into a text one using html2text
     *
     * @param string $html  the tring to be converted
     * @return string       the converted string
     * @access public
     * @static
     */
    static function htmlToText($html) {
        require_once 'packages/html2text/class.html2text.inc';
        $converter = new html2text($html);
        return $converter->get_text();
    }

    static function extractName( $string, &$params ) {
        $name = trim( $string );
        if ( empty( $name ) ) {
            return;
        }

        $names = explode( ' ', $name );
        if ( count( $names ) == 1 ) {
            $params['first_name'] = $names[0];
        } else if ( count( $names ) == 2 ) {
            $params['first_name'] = $names[0];
            $params['last_name' ] = $names[1];
        } else {
            $params['first_name' ] = $names[0];
            $params['middle_name'] = $names[1];
            $params['last_name'  ] = $names[2];
        }
    }

    static function &makeArray( $string ) {
        $string = trim( $string );

        $values = explode( "\n", $string );
        $result = array( );
        foreach ( $values as $value ) {
            list( $n, $v ) = CRM_Utils_System::explode( '=', $value, 2 );
            if ( ! empty( $v ) ) {
                $result[trim($n)] = trim($v);
            }
        }
        return $result;
    }
     
    /**
      * Filters XSS. Based on Drupal filter_xss() which is based on kses by Ulf Harnhammar, see
      * http://sourceforge.net/projects/kses
      *
      * For examples of various XSS attacks, see:
      * http://ha.ckers.org/xss.html
      *
      * This code does four things:
      * - Removes characters and constructs that can trick browsers
      * - Makes sure all HTML entities are well-formed
      * - Makes sure all HTML tags and attributes are well-formed
      * - Makes sure no HTML tags contain URLs with a disallowed protocol (e.g. javascript:)
      *
      * @param string $string
      *  The string with raw HTML in it. It will be stripped of everything that can cause
      *  an XSS attack.
      * @param mixed $allowed_tags = NULL
      *  Either NULL to allow only the default set of tags, 
      *  or a simple array of tags to allow.
      *   ex. 
      *    array('a', 'em', 'strong')
      * @return string
      *  The cleaned string.  
      */
    static function filterXSS($string, $allowed_tags = NULL) {
        //        return $string;
        
        if ( empty(self::$defaultAllowedTags) ) {
            
            // This is basically the same list that Drupal's filter_xss_admin() uses, but it also
            // allows form tags.  This is necessary because Smarty and QuickForms are not made to play 
            // well together.  We are outputting pre-rendered forms through Smarty.  
            self::$defaultAllowedTags = array( 'a', 'abbr', 'acronym', 'address', 'b', 'bdo', 'big', 
                                               'blockquote', 'button', 'br', 'caption', 'cite', 'code', 'col', 'colgroup', 'dd', 
                                               'del', 'dfn', 'div', 'dl', 'dt', 'em', 'fieldset', 'form', 'h1', 'h2', 'h3', 'h4', 
                                               'h5', 'h6', 'hr', 'i', 'img', 'ins', 'input', 'kbd', 'label', 'li', 'legend', 
                                               'ol', 'optgroup', 'option', 'p', 'pre', 'q', 'samp', 'select', 'small', 'span', 
                                               'strong', 'sub', 'sup', 'table', 'tbody', 'td', 'textarea'. 'tfoot', 'th', 
                                               'thead', 'tr', 'tt', 'ul', 'var' ); 
        }
        
        // Store the input format.
        self::$allowedTags = ( $allowed_tags === NULL ? self::$defaultAllowedTags : $allowed_tags );
        self::$allowedTags = array_flip( self::$allowedTags );
        
        // Only operate on valid UTF-8 strings. This is necessary to prevent cross
        // site scripting issues on Internet Explorer 6.
        if ( !self::isUtf8( $string ) ) {
            return '';
        }
        
        // Remove NUL characters (ignored by some browsers)
        $string = str_replace( chr(0), '', $string );
        // Remove Netscape 4 JS entities
        $string = preg_replace( '%&\s*\{[^}]*(\}\s*;?|$)%', '', $string );
        
        // Defuse all HTML entities
        $string = str_replace( '&', '&amp;', $string );
        // Change back only well-formed entities in our whitelist
        // Named entities
        $string = preg_replace( '/&amp;([A-Za-z][A-Za-z0-9]*;)/', '&\1', $string );
        // Decimal numeric entities
        $string = preg_replace( '/&amp;#([0-9]+;)/', '&#\1', $string );
        // Hexadecimal numeric entities
        $string = preg_replace( '/&amp;#[Xx]0*((?:[0-9A-Fa-f]{2})+;)/', '&#x\1', $string );
        
        return preg_replace_callback( '%
             (
             <(?=[^a-zA-Z!/])  # a lone <
             |                 # or
             <[^>]*(>|$)       # a string that starts with a <, 
                               # up until the > or the end of the string
             |                 # or
             >                 # just a >
             )%x', 'CRM_Utils_String::_filterXSSsplit', $string );
    }
    
    /**
     * Processes an HTML tag.
     *
     * @param array $tag
     *   An array that has one element, the HTML tag to process.
     * @return
     *   If the element isn't allowed, an empty string. Otherwise, the cleaned up
     *   version of the HTML element.
     */
    static public function _filterXSSsplit($tag) {
        
        $string = $tag[1];
        
        if ( substr($string, 0, 1) != '<' ) {
            // We matched a lone ">" character
            return '&gt;';
        }
        elseif ( strlen($string) == 1 ) {
            // We matched a lone "<" character
            return '&lt;';
        }
        
        if ( !preg_match( '%^<\s*(/\s*)?([a-zA-Z0-9]+)([^>]*)>?$%', $string, $matches ) ) {
            // Seriously malformed
            return '';
        }
        
        $slash = trim( $matches[1] );
        $elem = &$matches[2];
        $attrlist = &$matches[3];
        
        if ( !isset( self::$allowedTags[ strtolower( $elem ) ] ) ) {
            // Disallowed HTML element
            return '';
        }
        
        if ( $slash != '' ) {
            return "</$elem>";
        }
        
        // Is there a closing XHTML slash at the end of the attributes?
        $attrlist = preg_replace( '%(\s?)/\s*$%', '\1', $attrlist, -1, $count );
        $xhtml_slash = $count ? ' /' : '';
        
        // Clean up attributes
        $attr2 = implode( ' ', self::_filterXSSattributes( $attrlist ) );
        $attr2 = preg_replace( '/[<>]/', '', $attr2 );
        $attr2 = strlen( $attr2 ) ? ' '. $attr2 : '';
        
        return "<$elem$attr2$xhtml_slash>";
    }
    
    
    /**
     * Processes a string of HTML attributes.
     *
     * @return
     *   Cleaned up version of the HTML attributes.
     */
    static private function _filterXSSattributes( $attr ) {
        $attrarr = array();
        $mode = 0;
        $attrname = '';
        
        while ( strlen( $attr ) != 0) {
            // Was the last operation successful?
            $working = 0;
            
            switch ( $mode ) {
            case 0:
                // Attribute name, href for instance
                if ( preg_match('/^([-a-zA-Z]+)/', $attr, $match ) ) {
                    $attrname = strtolower( $match[1] );
                    $skip = ( $attrname == 'style' || substr( $attrname, 0, 2 ) == 'on' );
                    $working = $mode = 1;
                    $attr = preg_replace( '/^[-a-zA-Z]+/', '', $attr );
                }
                
                break;
                
            case 1:
                // Equals sign or valueless ("selected")
                if ( preg_match( '/^\s*=\s*/', $attr ) ) {
                    $working = 1; $mode = 2;
                    $attr = preg_replace( '/^\s*=\s*/', '', $attr );
                    break;
                }
                
                if ( preg_match( '/^\s+/', $attr ) ) {
                    $working = 1; $mode = 0;
                    if ( !$skip ) {
                        $attrarr[] = $attrname;
                    }
                    $attr = preg_replace( '/^\s+/', '', $attr );
                }
                
                break;
                
            case 2:
                // Attribute value, a URL after href= for instance
                if ( preg_match( '/^"([^"]*)"(\s+|$)/', $attr, $match ) ) {
                    $thisval = self::_filterXSSbadProtocol( $match[1] );
                    
                    if ( !$skip ) {
                        $attrarr[] = "$attrname=\"$thisval\"";
                    }
                    $working = 1;
                    $mode = 0;
                    $attr = preg_replace( '/^"[^"]*"(\s+|$)/', '', $attr );
                    break;
                }
                
                if ( preg_match( "/^'([^']*)'(\s+|$)/", $attr, $match ) ) {
                    $thisval = self::_filterXSSbadProtocol( $match[1] );
                    
                    if ( !$skip ) {
                        $attrarr[] = "$attrname='$thisval'";;
                    }
                    $working = 1; $mode = 0;
                    $attr = preg_replace( "/^'[^']*'(\s+|$)/", '', $attr );
                    break;
                }
                
                if ( preg_match( "%^([^\s\"']+)(\s+|$)%", $attr, $match ) ) {
                    $thisval = self::_filterXSSbadProtocol( $match[1] );
                    
                    if ( !$skip ) {
                        $attrarr[] = "$attrname=\"$thisval\"";
                    }
                    $working = 1; $mode = 0;
                    $attr = preg_replace( "%^[^\s\"']+(\s+|$)%", '', $attr );
                }
                
                break;
            }
            
            if ( $working == 0 ) {
                // not well formed, remove and try again
                $attr = preg_replace( '/
                   ^
                   (
                   "[^"]*("|$)     # - a string that starts with a double quote, 
                                   #   up until the next double quote or the end of the string
                   |               # or
                   \'[^\']*(\'|$)| # - a string that starts with a quote,  
                                   #   up until the next quote or the end of the string
                   |               # or
                   \S              # - a non-whitespace character
                   )*              # any number of the above three
                   \s*             # any number of whitespaces
                   /x', '', $attr );
                $mode = 0;
            }
        }
        
        // the attribute list ends with a valueless attribute like "selected"
        if ( $mode == 1 ) {
            $attrarr[] = $attrname;
        }
        return $attrarr;
    }
    
    /**
     * Processes an HTML attribute value and ensures it does not contain an URL
     * with a disallowed protocol (e.g. javascript:)
     *
     * @param $string
     *   The string with the attribute value.
     * @param $decode
     *   Whether to decode entities in the $string. Set to FALSE if the $string
     *   is in plain text, TRUE otherwise. Defaults to TRUE.
     * @return
     *   Cleaned up and HTML-escaped version of $string.
     */
    static private function _filterXSSbadProtocol( $string, $decode = TRUE ) {
        static $allowed_protocols;
        if ( !isset($allowed_protocols ) ) {
            $allowed_protocols = array_flip( array( 'http', 'https', 'ftp', 'news', 'nntp', 
                                                    'telnet', 'mailto', 'irc', 'ssh', 'sftp', 'webcal', 'rtsp' ) );
        }
        
        // Get the plain text representation of the attribute value (i.e. its meaning).
        if ($decode) {
            $string = decode_entities( $string );
        }
        
        // Iteratively remove any invalid protocol found.
        do {
            $before = $string;
            $colonpos = strpos( $string, ':' );
            if ( $colonpos > 0 ) {
                // We found a colon, possibly a protocol. Verify.
                $protocol = substr( $string, 0, $colonpos );
                // If a colon is preceded by a slash, question mark or hash, it cannot
                // possibly be part of the URL scheme. This must be a relative URL,
                // which inherits the (safe) protocol of the base document.
                if ( preg_match( '![/?#]!', $protocol ) ) {
                    break;
                }
                // Per RFC2616, section 3.2.3 (URI Comparison) scheme comparison must be case-insensitive
                // Check if this is a disallowed protocol.
                if ( !isset( $allowed_protocols[ strtolower( $protocol ) ] ) ) {
                    $string = substr( $string, $colonpos + 1 );
                }
            }
        } while ( $before != $string );
        return self::checkPlain( $string );
    }
    
    /**
     * Encode special characters in a plain-text string for display as HTML.
     *
     * Uses isUtf8 to prevent cross site scripting attacks on
     * Internet Explorer 6.
     */
    function checkPlain( $text ) {
        return self::isUtf8( $text ) ? htmlspecialchars( $text, ENT_QUOTES ) : '' ;
    }
    
}
