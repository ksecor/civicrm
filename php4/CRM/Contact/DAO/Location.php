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
    $GLOBALS['_CRM_CONTACT_DAO_LOCATION']['_tableName'] =  'crm_location';
$GLOBALS['_CRM_CONTACT_DAO_LOCATION']['_fields'] = null;
$GLOBALS['_CRM_CONTACT_DAO_LOCATION']['_links'] = null;
$GLOBALS['_CRM_CONTACT_DAO_LOCATION']['_import'] = null;


require_once 'CRM/Core/DAO.php';
require_once 'CRM/Utils/Array.php';
require_once 'CRM/Contact/DAO/LocationType.php';
require_once 'CRM/Core/DAO.php';
    require_once 'CRM/Utils/Type.php';
    class CRM_Contact_DAO_Location extends CRM_Core_DAO {

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
        * Unique Location ID
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
        * FK to Location Type ID
        *
        * @var int unsigned
        */
        var $location_type_id;

        /**
        * Is this the primary location for the contact. (allow only ONE primary location / contact.)
        *
        * @var boolean
        */
        var $is_primary;

        /**
        * class constructor
        *
        * @access public
        * @return crm_location
        */
        function CRM_Contact_DAO_Location() 
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
            if (!($GLOBALS['_CRM_CONTACT_DAO_LOCATION']['_links'])) {
                $GLOBALS['_CRM_CONTACT_DAO_LOCATION']['_links'] = array(
                    'contact_id'=>'crm_contact:id',
                    'location_type_id'=>'crm_location_type:id',
                );
            }
            return $GLOBALS['_CRM_CONTACT_DAO_LOCATION']['_links'];
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
            if (!($GLOBALS['_CRM_CONTACT_DAO_LOCATION']['_fields'])) {
                $GLOBALS['_CRM_CONTACT_DAO_LOCATION']['_fields'] = array(
                    'id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'required'=>true,
                    ) ,
                    'contact_id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'required'=>true,
                    ) ,
                    'location_type_id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'required'=>true,
                    ) ,
                    'is_primary'=>array(
                        'type'=>CRM_UTILS_TYPE_T_BOOLEAN,
                    ) ,
                );
            }
            return $GLOBALS['_CRM_CONTACT_DAO_LOCATION']['_fields'];
        }
        /**
        * returns the names of this table
        *
        * @access public
        * @return string
        */
        function getTableName() 
        {
            return $GLOBALS['_CRM_CONTACT_DAO_LOCATION']['_tableName'];
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
            if (!($GLOBALS['_CRM_CONTACT_DAO_LOCATION']['_import'])) {
                $GLOBALS['_CRM_CONTACT_DAO_LOCATION']['_import'] = array();
                $fields = &CRM_Contact_DAO_Location::fields();
                foreach($fields as $name=>$field) {
                    if (CRM_Utils_Array::value('import', $field)) {
                        if ($prefix) {
                            $GLOBALS['_CRM_CONTACT_DAO_LOCATION']['_import']['Location.'.$name] = &$field;
                        } else {
                            $GLOBALS['_CRM_CONTACT_DAO_LOCATION']['_import'][$name] = &$field;
                        }
                    }
                }
                $GLOBALS['_CRM_CONTACT_DAO_LOCATION']['_import'] = array_merge($GLOBALS['_CRM_CONTACT_DAO_LOCATION']['_import'], CRM_Contact_DAO_LocationType::import(true));
            }
            return $GLOBALS['_CRM_CONTACT_DAO_LOCATION']['_import'];
        }
    }
?>