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
    require_once 'CRM/DAO/Base.php';
    class CRM_Contact_DAO_Category extends CRM_DAO_Base {

        /**
        * static instance to hold the table name
        *
        * @var string
        * @static
        */
        static $_tableName = 'crm_category';
        /**
        * static instance to hold the field values
        *
        * @var string
        * @static
        */
        static $_fields;
        /**
        * Category ID
        *
        * @var int unsigned
        */
        public $id;

        /**
        * Which Domain owns this contact
        *
        * @var int unsigned
        */
        public $domain_id;

        /**
        * Name of Category.
        *
        * @var string
        */
        public $name;

        /**
        * Optional verbose description of the category.
        *
        * @var string
        */
        public $description;

        /**
        * Optional parent id for this category.
        *
        * @var int unsigned
        */
        public $parent_id;

        /**
        * class constructor
        *
        * @access public
        * @return crm_category
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
            static $links;
            if (!isset($links)) {
                $links = array(
                    'domain_id'=>'crm_domain:id',
                );
            }
            return $links;
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
                self::$_fields = array_merge(parent::fields() , array(
                    'id'=>array(
                        'type'=>CRM_Type::T_INT,
                        'required'=>true,
                    ) ,
                    'domain_id'=>array(
                        'type'=>CRM_Type::T_INT,
                        'required'=>true,
                    ) ,
                    'name'=>array(
                        'type'=>CRM_Type::T_STRING,
                        'length'=>64,
                    ) ,
                    'description'=>array(
                        'type'=>CRM_Type::T_STRING,
                        'length'=>255,
                    ) ,
                    'parent_id'=>array(
                        'type'=>CRM_Type::T_INT,
                    ) ,
                ));
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
