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
    class CRM_Contact_DAO_Address extends CRM_DAO {

        /**
        * static instance to hold the table name
        *
        * @var string
        * @static
        */
        static $_tableName = 'crm_address';
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
        * Unique Address ID
        *
        * @var int unsigned
        */
        public $id;

        /**
        * Which Location does this address belong to.
        *
        * @var int unsigned
        */
        public $location_id;

        /**
        * Concatenation of all routable street address components (prefix, street number, street name, suffix, unit number OR P.O. Box. Apps should be able to determine physical location with this data (for mapping, mail delivery, etc.).
        *
        * @var string
        */
        public $street_address;

        /**
        * Numeric portion of address number on the street, e.g. For 112A Main St, the street_number = 112.
        *
        * @var int
        */
        public $street_number;

        /**
        * Non-numeric portion of address number on the street, e.g. For 112A Main St, the street_number_suffix = A
        *
        * @var string
        */
        public $street_number_suffix;

        /**
        * Directional prefix, e.g. SE Main St, SE is the prefix.
        *
        * @var string
        */
        public $street_number_predirectional;

        /**
        * Actual street name, excluding St, Dr, Rd, Ave, e.g. For 112 Main St, the street_name = Main.
        *
        * @var string
        */
        public $street_name;

        /**
        * St, Rd, Dr, etc.
        *
        * @var string
        */
        public $street_type;

        /**
        * Directional prefix, e.g. Main St S, S is the suffix.
        *
        * @var string
        */
        public $street_number_postdirectional;

        /**
        * Secondary unit designator, e.g. Apt 3 or Unit # 14, or Bldg 1200
        *
        * @var string
        */
        public $street_unit;

        /**
        * Supplemental Address Information, Line 1
        *
        * @var string
        */
        public $supplemental_address_1;

        /**
        * Supplemental Address Information, Line 2
        *
        * @var string
        */
        public $supplemental_address_2;

        /**
        * Supplemental Address Information, Line 3
        *
        * @var string
        */
        public $supplemental_address_3;

        /**
        * City, Town or Village Name.
        *
        * @var string
        */
        public $city;

        /**
        * Which County does this address belong to.
        *
        * @var int unsigned
        */
        public $county_id;

        /**
        * Which State_Province does this address belong to.
        *
        * @var int unsigned
        */
        public $state_province_id;

        /**
        * Store both US (zip5) AND international postal codes. App is responsible for country/region appropriate validation.
        *
        * @var string
        */
        public $postal_code;

        /**
        * Store the suffix, like the +4 part in the USPS system.
        *
        * @var string
        */
        public $postal_code_suffix;

        /**
        * USPS Bulk mailing code.
        *
        * @var string
        */
        public $usps_adc;

        /**
        * Which Country does this address belong to.
        *
        * @var int unsigned
        */
        public $country_id;

        /**
        * Which Geo_Coord does this address belong to.
        *
        * @var int unsigned
        */
        public $geo_coord_id;

        /**
        * Latitude or UTM (Universal Transverse Mercator Grid) Northing.
        *
        * @var float
        */
        public $geo_code_1;

        /**
        * Longitude or UTM (Universal Transverse Mercator Grid) Easting.
        *
        * @var float
        */
        public $geo_code_2;

        /**
        * Timezone expressed as a UTC offset - e.g. United States CST would be written as "UTC-6".
        *
        * @var string
        */
        public $timezone;

        /**
        * Optional misc info (e.g. delivery instructions) for this address.
        *
        * @var string
        */
        public $address_note;

        /**
        * class constructor
        *
        * @access public
        * @return crm_address
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
                    'location_id'=>'crm_location:id',
                    'county_id'=>'crm_county:id',
                    'state_province_id'=>'crm_state_province:id',
                    'country_id'=>'crm_country:id',
                    'geo_coord_id'=>'crm_geo_coord:id',
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
                    'location_id'=>array(
                        'type'=>CRM_Type::T_INT,
                        'required'=>true,
                    ) ,
                    'street_address'=>array(
                        'type'=>CRM_Type::T_STRING,
                        'maxlength'=>96,
                        'size'=>40,
                    ) ,
                    'street_number'=>array(
                        'type'=>CRM_Type::T_INT,
                    ) ,
                    'street_number_suffix'=>array(
                        'type'=>CRM_Type::T_STRING,
                        'maxlength'=>8,
                        'size'=>8,
                    ) ,
                    'street_number_predirectional'=>array(
                        'type'=>CRM_Type::T_STRING,
                        'maxlength'=>8,
                        'size'=>8,
                    ) ,
                    'street_name'=>array(
                        'type'=>CRM_Type::T_STRING,
                        'maxlength'=>64,
                        'size'=>30,
                    ) ,
                    'street_type'=>array(
                        'type'=>CRM_Type::T_STRING,
                        'maxlength'=>8,
                        'size'=>8,
                    ) ,
                    'street_number_postdirectional'=>array(
                        'type'=>CRM_Type::T_STRING,
                        'maxlength'=>8,
                        'size'=>8,
                    ) ,
                    'street_unit'=>array(
                        'type'=>CRM_Type::T_STRING,
                        'maxlength'=>16,
                        'size'=>16,
                    ) ,
                    'supplemental_address_1'=>array(
                        'type'=>CRM_Type::T_STRING,
                        'maxlength'=>96,
                        'size'=>40,
                    ) ,
                    'supplemental_address_2'=>array(
                        'type'=>CRM_Type::T_STRING,
                        'maxlength'=>96,
                        'size'=>40,
                    ) ,
                    'supplemental_address_3'=>array(
                        'type'=>CRM_Type::T_STRING,
                        'maxlength'=>96,
                        'size'=>40,
                    ) ,
                    'city'=>array(
                        'type'=>CRM_Type::T_STRING,
                        'maxlength'=>64,
                        'size'=>30,
                    ) ,
                    'county_id'=>array(
                        'type'=>CRM_Type::T_INT,
                        'required'=>true,
                    ) ,
                    'state_province_id'=>array(
                        'type'=>CRM_Type::T_INT,
                        'required'=>true,
                    ) ,
                    'postal_code'=>array(
                        'type'=>CRM_Type::T_STRING,
                        'maxlength'=>12,
                        'size'=>12,
                    ) ,
                    'postal_code_suffix'=>array(
                        'type'=>CRM_Type::T_STRING,
                        'maxlength'=>12,
                        'size'=>12,
                    ) ,
                    'usps_adc'=>array(
                        'type'=>CRM_Type::T_STRING,
                        'maxlength'=>32,
                        'size'=>20,
                    ) ,
                    'country_id'=>array(
                        'type'=>CRM_Type::T_INT,
                        'required'=>true,
                    ) ,
                    'geo_coord_id'=>array(
                        'type'=>CRM_Type::T_INT,
                        'required'=>true,
                    ) ,
                    'geo_code_1'=>array(
                        'type'=>CRM_Type::T_FLOAT,
                    ) ,
                    'geo_code_2'=>array(
                        'type'=>CRM_Type::T_FLOAT,
                    ) ,
                    'timezone'=>array(
                        'type'=>CRM_Type::T_STRING,
                        'maxlength'=>8,
                        'size'=>8,
                    ) ,
                    'address_note'=>array(
                        'type'=>CRM_Type::T_STRING,
                        'maxlength'=>255,
                        'size'=>50,
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
