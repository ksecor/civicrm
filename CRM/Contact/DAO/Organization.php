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
    class CRM_Contact_DAO_Organization extends CRM_DAO {

        /**
        * static instance to hold the table name
        *
        * @var string
        * @static
        */
        static $_tableName = 'crm_organization';
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
        * Unique Organization ID
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
        * Organization Name.
        *
        * @var string
        */
        public $organization_name;

        /**
        * Legal Name.
        *
        * @var string
        */
        public $legal_name;

        /**
        * Nick Name.
        *
        * @var string
        */
        public $nick_name;

        /**
        * Standard Industry Classification Code.
        *
        * @var string
        */
        public $sic_code;

        /**
        * Optional FK to Primary Contact for this organization.
        *
        * @var int unsigned
        */
        public $primary_contact_id;

        /**
        * class constructor
        *
        * @access public
        * @return crm_organization
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
                    'contact_id'=>'crm_contact:id',
                    'primary_contact_id'=>'crm_contact:id',
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
                    'contact_id'=>array(
                        'type'=>CRM_Type::T_INT,
                        'required'=>true,
                    ) ,
                    'organization_name'=>array(
                        'type'=>CRM_Type::T_STRING,
                        'maxlength'=>64,
                        'size'=>30,
                    ) ,
                    'legal_name'=>array(
                        'type'=>CRM_Type::T_STRING,
                        'maxlength'=>64,
                        'size'=>30,
                    ) ,
                    'nick_name'=>array(
                        'type'=>CRM_Type::T_STRING,
                        'maxlength'=>64,
                        'size'=>30,
                    ) ,
                    'sic_code'=>array(
                        'type'=>CRM_Type::T_STRING,
                        'maxlength'=>8,
                        'size'=>8,
                    ) ,
                    'primary_contact_id'=>array(
                        'type'=>CRM_Type::T_INT,
                        'required'=>true,
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
