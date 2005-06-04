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
    $GLOBALS['_CRM_CONTACT_DAO_SAVEDSEARCH']['_tableName'] =  'crm_saved_search';
$GLOBALS['_CRM_CONTACT_DAO_SAVEDSEARCH']['_fields'] = null;
$GLOBALS['_CRM_CONTACT_DAO_SAVEDSEARCH']['_links'] = null;
$GLOBALS['_CRM_CONTACT_DAO_SAVEDSEARCH']['_import'] = null;


require_once 'CRM/Core/DAO.php';
require_once 'CRM/Utils/Array.php';
require_once 'CRM/Core/DAO.php';
    require_once 'CRM/Utils/Type.php';
    class CRM_Contact_DAO_SavedSearch extends CRM_Core_DAO {

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
        * Saved search ID
        *
        * @var int unsigned
        */
        var $id;

        /**
        * which organization/domain owns this search
        *
        * @var int unsigned
        */
        var $domain_id;

        /**
        * search name (brief)
        *
        * @var string
        */
        var $name;

        /**
        * used to identify the form associated with this saved search
        *
        * @var int unsigned
        */
        var $search_type;

        /**
        * verbose description
        *
        * @var string
        */
        var $description;

        /**
        * SQL query for this search
        *
        * @var text
        */
        var $query;

        /**
        * Submitted form values for this search
        *
        * @var text
        */
        var $form_values;

        /**
        * class constructor
        *
        * @access public
        * @return crm_saved_search
        */
        function CRM_Contact_DAO_SavedSearch() 
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
            if (!($GLOBALS['_CRM_CONTACT_DAO_SAVEDSEARCH']['_links'])) {
                $GLOBALS['_CRM_CONTACT_DAO_SAVEDSEARCH']['_links'] = array(
                    'domain_id'=>'crm_domain:id',
                );
            }
            return $GLOBALS['_CRM_CONTACT_DAO_SAVEDSEARCH']['_links'];
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
            if (!($GLOBALS['_CRM_CONTACT_DAO_SAVEDSEARCH']['_fields'])) {
                $GLOBALS['_CRM_CONTACT_DAO_SAVEDSEARCH']['_fields'] = array(
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
                        'title'=>ts('Search Name') ,
                        'maxlength'=>64,
                        'size'=>CRM_UTILS_TYPE_BIG,
                        'import'=>true,
                    ) ,
                    'search_type'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'title'=>ts('Identify form associated with saved search') ,
                        'import'=>true,
                    ) ,
                    'description'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Description') ,
                        'maxlength'=>255,
                        'size'=>CRM_UTILS_TYPE_HUGE,
                        'import'=>true,
                    ) ,
                    'query'=>array(
                        'type'=>CRM_UTILS_TYPE_T_TEXT,
                        'title'=>ts('SQL Query') ,
                        'import'=>true,
                    ) ,
                    'form_values'=>array(
                        'type'=>CRM_UTILS_TYPE_T_TEXT,
                        'title'=>ts('Submitted Form Values') ,
                        'import'=>true,
                    ) ,
                );
            }
            return $GLOBALS['_CRM_CONTACT_DAO_SAVEDSEARCH']['_fields'];
        }
        /**
        * returns the names of this table
        *
        * @access public
        * @return string
        */
        function getTableName() 
        {
            return $GLOBALS['_CRM_CONTACT_DAO_SAVEDSEARCH']['_tableName'];
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
            if (!($GLOBALS['_CRM_CONTACT_DAO_SAVEDSEARCH']['_import'])) {
                $GLOBALS['_CRM_CONTACT_DAO_SAVEDSEARCH']['_import'] = array();
                $fields = &CRM_Contact_DAO_SavedSearch::fields();
                foreach($fields as $name=>$field) {
                    if (CRM_Utils_Array::value('import', $field)) {
                        if ($prefix) {
                            $GLOBALS['_CRM_CONTACT_DAO_SAVEDSEARCH']['_import']['SavedSearch.'.$name] = &$field;
                        } else {
                            $GLOBALS['_CRM_CONTACT_DAO_SAVEDSEARCH']['_import'][$name] = &$field;
                        }
                    }
                }
            }
            return $GLOBALS['_CRM_CONTACT_DAO_SAVEDSEARCH']['_import'];
        }
    }
?>