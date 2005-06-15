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
    *
    * @package CRM
    * @author Donald A. Lobo <lobo@yahoo.com>
    * @copyright Donald A. Lobo 01/15/2005
    * $Id$
    *
    */
    require_once 'CRM/Core/DAO.php';
    require_once 'CRM/Utils/Type.php';
    class CRM_Core_DAO_CustomOption extends CRM_Core_DAO {

        /**
        * static instance to hold the table name
        *
        * @var string
        * @static
        */
        static $_tableName = 'crm_custom_option';
        /**
        * static instance to hold the field values
        *
        * @var array
        * @static
        */
        static $_fields = null;
        /**
        * static instance to hold the FK relationships
        *
        * @var string
        * @static
        */
        static $_links = null;
        /**
        * static instance to hold the values that can
        * be imported / apu
        *
        * @var array
        * @static
        */
        static $_import = null;
        /**
        * Unique Custom Option ID
        *
        * @var int unsigned
        */
        public $id;

        /**
        * FK to crm_custom_field.
        *
        * @var int unsigned
        */
        public $custom_field_id;

        /**
        * Label for option
        *
        * @var string
        */
        public $label;

        /**
        * Value of the option (when form is submitted)
        *
        * @var string
        */
        public $value;

        /**
        * Order in which the options are displayed
        *
        * @var int
        */
        public $weight;

        /**
        * Is this property active?
        *
        * @var boolean
        */
        public $is_active;

        /**
        * class constructor
        *
        * @access public
        * @return crm_custom_option
        */
        function __construct() 
        {
            parent::__construct();
        }
        /**
        * return foreign links
        *
        * @access public
        * @return array
        */
        function &links() 
        {
            // does not work with php4
            //if ( ! isset( self::$_links ) ) {
            if (!(self::$_links)) {
                self::$_links = array(
                    'custom_field_id'=>'crm_custom_field:id',
                );
            }
            return self::$_links;
        }
        /**
        * returns all the column names of this table
        *
        * @access public
        * @return array
        */
        function &fields() 
        {
            //if ( ! isset( self::$_fields ) ) {
            if (!(self::$_fields)) {
                self::$_fields = array(
                    'id'=>array(
                        'type'=>CRM_Utils_Type::T_INT,
                        'required'=>true,
                    ) ,
                    'custom_field_id'=>array(
                        'type'=>CRM_Utils_Type::T_INT,
                        'required'=>true,
                    ) ,
                    'label'=>array(
                        'type'=>CRM_Utils_Type::T_STRING,
                        'title'=>ts('Label') ,
                        'maxlength'=>64,
                        'size'=>CRM_Utils_Type::BIG,
                    ) ,
                    'value'=>array(
                        'type'=>CRM_Utils_Type::T_STRING,
                        'title'=>ts('Value') ,
                        'maxlength'=>64,
                        'size'=>CRM_Utils_Type::BIG,
                    ) ,
                    'weight'=>array(
                        'type'=>CRM_Utils_Type::T_INT,
                        'title'=>ts('Weight') ,
                        'required'=>true,
                    ) ,
                    'is_active'=>array(
                        'type'=>CRM_Utils_Type::T_BOOLEAN,
                    ) ,
                );
            }
            return self::$_fields;
        }
        /**
        * returns the names of this table
        *
        * @access public
        * @return string
        */
        function getTableName() 
        {
            return self::$_tableName;
        }
        /**
        * returns the list of fields that can be imported
        *
        * @access public
        * return array
        */
        function &import($prefix = false) 
        {
            //if ( ! isset( self::$_import ) ) {
            if (!(self::$_import)) {
                self::$_import = array();
                $fields = &self::fields();
                foreach($fields as $name=>$field) {
                    if (CRM_Utils_Array::value('import', $field)) {
                        if ($prefix) {
                            self::$_import['CustomOption.'.$name] = &$fields[$name];
                        } else {
                            self::$_import[$name] = &$fields[$name];
                        }
                    }
                }
            }
            return self::$_import;
        }
    }
?>