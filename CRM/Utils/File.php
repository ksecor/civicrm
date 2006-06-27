<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.4                                                |
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
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
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
        $lineCount = 0;
        while ( ! feof( $fd ) & $lineCount <= 5 ) {
            $lineCount++;
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
        mkdir( $path, 0777 );
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

    static function put( $name, $contents ) {
        if ( ! function_exists( 'file_put_contents' ) ) {
            if ( ! $file = fopen( $name, 'w' ) ) {
                return false;
            }
            $n = fwrite( $file, $contents );
            fclose( $file );
            return $n ? $n : false;
        } else {
            return file_put_contents( $name, $contents );
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

}

?>
