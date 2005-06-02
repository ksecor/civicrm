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
    $GLOBALS['_CRM_CORE_DAO_DRUPAL']['_tableName'] =  'crm_drupal';
$GLOBALS['_CRM_CORE_DAO_DRUPAL']['_fields'] = '';
$GLOBALS['_CRM_CORE_DAO_DRUPAL']['_links'] = '';
$GLOBALS['_CRM_CORE_DAO_DRUPAL']['_import'] = '';

require_once 'CRM/Core/DAO.php';
require_once 'CRM/Utils/Array.php';
require_once 'CRM/Core/DAO.php';
    require_once 'CRM/Utils/Type.php';
    class CRM_Core_DAO_Drupal extends CRM_Core_DAO {

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
        * System generated ID.
        *
        * @var int unsigned
        */
        var $id;

        /**
        * Drupal UID
        *
        * @var int unsigned
        */
        var $uid;

        /**
        * FK to Contact ID
        *
        * @var int unsigned
        */
        var $contact_id;

        /**
        * Which Domain owns this contact (cached here for ease of use reasons)
        *
        * @var int unsigned
        */
        var $domain_id;

        /**
        * class constructor
        *
        * @access public
        * @return crm_drupal
        */
        function CRM_Core_DAO_Drupal() 
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
            if (!($GLOBALS['_CRM_CORE_DAO_DRUPAL']['_links'])) {
                $GLOBALS['_CRM_CORE_DAO_DRUPAL']['_links'] = array(
                    'contact_id'=>'crm_contact:id',
                    'domain_id'=>'crm_domain:id',
                );
            }
            return $GLOBALS['_CRM_CORE_DAO_DRUPAL']['_links'];
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
            if (!($GLOBALS['_CRM_CORE_DAO_DRUPAL']['_fields'])) {
                $GLOBALS['_CRM_CORE_DAO_DRUPAL']['_fields'] = array(
                    'id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'required'=>true,
                    ) ,
                    'uid'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'title'=>ts('Uid') ,
                        'required'=>true,
                    ) ,
                    'contact_id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'required'=>true,
                    ) ,
                    'domain_id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'required'=>true,
                    ) ,
                );
            }
            return $GLOBALS['_CRM_CORE_DAO_DRUPAL']['_fields'];
        }
        /**
        * returns the names of this table
        *
        * @access public
        * @return string
        */
        function getTableName() 
        {
            return $GLOBALS['_CRM_CORE_DAO_DRUPAL']['_tableName'];
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
            if (!($GLOBALS['_CRM_CORE_DAO_DRUPAL']['_import'])) {
                $GLOBALS['_CRM_CORE_DAO_DRUPAL']['_import'] = array();
                $fields = &CRM_Core_DAO_Drupal::fields();
                foreach($fields as $name=>$field) {
                    if (CRM_Utils_Array::value('import', $field)) {
                        if ($prefix) {
                            $GLOBALS['_CRM_CORE_DAO_DRUPAL']['_import']['Drupal.'.$name] = &$field;
                        } else {
                            $GLOBALS['_CRM_CORE_DAO_DRUPAL']['_import'][$name] = &$field;
                        }
                    }
                }
            }
            return $GLOBALS['_CRM_CORE_DAO_DRUPAL']['_import'];
        }
    }
?>