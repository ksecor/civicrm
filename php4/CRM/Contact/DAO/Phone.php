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
    $GLOBALS['_CRM_CONTACT_DAO_PHONE']['_tableName'] =  'crm_phone';
$GLOBALS['_CRM_CONTACT_DAO_PHONE']['_fields'] = '';
$GLOBALS['_CRM_CONTACT_DAO_PHONE']['_links'] = '';
$GLOBALS['_CRM_CONTACT_DAO_PHONE']['_import'] = '';

require_once 'CRM/Core/DAO.php';
require_once 'CRM/Utils/Array.php';
require_once 'CRM/Core/DAO.php';
    require_once 'CRM/Utils/Type.php';
    class CRM_Contact_DAO_Phone extends CRM_Core_DAO {

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
        * Unique Phone ID
        *
        * @var int unsigned
        */
        var $id;

        /**
        * Which Location does this phone belong to.
        *
        * @var int unsigned
        */
        var $location_id;

        /**
        * Complete phone number.
        *
        * @var string
        */
        var $phone;

        /**
        * What type of telecom device is this.
        *
        * @var enum('Phone', 'Mobile', 'Fax', 'Pager')
        */
        var $phone_type;

        /**
        * Is this the primary phone for this contact and location.
        *
        * @var boolean
        */
        var $is_primary;

        /**
        * Which Mobile Provider does this phone belong to.
        *
        * @var int unsigned
        */
        var $mobile_provider_id;

        /**
        * class constructor
        *
        * @access public
        * @return crm_phone
        */
        function CRM_Contact_DAO_Phone() 
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
            if (!($GLOBALS['_CRM_CONTACT_DAO_PHONE']['_links'])) {
                $GLOBALS['_CRM_CONTACT_DAO_PHONE']['_links'] = array(
                    'location_id'=>'crm_location:id',
                    'mobile_provider_id'=>'crm_mobile_provider:id',
                );
            }
            return $GLOBALS['_CRM_CONTACT_DAO_PHONE']['_links'];
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
            if (!($GLOBALS['_CRM_CONTACT_DAO_PHONE']['_fields'])) {
                $GLOBALS['_CRM_CONTACT_DAO_PHONE']['_fields'] = array(
                    'id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'required'=>true,
                    ) ,
                    'location_id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'required'=>true,
                    ) ,
                    'phone'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Phone') ,
                        'maxlength'=>16,
                        'size'=>CRM_UTILS_TYPE_TWELVE,
                        'import'=>true,
                    ) ,
                    'phone_type'=>array(
                        'type'=>CRM_UTILS_TYPE_T_ENUM,
                        'title'=>ts('Phone Type') ,
                        'import'=>true,
                    ) ,
                    'is_primary'=>array(
                        'type'=>CRM_UTILS_TYPE_T_BOOLEAN,
                    ) ,
                    'mobile_provider_id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                    ) ,
                );
            }
            return $GLOBALS['_CRM_CONTACT_DAO_PHONE']['_fields'];
        }
        /**
        * returns the names of this table
        *
        * @access public
        * @return string
        */
        function getTableName() 
        {
            return $GLOBALS['_CRM_CONTACT_DAO_PHONE']['_tableName'];
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
            if (!($GLOBALS['_CRM_CONTACT_DAO_PHONE']['_import'])) {
                $GLOBALS['_CRM_CONTACT_DAO_PHONE']['_import'] = array();
                $fields = &CRM_Contact_DAO_Phone::fields();
                foreach($fields as $name=>$field) {
                    if (CRM_Utils_Array::value('import', $field)) {
                        if ($prefix) {
                            $GLOBALS['_CRM_CONTACT_DAO_PHONE']['_import']['Phone.'.$name] = &$field;
                        } else {
                            $GLOBALS['_CRM_CONTACT_DAO_PHONE']['_import'][$name] = &$field;
                        }
                    }
                }
            }
            return $GLOBALS['_CRM_CONTACT_DAO_PHONE']['_import'];
        }
    }
?>