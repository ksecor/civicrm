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
    class CRM_DAO_Country extends CRM_DAO {

        /**
        * static instance to hold the table name
        *
        * @var string
        * @static
        */
        static $_tableName = 'crm_country';
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
        * Country Id
        *
        * @var int unsigned
        */
        public $id;

        /**
        * Country Name
        *
        * @var string
        */
        public $name;

        /**
        * ISO Code
        *
        * @var string
        */
        public $iso_code;

        /**
        * National prefix to be used when dialing TO this country.
        *
        * @var string
        */
        public $country_code;

        /**
        * International direct dialing prefix from within the country TO another country
        *
        * @var string
        */
        public $idd_prefix;

        /**
        * Access prefix to call within a country to a different area
        *
        * @var string
        */
        public $ndd_prefix;

        /**
        * class constructor
        *
        * @access public
        * @return crm_country
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
                    'name'=>array(
                        'type'=>CRM_Type::T_STRING,
                        'maxlength'=>64,
                        'size'=>30,
                    ) ,
                    'iso_code'=>array(
                        'type'=>CRM_Type::T_STRING,
                        'maxlength'=>2,
                        'size'=>2,
                    ) ,
                    'country_code'=>array(
                        'type'=>CRM_Type::T_STRING,
                        'maxlength'=>4,
                        'size'=>4,
                    ) ,
                    'idd_prefix'=>array(
                        'type'=>CRM_Type::T_STRING,
                        'maxlength'=>4,
                        'size'=>4,
                    ) ,
                    'ndd_prefix'=>array(
                        'type'=>CRM_Type::T_STRING,
                        'maxlength'=>4,
                        'size'=>4,
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
