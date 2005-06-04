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
    $GLOBALS['_CRM_CORE_DAO_GEOCOORD']['_tableName'] =  'crm_geo_coord';
$GLOBALS['_CRM_CORE_DAO_GEOCOORD']['_fields'] = null;
$GLOBALS['_CRM_CORE_DAO_GEOCOORD']['_links'] = null;
$GLOBALS['_CRM_CORE_DAO_GEOCOORD']['_import'] = null;


require_once 'CRM/Core/DAO.php';
require_once 'CRM/Utils/Array.php';
require_once 'CRM/Core/DAO.php';
    require_once 'CRM/Utils/Type.php';
    class CRM_Core_DAO_GeoCoord extends CRM_Core_DAO {

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
        * Geo Coord ID
        *
        * @var int unsigned
        */
        var $id;

        /**
        * Projected or unprojected coordinates - projected coordinates (e.g. UTM) may be treated as cartesian by some modules.
        *
        * @var enum('LatLong', 'Projected')
        */
        var $coord_type;

        /**
        * If the coord_type is LATLONG, indicate the unit of angular measure: Degree|Grad|Radian; If the coord_type is Projected, indicate unit of distance measure: Foot|Meter.
        *
        * @var enum('Degree', 'Grad', 'Radian', 'Foot', 'Meter')
        */
        var $coord_units;

        /**
        * Coordinate sys description in Open GIS Consortium WKT (well known text) format - see http://www.opengeospatial.org/docs/01-009.pdf; this is provided for the convenience of the user or third party modules.
        *
        * @var text
        */
        var $coord_ogc_wkt_string;

        /**
        * class constructor
        *
        * @access public
        * @return crm_geo_coord
        */
        function CRM_Core_DAO_GeoCoord() 
        {
            parent::CRM_Core_DAO();
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
            if (!($GLOBALS['_CRM_CORE_DAO_GEOCOORD']['_fields'])) {
                $GLOBALS['_CRM_CORE_DAO_GEOCOORD']['_fields'] = array(
                    'id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'required'=>true,
                    ) ,
                    'coord_type'=>array(
                        'type'=>CRM_UTILS_TYPE_T_ENUM,
                        'title'=>ts('Coord Type') ,
                    ) ,
                    'coord_units'=>array(
                        'type'=>CRM_UTILS_TYPE_T_ENUM,
                        'title'=>ts('Coord Units') ,
                    ) ,
                    'coord_ogc_wkt_string'=>array(
                        'type'=>CRM_UTILS_TYPE_T_TEXT,
                        'title'=>ts('Coord Ogc Wkt String') ,
                    ) ,
                );
            }
            return $GLOBALS['_CRM_CORE_DAO_GEOCOORD']['_fields'];
        }
        /**
        * returns the names of this table
        *
        * @access public
        * @return string
        */
        function getTableName() 
        {
            return $GLOBALS['_CRM_CORE_DAO_GEOCOORD']['_tableName'];
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
            if (!($GLOBALS['_CRM_CORE_DAO_GEOCOORD']['_import'])) {
                $GLOBALS['_CRM_CORE_DAO_GEOCOORD']['_import'] = array();
                $fields = &CRM_Core_DAO_GeoCoord::fields();
                foreach($fields as $name=>$field) {
                    if (CRM_Utils_Array::value('import', $field)) {
                        if ($prefix) {
                            $GLOBALS['_CRM_CORE_DAO_GEOCOORD']['_import']['GeoCoord.'.$name] = &$field;
                        } else {
                            $GLOBALS['_CRM_CORE_DAO_GEOCOORD']['_import'][$name] = &$field;
                        }
                    }
                }
            }
            return $GLOBALS['_CRM_CORE_DAO_GEOCOORD']['_import'];
        }
    }
?>