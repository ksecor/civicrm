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

/** 
 *  This file contains functions for creating and altering CiviCRM-tables.
 */

require_once 'DB.php';

class CRM_Core_BAO_SchemaHandler
{

    /**
     * Function to create civicrm-db-connection
     *  
     * @param NULL
     * 
     * @return connection-object
     * 
     * @static
     * @access public
     */
    static function &createConnection( ) 
    {
        $config     =& CRM_Core_Config::singleton( );
        
        $db_civicrm = DB::connect($config->dsn);
        
        if ( DB::isError( $db_civicrm ) ) { 
            die( "Cannot connect to CiviCRM db via $dsn, " . $db_civicrm->getMessage( ) ); 
        } 
        
        return $db_civicrm;
    }

    /**
     * Function for creating a civiCRM-table
     *  
     * @param  String  $tableName        name of the table to be created.
     * @param  Array   $tableAttributes  array containing atrributes for the table that needs to be created
     * 
     * @return true if successfully created, false otherwise
     * 
     * @static
     * @access public
     */
    static function createTable( $tableName, $tableAttributes ) 
    {
        $db_civicrm =& self::createConnection();
        
        $sql    = self::buildQuery( $tableAttributes );
        $result = $db_civicrm->query( $sql );
        
        if ( DB::isError( $result ) ) {
            return false;
        }
        
        $db_civicrm->disconnect( );
        return true;
    }

    /**
     * Function to delete a civiCRM-table
     *  
     * @param  String  $tableName   name of the table to be created.
     * 
     * @return true if successfully deleted, false otherwise
     * 
     * @static
     * @access public
     */
    static function dropTable( $tableName ) 
    {
        $db_civicrm =& self::createConnection();
        
        $sql    = "DROP TABLE IF EXISTS $tableName";
        $result = $db_civicrm->query( $sql );
        
        if ( DB::isError( $result ) ) {
            return false;
        }
        
        $db_civicrm->disconnect( );
        return true;
    }

    /**
     * Function for adding a column to the specified table.
     *  
     * @param  String  $tableName         name of the table which is to be altered
     * @param  Array   $columnAttributes  array containing atrributes for the column, to be added
     * 
     * @return true if successfully added, false otherwise
     * 
     * @static
     * @access public
     */
    static function addColumn( $tableName, $columnAttributes ) 
    {
        $db_civicrm =& self::createConnection();
        
        $sql    = self::buildQuery( $columnAttributes );
        $result = $db_civicrm->query( $sql );
        
        if ( DB::isError( $result ) ) {
            return false;
        }
        
        $db_civicrm->disconnect( );
        return true;
    }

    /**
     * Function for removing a column from a table
     *  
     * @param  String  $tableName     name of the table to be altered.
     * @param  Array   $columnName    name of the column to be deleted.
     * 
     * @return true if successfully deleted, false otherwise
     * 
     * @static
     * @access public
     */
    static function dropColumn( $tableName, $columnName ) 
    {
        $db_civicrm =& self::createConnection();
        
        $sql    = "ALTER TABLE $tableName DROP COLUMN $columnName";
        $result = $db_civicrm->query( $sql );
        
        if ( DB::isError( $result ) ) {
            return false;
        }
        
        $db_civicrm->disconnect( );
        return true;
    }

}
?>
