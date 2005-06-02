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
    $GLOBALS['_CRM_CORE_DAO_CUSTOMVALUE']['_tableName'] =  'crm_custom_value';
$GLOBALS['_CRM_CORE_DAO_CUSTOMVALUE']['_fields'] = '';
$GLOBALS['_CRM_CORE_DAO_CUSTOMVALUE']['_links'] = '';
$GLOBALS['_CRM_CORE_DAO_CUSTOMVALUE']['_import'] = '';

require_once 'CRM/Core/DAO.php';
require_once 'CRM/Utils/Array.php';
require_once 'CRM/Core/DAO.php';
    require_once 'CRM/Utils/Type.php';
    class CRM_Core_DAO_CustomValue extends CRM_Core_DAO {

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
        * Unique ID
        *
        * @var int unsigned
        */
        var $id;

        /**
        * Foreign key to crm_ext_property.
        *
        * @var int unsigned
        */
        var $custom_field_id;

        /**
        * physical tablename for entity being extended by this data, e.g. crm_contact
        *
        * @var string
        */
        var $entity_table;

        /**
        * FK to record in the entity table specified by entity_table column.
        *
        * @var int unsigned
        */
        var $entity_id;

        /**
        * stores data for ext property data_type = integer. This col supports signed integers.
        *
        * @var int
        */
        var $int_data;

        /**
        * stores data for ext property data_type = float and money.
        *
        * @var float
        */
        var $float_data;

        /**
        * data for ext property data_type = text.
        *
        * @var string
        */
        var $char_data;

        /**
        * data for ext property data_type = date.
        *
        * @var date
        */
        var $date_data;

        /**
        * data for ext property data_type = memo.
        *
        * @var text
        */
        var $memo_data;

        /**
        * class constructor
        *
        * @access public
        * @return crm_custom_value
        */
        function CRM_Core_DAO_CustomValue() 
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
            if (!($GLOBALS['_CRM_CORE_DAO_CUSTOMVALUE']['_links'])) {
                $GLOBALS['_CRM_CORE_DAO_CUSTOMVALUE']['_links'] = array(
                    'custom_field_id'=>'crm_custom_field:id',
                );
            }
            return $GLOBALS['_CRM_CORE_DAO_CUSTOMVALUE']['_links'];
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
            if (!($GLOBALS['_CRM_CORE_DAO_CUSTOMVALUE']['_fields'])) {
                $GLOBALS['_CRM_CORE_DAO_CUSTOMVALUE']['_fields'] = array(
                    'id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'required'=>true,
                    ) ,
                    'custom_field_id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'required'=>true,
                    ) ,
                    'entity_table'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Entity Table') ,
                        'maxlength'=>64,
                        'size'=>CRM_UTILS_TYPE_BIG,
                    ) ,
                    'entity_id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'required'=>true,
                    ) ,
                    'int_data'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'title'=>ts('Int Data') ,
                    ) ,
                    'float_data'=>array(
                        'type'=>CRM_UTILS_TYPE_T_FLOAT,
                        'title'=>ts('Float Data') ,
                    ) ,
                    'char_data'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Char Data') ,
                        'maxlength'=>255,
                        'size'=>CRM_UTILS_TYPE_HUGE,
                    ) ,
                    'date_data'=>array(
                        'type'=>CRM_UTILS_TYPE_T_DATE,
                        'title'=>ts('Date Data') ,
                    ) ,
                    'memo_data'=>array(
                        'type'=>CRM_UTILS_TYPE_T_TEXT,
                        'title'=>ts('Memo Data') ,
                    ) ,
                );
            }
            return $GLOBALS['_CRM_CORE_DAO_CUSTOMVALUE']['_fields'];
        }
        /**
        * returns the names of this table
        *
        * @access public
        * @return string
        */
        function getTableName() 
        {
            return $GLOBALS['_CRM_CORE_DAO_CUSTOMVALUE']['_tableName'];
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
            if (!($GLOBALS['_CRM_CORE_DAO_CUSTOMVALUE']['_import'])) {
                $GLOBALS['_CRM_CORE_DAO_CUSTOMVALUE']['_import'] = array();
                $fields = &CRM_Core_DAO_CustomValue::fields();
                foreach($fields as $name=>$field) {
                    if (CRM_Utils_Array::value('import', $field)) {
                        if ($prefix) {
                            $GLOBALS['_CRM_CORE_DAO_CUSTOMVALUE']['_import']['CustomValue.'.$name] = &$field;
                        } else {
                            $GLOBALS['_CRM_CORE_DAO_CUSTOMVALUE']['_import'][$name] = &$field;
                        }
                    }
                }
            }
            return $GLOBALS['_CRM_CORE_DAO_CUSTOMVALUE']['_import'];
        }
    }
?>