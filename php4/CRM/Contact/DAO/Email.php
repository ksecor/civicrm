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
    $GLOBALS['_CRM_CONTACT_DAO_EMAIL']['_tableName'] =  'crm_email';
$GLOBALS['_CRM_CONTACT_DAO_EMAIL']['_fields'] = '';
$GLOBALS['_CRM_CONTACT_DAO_EMAIL']['_links'] = '';
$GLOBALS['_CRM_CONTACT_DAO_EMAIL']['_import'] = '';

require_once 'CRM/Core/DAO.php';
require_once 'CRM/Utils/Array.php';
require_once 'CRM/Core/DAO.php';
    require_once 'CRM/Utils/Type.php';
    class CRM_Contact_DAO_Email extends CRM_Core_DAO {

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
        * Unique Email ID
        *
        * @var int unsigned
        */
        var $id;

        /**
        * Which Location does this email belong to.
        *
        * @var int unsigned
        */
        var $location_id;

        /**
        * Email address
        *
        * @var string
        */
        var $email;

        /**
        * Is this the primary email for this contact and location.
        *
        * @var boolean
        */
        var $is_primary;

        /**
        * class constructor
        *
        * @access public
        * @return crm_email
        */
        function CRM_Contact_DAO_Email() 
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
            if (!($GLOBALS['_CRM_CONTACT_DAO_EMAIL']['_links'])) {
                $GLOBALS['_CRM_CONTACT_DAO_EMAIL']['_links'] = array(
                    'location_id'=>'crm_location:id',
                );
            }
            return $GLOBALS['_CRM_CONTACT_DAO_EMAIL']['_links'];
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
            if (!($GLOBALS['_CRM_CONTACT_DAO_EMAIL']['_fields'])) {
                $GLOBALS['_CRM_CONTACT_DAO_EMAIL']['_fields'] = array(
                    'id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'required'=>true,
                    ) ,
                    'location_id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'required'=>true,
                    ) ,
                    'email'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Email') ,
                        'maxlength'=>64,
                        'size'=>CRM_UTILS_TYPE_BIG,
                        'import'=>true,
                    ) ,
                    'is_primary'=>array(
                        'type'=>CRM_UTILS_TYPE_T_BOOLEAN,
                    ) ,
                );
            }
            return $GLOBALS['_CRM_CONTACT_DAO_EMAIL']['_fields'];
        }
        /**
        * returns the names of this table
        *
        * @access public
        * @return string
        */
        function getTableName() 
        {
            return $GLOBALS['_CRM_CONTACT_DAO_EMAIL']['_tableName'];
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
            if (!($GLOBALS['_CRM_CONTACT_DAO_EMAIL']['_import'])) {
                $GLOBALS['_CRM_CONTACT_DAO_EMAIL']['_import'] = array();
                $fields = &CRM_Contact_DAO_Email::fields();
                foreach($fields as $name=>$field) {
                    if (CRM_Utils_Array::value('import', $field)) {
                        if ($prefix) {
                            $GLOBALS['_CRM_CONTACT_DAO_EMAIL']['_import']['Email.'.$name] = &$field;
                        } else {
                            $GLOBALS['_CRM_CONTACT_DAO_EMAIL']['_import'][$name] = &$field;
                        }
                    }
                }
            }
            return $GLOBALS['_CRM_CONTACT_DAO_EMAIL']['_import'];
        }
    }
?>