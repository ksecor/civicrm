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
    $GLOBALS['_CRM_CORE_DAO_DOMAIN']['_tableName'] =  'crm_domain';
$GLOBALS['_CRM_CORE_DAO_DOMAIN']['_fields'] = '';
$GLOBALS['_CRM_CORE_DAO_DOMAIN']['_links'] = '';
$GLOBALS['_CRM_CORE_DAO_DOMAIN']['_import'] = '';

require_once 'CRM/Core/DAO.php';
require_once 'CRM/Utils/Array.php';
require_once 'CRM/Core/DAO.php';
    require_once 'CRM/Utils/Type.php';
    class CRM_Core_DAO_Domain extends CRM_Core_DAO {

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
        * Domain ID
        *
        * @var int unsigned
        */
        var $id;

        /**
        * Name of Domain / Organization
        *
        * @var string
        */
        var $name;

        /**
        * Description of Domain.
        *
        * @var string
        */
        var $description;

        /**
        * class constructor
        *
        * @access public
        * @return crm_domain
        */
        function CRM_Core_DAO_Domain() 
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
            if (!($GLOBALS['_CRM_CORE_DAO_DOMAIN']['_fields'])) {
                $GLOBALS['_CRM_CORE_DAO_DOMAIN']['_fields'] = array(
                    'id'=>array(
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
                );
            }
            return $GLOBALS['_CRM_CORE_DAO_DOMAIN']['_fields'];
        }
        /**
        * returns the names of this table
        *
        * @access public
        * @return string
        */
        function getTableName() 
        {
            return $GLOBALS['_CRM_CORE_DAO_DOMAIN']['_tableName'];
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
            if (!($GLOBALS['_CRM_CORE_DAO_DOMAIN']['_import'])) {
                $GLOBALS['_CRM_CORE_DAO_DOMAIN']['_import'] = array();
                $fields = &CRM_Core_DAO_Domain::fields();
                foreach($fields as $name=>$field) {
                    if (CRM_Utils_Array::value('import', $field)) {
                        if ($prefix) {
                            $GLOBALS['_CRM_CORE_DAO_DOMAIN']['_import']['Domain.'.$name] = &$field;
                        } else {
                            $GLOBALS['_CRM_CORE_DAO_DOMAIN']['_import'][$name] = &$field;
                        }
                    }
                }
            }
            return $GLOBALS['_CRM_CORE_DAO_DOMAIN']['_import'];
        }
    }
?>