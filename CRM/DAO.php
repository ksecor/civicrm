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


/**
 * Our base DAO class. All DAO classes should inherit from this class.
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'PEAR.php';
require_once 'DB/DataObject.php';

abstract class CRM_DAO extends DB_DataObject {

    const
        NOT_NULL       =   1,
        IS_NULL        =   2,

        DB_DAO_NOTNULL = 128;

    /**
     * the factory class for this application
     * @var object
     */
    static $_factory = null;

    /**
     * Class constructor
     *
     * @return object
     * @access public
     */
    function __construct() {
        $this->initialize( );
        $this->__table = $this->getTableName();
    }
	
    /**
     * initialize the DAO object
     *
     * @param string $dsn   the database connection string
     * @param int    $debug the debug level for DB_DataObject
     *
     * @return void
     * @access private
     */
    function init( $dsn, $debug = 0 ) {
        $options =& PEAR::getStaticProperty('DB_DataObject', 'options');
        $options =  array(
                          'database'         => $dsn,
                          );
    
        if ( $debug ) {
            DB_DataObject::DebugLevel($debug);
        }
    }
	
    /**
     * reset the DAO object. DAO is kinda crappy in that there is an unwritten
     * rule of one query per DAO. We attempt to get around this crappy restricrion
     * by resetting some of DAO's internal fields. Use this with caution
     *
     * @return void
     * @access public
     *
     */
    function reset() {
        
        foreach( array_keys( $this->table() ) as $field ) {
            unset($this->$field);
        }

        /**
         * reset the various DB_DAO structures manually
         */
        $this->_query = array( );
        $this->whereAdd ( );
        $this->selectAdd( );
        $this->joinAdd  ( );
    }
	
    /**
     * Static function to set the factory instance for this class.
     *
     * @param object $factory  the factory application object
     *
     * @return void
     * @access public
     */
    function setFactory(&$factory) {
        self::$_factory =& $factory;
    }
	
    /**
     * Factory method to instantiate a new object from a table name.
     *
     * @return void 
     * @access public
     */
    function factory($table) {
        if ( ! isset( self::$_factory ) ) {
            return parent::factory($table);
        }
		
        return self::$_factory->create($table);
    }
	
    /**
     * Initialization for all DAO objects. Since we access DB_DO programatically
     * we need to set the links manually.
     *
     * @return void
     * @access protected
     */
    function initialize() {
        $links = $this->links();
        if ( empty( $links ) ) {
            return;
        }

        $this->_connect();
    
        if ( !isset($GLOBALS['_DB_DATAOBJECT']['LINKS'][$this->_database]) ) {
            $GLOBALS['_DB_DATAOBJECT']['LINKS'][$this->_database] = array();
        }
	    
        if ( ! array_key_exists( $this->__table, $GLOBALS['_DB_DATAOBJECT']['LINKS'][$this->_database] ) ) {
            $GLOBALS['_DB_DATAOBJECT']['LINKS'][$this->_database][$this->__table] = $links;
        }
    }
	
    /**
     * Defines the default key as 'id'.
     *
     * @access protected
     * @return array
     */
    function keys() {
        static $keys;
        if ( !isset ($keys) ) {
            $keys = array('id');
        }
        return $keys;
    }
    
    /**
     * Tells DB_DataObject which keys use autoincrement.
     * 'id' is autoincrementing by default.
     * 
     * @access protected
     * @return array
     */
    function sequenceKey() {
        static $keys;
        if ( !isset ($keys) ) {
            $keys = array('id', true);
        }
        return $keys;
    }

    /**
     * returns list of FK relationships
     *
     * @access public
     * @return array
     */
    function links( ) {
        return null;
    }


    /**
     * returns all the column names of this table
     *
     * @access public
     * @return array
     */
    abstract function &fields( );

    function table() {
        $fields =& $this->fields();

        $table = array();
        foreach ( $fields as $name => $value ) {
            $table[$name] = $value['type'];
            if ( CRM_Array::value( 'required', $value ) ) {
                $table[$name] += self::DB_DAO_NOTNULL;
            }
        }

        // set the links
        $this->links();

        return $table;
    }

    function save( ) {
        if ($this->id) {
            $this->update();
        } else {
            $this->insert();
        }
        return $this;
    }

    /**
     * Given an associative array of name/value pairs, extract all the values
     * that belong to this object and initialize the object with said values
     *
     * @param array $params (reference ) associative array of name/value pairs
     *
     * @return void
     * @access public
     */
    function copyValues( &$params ) {
        $fields =& $this->fields( );
        foreach ( $fields as $name => &$value ) {
            if ( array_key_exists( $name, $params ) ) {
                $this->$name = $params[$name];
            }
        }
    }

    /**
     * Store all the values from this object in an associative array
     * this is a destructive store, calling function is responsible
     * for keeping sanity of id's
     *
     * @param array $values (reference ) associative array of name/value pairs
     *
     * @return void
     * @access public
     */
    function storeValues( &$values ) {
        $fields =& $this->fields( );
        foreach ( $fields as $name => &$value ) {
            if ( isset( $this->$name ) ) {
                $values[$name] = $this->$name;
            }
        }
    }

    /**
     * Get the size and maxLength attributes for this text field
     * (or for all text fields) in the DAO object.
     *
     * @param string $class     name of DAO class
     * @param string $fieldName field that i'm interested in or null if 
     *                          you want the attributes for all DAO text fields
     *
     * @return array assoc array of name => attribute pairs
     * @access public
     * @static
     */
    function getAttribute( $class, $fieldName = null) {
        eval('$fields =& ' . $class . '::fields( );');
        if ( $fieldName != null ) {
            $field = CRM_Array::value( $fieldName, $fields );
            if ( $field && $field['type'] == CRM_Type::T_STRING ) {
                $maxLength  = CRM_Array::value( 'maxlength', $field );
                $size       = CRM_Array::value( 'size'     , $field );
                if ( $maxLength || $size ) {
                    $attributes = array( );
                    if ( $maxLength ) {
                        $attributes['maxlength'] = $maxLength;
                    }
                    if ( $size ) {
                        $attributes['size'] = $size;
                    }
                    return $attributes;
                }
            }
        } else {
            $attributes = array( );
            foreach ($fields as $name => &$field) {
                if ( $field && $field['type'] == CRM_Type::T_STRING ) {
                    $maxLength  = CRM_Array::value( 'maxlength', $field );
                    $size       = CRM_Array::value( 'size'     , $field );
                    if ( $maxLength || $size ) {
                        if ( $maxLength ) {
                            $attributes[$name]['maxlength'] = $maxLength;
                        }
                        if ( $size ) {
                            $attributes[$name]['size'] = $size;
                        }
                    }
                }
            }
            if ( !empty($attributes)) {
                return $attributes;
            }
        }

        return null;
    }

}

?>