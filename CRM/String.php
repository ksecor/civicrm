<?php
/**
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

    const
    EMAIL      = 'email'  ,
        STATE      = 'state'  ,
        ADDRESS    = 'street1',
        POSTALCODE = 'zipcode',
        PHONE      = 'phone'  ;
  
    /**
     * Takes a valid name and converts it to a valid variable
     * name (needed typically for form keys)
     * 
     * @param  name    Name of the string
     * @return string  The display name munged into a string
     *
     * @access public
     * @return string (or null)
     */
    static function nameToVariable( $name ) {
        if ( ! CRM_Validate::name( $name ) ) {
            return null;
        }

        $variable = CRM_String::mungeName( $name );

        if ( CRM_Validate::variable( $variable ) ) {
            return $variable;
        }
      
        return null;
    }

    static function mungeName( $name ) {
        $variable = trim( $name );

        // replace all white space with an '_'
        $variable = preg_replace('/\s+/', '_', $variable );

        // replace all non-alpha numeric characters with an '_'
        $variable = preg_replace( '/[^\w]/', '_', $variable );

        // reduce the length of this to 31 characters
        $variable = substr( $variable, 0, 31 );

        return $variable;
    }


    /* 
     * Takes a variable name and munges it randomly into another variable name
     *  
     * @param  name    Initial Variable Name
     * @return string  Randomized Variable Name
     * 
     * @access public 
     */
    static function renameVariable( $name ) {
        // generate a 4 digit string
        $rand = sprintf( "%04d", rand( ) % 10000 );
        return substr_replace( $name, $rand, -4, 4 );
    }

    /**
     * takes a string and returns the count of the number of words
     * in the string
     *
     * @param string $string - string to word counted
     *
     * @return int numberOfWords
     * 
     * @access public
     *
     */
    static function wordCount( $string ) {
        $words = preg_split( "\s+", $string );
        return count( $words );
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
     *
     */
    static function getClassName( $string, $char = '_' ) {
        $path = explode( $char, $string );
        return $path[ count( $path ) - 1 ];
    }

    static function trimArrayValues( $array ) {
        $fields = array();
        foreach (array_keys($array) as $fldKey) {
            $fields[$fldKey] = trim($array[$fldKey]);
        }
        return $fields;
    }

    static function matchEmail( $values ) {
        $validator = new HTML_QuickForm_Rule_Email( );
        $match = $misMatch = 0;
        foreach ( $values as $value ) {
            $validator->validate( $value ) ? $match++ : $misMatch++;
        }
        return ( $match > $misMatch ) ? true : false; 
    }

    // actually this should check the associative array associated with a Locale
    static function matchStateProvince( $values ) {
        $match = $misMatch = 0;
        foreach ( $values as $value ) {
            $value = trim( $value );
            if ( preg_match( '/^[a-z][a-z]$/i', $value ) ) {
                $match++;
            } else {
                $misMatch++;
            }
        }
      
        return ( $match > $misMatch ) ? true : false; 
    }

    static function matchPostalCode( $values ) {
        $match = $misMatch = 0;
        foreach ( $values as $value ) {
            $value = trim( $value );
            if ( preg_match( '/^\d{5}$/i', $value ) ) {
                $match++;
            } else {
                $misMatch++;
            }
        }
      
        return ( $match > $misMatch ) ? true : false; 
    }

    static function matchAddress( $values ) {
        $match = $misMatch = 0;
        foreach ( $values as $value ) {
            $value = trim( $value );
            if ( CRM_String::wordCount( $value ) > 1 ) {
                $match++;
            } else {
                $misMatch++;
            }
        }
      
        return ( $match > $misMatch ) ? true : false; 
    }
    
    /**
     * Takes an array of values and attempts to match them to some pre-defined tokens 
     * Tokens currently matched include:
     *   email ( if @ sign is present )
     *   state ( if tokens are mainly 2 alphabetic characters )
     *   address ( if token has more than 1 space in it )
     *   postalCode ( if token is made up of numbers and - )
     *
     * @param array $values a vector of strings
     * 
     * @return mixed match - a string constant
     * @access public
     */
    static function findBestProperty( $values ) {
        if ( CRM_String::matchEmail( $values ) ) {
            return CRM_String::EMAIL;
        }

        if ( CRM_String::matchStateProvince( $values ) ) {
            return CRM_String::STATE;
        }

        if ( CRM_String::matchAddress( $values ) ) {
            return CRM_String::ADDRESS;
        }

        if ( CRM_String::matchPostalCode( $values ) ) {
            return CRM_String::POSTALCODE;
        }

        return null;
    }


    /**
     * Takes an array of lines and returns the seperator that occurs
     * the most in the group of lines
     * Note: this is not the smartest way to do things, on the other
     * hand its the simplest. This is an O(n^2) algorithm, so make
     * sure the number of lines is pretty small
     *
     * @param lines an array of lines
     * 
     * @return string the best seperator found
     * @access public
     *
     */
    static function findBestSeperator( $lines ) {
        $seperators = array(
                            CRM_String::COMMA          => 0,
                            CRM_String::SEMICOLON      => 0,
                            CRM_String::TAB            => 0,
                            CRM_String::SPACE          => 0,
                            CRM_String::LINEFEED       => 0,
                            CRM_String::CARRIAGELINE   => 0,
                            CRM_String::CARRIAGERETURN => 0,
                            );

        $count = array( );

        $max  = -1;
        $best = CRM_String::COMMA; // default, in case we dont find any or dont get into the loop
        foreach ( $seperators as $seperator => $value ) {
            foreach ( $lines as $line ) {
                $seperators[$seperator] = $seperators[$seperator] + substr_count( $line, $seperator );
            }

            if ( $seperators[$seperator] > $max ) {
                $max  = $seperators[$seperator];
                $best = $seperator;
            }
        }

        return $best;
    }

    /**
     * explodeLine: a smarter explode function. Takes care of quotes and
     * eliminates them if necessary. Ignores seperator charaters within
     * quotes
     *
     * @author Michal Mach
     * @author Donald Lobo
     *
     * @access public
     *
     * @param string  $line         the csv line to be split
     * @param string  $seperator    split the line based on seperator
     * @param boolean $removeQuotes should we eliminate quotes from the
     * output fields
     *
     * @return array  returns an array of words extracted from the line
     *
     */
    static function explodeLine( $line, $seperator, $removeQuotes = true, $removeBrackets = true ) {

        $fields   = array();
        $fldCount = 0;
        $inQuotes = false;

        $sepLen = strlen($seperator);
        $strLen = strlen($line) - 1;

        for ($i = 0; $i <= $strLen; $i++) {
            if ( ! isset( $fields[$fldCount] ) ) {
                $fields[$fldCount] = "";
            }

            $tmp = substr( $line, $i, $sepLen ) ;

            if ( $tmp === $seperator && ! $inQuotes ) {
                $fldCount++;
                $i += $sepLen - 1;
            } elseif ( $fields[$fldCount] == "" && $line[$i] == '"' && !$inQuotes ) {
                if ( ! $removeQuotes ) {
                    $fields[$fldCount] .= $line[$i];
                }
                $inQuotes = true;
            } else if ( $line[$i] == '"' ) {
                if ( $i != $strLen && $line[$i + 1] == '"' ) {
                    $i++;
                    $fields[$fldCount] .= $line[$i];
                } else {
                    if ( ! $removeQuotes ) {
                        $fields[$fldCount] .= $line[$i];
                    }
                    $inQuotes = false;
                }
            } else {
                $fields[$fldCount] .= $line[$i];
            }
        }

        if ( $removeBrackets ) {
            $matches = array( );
            for ( $i = 0; $i < count( $fields ); $i++ ) {
                if ( preg_match( '/^<(.*?)>$/', $fields[$i], $matches ) ) {
                    $fields[$i] = $matches[1];
                }
            }
        }
        return CRM_String::trimArrayValues($fields);
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

    function isHTMLLine( $line ) {
        $htmlTags = array( '<br>', '<p>', '<html>', '<head>',
                           '<body', '<title>', 'href',
                           '<hr>', '<b>', '<i>', '<em>' );
    
        $found    = 0;
        $notFound = 0;
    
        foreach ( $htmlTags as $tag ) {
            if ( stripos( $line, $tag ) !== false ) {
                return true;
            }
        }
    }

    function isAsciiLine( $line ) {
        $line = trim( $line );

        return preg_match( '/[\x80-\xFF]/', $line ) ? false : true;
    }

    function isAsciiFile( $name ) {
        $fd = fopen( $name, "r" );
        if ( ! $fd ) {
            return false;
        }

        $ascii = true;
        $lineCount = 0;
        while ( ! feof( $fd ) & $lineCount <= 5 ) {
            $lineCount++;
            $line = fgets( $fd, 8192 );
            if ( ! CRM_String::isAsciiLine( $line ) ) {
                $ascii = false; 
                break;
            }
        }

        fclose( $fd );
        return $ascii;
    }

    function isHTMLFile( $name ) {
        $fd = fopen( $name, "r" );
        if ( ! $fd ) {
            return false;
        }

        $html = false;
        $lineCount = 0;
        while ( ! feof( $fd ) & $lineCount <= 50 ) {
            $lineCount++;
            $line = fgets( $fd, 8192 );
            if ( CRM_String::isHTMLLine( $line ) ) {
                $html = true;
                break;
            }
        }

        fclose( $fd );
        return $html;
    }

    function isWhiteSpace( $string ) {
        return preg_match( '/^\s*$/', $string ) ? true : false;
    }

    function isComment( $string ) {
        return preg_match( '/^#/', $string ) ? true : false;
    }

}

?>
