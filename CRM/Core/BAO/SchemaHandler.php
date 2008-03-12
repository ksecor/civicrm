<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
        $dao->free();

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
                $sql       .= self::buildForeignKeySQL ( $field, $separator, $prefix, $params['name'] );
            }
        }
        $sql .= "\n) {$params['attributes']};";
        return $sql;
    }

    static function buildFieldSQL( &$params, $separator, $prefix ) {
        $sql .= $separator;
        $sql .= str_repeat( ' ', 8 );
        $sql .= $prefix;
        $sql .= "`{$params['name']}` {$params['type']}";

        if ( CRM_Utils_Array::value( 'required', $params ) ) {
            $sql .= " NOT NULL";
        }

        if ( CRM_Utils_Array::value( 'attributes', $params ) ) {
            $sql .= " {$params['attributes']}";
        }

        if ( CRM_Utils_Array::value( 'default', $params ) &&
             $params['type'] != 'text' ) {
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

    static function buildSearchIndexSQL( &$params, $separator, $prefix, $dropIndex = false ) {
        $sql     = null;

        // dont index blob
        if ( $params['type'] == 'text' ) {
            return $sql;
        }

        if ( $dropIndex ) {
            $sql .= $separator;
            $sql .= str_repeat( ' ', 8 );
            $sql .= "DROP INDEX INDEX_{$params['name']}";
        }

        if ( CRM_Utils_Array::value( 'searchable', $params ) ) {
            // optimize this, we dont need to drop and recreate the index
            if ( $dropIndex ) {
                return null;
            }
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
            $indexName  = 'unique';
        } else {
            $sql       .= 'INDEX';
            $indexName  = 'index';
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

    static function buildForeignKeySQL( &$params, $separator, $prefix, $tableName ) {
        $sql = null;
        if ( CRM_Utils_Array::value( 'fk_table_name', $params ) &&
             CRM_Utils_Array::value( 'fk_field_name', $params ) ) {
            $sql .= $separator;
            $sql .= str_repeat( ' ', 8 );
            $sql .= $prefix;
            $sql .= "CONSTRAINT FK_{$tableName}_{$params['name']} FOREIGN KEY ( `{$params['name']}` ) REFERENCES {$params['fk_table_name']} ( {$params['fk_field_name']} ) ";
            $sql .= CRM_Utils_Array::value( 'fk_attributes', $params );
        }
        return $sql;
    }

    static function alterFieldSQL( &$params, $dropIndex = false ) {
        $sql  = str_repeat( ' ', 8 );
        $sql .= "ALTER TABLE {$params['table_name']}";

        // lets suppress the required flag, since that can cause sql issue
        $params['required'] = false;

        switch ( $params['operation'] ) {
        case 'add':
            $separator = "\n";
            $prefix    = "ADD ";
            $sql       .= self::buildFieldSQL      ( $params, $separator, "ADD COLUMN " );
            $separator = ",\n";
            $sql       .= self::buildPrimaryKeySQL ( $params, $separator, "ADD PRIMARY KEY " );
            $sql       .= self::buildSearchIndexSQL( $params, $separator, "ADD INDEX " );
            $sql       .= self::buildForeignKeySQL ( $params, $separator, "ADD ", $params['table_name'] );
            break;
            
        case 'modify':
            $separator = "\n";
            $prefix    = "MODIFY ";
            $sql      .= self::buildFieldSQL      ( $params, $separator, $prefix );
            $separator = ",\n";
            $sql      .= self::buildSearchIndexSQL( $params, $separator, "ADD INDEX ", $dropIndex );
            break;

        case 'delete':
            $sql  .= " DROP COLUMN `{$params['name']}`";
            if ( CRM_Utils_Array::value( 'primary', $params ) ) {
                $sql .= ", DROP PRIMARY KEY";
            }
            if ( CRM_Utils_Array::value( 'searchable', $params ) ) {
                $sql .= ", DROP INDEX INDEX_{$params['name']}";
            }
            if ( CRM_Utils_Array::value( 'fk_table_name', $params ) ) {
                $sql .= ", DROP FOREIGN KEY FK_{$params['table_name']}_{$params['name']}";
            }
            break;

        }

        $dao =& CRM_Core_DAO::executeQuery( $sql, CRM_Core_DAO::$_nullArray );
        $dao->free();

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
        $sql = "DROP TABLE $tableName";
        $dao =& CRM_Core_DAO::executeQuery( $sql, CRM_Core_DAO::$_nullArray );
    }

    static function dropColumn( $tableName, $columnName ) 
    {
        $sql = "ALTER TABLE $tableName DROP COLUMN $columnName";
        $dao =& CRM_Core_DAO::executeQuery( $sql, CRM_Core_DAO::$_nullArray );
    }
}

?>
