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
    class CRM_Contact_DAO_Contact extends CRM_DAO_Base {

        /**
        * static instance to hold the table name
        *
        * @var string
        * @static
        */
        static $_tableName = 'crm_contact';
        /**
        * static instance to hold the field values
        *
        * @var string
        * @static
        */
        static $_fields;
        /**
        * Unique Contact ID
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
        * Type of Contact.
        *
        * @var enum('Individual', 'Organization', 'Household')
        */
        public $contact_type;

        /**
        * May be used for SSN, EIN/TIN, Household ID (census) or other applicable unique legal/government ID.
        *
        * @var string
        */
        public $legal_id;

        /**
        * Unique trusted external ID (generally from a legacy app/datasource). Particularly useful for deduping operations.
        *
        * @var string
        */
        public $external_id;

        /**
        * Name used for sorting different contact types
        *
        * @var string
        */
        public $sort_name;

        /**
        * optional "home page" URL for this contact.
        *
        * @var string
        */
        public $home_URL;

        /**
        * optional URL for preferred image (photo, logo, etc.) to display for this contact.
        *
        * @var string
        */
        public $image_URL;

        /**
        * where domain_id contact come from, e.g. import, donate module insert...
        *
        * @var string
        */
        public $source;

        /**
        * What is the preferred mode of communication.
        *
        * @var enum('Phone', 'Email', 'Post')
        */
        public $preferred_communication_method;

        /**
        *
        * @var boolean
        */
        public $do_not_phone;

        /**
        *
        * @var boolean
        */
        public $do_not_email;

        /**
        *
        * @var boolean
        */
        public $do_not_mail;

        /**
        * Key for validating requests related to this contact.
        *
        * @var int unsigned
        */
        public $hash;

        /**
        * class constructor
        *
        * @access public
        * @return crm_contact
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
                    'contact_type'=>array(
                        'type'=>CRM_Type::T_ENUM,
                    ) ,
                    'legal_id'=>array(
                        'type'=>CRM_Type::T_STRING,
                        'length'=>32,
                    ) ,
                    'external_id'=>array(
                        'type'=>CRM_Type::T_STRING,
                        'length'=>32,
                    ) ,
                    'sort_name'=>array(
                        'type'=>CRM_Type::T_STRING,
                        'length'=>64,
                    ) ,
                    'home_URL'=>array(
                        'type'=>CRM_Type::T_STRING,
                        'length'=>128,
                    ) ,
                    'image_URL'=>array(
                        'type'=>CRM_Type::T_STRING,
                        'length'=>128,
                    ) ,
                    'source'=>array(
                        'type'=>CRM_Type::T_STRING,
                        'length'=>255,
                    ) ,
                    'preferred_communication_method'=>array(
                        'type'=>CRM_Type::T_ENUM,
                    ) ,
                    'do_not_phone'=>array(
                        'type'=>CRM_Type::T_BOOLEAN,
                    ) ,
                    'do_not_email'=>array(
                        'type'=>CRM_Type::T_BOOLEAN,
                    ) ,
                    'do_not_mail'=>array(
                        'type'=>CRM_Type::T_BOOLEAN,
                    ) ,
                    'hash'=>array(
                        'type'=>CRM_Type::T_INT,
                        'required'=>true,
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
