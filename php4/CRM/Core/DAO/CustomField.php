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
    $GLOBALS['_CRM_CORE_DAO_CUSTOMFIELD']['_tableName'] =  'crm_custom_field';
$GLOBALS['_CRM_CORE_DAO_CUSTOMFIELD']['_fields'] = '';
$GLOBALS['_CRM_CORE_DAO_CUSTOMFIELD']['_links'] = '';
$GLOBALS['_CRM_CORE_DAO_CUSTOMFIELD']['_import'] = '';

require_once 'CRM/Core/DAO.php';
require_once 'CRM/Utils/Array.php';
require_once 'CRM/Core/DAO.php';
    require_once 'CRM/Utils/Type.php';
    class CRM_Core_DAO_CustomField extends CRM_Core_DAO {

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
        * Unique Custom Field ID
        *
        * @var int unsigned
        */
        var $id;

        /**
        * FK to crm_custom_group.
        *
        * @var int unsigned
        */
        var $custom_group_id;

        /**
        * Variable name/programmatic handle for this property.
        *
        * @var string
        */
        var $name;

        /**
        * Text for form field label (also friendly name for administering this custom property).
        *
        * @var string
        */
        var $label;

        /**
        * Controls location of data storage in extended_data table.
        *
        * @var enum('String', 'Int', 'Float', 'Money', 'Memo', 'Date', 'Boolean')
        */
        var $data_type;

        /**
        * HTML types plus several built-in extended types.
        *
        * @var enum('Text', 'TextArea', 'Select', 'Radio', 'CheckBox', 'Select Date', 'Select State / Province', 'Select Country')
        */
        var $html_type;

        /**
        * Use form_options.is_default for field_types which use options.
        *
        * @var string
        */
        var $default_value;

        /**
        * Is a value required for this property.
        *
        * @var boolean
        */
        var $is_required;

        /**
        * Controls field display order within an extended property group.
        *
        * @var int
        */
        var $weight;

        /**
        * FK to crm_validation. Will be used for custom validation functions.
        *
        * @var int unsigned
        */
        var $validation_id;

        /**
        * Description and/or help text to display before this field.
        *
        * @var text
        */
        var $help_pre;

        /**
        * Description and/or help text to display after this field.
        *
        * @var text
        */
        var $help_post;

        /**
        * Optional format instructions for specific field types, like date types.
        *
        * @var string
        */
        var $mask;

        /**
        * Store collection of type-appropriate attributes, e.g. textarea  needs rows/cols attributes
        *
        * @var string
        */
        var $attributes;

        /**
        * Optional scripting attributes for field.
        *
        * @var string
        */
        var $javascript;

        /**
        * Is this property active?
        *
        * @var boolean
        */
        var $is_active;

        /**
        * class constructor
        *
        * @access public
        * @return crm_custom_field
        */
        function CRM_Core_DAO_CustomField() 
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
            if (!($GLOBALS['_CRM_CORE_DAO_CUSTOMFIELD']['_links'])) {
                $GLOBALS['_CRM_CORE_DAO_CUSTOMFIELD']['_links'] = array(
                    'custom_group_id'=>'crm_custom_group:id',
                    'validation_id'=>'crm_validation:id',
                );
            }
            return $GLOBALS['_CRM_CORE_DAO_CUSTOMFIELD']['_links'];
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
            if (!($GLOBALS['_CRM_CORE_DAO_CUSTOMFIELD']['_fields'])) {
                $GLOBALS['_CRM_CORE_DAO_CUSTOMFIELD']['_fields'] = array(
                    'id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'required'=>true,
                    ) ,
                    'custom_group_id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'required'=>true,
                    ) ,
                    'name'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Name') ,
                        'maxlength'=>64,
                        'size'=>CRM_UTILS_TYPE_BIG,
                    ) ,
                    'label'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Label') ,
                        'maxlength'=>64,
                        'size'=>CRM_UTILS_TYPE_BIG,
                    ) ,
                    'data_type'=>array(
                        'type'=>CRM_UTILS_TYPE_T_ENUM,
                        'title'=>ts('Data Type') ,
                    ) ,
                    'html_type'=>array(
                        'type'=>CRM_UTILS_TYPE_T_ENUM,
                        'title'=>ts('Html Type') ,
                    ) ,
                    'default_value'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Default Value') ,
                        'maxlength'=>255,
                        'size'=>CRM_UTILS_TYPE_HUGE,
                    ) ,
                    'is_required'=>array(
                        'type'=>CRM_UTILS_TYPE_T_BOOLEAN,
                    ) ,
                    'weight'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'title'=>ts('Weight') ,
                        'required'=>true,
                    ) ,
                    'validation_id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                    ) ,
                    'help_pre'=>array(
                        'type'=>CRM_UTILS_TYPE_T_TEXT,
                        'title'=>ts('Help Pre') ,
                    ) ,
                    'help_post'=>array(
                        'type'=>CRM_UTILS_TYPE_T_TEXT,
                        'title'=>ts('Help Post') ,
                    ) ,
                    'mask'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Mask') ,
                        'maxlength'=>64,
                        'size'=>CRM_UTILS_TYPE_BIG,
                    ) ,
                    'attributes'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Attributes') ,
                        'maxlength'=>255,
                        'size'=>CRM_UTILS_TYPE_HUGE,
                    ) ,
                    'javascript'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Javascript') ,
                        'maxlength'=>255,
                        'size'=>CRM_UTILS_TYPE_HUGE,
                    ) ,
                    'is_active'=>array(
                        'type'=>CRM_UTILS_TYPE_T_BOOLEAN,
                    ) ,
                );
            }
            return $GLOBALS['_CRM_CORE_DAO_CUSTOMFIELD']['_fields'];
        }
        /**
        * returns the names of this table
        *
        * @access public
        * @return string
        */
        function getTableName() 
        {
            return $GLOBALS['_CRM_CORE_DAO_CUSTOMFIELD']['_tableName'];
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
            if (!($GLOBALS['_CRM_CORE_DAO_CUSTOMFIELD']['_import'])) {
                $GLOBALS['_CRM_CORE_DAO_CUSTOMFIELD']['_import'] = array();
                $fields = &CRM_Core_DAO_CustomField::fields();
                foreach($fields as $name=>$field) {
                    if (CRM_Utils_Array::value('import', $field)) {
                        if ($prefix) {
                            $GLOBALS['_CRM_CORE_DAO_CUSTOMFIELD']['_import']['CustomField.'.$name] = &$field;
                        } else {
                            $GLOBALS['_CRM_CORE_DAO_CUSTOMFIELD']['_import'][$name] = &$field;
                        }
                    }
                }
            }
            return $GLOBALS['_CRM_CORE_DAO_CUSTOMFIELD']['_import'];
        }
    }
?>