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
    $GLOBALS['_CRM_CORE_DAO_COUNTRY']['_tableName'] =  'crm_country';
$GLOBALS['_CRM_CORE_DAO_COUNTRY']['_fields'] = null;
$GLOBALS['_CRM_CORE_DAO_COUNTRY']['_links'] = null;
$GLOBALS['_CRM_CORE_DAO_COUNTRY']['_import'] = null;


require_once 'CRM/Core/DAO.php';
require_once 'CRM/Utils/Array.php';
require_once 'CRM/Core/DAO.php';
    require_once 'CRM/Utils/Type.php';
    class CRM_Core_DAO_Country extends CRM_Core_DAO {

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
        * Country Id
        *
        * @var int unsigned
        */
        var $id;

        /**
        * Country Name
        *
        * @var string
        */
        var $name;

        /**
        * ISO Code
        *
        * @var string
        */
        var $iso_code;

        /**
        * National prefix to be used when dialing TO this country.
        *
        * @var string
        */
        var $country_code;

        /**
        * International direct dialing prefix from within the country TO another country
        *
        * @var string
        */
        var $idd_prefix;

        /**
        * Access prefix to call within a country to a different area
        *
        * @var string
        */
        var $ndd_prefix;

        /**
        * class constructor
        *
        * @access public
        * @return crm_country
        */
        function CRM_Core_DAO_Country() 
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
            if (!($GLOBALS['_CRM_CORE_DAO_COUNTRY']['_fields'])) {
                $GLOBALS['_CRM_CORE_DAO_COUNTRY']['_fields'] = array(
                    'id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'required'=>true,
                    ) ,
                    'name'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Country') ,
                        'maxlength'=>64,
                        'size'=>CRM_UTILS_TYPE_BIG,
                        'import'=>true,
                    ) ,
                    'iso_code'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Iso Code') ,
                        'maxlength'=>2,
                        'size'=>CRM_UTILS_TYPE_TWO,
                    ) ,
                    'country_code'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Country Code') ,
                        'maxlength'=>4,
                        'size'=>CRM_UTILS_TYPE_FOUR,
                    ) ,
                    'idd_prefix'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Idd Prefix') ,
                        'maxlength'=>4,
                        'size'=>CRM_UTILS_TYPE_FOUR,
                    ) ,
                    'ndd_prefix'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Ndd Prefix') ,
                        'maxlength'=>4,
                        'size'=>CRM_UTILS_TYPE_FOUR,
                    ) ,
                );
            }
            return $GLOBALS['_CRM_CORE_DAO_COUNTRY']['_fields'];
        }
        /**
        * returns the names of this table
        *
        * @access public
        * @return string
        */
        function getTableName() 
        {
            return $GLOBALS['_CRM_CORE_DAO_COUNTRY']['_tableName'];
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
            if (!($GLOBALS['_CRM_CORE_DAO_COUNTRY']['_import'])) {
                $GLOBALS['_CRM_CORE_DAO_COUNTRY']['_import'] = array();
                $fields = &CRM_Core_DAO_Country::fields();
                foreach($fields as $name=>$field) {
                    if (CRM_Utils_Array::value('import', $field)) {
                        if ($prefix) {
                            $GLOBALS['_CRM_CORE_DAO_COUNTRY']['_import']['Country.'.$name] = &$field;
                        } else {
                            $GLOBALS['_CRM_CORE_DAO_COUNTRY']['_import'][$name] = &$field;
                        }
                    }
                }
            }
            return $GLOBALS['_CRM_CORE_DAO_COUNTRY']['_import'];
        }
    }
?>