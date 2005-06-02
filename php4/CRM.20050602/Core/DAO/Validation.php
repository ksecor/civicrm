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
    $GLOBALS['_CRM_CORE_DAO_VALIDATION']['_tableName'] =  'crm_validation';
$GLOBALS['_CRM_CORE_DAO_VALIDATION']['_fields'] = '';
$GLOBALS['_CRM_CORE_DAO_VALIDATION']['_links'] = '';
$GLOBALS['_CRM_CORE_DAO_VALIDATION']['_import'] = '';

require_once 'CRM/Core/DAO.php';
require_once 'CRM/Utils/Array.php';
require_once 'CRM/Core/DAO.php';
    require_once 'CRM/Utils/Type.php';
    class CRM_Core_DAO_Validation extends CRM_Core_DAO {

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
        * Unique Validation ID
        *
        * @var int unsigned
        */
        var $id;

        /**
        * Which Domain owns this contact
        *
        * @var int unsigned
        */
        var $domain_id;

        /**
        * List of rule built-in rule types. custom types may be added to ENUM via directory scan.
        *
        * @var enum('Email', 'Money', 'URL', 'Phone', 'Positive Integer', 'Variable Name', 'Range', 'Regular Expression Match', 'Regular Expression No Match')
        */
        var $type;

        /**
        * optional value(s) passed to validation function, e.g. a regular expression, min and max for Range, operator + number for Comparison type, etc.
        *
        * @var string
        */
        var $parameters;

        /**
        * custom validation function name. Class methods should be invoked using php syntax array(CLASS_NAME, FN_NAME)
        *
        * @var string
        */
        var $function_name;

        /**
        * Rule Description.
        *
        * @var string
        */
        var $description;

        /**
        * class constructor
        *
        * @access public
        * @return crm_validation
        */
        function CRM_Core_DAO_Validation() 
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
            if (!isset($GLOBALS['_CRM_CORE_DAO_VALIDATION']['_links'])) {
                $GLOBALS['_CRM_CORE_DAO_VALIDATION']['_links'] = array(
                    'domain_id'=>'crm_domain:id',
                );
            }
            return $GLOBALS['_CRM_CORE_DAO_VALIDATION']['_links'];
        }
        /**
        * returns all the column names of this table
        *
        * @access public
        * @return array
        */
        function &fields() 
        {
            if (!isset($GLOBALS['_CRM_CORE_DAO_VALIDATION']['_fields'])) {
                $GLOBALS['_CRM_CORE_DAO_VALIDATION']['_fields'] = array(
                    'id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'required'=>true,
                    ) ,
                    'domain_id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'required'=>true,
                    ) ,
                    'type'=>array(
                        'type'=>CRM_UTILS_TYPE_T_ENUM,
                        'title'=>ts('Type') ,
                    ) ,
                    'parameters'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Parameters') ,
                        'maxlength'=>255,
                        'size'=>CRM_UTILS_TYPE_HUGE,
                    ) ,
                    'function_name'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Function Name') ,
                        'maxlength'=>128,
                        'size'=>CRM_UTILS_TYPE_HUGE,
                    ) ,
                    'description'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Description') ,
                        'maxlength'=>255,
                        'size'=>CRM_UTILS_TYPE_HUGE,
                    ) ,
                );
            }
            return $GLOBALS['_CRM_CORE_DAO_VALIDATION']['_fields'];
        }
        /**
        * returns the names of this table
        *
        * @access public
        * @return string
        */
        function getTableName() 
        {
            return $GLOBALS['_CRM_CORE_DAO_VALIDATION']['_tableName'];
        }
        /**
        * returns the list of fields that can be imported
        *
        * @access public
        * return array
        */
        function &import($prefix = false) 
        {
            if (!isset($GLOBALS['_CRM_CORE_DAO_VALIDATION']['_import'])) {
                $GLOBALS['_CRM_CORE_DAO_VALIDATION']['_import'] = array();
                $fields = &CRM_Core_DAO_Validation::fields();
                foreach($fields as $name=>$field) {
                    if (CRM_Utils_Array::value('import', $field)) {
                        if ($prefix) {
                            $GLOBALS['_CRM_CORE_DAO_VALIDATION']['_import']['Validation.'.$name] = &$field;
                        } else {
                            $GLOBALS['_CRM_CORE_DAO_VALIDATION']['_import'][$name] = &$field;
                        }
                    }
                }
            }
            return $GLOBALS['_CRM_CORE_DAO_VALIDATION']['_import'];
        }
    }
?>