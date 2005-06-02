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
    $GLOBALS['_CRM_CONTACT_DAO_RELATIONSHIPTYPE']['_tableName'] =  'crm_relationship_type';
$GLOBALS['_CRM_CONTACT_DAO_RELATIONSHIPTYPE']['_fields'] = '';
$GLOBALS['_CRM_CONTACT_DAO_RELATIONSHIPTYPE']['_links'] = '';
$GLOBALS['_CRM_CONTACT_DAO_RELATIONSHIPTYPE']['_import'] = '';

require_once 'CRM/Core/DAO.php';
require_once 'CRM/Utils/Array.php';
require_once 'CRM/Core/DAO.php';
    require_once 'CRM/Utils/Type.php';
    class CRM_Contact_DAO_RelationshipType extends CRM_Core_DAO {

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
        * Primary key
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
        * name/label for relationship of contact_a to contact_b.
        *
        * @var string
        */
        var $name_a_b;

        /**
        * Optional name/label for relationship of contact_b to contact_a.
        *
        * @var string
        */
        var $name_b_a;

        /**
        * Optional verbose description of the relationship type.
        *
        * @var string
        */
        var $description;

        /**
        * If defined, contact_a in a relationship of this type must be a specific contact_type.
        *
        * @var enum('Individual', 'Organization', 'Household')
        */
        var $contact_type_a;

        /**
        * If defined, contact_b in a relationship of this type must be a specific contact_type.
        *
        * @var enum('Individual', 'Organization', 'Household')
        */
        var $contact_type_b;

        /**
        * Is this relationship type a predefined system type (can not be changed or de-activated)?
        *
        * @var boolean
        */
        var $is_reserved;

        /**
        * Is this relationship type currently active (i.e. can be used when creating or editing relationships)?
        *
        * @var boolean
        */
        var $is_active;

        /**
        * class constructor
        *
        * @access public
        * @return crm_relationship_type
        */
        function CRM_Contact_DAO_RelationshipType() 
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
            if (!($GLOBALS['_CRM_CONTACT_DAO_RELATIONSHIPTYPE']['_links'])) {
                $GLOBALS['_CRM_CONTACT_DAO_RELATIONSHIPTYPE']['_links'] = array(
                    'domain_id'=>'crm_domain:id',
                );
            }
            return $GLOBALS['_CRM_CONTACT_DAO_RELATIONSHIPTYPE']['_links'];
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
            if (!($GLOBALS['_CRM_CONTACT_DAO_RELATIONSHIPTYPE']['_fields'])) {
                $GLOBALS['_CRM_CONTACT_DAO_RELATIONSHIPTYPE']['_fields'] = array(
                    'id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'required'=>true,
                    ) ,
                    'domain_id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'required'=>true,
                    ) ,
                    'name_a_b'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Name A B') ,
                        'maxlength'=>64,
                        'size'=>CRM_UTILS_TYPE_BIG,
                    ) ,
                    'name_b_a'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Name B A') ,
                        'maxlength'=>64,
                        'size'=>CRM_UTILS_TYPE_BIG,
                    ) ,
                    'description'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Description') ,
                        'maxlength'=>255,
                        'size'=>CRM_UTILS_TYPE_HUGE,
                    ) ,
                    'contact_type_a'=>array(
                        'type'=>CRM_UTILS_TYPE_T_ENUM,
                        'title'=>ts('Contact Type A') ,
                    ) ,
                    'contact_type_b'=>array(
                        'type'=>CRM_UTILS_TYPE_T_ENUM,
                        'title'=>ts('Contact Type B') ,
                    ) ,
                    'is_reserved'=>array(
                        'type'=>CRM_UTILS_TYPE_T_BOOLEAN,
                    ) ,
                    'is_active'=>array(
                        'type'=>CRM_UTILS_TYPE_T_BOOLEAN,
                    ) ,
                );
            }
            return $GLOBALS['_CRM_CONTACT_DAO_RELATIONSHIPTYPE']['_fields'];
        }
        /**
        * returns the names of this table
        *
        * @access public
        * @return string
        */
        function getTableName() 
        {
            return $GLOBALS['_CRM_CONTACT_DAO_RELATIONSHIPTYPE']['_tableName'];
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
            if (!($GLOBALS['_CRM_CONTACT_DAO_RELATIONSHIPTYPE']['_import'])) {
                $GLOBALS['_CRM_CONTACT_DAO_RELATIONSHIPTYPE']['_import'] = array();
                $fields = &CRM_Contact_DAO_RelationshipType::fields();
                foreach($fields as $name=>$field) {
                    if (CRM_Utils_Array::value('import', $field)) {
                        if ($prefix) {
                            $GLOBALS['_CRM_CONTACT_DAO_RELATIONSHIPTYPE']['_import']['RelationshipType.'.$name] = &$field;
                        } else {
                            $GLOBALS['_CRM_CONTACT_DAO_RELATIONSHIPTYPE']['_import'][$name] = &$field;
                        }
                    }
                }
            }
            return $GLOBALS['_CRM_CONTACT_DAO_RELATIONSHIPTYPE']['_import'];
        }
    }
?>