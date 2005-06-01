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
    $GLOBALS['_CRM_CORE_DAO_CUSTOMGROUP']['_tableName'] =  'crm_custom_group';
$GLOBALS['_CRM_CORE_DAO_CUSTOMGROUP']['_fields'] = '';
$GLOBALS['_CRM_CORE_DAO_CUSTOMGROUP']['_links'] = '';
$GLOBALS['_CRM_CORE_DAO_CUSTOMGROUP']['_import'] = '';

require_once 'CRM/Core/DAO.php';
require_once 'CRM/Utils/Array.php';
require_once 'CRM/Core/DAO.php';
    require_once 'CRM/Utils/Type.php';
    class CRM_Core_DAO_CustomGroup extends CRM_Core_DAO {

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
        * Unique Custom Group ID
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
        * Variable name/programmatic handle for this group.
        *
        * @var string
        */
        var $name;

        /**
        * Friendly Name.
        *
        * @var string
        */
        var $title;

        /**
        * Type of object this group extends (can add other options later e.g. contact_address, etc.).
        *
        * @var enum('Contact', 'Individual', 'Household', 'Organization', 'Location', 'Address')
        */
        var $extends;

        /**
        * Visual relationship between this form and its parent.
        *
        * @var enum('Tab', 'Inline')
        */
        var $style;

        /**
        * Description and/or help text to display before fields in form.
        *
        * @var text
        */
        var $help_pre;

        /**
        * Description and/or help text to display after fields in form.
        *
        * @var text
        */
        var $help_post;

        /**
        * Controls display order when multiple extended property groups are setup for the same class.
        *
        * @var int
        */
        var $weight;

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
        * @return crm_custom_group
        */
        function CRM_Core_DAO_CustomGroup() 
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
            if (!isset($GLOBALS['_CRM_CORE_DAO_CUSTOMGROUP']['_links'])) {
                $GLOBALS['_CRM_CORE_DAO_CUSTOMGROUP']['_links'] = array(
                    'domain_id'=>'crm_domain:id',
                );
            }
            return $GLOBALS['_CRM_CORE_DAO_CUSTOMGROUP']['_links'];
        }
        /**
        * returns all the column names of this table
        *
        * @access public
        * @return array
        */
        function &fields() 
        {
            if (!isset($GLOBALS['_CRM_CORE_DAO_CUSTOMGROUP']['_fields'])) {
                $GLOBALS['_CRM_CORE_DAO_CUSTOMGROUP']['_fields'] = array(
                    'id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'required'=>true,
                    ) ,
                    'domain_id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'required'=>true,
                    ) ,
                    'name'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Name') ,
                        'maxlength'=>64,
                        'size'=>CRM_UTILS_TYPE_BIG,
                    ) ,
                    'title'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Title') ,
                        'maxlength'=>64,
                        'size'=>CRM_UTILS_TYPE_BIG,
                    ) ,
                    'extends'=>array(
                        'type'=>CRM_UTILS_TYPE_T_ENUM,
                        'title'=>ts('Extends') ,
                    ) ,
                    'style'=>array(
                        'type'=>CRM_UTILS_TYPE_T_ENUM,
                        'title'=>ts('Style') ,
                    ) ,
                    'help_pre'=>array(
                        'type'=>CRM_UTILS_TYPE_T_TEXT,
                        'title'=>ts('Help Pre') ,
                        'rows'=>4,
                        'cols'=>80,
                    ) ,
                    'help_post'=>array(
                        'type'=>CRM_UTILS_TYPE_T_TEXT,
                        'title'=>ts('Help Post') ,
                        'rows'=>4,
                        'cols'=>80,
                    ) ,
                    'weight'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'title'=>ts('Weight') ,
                        'required'=>true,
                    ) ,
                    'is_active'=>array(
                        'type'=>CRM_UTILS_TYPE_T_BOOLEAN,
                    ) ,
                );
            }
            return $GLOBALS['_CRM_CORE_DAO_CUSTOMGROUP']['_fields'];
        }
        /**
        * returns the names of this table
        *
        * @access public
        * @return string
        */
        function getTableName() 
        {
            return $GLOBALS['_CRM_CORE_DAO_CUSTOMGROUP']['_tableName'];
        }
        /**
        * returns the list of fields that can be imported
        *
        * @access public
        * return array
        */
        function &import($prefix = false) 
        {
            if (!isset($GLOBALS['_CRM_CORE_DAO_CUSTOMGROUP']['_import'])) {
                $GLOBALS['_CRM_CORE_DAO_CUSTOMGROUP']['_import'] = array();
                $fields = &CRM_Core_DAO_CustomGroup::fields();
                foreach($fields as $name=>$field) {
                    if (CRM_Utils_Array::value('import', $field)) {
                        if ($prefix) {
                            $GLOBALS['_CRM_CORE_DAO_CUSTOMGROUP']['_import']['CustomGroup.'.$name] = &$field;
                        } else {
                            $GLOBALS['_CRM_CORE_DAO_CUSTOMGROUP']['_import'][$name] = &$field;
                        }
                    }
                }
            }
            return $GLOBALS['_CRM_CORE_DAO_CUSTOMGROUP']['_import'];
        }
    }
?>