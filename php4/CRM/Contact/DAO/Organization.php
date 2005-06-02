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
    $GLOBALS['_CRM_CONTACT_DAO_ORGANIZATION']['_tableName'] =  'crm_organization';
$GLOBALS['_CRM_CONTACT_DAO_ORGANIZATION']['_fields'] = '';
$GLOBALS['_CRM_CONTACT_DAO_ORGANIZATION']['_links'] = '';
$GLOBALS['_CRM_CONTACT_DAO_ORGANIZATION']['_import'] = '';

require_once 'CRM/Core/DAO.php';
require_once 'CRM/Utils/Array.php';
require_once 'CRM/Core/DAO.php';
    require_once 'CRM/Utils/Type.php';
    class CRM_Contact_DAO_Organization extends CRM_Core_DAO {

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
        * Unique Organization ID
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
        * Organization Name.
        *
        * @var string
        */
        var $organization_name;

        /**
        * Legal Name.
        *
        * @var string
        */
        var $legal_name;

        /**
        * Nick Name.
        *
        * @var string
        */
        var $nick_name;

        /**
        * Standard Industry Classification Code.
        *
        * @var string
        */
        var $sic_code;

        /**
        * Optional FK to Primary Contact for this organization.
        *
        * @var int unsigned
        */
        var $primary_contact_id;

        /**
        * class constructor
        *
        * @access public
        * @return crm_organization
        */
        function CRM_Contact_DAO_Organization() 
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
            if (!($GLOBALS['_CRM_CONTACT_DAO_ORGANIZATION']['_links'])) {
                $GLOBALS['_CRM_CONTACT_DAO_ORGANIZATION']['_links'] = array(
                    'contact_id'=>'crm_contact:id',
                    'primary_contact_id'=>'crm_contact:id',
                );
            }
            return $GLOBALS['_CRM_CONTACT_DAO_ORGANIZATION']['_links'];
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
            if (!($GLOBALS['_CRM_CONTACT_DAO_ORGANIZATION']['_fields'])) {
                $GLOBALS['_CRM_CONTACT_DAO_ORGANIZATION']['_fields'] = array(
                    'id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'required'=>true,
                    ) ,
                    'contact_id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'required'=>true,
                    ) ,
                    'organization_name'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Organization Name') ,
                        'maxlength'=>64,
                        'size'=>CRM_UTILS_TYPE_BIG,
                    ) ,
                    'legal_name'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Legal Name') ,
                        'maxlength'=>64,
                        'size'=>CRM_UTILS_TYPE_BIG,
                    ) ,
                    'nick_name'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Nick Name') ,
                        'maxlength'=>64,
                        'size'=>CRM_UTILS_TYPE_BIG,
                    ) ,
                    'sic_code'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Sic Code') ,
                        'maxlength'=>8,
                        'size'=>CRM_UTILS_TYPE_EIGHT,
                    ) ,
                    'primary_contact_id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                    ) ,
                );
            }
            return $GLOBALS['_CRM_CONTACT_DAO_ORGANIZATION']['_fields'];
        }
        /**
        * returns the names of this table
        *
        * @access public
        * @return string
        */
        function getTableName() 
        {
            return $GLOBALS['_CRM_CONTACT_DAO_ORGANIZATION']['_tableName'];
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
            if (!($GLOBALS['_CRM_CONTACT_DAO_ORGANIZATION']['_import'])) {
                $GLOBALS['_CRM_CONTACT_DAO_ORGANIZATION']['_import'] = array();
                $fields = &CRM_Contact_DAO_Organization::fields();
                foreach($fields as $name=>$field) {
                    if (CRM_Utils_Array::value('import', $field)) {
                        if ($prefix) {
                            $GLOBALS['_CRM_CONTACT_DAO_ORGANIZATION']['_import']['Organization.'.$name] = &$field;
                        } else {
                            $GLOBALS['_CRM_CONTACT_DAO_ORGANIZATION']['_import'][$name] = &$field;
                        }
                    }
                }
            }
            return $GLOBALS['_CRM_CONTACT_DAO_ORGANIZATION']['_import'];
        }
    }
?>