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
    $GLOBALS['_CRM_CORE_DAO_UFFORMFIELD']['_tableName'] =  'crm_uf_form_field';
$GLOBALS['_CRM_CORE_DAO_UFFORMFIELD']['_fields'] = '';
$GLOBALS['_CRM_CORE_DAO_UFFORMFIELD']['_links'] = '';
$GLOBALS['_CRM_CORE_DAO_UFFORMFIELD']['_import'] = '';

require_once 'CRM/Core/DAO.php';
require_once 'CRM/Utils/Array.php';
require_once 'CRM/Core/DAO.php';
    require_once 'CRM/Utils/Type.php';
    class CRM_Core_DAO_UFFormField extends CRM_Core_DAO {

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
        * Unique table ID
        *
        * @var int unsigned
        */
        var $id;

        /**
        * Which form does this field belong to.
        *
        * @var int unsigned
        */
        var $uf_form_id;

        /**
        * Name for CiviCRM field which is being exposed for sharing.
        *
        * @var string
        */
        var $field_name;

        /**
        * Is this field currently shareable? If false, hide the field for all sharing contexts.
        *
        * @var boolean
        */
        var $is_active;

        /**
        * If true, data is displayed to user and user admin, but not editable in user forms.
        *
        * @var boolean
        */
        var $view_only;

        /**
        * Is this field required when included in a user or registration form?
        *
        * @var boolean
        */
        var $is_required;

        /**
        * Is this field included in new user registration forms?
        *
        * @var boolean
        */
        var $for_registration;

        /**
        * In what context(s) is this field visible.
        *
        * @var enum('User and User Admin Only', 'Public User Pages', 'Public User Pages and Listings')
        */
        var $visibility;

        /**
        * Page title for listings page (users who share a common value for this property).
        *
        * @var string
        */
        var $listings_title;

        /**
        * class constructor
        *
        * @access public
        * @return crm_uf_form_field
        */
        function CRM_Core_DAO_UFFormField() 
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
            if (!isset($GLOBALS['_CRM_CORE_DAO_UFFORMFIELD']['_links'])) {
                $GLOBALS['_CRM_CORE_DAO_UFFORMFIELD']['_links'] = array(
                    'uf_form_id'=>'crm_uf_form:id',
                );
            }
            return $GLOBALS['_CRM_CORE_DAO_UFFORMFIELD']['_links'];
        }
        /**
        * returns all the column names of this table
        *
        * @access public
        * @return array
        */
        function &fields() 
        {
            if (!isset($GLOBALS['_CRM_CORE_DAO_UFFORMFIELD']['_fields'])) {
                $GLOBALS['_CRM_CORE_DAO_UFFORMFIELD']['_fields'] = array(
                    'id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'required'=>true,
                    ) ,
                    'uf_form_id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'required'=>true,
                    ) ,
                    'field_name'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Field Name') ,
                        'maxlength'=>64,
                        'size'=>CRM_UTILS_TYPE_BIG,
                    ) ,
                    'is_active'=>array(
                        'type'=>CRM_UTILS_TYPE_T_BOOLEAN,
                    ) ,
                    'view_only'=>array(
                        'type'=>CRM_UTILS_TYPE_T_BOOLEAN,
                        'title'=>ts('View Only') ,
                    ) ,
                    'is_required'=>array(
                        'type'=>CRM_UTILS_TYPE_T_BOOLEAN,
                    ) ,
                    'for_registration'=>array(
                        'type'=>CRM_UTILS_TYPE_T_BOOLEAN,
                        'title'=>ts('For Registration') ,
                    ) ,
                    'visibility'=>array(
                        'type'=>CRM_UTILS_TYPE_T_ENUM,
                        'title'=>ts('Visibility') ,
                    ) ,
                    'listings_title'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Listings Title') ,
                        'maxlength'=>64,
                        'size'=>CRM_UTILS_TYPE_BIG,
                    ) ,
                );
            }
            return $GLOBALS['_CRM_CORE_DAO_UFFORMFIELD']['_fields'];
        }
        /**
        * returns the names of this table
        *
        * @access public
        * @return string
        */
        function getTableName() 
        {
            return $GLOBALS['_CRM_CORE_DAO_UFFORMFIELD']['_tableName'];
        }
        /**
        * returns the list of fields that can be imported
        *
        * @access public
        * return array
        */
        function &import($prefix = false) 
        {
            if (!isset($GLOBALS['_CRM_CORE_DAO_UFFORMFIELD']['_import'])) {
                $GLOBALS['_CRM_CORE_DAO_UFFORMFIELD']['_import'] = array();
                $fields = &CRM_Core_DAO_UFFormField::fields();
                foreach($fields as $name=>$field) {
                    if (CRM_Utils_Array::value('import', $field)) {
                        if ($prefix) {
                            $GLOBALS['_CRM_CORE_DAO_UFFORMFIELD']['_import']['UFFormField.'.$name] = &$field;
                        } else {
                            $GLOBALS['_CRM_CORE_DAO_UFFORMFIELD']['_import'][$name] = &$field;
                        }
                    }
                }
            }
            return $GLOBALS['_CRM_CORE_DAO_UFFORMFIELD']['_import'];
        }
    }
?>