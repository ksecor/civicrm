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
    $GLOBALS['_CRM_CONTACT_DAO_LOCATIONTYPE']['_tableName'] =  'crm_location_type';
$GLOBALS['_CRM_CONTACT_DAO_LOCATIONTYPE']['_fields'] = '';
$GLOBALS['_CRM_CONTACT_DAO_LOCATIONTYPE']['_links'] = '';
$GLOBALS['_CRM_CONTACT_DAO_LOCATIONTYPE']['_import'] = '';

require_once 'CRM/Core/DAO.php';
require_once 'CRM/Utils/Array.php';
require_once 'CRM/Core/DAO.php';
    require_once 'CRM/Utils/Type.php';
    class CRM_Contact_DAO_LocationType extends CRM_Core_DAO {

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
        * Location Type ID
        *
        * @var int unsigned
        */
        var $id;

        /**
        * Which Domain owns this location type.
        *
        * @var int unsigned
        */
        var $domain_id;

        /**
        * Location Type Name.
        *
        * @var string
        */
        var $name;

        /**
        * Location Type Description.
        *
        * @var string
        */
        var $description;

        /**
        * Is this location type a predefined system location?
        *
        * @var boolean
        */
        var $is_reserved;

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
        * @return crm_location_type
        */
        function CRM_Contact_DAO_LocationType() 
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
            if (!($GLOBALS['_CRM_CONTACT_DAO_LOCATIONTYPE']['_links'])) {
                $GLOBALS['_CRM_CONTACT_DAO_LOCATIONTYPE']['_links'] = array(
                    'domain_id'=>'crm_domain:id',
                );
            }
            return $GLOBALS['_CRM_CONTACT_DAO_LOCATIONTYPE']['_links'];
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
            if (!($GLOBALS['_CRM_CONTACT_DAO_LOCATIONTYPE']['_fields'])) {
                $GLOBALS['_CRM_CONTACT_DAO_LOCATIONTYPE']['_fields'] = array(
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
                        'title'=>ts('Location Type') ,
                        'maxlength'=>64,
                        'size'=>CRM_UTILS_TYPE_BIG,
                        'import'=>true,
                    ) ,
                    'description'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Description') ,
                        'maxlength'=>255,
                        'size'=>CRM_UTILS_TYPE_HUGE,
                    ) ,
                    'is_reserved'=>array(
                        'type'=>CRM_UTILS_TYPE_T_BOOLEAN,
                    ) ,
                    'is_active'=>array(
                        'type'=>CRM_UTILS_TYPE_T_BOOLEAN,
                    ) ,
                );
            }
            return $GLOBALS['_CRM_CONTACT_DAO_LOCATIONTYPE']['_fields'];
        }
        /**
        * returns the names of this table
        *
        * @access public
        * @return string
        */
        function getTableName() 
        {
            return $GLOBALS['_CRM_CONTACT_DAO_LOCATIONTYPE']['_tableName'];
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
            if (!($GLOBALS['_CRM_CONTACT_DAO_LOCATIONTYPE']['_import'])) {
                $GLOBALS['_CRM_CONTACT_DAO_LOCATIONTYPE']['_import'] = array();
                $fields = &CRM_Contact_DAO_LocationType::fields();
                foreach($fields as $name=>$field) {
                    if (CRM_Utils_Array::value('import', $field)) {
                        if ($prefix) {
                            $GLOBALS['_CRM_CONTACT_DAO_LOCATIONTYPE']['_import']['LocationType.'.$name] = &$field;
                        } else {
                            $GLOBALS['_CRM_CONTACT_DAO_LOCATIONTYPE']['_import'][$name] = &$field;
                        }
                    }
                }
            }
            return $GLOBALS['_CRM_CONTACT_DAO_LOCATIONTYPE']['_import'];
        }
    }
?>