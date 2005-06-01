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
    $GLOBALS['_CRM_CONTACT_DAO_INDIVIDUAL']['_tableName'] =  'crm_individual';
$GLOBALS['_CRM_CONTACT_DAO_INDIVIDUAL']['_fields'] = '';
$GLOBALS['_CRM_CONTACT_DAO_INDIVIDUAL']['_links'] = '';
$GLOBALS['_CRM_CONTACT_DAO_INDIVIDUAL']['_import'] = '';

require_once 'CRM/Core/DAO.php';
require_once 'CRM/Utils/Array.php';
require_once 'CRM/Core/DAO.php';
    require_once 'CRM/Utils/Type.php';
    class CRM_Contact_DAO_Individual extends CRM_Core_DAO {

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
        * Unique Individual ID
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
        * First Name.
        *
        * @var string
        */
        var $first_name;

        /**
        * Middle Name.
        *
        * @var string
        */
        var $middle_name;

        /**
        * Last Name.
        *
        * @var string
        */
        var $last_name;

        /**
        * Prefix to Name.
        *
        * @var string
        */
        var $prefix;

        /**
        * Suffix to Name.
        *
        * @var string
        */
        var $suffix;

        /**
        * Formatted name representing preferred format for display/print/other output.
        *
        * @var string
        */
        var $display_name;

        /**
        * Preferred greeting format.
        *
        * @var enum('Formal', 'Informal', 'Honorific', 'Custom', 'Other')
        */
        var $greeting_type;

        /**
        * Custom greeting message.
        *
        * @var string
        */
        var $custom_greeting;

        /**
        *
        * @var string
        */
        var $job_title;

        /**
        *
        * @var enum('Female', 'Male', 'Transgender')
        */
        var $gender;

        /**
        *
        * @var date
        */
        var $birth_date;

        /**
        *
        * @var boolean
        */
        var $is_deceased;

        /**
        * OPTIONAL FK to crm_contact_household record. If NOT NULL, direct phone communications to household rather than individual location.
        *
        * @var int unsigned
        */
        var $phone_to_household_id;

        /**
        * OPTIONAL FK to crm_contact_household record. If NOT NULL, direct phone communications to household rather than individual location.
        *
        * @var int unsigned
        */
        var $email_to_household_id;

        /**
        * OPTIONAL FK to crm_contact_household record. If NOT NULL, direct mail communications to household rather than individual location.
        *
        * @var int unsigned
        */
        var $mail_to_household_id;

        /**
        * class constructor
        *
        * @access public
        * @return crm_individual
        */
        function CRM_Contact_DAO_Individual() 
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
            if (!isset($GLOBALS['_CRM_CONTACT_DAO_INDIVIDUAL']['_links'])) {
                $GLOBALS['_CRM_CONTACT_DAO_INDIVIDUAL']['_links'] = array(
                    'contact_id'=>'crm_contact:id',
                );
            }
            return $GLOBALS['_CRM_CONTACT_DAO_INDIVIDUAL']['_links'];
        }
        /**
        * returns all the column names of this table
        *
        * @access public
        * @return array
        */
        function &fields() 
        {
            if (!isset($GLOBALS['_CRM_CONTACT_DAO_INDIVIDUAL']['_fields'])) {
                $GLOBALS['_CRM_CONTACT_DAO_INDIVIDUAL']['_fields'] = array(
                    'id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'required'=>true,
                    ) ,
                    'contact_id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'required'=>true,
                    ) ,
                    'first_name'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('First Name') ,
                        'maxlength'=>64,
                        'size'=>CRM_UTILS_TYPE_BIG,
                        'import'=>true,
                    ) ,
                    'middle_name'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Middle Name') ,
                        'maxlength'=>64,
                        'size'=>CRM_UTILS_TYPE_BIG,
                    ) ,
                    'last_name'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Last Name') ,
                        'maxlength'=>64,
                        'size'=>CRM_UTILS_TYPE_BIG,
                        'import'=>true,
                    ) ,
                    'prefix'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Prefix') ,
                        'maxlength'=>64,
                        'size'=>CRM_UTILS_TYPE_BIG,
                        'import'=>true,
                    ) ,
                    'suffix'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Suffix') ,
                        'maxlength'=>64,
                        'size'=>CRM_UTILS_TYPE_BIG,
                        'import'=>true,
                    ) ,
                    'display_name'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Display Name') ,
                        'maxlength'=>128,
                        'size'=>CRM_UTILS_TYPE_HUGE,
                    ) ,
                    'greeting_type'=>array(
                        'type'=>CRM_UTILS_TYPE_T_ENUM,
                        'title'=>ts('Greeting Type') ,
                    ) ,
                    'custom_greeting'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Custom Greeting') ,
                        'maxlength'=>128,
                        'size'=>CRM_UTILS_TYPE_HUGE,
                    ) ,
                    'job_title'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Job Title') ,
                        'maxlength'=>64,
                        'size'=>CRM_UTILS_TYPE_BIG,
                    ) ,
                    'gender'=>array(
                        'type'=>CRM_UTILS_TYPE_T_ENUM,
                        'title'=>ts('Gender') ,
                    ) ,
                    'birth_date'=>array(
                        'type'=>CRM_UTILS_TYPE_T_DATE,
                        'title'=>ts('Birth Date') ,
                    ) ,
                    'is_deceased'=>array(
                        'type'=>CRM_UTILS_TYPE_T_BOOLEAN,
                    ) ,
                    'phone_to_household_id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                    ) ,
                    'email_to_household_id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                    ) ,
                    'mail_to_household_id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                    ) ,
                );
            }
            return $GLOBALS['_CRM_CONTACT_DAO_INDIVIDUAL']['_fields'];
        }
        /**
        * returns the names of this table
        *
        * @access public
        * @return string
        */
        function getTableName() 
        {
            return $GLOBALS['_CRM_CONTACT_DAO_INDIVIDUAL']['_tableName'];
        }
        /**
        * returns the list of fields that can be imported
        *
        * @access public
        * return array
        */
        function &import($prefix = false) 
        {
            if (!isset($GLOBALS['_CRM_CONTACT_DAO_INDIVIDUAL']['_import'])) {
                $GLOBALS['_CRM_CONTACT_DAO_INDIVIDUAL']['_import'] = array();
                $fields = &CRM_Contact_DAO_Individual::fields();
                foreach($fields as $name=>$field) {
                    if (CRM_Utils_Array::value('import', $field)) {
                        if ($prefix) {
                            $GLOBALS['_CRM_CONTACT_DAO_INDIVIDUAL']['_import']['Individual.'.$name] = &$field;
                        } else {
                            $GLOBALS['_CRM_CONTACT_DAO_INDIVIDUAL']['_import'][$name] = &$field;
                        }
                    }
                }
            }
            return $GLOBALS['_CRM_CONTACT_DAO_INDIVIDUAL']['_import'];
        }
    }
?>