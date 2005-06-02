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
    $GLOBALS['_CRM_CONTACT_DAO_HOUSEHOLD']['_tableName'] =  'crm_household';
$GLOBALS['_CRM_CONTACT_DAO_HOUSEHOLD']['_fields'] = '';
$GLOBALS['_CRM_CONTACT_DAO_HOUSEHOLD']['_links'] = '';
$GLOBALS['_CRM_CONTACT_DAO_HOUSEHOLD']['_import'] = '';

require_once 'CRM/Core/DAO.php';
require_once 'CRM/Utils/Array.php';
require_once 'CRM/Core/DAO.php';
    require_once 'CRM/Utils/Type.php';
    class CRM_Contact_DAO_Household extends CRM_Core_DAO {

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
        * Unique Household ID
        *
        * @var int unsigned
        */
        var $id;

        /**
        * FK to Contact ID
        *
        * @var int unsigned
        */
        var $contact_id;

        /**
        * Household Name.
        *
        * @var string
        */
        var $household_name;

        /**
        * Nick Name.
        *
        * @var string
        */
        var $nick_name;

        /**
        * Optional FK to Primary Contact for this household.
        *
        * @var int unsigned
        */
        var $primary_contact_id;

        /**
        * class constructor
        *
        * @access public
        * @return crm_household
        */
        function CRM_Contact_DAO_Household() 
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
            if (!isset($GLOBALS['_CRM_CONTACT_DAO_HOUSEHOLD']['_links'])) {
                $GLOBALS['_CRM_CONTACT_DAO_HOUSEHOLD']['_links'] = array(
                    'contact_id'=>'crm_contact:id',
                    'primary_contact_id'=>'crm_contact:id',
                );
            }
            return $GLOBALS['_CRM_CONTACT_DAO_HOUSEHOLD']['_links'];
        }
        /**
        * returns all the column names of this table
        *
        * @access public
        * @return array
        */
        function &fields() 
        {
            if (!isset($GLOBALS['_CRM_CONTACT_DAO_HOUSEHOLD']['_fields'])) {
                $GLOBALS['_CRM_CONTACT_DAO_HOUSEHOLD']['_fields'] = array(
                    'id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'required'=>true,
                    ) ,
                    'contact_id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'required'=>true,
                    ) ,
                    'household_name'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Household Name') ,
                        'maxlength'=>64,
                        'size'=>CRM_UTILS_TYPE_BIG,
                    ) ,
                    'nick_name'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Nick Name') ,
                        'maxlength'=>64,
                        'size'=>CRM_UTILS_TYPE_BIG,
                    ) ,
                    'primary_contact_id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                    ) ,
                );
            }
            return $GLOBALS['_CRM_CONTACT_DAO_HOUSEHOLD']['_fields'];
        }
        /**
        * returns the names of this table
        *
        * @access public
        * @return string
        */
        function getTableName() 
        {
            return $GLOBALS['_CRM_CONTACT_DAO_HOUSEHOLD']['_tableName'];
        }
        /**
        * returns the list of fields that can be imported
        *
        * @access public
        * return array
        */
        function &import($prefix = false) 
        {
            if (!isset($GLOBALS['_CRM_CONTACT_DAO_HOUSEHOLD']['_import'])) {
                $GLOBALS['_CRM_CONTACT_DAO_HOUSEHOLD']['_import'] = array();
                $fields = &CRM_Contact_DAO_Household::fields();
                foreach($fields as $name=>$field) {
                    if (CRM_Utils_Array::value('import', $field)) {
                        if ($prefix) {
                            $GLOBALS['_CRM_CONTACT_DAO_HOUSEHOLD']['_import']['Household.'.$name] = &$field;
                        } else {
                            $GLOBALS['_CRM_CONTACT_DAO_HOUSEHOLD']['_import'][$name] = &$field;
                        }
                    }
                }
            }
            return $GLOBALS['_CRM_CONTACT_DAO_HOUSEHOLD']['_import'];
        }
    }
?>