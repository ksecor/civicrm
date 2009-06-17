<?php  // vim: set si ai expandtab tabstop=4 shiftwidth=4 softtabstop=4:

/**
 *  File for the AllTests class
 *
 *  (PHP 5)
 *  
 *   @author Walt Haas <walt@dharmatech.org> (801) 534-1262
 *   @copyright Copyright CiviCRM LLC (C) 2009
 *   @license   http://www.fsf.org/licensing/licenses/agpl-3.0.html
 *              GNU Affero General Public License version 3
 *   @version   $Id$
 *   @package   CiviCRM
 *
 *   This file is part of CiviCRM
 *
 *   CiviCRM is free software; you can redistribute it and/or
 *   modify it under the terms of the GNU Affero General Public License
 *   as published by the Free Software Foundation; either version 3 of
 *   the License, or (at your option) any later version.
 *
 *   CiviCRM is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU Affero General Public License for more details.
 *
 *   You should have received a copy of the GNU Affero General Public
 *   License along with this program.  If not, see
 *   <http://www.gnu.org/licenses/>.
 */

/**
 *  Include parent class definition
 */
require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'Utils.php';
require_once 'standalone/civicrm.settings.php';

/**
 *  Class containing all test suites
 *
 *  @package   CiviCRM_DB_Tools
 */
class AllTests
{
    /**
     * @var what DB connection
     */
    public static $db_conn;

    /**
     *  @var Utils instance
     */
    public static $utils;

    /**
     *  Build test suite dynamically
     */
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('CiviCRM');
        $dir_name = dirname( __FILE__ );
        $dir = opendir( $dir_name );
        while( false !== ( $file = readdir( $dir ) ) ) {
            $path = $dir_name . '/' . $file ;
            if ( is_dir( $path )
                && ( substr( $file, 0, 1 ) != '.' ) ) {
                self::addAllTests( $suite, $path, $file );
            }
        }
        return $suite;
    } 

    /**
     *  Install the test database
     */
    public static function installDB()
    {
        static $dbInit = false;

        if ( !$dbInit ) {
            echo PHP_EOL
                . "Installing test_civicrm database"
                . PHP_EOL;

            //  create test database
            self::$utils = new Utils( $GLOBALS['mysql_host'],
                                'test_civicrm',
                                $GLOBALS['mysql_user'],
                                $GLOBALS['mysql_pass'] );
            $query = "DROP DATABASE IF EXISTS test_civicrm;"
                   . "CREATE DATABASE test_civicrm DEFAULT"
                   . " CHARACTER SET utf8 COLLATE utf8_unicode_ci;"
                   . "USE test_civicrm;";
            if ( self::$utils->do_query($query) === false ) {

                //  failed to create test database
                exit;
            }

            //  initialize test database
            $sql_file = dirname( dirname( dirname( __FILE__ ) ) )
                . "/sql/civicrm.mysql";
            $query = file_get_contents( $sql_file );
            if ( self::$utils->do_query($query) === false ) {

                //  failed to initialze test database
                exit;
            }
            $dbInit = true;
        }
        return self::$db_conn;
    }

    /**
     *  Add all test classes Test* in subdirectories
     *
     *  @param  &object Test suite object to add tests to
     *  @param  string  Name of directory to scan
     *  @return Test suite has been updated
     */
    private static function addAllTests( &$suite, $dir_name, $prefix )
    {
        $dir = opendir( $dir_name );
        if ( $dir === false ) {
            return $result;
        }
        while( false !== ( $file = readdir( $dir ) ) ) {
            $path = $dir_name . '/' . $file ;
            if ( is_dir( $path )
                 && ( substr( $file, 0, 1 ) != '.' ) ) {
                self::addAllTests( $suite, $path, $prefix . '_' . $file ) ;
            } else {
                if ( preg_match( '/Test.*\.php/', $file ) ) {
                    $oldClassNames = get_declared_classes();
                    require_once $path;
                    $newClassNames = get_declared_classes();
                    foreach( array_diff( $newClassNames,
                                         $oldClassNames ) as $name ) {
                        if ( preg_match( "/^{$prefix}_Test/", $name ) ) {
                            $suite->addTestSuite( $name );
                        }
                    }
                }
            }
        }
    }


} // class AllTests

// -- set Emacs parameters --
// Local variables:
// mode: php;
// tab-width: 4
// c-basic-offset: 4
// c-hanging-comment-ender-p: nil
// indent-tabs-mode: nil
// End: