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
    class CRM_Contact_DAO_Location extends CRM_DAO {

        /**
        * static instance to hold the table name
        *
        * @var string
        * @static
        */
        static $_tableName = 'crm_location';
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
        * Unique Location ID
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
        * FK to Location Type ID
        *
        * @var int unsigned
        */
        public $location_type_id;

        /**
        * Is this the primary location for the contact. (allow only ONE primary location / contact.)
        *
        * @var boolean
        */
        public $is_primary;

        /**
        * class constructor
        *
        * @access public
        * @return crm_location
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
                    'location_type_id'=>'crm_location_type:id',
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
                    'location_type_id'=>array(
                        'type'=>CRM_Type::T_INT,
                        'required'=>true,
                    ) ,
                    'is_primary'=>array(
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
