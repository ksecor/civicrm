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
    $GLOBALS['_CRM_CORE_DAO_NOTE']['_tableName'] =  'crm_note';
$GLOBALS['_CRM_CORE_DAO_NOTE']['_fields'] = null;
$GLOBALS['_CRM_CORE_DAO_NOTE']['_links'] = null;
$GLOBALS['_CRM_CORE_DAO_NOTE']['_import'] = null;


require_once 'CRM/Core/DAO.php';
require_once 'CRM/Utils/Array.php';
require_once 'CRM/Core/DAO.php';
    require_once 'CRM/Utils/Type.php';
    class CRM_Core_DAO_Note extends CRM_Core_DAO {

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
        * Note ID
        *
        * @var int unsigned
        */
        var $id;

        /**
        * Name of table where item being referenced is stored.
        *
        * @var string
        */
        var $table_name;

        /**
        * Foreign key to the referenced item.
        *
        * @var int unsigned
        */
        var $table_id;

        /**
        * Note and/or Comment.
        *
        * @var text
        */
        var $note;

        /**
        * FK to Contact ID creator
        *
        * @var int unsigned
        */
        var $contact_id;

        /**
        * When was this note last modified/edited
        *
        * @var date
        */
        var $modified_date;

        /**
        * class constructor
        *
        * @access public
        * @return crm_note
        */
        function CRM_Core_DAO_Note() 
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
            if (!($GLOBALS['_CRM_CORE_DAO_NOTE']['_links'])) {
                $GLOBALS['_CRM_CORE_DAO_NOTE']['_links'] = array(
                    'contact_id'=>'crm_contact:id',
                );
            }
            return $GLOBALS['_CRM_CORE_DAO_NOTE']['_links'];
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
            if (!($GLOBALS['_CRM_CORE_DAO_NOTE']['_fields'])) {
                $GLOBALS['_CRM_CORE_DAO_NOTE']['_fields'] = array(
                    'id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'required'=>true,
                    ) ,
                    'table_name'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Table Name') ,
                        'required'=>true,
                        'maxlength'=>64,
                        'size'=>CRM_UTILS_TYPE_BIG,
                    ) ,
                    'table_id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'required'=>true,
                    ) ,
                    'note'=>array(
                        'type'=>CRM_UTILS_TYPE_T_TEXT,
                        'title'=>ts('Note') ,
                        'rows'=>4,
                        'cols'=>80,
                    ) ,
                    'contact_id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'required'=>true,
                    ) ,
                    'modified_date'=>array(
                        'type'=>CRM_UTILS_TYPE_T_DATE,
                        'title'=>ts('Modified Date') ,
                    ) ,
                );
            }
            return $GLOBALS['_CRM_CORE_DAO_NOTE']['_fields'];
        }
        /**
        * returns the names of this table
        *
        * @access public
        * @return string
        */
        function getTableName() 
        {
            return $GLOBALS['_CRM_CORE_DAO_NOTE']['_tableName'];
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
            if (!($GLOBALS['_CRM_CORE_DAO_NOTE']['_import'])) {
                $GLOBALS['_CRM_CORE_DAO_NOTE']['_import'] = array();
                $fields = &CRM_Core_DAO_Note::fields();
                foreach($fields as $name=>$field) {
                    if (CRM_Utils_Array::value('import', $field)) {
                        if ($prefix) {
                            $GLOBALS['_CRM_CORE_DAO_NOTE']['_import']['Note.'.$name] = &$field;
                        } else {
                            $GLOBALS['_CRM_CORE_DAO_NOTE']['_import'][$name] = &$field;
                        }
                    }
                }
            }
            return $GLOBALS['_CRM_CORE_DAO_NOTE']['_import'];
        }
    }
?>