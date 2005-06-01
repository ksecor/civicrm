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
    $GLOBALS['_CRM_CONTACT_DAO_CATEGORY']['_tableName'] =  'crm_category';
$GLOBALS['_CRM_CONTACT_DAO_CATEGORY']['_fields'] = '';
$GLOBALS['_CRM_CONTACT_DAO_CATEGORY']['_links'] = '';
$GLOBALS['_CRM_CONTACT_DAO_CATEGORY']['_import'] = '';

require_once 'CRM/Core/DAO.php';
require_once 'CRM/Utils/Array.php';
require_once 'CRM/Core/DAO.php';
    require_once 'CRM/Utils/Type.php';
    class CRM_Contact_DAO_Category extends CRM_Core_DAO {

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
        * Category ID
        *
        * @var int unsigned
        */
        var $id;

        /**
        * Which Domain owns this category
        *
        * @var int unsigned
        */
        var $domain_id;

        /**
        * Name of Category.
        *
        * @var string
        */
        var $name;

        /**
        * Optional verbose description of the category.
        *
        * @var string
        */
        var $description;

        /**
        * Optional parent id for this category.
        *
        * @var int unsigned
        */
        var $parent_id;

        /**
        * class constructor
        *
        * @access public
        * @return crm_category
        */
        function CRM_Contact_DAO_Category() 
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
            if (!isset($GLOBALS['_CRM_CONTACT_DAO_CATEGORY']['_links'])) {
                $GLOBALS['_CRM_CONTACT_DAO_CATEGORY']['_links'] = array(
                    'domain_id'=>'crm_domain:id',
                    'parent_id'=>'crm_category:id',
                );
            }
            return $GLOBALS['_CRM_CONTACT_DAO_CATEGORY']['_links'];
        }
        /**
        * returns all the column names of this table
        *
        * @access public
        * @return array
        */
        function &fields() 
        {
            if (!isset($GLOBALS['_CRM_CONTACT_DAO_CATEGORY']['_fields'])) {
                $GLOBALS['_CRM_CONTACT_DAO_CATEGORY']['_fields'] = array(
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
                    'description'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Description') ,
                        'maxlength'=>255,
                        'size'=>CRM_UTILS_TYPE_HUGE,
                    ) ,
                    'parent_id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                    ) ,
                );
            }
            return $GLOBALS['_CRM_CONTACT_DAO_CATEGORY']['_fields'];
        }
        /**
        * returns the names of this table
        *
        * @access public
        * @return string
        */
        function getTableName() 
        {
            return $GLOBALS['_CRM_CONTACT_DAO_CATEGORY']['_tableName'];
        }
        /**
        * returns the list of fields that can be imported
        *
        * @access public
        * return array
        */
        function &import($prefix = false) 
        {
            if (!isset($GLOBALS['_CRM_CONTACT_DAO_CATEGORY']['_import'])) {
                $GLOBALS['_CRM_CONTACT_DAO_CATEGORY']['_import'] = array();
                $fields = &CRM_Contact_DAO_Category::fields();
                foreach($fields as $name=>&$field) {
                    if (CRM_Utils_Array::value('import', $field)) {
                        if ($prefix) {
                            $GLOBALS['_CRM_CONTACT_DAO_CATEGORY']['_import']['Category.'.$name] = &$field;
                        } else {
                            $GLOBALS['_CRM_CONTACT_DAO_CATEGORY']['_import'][$name] = &$field;
                        }
                    }
                }
            }
            return $GLOBALS['_CRM_CONTACT_DAO_CATEGORY']['_import'];
        }
    }
?>