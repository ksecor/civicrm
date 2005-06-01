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
    $GLOBALS['_CRM_CONTACT_DAO_RELATIONSHIP']['_tableName'] =  'crm_relationship';
$GLOBALS['_CRM_CONTACT_DAO_RELATIONSHIP']['_fields'] = '';
$GLOBALS['_CRM_CONTACT_DAO_RELATIONSHIP']['_links'] = '';
$GLOBALS['_CRM_CONTACT_DAO_RELATIONSHIP']['_import'] = '';

require_once 'CRM/Core/DAO.php';
require_once 'CRM/Utils/Array.php';
require_once 'CRM/Core/DAO.php';
    require_once 'CRM/Utils/Type.php';
    class CRM_Contact_DAO_Relationship extends CRM_Core_DAO {

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
        * Relationship ID
        *
        * @var int unsigned
        */
        var $id;

        /**
        * id of the first contact
        *
        * @var int unsigned
        */
        var $contact_id_a;

        /**
        * id of the second contact
        *
        * @var int unsigned
        */
        var $contact_id_b;

        /**
        * id of the relationship
        *
        * @var int unsigned
        */
        var $relationship_type_id;

        /**
        * date when the relationship started
        *
        * @var date
        */
        var $start_date;

        /**
        * date when the relationship ended
        *
        * @var date
        */
        var $end_date;

        /**
        * is the relationship active ?
        *
        * @var boolean
        */
        var $is_active;

        /**
        * class constructor
        *
        * @access public
        * @return crm_relationship
        */
        function CRM_Contact_DAO_Relationship() 
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
            if (!isset($GLOBALS['_CRM_CONTACT_DAO_RELATIONSHIP']['_links'])) {
                $GLOBALS['_CRM_CONTACT_DAO_RELATIONSHIP']['_links'] = array(
                    'contact_id_a'=>'crm_contact:id',
                    'contact_id_b'=>'crm_contact:id',
                    'relationship_type_id'=>'crm_relationship_type:id',
                );
            }
            return $GLOBALS['_CRM_CONTACT_DAO_RELATIONSHIP']['_links'];
        }
        /**
        * returns all the column names of this table
        *
        * @access public
        * @return array
        */
        function &fields() 
        {
            if (!isset($GLOBALS['_CRM_CONTACT_DAO_RELATIONSHIP']['_fields'])) {
                $GLOBALS['_CRM_CONTACT_DAO_RELATIONSHIP']['_fields'] = array(
                    'id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'required'=>true,
                    ) ,
                    'contact_id_a'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'required'=>true,
                    ) ,
                    'contact_id_b'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'required'=>true,
                    ) ,
                    'relationship_type_id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'required'=>true,
                    ) ,
                    'start_date'=>array(
                        'type'=>CRM_UTILS_TYPE_T_DATE,
                        'title'=>ts('Start Date') ,
                    ) ,
                    'end_date'=>array(
                        'type'=>CRM_UTILS_TYPE_T_DATE,
                        'title'=>ts('End Date') ,
                    ) ,
                    'is_active'=>array(
                        'type'=>CRM_UTILS_TYPE_T_BOOLEAN,
                    ) ,
                );
            }
            return $GLOBALS['_CRM_CONTACT_DAO_RELATIONSHIP']['_fields'];
        }
        /**
        * returns the names of this table
        *
        * @access public
        * @return string
        */
        function getTableName() 
        {
            return $GLOBALS['_CRM_CONTACT_DAO_RELATIONSHIP']['_tableName'];
        }
        /**
        * returns the list of fields that can be imported
        *
        * @access public
        * return array
        */
        function &import($prefix = false) 
        {
            if (!isset($GLOBALS['_CRM_CONTACT_DAO_RELATIONSHIP']['_import'])) {
                $GLOBALS['_CRM_CONTACT_DAO_RELATIONSHIP']['_import'] = array();
                $fields = &CRM_Contact_DAO_Relationship::fields();
                foreach($fields as $name=>$field) {
                    if (CRM_Utils_Array::value('import', $field)) {
                        if ($prefix) {
                            $GLOBALS['_CRM_CONTACT_DAO_RELATIONSHIP']['_import']['Relationship.'.$name] = &$field;
                        } else {
                            $GLOBALS['_CRM_CONTACT_DAO_RELATIONSHIP']['_import'][$name] = &$field;
                        }
                    }
                }
            }
            return $GLOBALS['_CRM_CONTACT_DAO_RELATIONSHIP']['_import'];
        }
    }
?>