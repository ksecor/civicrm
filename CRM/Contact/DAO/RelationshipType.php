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
    class CRM_Contact_DAO_RelationshipType extends CRM_DAO_Base {

        /**
        * static instance to hold the table name
        *
        * @var string
        * @static
        */
        static $_tableName = 'crm_relationship_type';
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
        * name/label for relationship of contact_a to contact_b.
        *
        * @var string
        */
        public $name_a_b;

        /**
        * Optional name/label for relationship of contact_b to contact_a.
        *
        * @var string
        */
        public $name_b_a;

        /**
        * Optional verbose description of the category.
        *
        * @var string
        */
        public $description;

        /**
        * If defined, contact_a in a relationship of this type must be a specific contact_type.
        *
        * @var enum('Individual', 'Organization', 'Household')
        */
        public $contact_type_a;

        /**
        * If defined, contact_b in a relationship of this type must be a specific contact_type.
        *
        * @var enum('Individual', 'Organization', 'Household')
        */
        public $contact_type_b;

        /**
        * Is this location type a predefined system location?
        *
        * @var boolean
        */
        public $is_reserved;

        /**
        * class constructor
        *
        * @access public
        * @return crm_relationship_type
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
                    'name_a_b'=>array(
                        'type'=>CRM_Type::T_STRING,
                        'length'=>64,
                    ) ,
                    'name_b_a'=>array(
                        'type'=>CRM_Type::T_STRING,
                        'length'=>64,
                    ) ,
                    'description'=>array(
                        'type'=>CRM_Type::T_STRING,
                        'length'=>255,
                    ) ,
                    'contact_type_a'=>array(
                        'type'=>CRM_Type::T_ENUM,
                    ) ,
                    'contact_type_b'=>array(
                        'type'=>CRM_Type::T_ENUM,
                    ) ,
                    'is_reserved'=>array(
                        'type'=>CRM_Type::T_BOOLEAN,
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
