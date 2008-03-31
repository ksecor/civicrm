<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
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


/**
 * class to provide simple static functions for file objects
 */
class CRM_Utils_File {

    /**
     * Given a file name, determine if the file contents make it an ascii file
     *
     * @param string $name name of file
     *
     * @return boolean     true if file is ascii
     * @access public
     */
    static function isAscii( $name ) {
        $fd = fopen( $name, "r" );
        if ( ! $fd ) {
            return false;
        }

        $ascii = true;
        while (!feof($fd)) {
            $line = fgets( $fd, 8192 );
            if ( ! CRM_Utils_String::isAscii( $line ) ) {
                $ascii = false;
                break;
            }
        }

        fclose( $fd );
        return $ascii;
    }

    /**
     * Given a file name, determine if the file contents make it an html file
     *
     * @param string $name name of file
     *
     * @return boolean     true if file is html
     * @access public
     */
    static function isHtml( $name ) {
        $fd = fopen( $name, "r" );
        if ( ! $fd ) {
            return false;
        }

        $html = false;
        $lineCount = 0;
        while ( ! feof( $fd ) & $lineCount <= 5 ) {
            $lineCount++;
            $line = fgets( $fd, 8192 );
            if ( ! CRM_Utils_String::isHtml( $line ) ) {
                $html = true;
                break;
            }
        }

        fclose( $fd );
        return $html;
    }

    /**
     * create a directory given a path name, creates parent directories
     * if needed
     * 
     * @param string $path  the path name
     *
     * @return void
     * @access public
     * @static
     */
    function createDir( $path ) {
        if ( is_dir( $path ) || empty( $path ) ) {
            return;
        }

        CRM_Utils_File::createDir( dirname( $path ) );
        if ( mkdir( $path, 0777 ) == false ) {
            echo "Error: Could not create directory: $path. <p>";
            exit( );
        }

    }

    /** 
     * delete a directory given a path name, delete children directories
     * and files if needed 
     *  
     * @param string $path  the path name 
     * 
     * @return void 
     * @access public 
     * @static 
     */ 
    public function cleanDir( $target ) {
        static $exceptions = array( '.', '..' );

        if ( $sourcedir = @opendir( $target ) ) {
            while ( false !== ( $sibling = readdir( $sourcedir ) ) ) {
                if ( ! in_array( $sibling, $exceptions ) ) {
                    $object = $target . DIRECTORY_SEPARATOR . $sibling;
                    
                    if ( is_dir( $object ) ) {
                        CRM_Utils_File::cleanDir( $object );
                    } else if ( is_file( $object ) ) {
                        $result = @unlink( $object );
                    }
                }
            }
            closedir( $sourcedir );
            $result = @rmdir( $target );
        }
    }

    /**
     * Given a file name, recode it (in place!) to UTF-8
     *
     * @param string $name name of file
     *
     * @return boolean  whether the file was recoded properly
     * @access public
     */
    static function toUtf8( $name ) {
        require_once 'CRM/Core/Config.php';
        static $config         = null;
        static $legacyEncoding = null;
        if ($config == null) {
            $config =& CRM_Core_Config::singleton();
            $legacyEncoding = $config->legacyEncoding;
        }

        if (!function_exists('iconv')) return false;

        $contents = file_get_contents($name);
        if ($contents === false) return false;

        $contents = iconv($legacyEncoding, 'UTF-8', $contents);
        if ($contents === false) return false;

        $file = fopen($name, 'w');
        if ($file === false) return false;

        $written = fwrite($file, $contents);
        $closed  = fclose($file);
        if ($written === false or !$closed) return false;

        return true;
    }


    /** 
     * Appends trailing slashed to paths
     * 
     * @return string
     * @access public
     * @static
     */
    static function addTrailingSlash( $name, $separator = null ) 
    {
        if ( ! $separator ) {
            $separator = DIRECTORY_SEPARATOR;
        }
            
        if ( substr( $name, -1, 1 ) != $separator ) {
            $name .= $separator;
        }
        return $name;
    }


    function sourceSQLFile( $dsn, $fileName, $prefix = null ) {
        require_once 'DB.php';

        $db  =& DB::connect( $dsn );
        if ( PEAR::isError( $db ) ) {
            die( "Cannot open $dsn: " . $db->getMessage( ) );
        }

        $string = $prefix . file_get_contents( $fileName );
        
        //get rid of comments starting with # and --
        $string = preg_replace("/^#[^\n]*$/m", "\n", $string );
        $string = preg_replace("/^\-\-[^\n]*$/m", "\n", $string );
        
        $queries  = explode( ';', $string );
        foreach ( $queries as $query ) {
            $query = trim( $query );
            if ( ! empty( $query ) ) {
                $res =& $db->query( $query );
                if ( PEAR::isError( $res ) ) {
                    die( "Cannot execute $query: " . $res->getMessage( ) );
                }
            }
        }
    }

    static function isExtensionSafe( $ext ) {
        static $extensions = null;
        if ( ! $extensions ) {
            require_once 'CRM/Core/OptionGroup.php';
            $extensions = CRM_Core_OptionGroup::values( 'safe_file_extension' );
            $extensions = array_values( $extensions );
        }
        return in_array( $ext, $extensions ) ? true : false;
    }


}


