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
    $GLOBALS['_CRM_CONTACT_DAO_ADDRESS']['_tableName'] =  'crm_address';
$GLOBALS['_CRM_CONTACT_DAO_ADDRESS']['_fields'] = null;
$GLOBALS['_CRM_CONTACT_DAO_ADDRESS']['_links'] = null;
$GLOBALS['_CRM_CONTACT_DAO_ADDRESS']['_import'] = null;


require_once 'CRM/Core/DAO.php';
require_once 'CRM/Utils/Array.php';
require_once 'CRM/Core/DAO/StateProvince.php';
require_once 'CRM/Core/DAO/Country.php';
require_once 'CRM/Core/DAO.php';
    require_once 'CRM/Utils/Type.php';
    class CRM_Contact_DAO_Address extends CRM_Core_DAO {

        /**
        * static instance to hold the table name
        *
        * @var string
        * @static
        */
        
        /**
        * static instance to hold the field values
        *
        * @var array
        * @static
        */
        
        /**
        * static instance to hold the FK relationships
        *
        * @var string
        * @static
        */
        
        /**
        * static instance to hold the values that can
        * be imported / apu
        *
        * @var array
        * @static
        */
        
        /**
        * Unique Address ID
        *
        * @var int unsigned
        */
        var $id;

        /**
        * Which Location does this address belong to.
        *
        * @var int unsigned
        */
        var $location_id;

        /**
        * Concatenation of all routable street address components (prefix, street number, street name, suffix, unit number OR P.O. Box. Apps should be able to determine physical location with this data (for mapping, mail delivery, etc.).
        *
        * @var string
        */
        var $street_address;

        /**
        * Numeric portion of address number on the street, e.g. For 112A Main St, the street_number = 112.
        *
        * @var int
        */
        var $street_number;

        /**
        * Non-numeric portion of address number on the street, e.g. For 112A Main St, the street_number_suffix = A
        *
        * @var string
        */
        var $street_number_suffix;

        /**
        * Directional prefix, e.g. SE Main St, SE is the prefix.
        *
        * @var string
        */
        var $street_number_predirectional;

        /**
        * Actual street name, excluding St, Dr, Rd, Ave, e.g. For 112 Main St, the street_name = Main.
        *
        * @var string
        */
        var $street_name;

        /**
        * St, Rd, Dr, etc.
        *
        * @var string
        */
        var $street_type;

        /**
        * Directional prefix, e.g. Main St S, S is the suffix.
        *
        * @var string
        */
        var $street_number_postdirectional;

        /**
        * Secondary unit designator, e.g. Apt 3 or Unit # 14, or Bldg 1200
        *
        * @var string
        */
        var $street_unit;

        /**
        * Supplemental Address Information, Line 1
        *
        * @var string
        */
        var $supplemental_address_1;

        /**
        * Supplemental Address Information, Line 2
        *
        * @var string
        */
        var $supplemental_address_2;

        /**
        * Supplemental Address Information, Line 3
        *
        * @var string
        */
        var $supplemental_address_3;

        /**
        * City, Town or Village Name.
        *
        * @var string
        */
        var $city;

        /**
        * Which County does this address belong to.
        *
        * @var int unsigned
        */
        var $county_id;

        /**
        * Which State_Province does this address belong to.
        *
        * @var int unsigned
        */
        var $state_province_id;

        /**
        * Store both US (zip5) AND international postal codes. App is responsible for country/region appropriate validation.
        *
        * @var string
        */
        var $postal_code;

        /**
        * Store the suffix, like the +4 part in the USPS system.
        *
        * @var string
        */
        var $postal_code_suffix;

        /**
        * USPS Bulk mailing code.
        *
        * @var string
        */
        var $usps_adc;

        /**
        * Which Country does this address belong to.
        *
        * @var int unsigned
        */
        var $country_id;

        /**
        * Which Geo_Coord does this address belong to.
        *
        * @var int unsigned
        */
        var $geo_coord_id;

        /**
        * Latitude or UTM (Universal Transverse Mercator Grid) Northing.
        *
        * @var float
        */
        var $geo_code_1;

        /**
        * Longitude or UTM (Universal Transverse Mercator Grid) Easting.
        *
        * @var float
        */
        var $geo_code_2;

        /**
        * Timezone expressed as a UTC offset - e.g. United States CST would be written as "UTC-6".
        *
        * @var string
        */
        var $timezone;

        /**
        * Optional misc info (e.g. delivery instructions) for this address.
        *
        * @var string
        */
        var $note;

        /**
        * class constructor
        *
        * @access public
        * @return crm_address
        */
        function CRM_Contact_DAO_Address() 
        {
            parent::CRM_Core_DAO();
        }
        /**
        * return foreign links
        *
        * @access public
        * @return array
        */
        function &links() 
        {
            // does not work with php4
            //if ( ! isset( self::$_links ) ) {
            if (!($GLOBALS['_CRM_CONTACT_DAO_ADDRESS']['_links'])) {
                $GLOBALS['_CRM_CONTACT_DAO_ADDRESS']['_links'] = array(
                    'location_id'=>'crm_location:id',
                    'county_id'=>'crm_county:id',
                    'state_province_id'=>'crm_state_province:id',
                    'country_id'=>'crm_country:id',
                    'geo_coord_id'=>'crm_geo_coord:id',
                );
            }
            return $GLOBALS['_CRM_CONTACT_DAO_ADDRESS']['_links'];
        }
        /**
        * returns all the column names of this table
        *
        * @access public
        * @return array
        */
        function &fields() 
        {
            //if ( ! isset( self::$_fields ) ) {
            if (!($GLOBALS['_CRM_CONTACT_DAO_ADDRESS']['_fields'])) {
                $GLOBALS['_CRM_CONTACT_DAO_ADDRESS']['_fields'] = array(
                    'id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'required'=>true,
                    ) ,
                    'location_id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'required'=>true,
                    ) ,
                    'street_address'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Street Address') ,
                        'maxlength'=>96,
                        'size'=>CRM_UTILS_TYPE_HUGE,
                        'import'=>true,
                    ) ,
                    'street_number'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'title'=>ts('Street Number') ,
                    ) ,
                    'street_number_suffix'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Street Number Suffix') ,
                        'maxlength'=>8,
                        'size'=>CRM_UTILS_TYPE_EIGHT,
                    ) ,
                    'street_number_predirectional'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Street Number Predirectional') ,
                        'maxlength'=>8,
                        'size'=>CRM_UTILS_TYPE_EIGHT,
                    ) ,
                    'street_name'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Street Name') ,
                        'maxlength'=>64,
                        'size'=>CRM_UTILS_TYPE_BIG,
                    ) ,
                    'street_type'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Street Type') ,
                        'maxlength'=>8,
                        'size'=>CRM_UTILS_TYPE_EIGHT,
                    ) ,
                    'street_number_postdirectional'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Street Number Postdirectional') ,
                        'maxlength'=>8,
                        'size'=>CRM_UTILS_TYPE_EIGHT,
                    ) ,
                    'street_unit'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Street Unit') ,
                        'maxlength'=>16,
                        'size'=>CRM_UTILS_TYPE_TWELVE,
                    ) ,
                    'supplemental_address_1'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Supplemental Address 1') ,
                        'maxlength'=>96,
                        'size'=>CRM_UTILS_TYPE_HUGE,
                        'import'=>true,
                    ) ,
                    'supplemental_address_2'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Supplemental Address 2') ,
                        'maxlength'=>96,
                        'size'=>CRM_UTILS_TYPE_HUGE,
                        'import'=>true,
                    ) ,
                    'supplemental_address_3'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Supplemental Address 3') ,
                        'maxlength'=>96,
                        'size'=>CRM_UTILS_TYPE_HUGE,
                    ) ,
                    'city'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('City') ,
                        'maxlength'=>64,
                        'size'=>CRM_UTILS_TYPE_BIG,
                        'import'=>true,
                    ) ,
                    'county_id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                    ) ,
                    'state_province_id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                    ) ,
                    'postal_code'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Postal Code') ,
                        'maxlength'=>12,
                        'size'=>CRM_UTILS_TYPE_TWELVE,
                        'import'=>true,
                    ) ,
                    'postal_code_suffix'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Postal Code Suffix') ,
                        'maxlength'=>12,
                        'size'=>CRM_UTILS_TYPE_TWELVE,
                    ) ,
                    'usps_adc'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Usps Adc') ,
                        'maxlength'=>32,
                        'size'=>CRM_UTILS_TYPE_MEDIUM,
                    ) ,
                    'country_id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                    ) ,
                    'geo_coord_id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                    ) ,
                    'geo_code_1'=>array(
                        'type'=>CRM_UTILS_TYPE_T_FLOAT,
                        'title'=>ts('Geo Code 1') ,
                    ) ,
                    'geo_code_2'=>array(
                        'type'=>CRM_UTILS_TYPE_T_FLOAT,
                        'title'=>ts('Geo Code 2') ,
                    ) ,
                    'timezone'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Timezone') ,
                        'maxlength'=>8,
                        'size'=>CRM_UTILS_TYPE_EIGHT,
                    ) ,
                    'note'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Note') ,
                        'maxlength'=>255,
                        'size'=>CRM_UTILS_TYPE_HUGE,
                    ) ,
                );
            }
            return $GLOBALS['_CRM_CONTACT_DAO_ADDRESS']['_fields'];
        }
        /**
        * returns the names of this table
        *
        * @access public
        * @return string
        */
        function getTableName() 
        {
            return $GLOBALS['_CRM_CONTACT_DAO_ADDRESS']['_tableName'];
        }
        /**
        * returns the list of fields that can be imported
        *
        * @access public
        * return array
        */
        function &import($prefix = false) 
        {
            //if ( ! isset( self::$_import ) ) {
            if (!($GLOBALS['_CRM_CONTACT_DAO_ADDRESS']['_import'])) {
                $GLOBALS['_CRM_CONTACT_DAO_ADDRESS']['_import'] = array();
                $fields = &CRM_Contact_DAO_Address::fields();
                foreach($fields as $name=>$field) {
                    if (CRM_Utils_Array::value('import', $field)) {
                        if ($prefix) {
                            $GLOBALS['_CRM_CONTACT_DAO_ADDRESS']['_import']['Address.'.$name] = &$field;
                        } else {
                            $GLOBALS['_CRM_CONTACT_DAO_ADDRESS']['_import'][$name] = &$field;
                        }
                    }
                }
                $GLOBALS['_CRM_CONTACT_DAO_ADDRESS']['_import'] = array_merge($GLOBALS['_CRM_CONTACT_DAO_ADDRESS']['_import'], CRM_Core_DAO_StateProvince::import(true));
                $GLOBALS['_CRM_CONTACT_DAO_ADDRESS']['_import'] = array_merge($GLOBALS['_CRM_CONTACT_DAO_ADDRESS']['_import'], CRM_Core_DAO_Country::import(true));
            }
            return $GLOBALS['_CRM_CONTACT_DAO_ADDRESS']['_import'];
        }
    }
?>