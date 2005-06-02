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
    $GLOBALS['_CRM_CONTACT_DAO_CONTACT']['_tableName'] =  'crm_contact';
$GLOBALS['_CRM_CONTACT_DAO_CONTACT']['_fields'] = '';
$GLOBALS['_CRM_CONTACT_DAO_CONTACT']['_links'] = '';
$GLOBALS['_CRM_CONTACT_DAO_CONTACT']['_import'] = '';

require_once 'CRM/Core/DAO.php';
require_once 'CRM/Utils/Array.php';
require_once 'CRM/Core/DAO.php';
    require_once 'CRM/Utils/Type.php';
    class CRM_Contact_DAO_Contact extends CRM_Core_DAO {

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
        * Unique Contact ID
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
        * Type of Contact.
        *
        * @var enum('Individual', 'Organization', 'Household')
        */
        var $contact_type;

        /**
        * May be used for SSN, EIN/TIN, Household ID (census) or other applicable unique legal/government ID.
        *
        * @var string
        */
        var $legal_identifier;

        /**
        * Unique trusted external ID (generally from a legacy app/datasource). Particularly useful for deduping operations.
        *
        * @var string
        */
        var $external_identifier;

        /**
        * Name used for sorting different contact types
        *
        * @var string
        */
        var $sort_name;

        /**
        * optional "home page" URL for this contact.
        *
        * @var string
        */
        var $home_URL;

        /**
        * optional URL for preferred image (photo, logo, etc.) to display for this contact.
        *
        * @var string
        */
        var $image_URL;

        /**
        * where domain_id contact come from, e.g. import, donate module insert...
        *
        * @var string
        */
        var $source;

        /**
        * What is the preferred mode of communication.
        *
        * @var enum('Phone', 'Email', 'Post')
        */
        var $preferred_communication_method;

        /**
        *
        * @var boolean
        */
        var $do_not_phone;

        /**
        *
        * @var boolean
        */
        var $do_not_email;

        /**
        *
        * @var boolean
        */
        var $do_not_mail;

        /**
        * Key for validating requests related to this contact.
        *
        * @var int unsigned
        */
        var $hash;

        /**
        * class constructor
        *
        * @access public
        * @return crm_contact
        */
        function CRM_Contact_DAO_Contact() 
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
            //if (!isset($GLOBALS['_CRM_CONTACT_DAO_CONTACT']['_links'])) {
            $GLOBALS['_CRM_CONTACT_DAO_CONTACT']['_links'] = array(
                                                                   'domain_id'=>'crm_domain:id',
                                                                   );
            //}
            return $GLOBALS['_CRM_CONTACT_DAO_CONTACT']['_links'];
        }
        /**
        * returns all the column names of this table
        *
        * @access public
        * @return array
        */
        function &fields() 
        {

            //if (!isset($GLOBALS['_CRM_CONTACT_DAO_CONTACT']['_fields'])) {

                $GLOBALS['_CRM_CONTACT_DAO_CONTACT']['_fields'] = array(
                    'id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'required'=>true,
                    ) ,
                    'domain_id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'required'=>true,
                    ) ,
                    'contact_type'=>array(
                        'type'=>CRM_UTILS_TYPE_T_ENUM,
                        'title'=>ts('Contact Type') ,
                    ) ,
                    'legal_identifier'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Legal Identifier') ,
                        'maxlength'=>32,
                        'size'=>CRM_UTILS_TYPE_MEDIUM,
                        'import'=>true,
                    ) ,
                    'external_identifier'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('External Identifier') ,
                        'maxlength'=>32,
                        'size'=>CRM_UTILS_TYPE_MEDIUM,
                        'import'=>true,
                    ) ,
                    'sort_name'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Sort Name') ,
                        'maxlength'=>64,
                        'size'=>CRM_UTILS_TYPE_BIG,
                    ) ,
                    'home_URL'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Home Url') ,
                        'maxlength'=>128,
                        'size'=>CRM_UTILS_TYPE_HUGE,
                    ) ,
                    'image_URL'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Image Url') ,
                        'maxlength'=>128,
                        'size'=>CRM_UTILS_TYPE_HUGE,
                    ) ,
                    'source'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Source') ,
                        'maxlength'=>255,
                        'size'=>CRM_UTILS_TYPE_HUGE,
                    ) ,
                    'preferred_communication_method'=>array(
                        'type'=>CRM_UTILS_TYPE_T_ENUM,
                        'title'=>ts('Preferred Communication Method') ,
                    ) ,
                    'do_not_phone'=>array(
                        'type'=>CRM_UTILS_TYPE_T_BOOLEAN,
                        'title'=>ts('Do Not Phone') ,
                    ) ,
                    'do_not_email'=>array(
                        'type'=>CRM_UTILS_TYPE_T_BOOLEAN,
                        'title'=>ts('Do Not Email') ,
                    ) ,
                    'do_not_mail'=>array(
                        'type'=>CRM_UTILS_TYPE_T_BOOLEAN,
                        'title'=>ts('Do Not Mail') ,
                    ) ,
                    'hash'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'title'=>ts('Hash') ,
                        'required'=>true,
                    ) ,
                );
                //}
            return $GLOBALS['_CRM_CONTACT_DAO_CONTACT']['_fields'];
        }
        /**
        * returns the names of this table
        *
        * @access public
        * @return string
        */
        function getTableName() 
        {
            return $GLOBALS['_CRM_CONTACT_DAO_CONTACT']['_tableName'];
        }
        /**
        * returns the list of fields that can be imported
        *
        * @access public
        * return array
        */
        function &import($prefix = false) 
        {
            if (!isset($GLOBALS['_CRM_CONTACT_DAO_CONTACT']['_import'])) {
                $GLOBALS['_CRM_CONTACT_DAO_CONTACT']['_import'] = array();
                $fields = &CRM_Contact_DAO_Contact::fields();
                foreach($fields as $name=>$field) {
                    if (CRM_Utils_Array::value('import', $field)) {
                        if ($prefix) {
                            $GLOBALS['_CRM_CONTACT_DAO_CONTACT']['_import']['Contact.'.$name] = &$field;
                        } else {
                            $GLOBALS['_CRM_CONTACT_DAO_CONTACT']['_import'][$name] = &$field;
                        }
                    }
                }
            }
            return $GLOBALS['_CRM_CONTACT_DAO_CONTACT']['_import'];
        }
    }
?>