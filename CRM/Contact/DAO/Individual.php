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
    class CRM_Contact_DAO_Individual extends CRM_DAO_Base {

        /**
        * static instance to hold the table name
        *
        * @var string
        * @static
        */
        static $_tableName = 'crm_individual';
        /**
        * static instance to hold the field values
        *
        * @var string
        * @static
        */
        static $_fields;
        /**
        * Unique Individual ID
        *
        * @var int unsigned
        */
        public $id;

        /**
        * FK to Contact ID
        *
        * @var int unsigned
        */
        public $contact_id;

        /**
        * First Name.
        *
        * @var string
        */
        public $first_name;

        /**
        * Middle Name.
        *
        * @var string
        */
        public $middle_name;

        /**
        * Last Name.
        *
        * @var string
        */
        public $last_name;

        /**
        * Prefix to Name.
        *
        * @var string
        */
        public $prefix;

        /**
        * Suffix to Name.
        *
        * @var string
        */
        public $suffix;

        /**
        * Formatted name representing preferred format for display/print/other output.
        *
        * @var string
        */
        public $display_name;

        /**
        * Preferred greeting format.
        *
        * @var enum('Formal', 'Informal', 'Honorific', 'Custom', 'Other')
        */
        public $greeting_type;

        /**
        * Custom greeting message.
        *
        * @var string
        */
        public $custom_greeting;

        /**
        *
        * @var string
        */
        public $job_title;

        /**
        *
        * @var enum('Female', 'Male', 'Transgender')
        */
        public $gender;

        /**
        *
        * @var date
        */
        public $birth_date;

        /**
        *
        * @var boolean
        */
        public $is_deceased;

        /**
        * OPTIONAL FK to crm_contact_household record. If NOT NULL, direct phone communications to household rather than individual location.
        *
        * @var int unsigned
        */
        public $phone_to_household_id;

        /**
        * OPTIONAL FK to crm_contact_household record. If NOT NULL, direct phone communications to household rather than individual location.
        *
        * @var int unsigned
        */
        public $email_to_household_id;

        /**
        * OPTIONAL FK to crm_contact_household record. If NOT NULL, direct mail communications to household rather than individual location.
        *
        * @var int unsigned
        */
        public $mail_to_household_id;

        /**
        * class constructor
        *
        * @access public
        * @return crm_individual
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
                    'contact_id'=>'crm_contact:id',
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
                    'contact_id'=>array(
                        'type'=>CRM_Type::T_INT,
                        'required'=>true,
                    ) ,
                    'first_name'=>array(
                        'type'=>CRM_Type::T_STRING,
                        'length'=>64,
                    ) ,
                    'middle_name'=>array(
                        'type'=>CRM_Type::T_STRING,
                        'length'=>64,
                    ) ,
                    'last_name'=>array(
                        'type'=>CRM_Type::T_STRING,
                        'length'=>64,
                    ) ,
                    'prefix'=>array(
                        'type'=>CRM_Type::T_STRING,
                        'length'=>64,
                    ) ,
                    'suffix'=>array(
                        'type'=>CRM_Type::T_STRING,
                        'length'=>64,
                    ) ,
                    'display_name'=>array(
                        'type'=>CRM_Type::T_STRING,
                        'length'=>128,
                    ) ,
                    'greeting_type'=>array(
                        'type'=>CRM_Type::T_ENUM,
                    ) ,
                    'custom_greeting'=>array(
                        'type'=>CRM_Type::T_STRING,
                        'length'=>128,
                    ) ,
                    'job_title'=>array(
                        'type'=>CRM_Type::T_STRING,
                        'length'=>64,
                    ) ,
                    'gender'=>array(
                        'type'=>CRM_Type::T_ENUM,
                    ) ,
                    'birth_date'=>array(
                        'type'=>CRM_Type::T_DATE,
                    ) ,
                    'is_deceased'=>array(
                        'type'=>CRM_Type::T_BOOLEAN,
                    ) ,
                    'phone_to_household_id'=>array(
                        'type'=>CRM_Type::T_INT,
                    ) ,
                    'email_to_household_id'=>array(
                        'type'=>CRM_Type::T_INT,
                    ) ,
                    'mail_to_household_id'=>array(
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
