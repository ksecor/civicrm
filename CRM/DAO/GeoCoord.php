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
    class CRM_Contact_DAO_GeoCoord extends CRM_DAO_Base {

        /**
        * static instance to hold the table name
        *
        * @var string
        * @static
        */
        static $_tableName = 'crm_geo_coord';
        /**
        * static instance to hold the field values
        *
        * @var string
        * @static
        */
        static $_fields;
        /**
        * Geo Coord ID
        *
        * @var int unsigned
        */
        public $id;

        /**
        * Projected or unprojected coordinates - projected coordinates (e.g. UTM) may be treated as cartesian by some modules.
        *
        * @var enum('LatLong', 'Projected')
        */
        public $coord_type;

        /**
        * If the coord_type is LATLONG, indicate the unit of angular measure: Degree|Grad|Radian; If the coord_type is Projected, indicate unit of distance measure: Foot|Meter.
        *
        * @var enum('Degree', 'Grad', 'Radian', 'Foot', 'Meter')
        */
        public $coord_units;

        /**
        * Coordinate sys description in Open GIS Consortium WKT (well known text) format - see http://www.opengeospatial.org/docs/01-009.pdf; this is provided for the convenience of the user or third party modules.
        *
        * @var text
        */
        public $coord_ogc_wkt_string;

        /**
        * class constructor
        *
        * @access public
        * @return crm_geo_coord
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
                self::$_fields = array_merge(parent::fields() , array(
                    'id'=>array(
                        'type'=>CRM_Type::T_INT,
                        'required'=>true,
                    ) ,
                    'coord_type'=>array(
                        'type'=>CRM_Type::T_ENUM,
                    ) ,
                    'coord_units'=>array(
                        'type'=>CRM_Type::T_ENUM,
                    ) ,
                    'coord_ogc_wkt_string'=>array(
                        'type'=>CRM_Type::T_TEXT,
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
