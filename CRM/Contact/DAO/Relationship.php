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
    class CRM_Contact_DAO_Relationship extends CRM_DAO {

        /**
        * static instance to hold the table name
        *
        * @var string
        * @static
        */
        static $_tableName = 'crm_relationship';
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
        * Relationship ID
        *
        * @var int unsigned
        */
        public $id;

        /**
        * id of the first contact
        *
        * @var int unsigned
        */
        public $contact_id_a;

        /**
        * id of the second contact
        *
        * @var int unsigned
        */
        public $contact_id_b;

        /**
        * id of the relationship
        *
        * @var int unsigned
        */
        public $relationship_type_id;

        /**
        * date when the relationship started
        *
        * @var date
        */
        public $start_date;

        /**
        * date when the relationship ended
        *
        * @var date
        */
        public $end_date;

        /**
        * is the relationship active ?
        *
        * @var boolean
        */
        public $is_active;

        /**
        * class constructor
        *
        * @access public
        * @return crm_relationship
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
            if (!isset(self::$_links)) {
                self::$_links = array(
                    'contact_id_a'=>'crm_contact:id',
                    'contact_id_b'=>'crm_contact:id',
                    'relationship_type_id'=>'crm_relationship_type:id',
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
            if (!isset(self::$_fields)) {
                self::$_fields = array(
                    'id'=>array(
                        'type'=>CRM_Type::T_INT,
                        'required'=>true,
                    ) ,
                    'contact_id_a'=>array(
                        'type'=>CRM_Type::T_INT,
                        'required'=>true,
                    ) ,
                    'contact_id_b'=>array(
                        'type'=>CRM_Type::T_INT,
                        'required'=>true,
                    ) ,
                    'relationship_type_id'=>array(
                        'type'=>CRM_Type::T_INT,
                        'required'=>true,
                    ) ,
                    'start_date'=>array(
                        'type'=>CRM_Type::T_DATE,
                    ) ,
                    'end_date'=>array(
                        'type'=>CRM_Type::T_DATE,
                    ) ,
                    'is_active'=>array(
                        'type'=>CRM_Type::T_BOOLEAN,
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
