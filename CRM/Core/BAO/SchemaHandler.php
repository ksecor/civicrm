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

require_once 'CRM/Core/DAO.php';

/**
 * structure, similar to what is used in GenCode.php
 *
 *
 * $table = array( 'name'       => TABLE_NAME,
 *                'attributes' => ATTRIBUTES,
 *                'fields'     => array( 
 *                                      array( 'name'          => FIELD_NAME,
 *                                             'type'          => FIELD_SQL_TYPE,
 *                                             'class'         => FIELD_CLASS_TYPE, // can be field, index, constraint
 *                                             'primary'       => BOOLEAN,
 *                                             'required'      => BOOLEAN,
 *                                             'searchable'    => true,
 *                                             'fk_table_name' => FOREIGN_KEY_TABLE_NAME,
 *                                             'fk_field_name' => FOREIGN_KEY_FIELD_NAME,
 *                                             'comment'       => COMMENT,
 *                                             'default'       => DEFAULT, )
 *                                      ...
 *                                      ) );
 *                '
 */                                       

class CRM_Core_BAO_SchemaHandler
{
    static function &defaultCustomTableFields( &$params ) {
        // add the id, domain_id, and extends_id
        $table = array( 'name'       => $params['name'],
                        'attributes' => "ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci",
                        'fields'     => array(
                                              array( 'name'          => 'id',
                                                     'type'          => 'int unsigned',
                                                     'primary'       => true,
                                                     'required'      => true,
                                                     'comment'       => 'Default MySQL primary key' ),
                                              array( 'name'          => 'domain_id',
                                                     'type'          => 'int unsigned',
                                                     'required'      => true,
                                                     'comment'       => 'Default Domain that this data belongs to',
                                                     'fk_table_name' => 'civicrm_domain',
                                                     'fk_field_name' => 'id' ),
                                              array( 'name'          => 'entity_id',
                                                     'type'          => 'int unsigned',
                                                     'required'      => true,
                                                     'comment'       => 'Table that this extends',
                                                     'fk_table_name' => $params['extends_name'],
                                                     'fk_field_name' => 'id' )
                                              ),
                        'indexes'    => array(
                                              array( 'unique'        => true,
                                                     'field_name_1'  => 'domain_id',
                                                     'field_name_2'  => 'entity_id' )
                                              ),
                                                    
                        );
        return $table;
                                              
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
    static function createTable( &$params )
    {
        $sql =  self::buildTableSQL( $params );
        $dao =& CRM_Core_DAO::executeQuery( $sql, CRM_Core_DAO::$_nullArray );
        return true;
    }

    static function buildTableSQL( &$params ) {
        $sql = "CREATE TABLE {$params['name']} (";
        if ( isset( $params['fields'] ) &&
             is_array( $params['fields'] ) ) {
            $separator = "\n";
            $prefix    = null;
            foreach ( $params['fields'] as $field ) {
                $sql       .= self::buildFieldSQL      ( $field, $separator, $prefix );
                $separator  = ",\n";
            }
            foreach ( $params['fields'] as $field ) {
                $sql       .= self::buildPrimaryKeySQL ( $field, $separator, $prefix );
            }
            foreach ( $params['fields'] as $field ) {
                $sql       .= self::buildSearchIndexSQL( $field, $separator, $prefix );
            }
            foreach ( $params['indexes'] as $index ) {
                $sql       .= self::buildIndexSQL      ( $index, $separator, $prefix );
            }
            foreach ( $params['fields'] as $field ) {
                $sql       .= self::buildForeignKeySQL ( $field, $separator, $prefix );
            }
        }
        $sql .= "\n) {$params['attributes']};";
        return $sql;
    }

    static function buildFieldSQL( &$params, $separator, $prefix ) {
        $sql .= $separator;
        $sql .= str_repeat( ' ', 8 );
        $sql .= $prefix;
        $sql .= "{$params['name']} {$params['type']}";

        if ( CRM_Utils_Array::value( 'required', $params ) ) {
            $sql .= " NOT NULL";
        }

        if ( CRM_Utils_Array::value( 'default', $params ) ) {
            $sql .= " DEFAULT {$params['default']}";
        }

        if ( CRM_Utils_Array::value( 'comment', $params ) ) {
            $sql .= " COMMENT '{$params['comment']}'";
        }

        return $sql;
    }

    static function buildPrimaryKeySQL( &$params, $separator, $prefix ) {
        $sql = null;
        if ( CRM_Utils_Array::value( 'primary', $params ) ) {
            $sql .= $separator;
            $sql .= str_repeat( ' ', 8 );
            $sql .= $prefix;
            $sql .= "PRIMARY KEY ( {$params['name']} )";
        }
        return $sql;
    }

    static function buildSearchIndexSQL( &$params, $separator, $prefix ) {
        $sql = null;
        if ( CRM_Utils_Array::value( 'searchable', $params ) ) {
            $sql .= $separator;
            $sql .= str_repeat( ' ', 8 );
            $sql .= $prefix;
            $sql .= "INDEX_{$params['name']} ( {$params['name']} )";
        }
        return $sql;
    }

    static function buildIndexSQL( &$params, $separator, $prefix ) {
        $sql .= $separator;
        $sql .= str_repeat( ' ', 8 );
        if ( $params['unique'] ) {
            $sql       .= 'UNIQUE INDEX';
            $indexName  = 'UI';
        } else {
            $sql       .= 'UNIQUE INDEX';
            $indexName  = 'INDEX';
        }
        $indexFields = null;
        
        foreach ( $params as $name => $value ) {
            if ( substr( $name, 0, 11 ) == 'field_name_' ) {
                $indexName   .= "_{$value}";
                $indexFields .= " $value,";
            }
        }
        $indexFields = substr( $indexFields, 0, -1 );
        
        $sql .= " $indexName ( $indexFields )";
        return $sql;
    }

    static function buildForeignKeySQL( &$params, $separator, $prefix ) {
        $sql = null;
        if ( CRM_Utils_Array::value( 'fk_table_name', $params ) &&
             CRM_Utils_Array::value( 'fk_field_name', $params ) ) {
            $sql .= $separator;
            $sql .= str_repeat( ' ', 8 );
            $sql .= $prefix;
            $sql .= "CONSTRAINT FK_{$params['fk_table_name']}_{$params['fk_field_name']} FOREIGN KEY ( {$params['name']} ) REFERENCES {$params['fk_table_name']} ( {$params['fk_field_name']} )";
        }
        return $sql;
    }

    static function alterFieldSQL( &$params, $operation ) {
        $sql  = str_repeat( ' ', 8 );
        $sql .= "ALTER TABLE {$params['tableName']}";

        switch ( $operation ) {
        case 'add':
            $separator = "\n";
            $prefix    = "ADD ";
            $sql       .= self::buildFieldSQL     ( $params, $separator, $prefix );
            $separator = ",\n";
            $sql       .= self::buildPrimaryKeySQL( $params['fields'], $separator, $prefix );
            $sql       .= self::buildIndexSQL     ( $params['fields'], $separator, $prefix );
            $sql       .= self::buildForeignKeySQL( $params['fields'], $separator, $prefix );
            break;
            
        case 'modify':
            $separator = "\n";
            $prefix    = "MODIFY ";
            $sql       .= self::buildFieldSQL     ( $params, $separator, $prefix );
            $separator = ",\n";
            $sql       .= self::buildPrimaryKeySQL( $params['fields'], $separator, $prefix );
            $sql       .= self::buildIndexSQL     ( $params['fields'], $separator, $prefix );
            $sql       .= self::buildForeignKeySQL( $params['fields'], $separator, $prefix );
            break;

        case 'delete':
            $sql  = "DROP {$params['name']}";
            if ( CRM_Utils_Array::value( 'primary', $params ) ) {
                $sql .= ", DROP PRIMARY KEY";
            }
            break;
            if ( CRM_Utils_Array::value( 'searchable', $params ) ) {
                $sql .= ", DROP INDEX INDEX_{$params['name']}";
            }
            if ( CRM_Utils_Array::value( 'fk_table_name', $params ) ) {
                $sql .= ", DROP FOREIGN KEY FK_{$params['fk_table_name']}_{$params['fk_field_name']}";
            }
            break;

        }

        return $sql;
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
        $sql = "DROP TABLE IF EXISTS $tableName";
        $dao =& CRM_Core_DAO::executeQuery( $sql, CRM_Core_DAO::$_nullArray );
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
        $sql =  self::buildQuery( $columnAttributes );
        $dao =& CRM_Core_DAO::executeQuery( $sql, CRM_Core_DAO::$_nullArray );
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
        $sql =  "ALTER TABLE $tableName DROP COLUMN $columnName";
        $dao =& CRM_Core_DAO::executeQuery( $sql, CRM_Core_DAO::$_nullArray );
        return true;
    }

}
?>
