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
    require_once 'CRM/DAO.php';
    class CRM_DAO_Note extends CRM_DAO {

        /**
        * static instance to hold the table name
        *
        * @var string
        * @static
        */
        static $_tableName = 'crm_note';
        /**
        * static instance to hold the field values
        *
        * @var array
        * @static
        */
        static $_fields;
        /**
        * static instance to hold the FK relationships
        *
        * @var string
        * @static
        */
        static $_links;
        /**
        * Note ID
        *
        * @var int unsigned
        */
        public $id;

        /**
        * Name of table where item being referenced is stored.
        *
        * @var string
        */
        public $table_name;

        /**
        * Foreign key to the referenced item.
        *
        * @var int unsigned
        */
        public $table_id;

        /**
        * Note and/or Comment.
        *
        * @var text
        */
        public $note;

        /**
        * class constructor
        *
        * @access public
        * @return crm_note
        */
        function __construct() 
        {
            parent::__construct();
        }
        /**
        * returns all the column names of this table
        *
        * @access public
        * @return array
        */
        function &fields() 
        {
            if (!isset(self::$_fields)) {
                self::$_fields = array(
                    'id'=>array(
                        'type'=>CRM_Type::T_INT,
                        'required'=>true,
                    ) ,
                    'table_name'=>array(
                        'type'=>CRM_Type::T_STRING,
                        'required'=>true,
                        'maxlength'=>64,
                        'size'=>30,
                    ) ,
                    'table_id'=>array(
                        'type'=>CRM_Type::T_INT,
                        'required'=>true,
                    ) ,
                    'note'=>array(
                        'type'=>CRM_Type::T_TEXT,
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
    }
?>
