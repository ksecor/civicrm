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
    $GLOBALS['_CRM_CORE_DAO_ACTIVITYHISTORY']['_tableName'] =  'crm_activity_history';
$GLOBALS['_CRM_CORE_DAO_ACTIVITYHISTORY']['_fields'] = null;
$GLOBALS['_CRM_CORE_DAO_ACTIVITYHISTORY']['_links'] = null;
$GLOBALS['_CRM_CORE_DAO_ACTIVITYHISTORY']['_import'] = null;


require_once 'CRM/Core/DAO.php';
require_once 'CRM/Utils/Array.php';
require_once 'CRM/Core/DAO.php';
    require_once 'CRM/Utils/Type.php';
    class CRM_Core_DAO_ActivityHistory extends CRM_Core_DAO {

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
        * table record id
        *
        * @var int unsigned
        */
        var $id;

        /**
        * physical tablename for entity being tagged, e.g. crm_contact
        *
        * @var string
        */
        var $entity_table;

        /**
        * FK to entity table specified in entity_table column
        *
        * @var int unsigned
        */
        var $entity_id;

        /**
        * sortable label for this activity assigned be registering module or user (e.g. Phone Call)
        *
        * @var string
        */
        var $activity_type;

        /**
        * Display name of module which registered this activity
        *
        * @var string
        */
        var $module;

        /**
        * Function to call which will return URL for viewing details
        *
        * @var string
        */
        var $callback;

        /**
        * FK to details item - passed to callback
        *
        * @var int unsigned
        */
        var $activity_id;

        /**
        * brief description of activity for summary display - as populated by registering module
        *
        * @var string
        */
        var $activity_summary;

        /**
        * when did this activity occur
        *
        * @var date
        */
        var $activity_date;

        /**
        * OPTIONAL FK to crm_relationship.id. Which relationship (of this contact) potentially triggered this activity, i.e. he donated because he was a Board Member of Org X / Employee of Org Y
        *
        * @var int unsigned
        */
        var $relationship_id;

        /**
        * OPTIONAL FK to crm_group.id. Was this part of a group communication that triggered this activity?
        *
        * @var int unsigned
        */
        var $group_id;

        /**
        * class constructor
        *
        * @access public
        * @return crm_activity_history
        */
        function CRM_Core_DAO_ActivityHistory() 
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
            if (!($GLOBALS['_CRM_CORE_DAO_ACTIVITYHISTORY']['_links'])) {
                $GLOBALS['_CRM_CORE_DAO_ACTIVITYHISTORY']['_links'] = array(
                    'relationship_id'=>'crm_relationship:id',
                    'group_id'=>'crm_group:id',
                );
            }
            return $GLOBALS['_CRM_CORE_DAO_ACTIVITYHISTORY']['_links'];
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
            if (!($GLOBALS['_CRM_CORE_DAO_ACTIVITYHISTORY']['_fields'])) {
                $GLOBALS['_CRM_CORE_DAO_ACTIVITYHISTORY']['_fields'] = array(
                    'id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'required'=>true,
                    ) ,
                    'entity_table'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Entity Table') ,
                        'maxlength'=>64,
                        'size'=>CRM_UTILS_TYPE_BIG,
                    ) ,
                    'entity_id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'required'=>true,
                    ) ,
                    'activity_type'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Activity Type') ,
                        'maxlength'=>64,
                        'size'=>CRM_UTILS_TYPE_BIG,
                    ) ,
                    'module'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Module') ,
                        'maxlength'=>64,
                        'size'=>CRM_UTILS_TYPE_BIG,
                    ) ,
                    'callback'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Callback') ,
                        'maxlength'=>64,
                        'size'=>CRM_UTILS_TYPE_BIG,
                    ) ,
                    'activity_id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'required'=>true,
                    ) ,
                    'activity_summary'=>array(
                        'type'=>CRM_UTILS_TYPE_T_STRING,
                        'title'=>ts('Activity Summary') ,
                        'maxlength'=>255,
                        'size'=>CRM_UTILS_TYPE_HUGE,
                    ) ,
                    'activity_date'=>array(
                        'type'=>CRM_UTILS_TYPE_T_DATE,
                        'title'=>ts('Activity Date') ,
                    ) ,
                    'relationship_id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                    ) ,
                    'group_id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                    ) ,
                );
            }
            return $GLOBALS['_CRM_CORE_DAO_ACTIVITYHISTORY']['_fields'];
        }
        /**
        * returns the names of this table
        *
        * @access public
        * @return string
        */
        function getTableName() 
        {
            return $GLOBALS['_CRM_CORE_DAO_ACTIVITYHISTORY']['_tableName'];
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
            if (!($GLOBALS['_CRM_CORE_DAO_ACTIVITYHISTORY']['_import'])) {
                $GLOBALS['_CRM_CORE_DAO_ACTIVITYHISTORY']['_import'] = array();
                $fields = &CRM_Core_DAO_ActivityHistory::fields();
                foreach($fields as $name=>$field) {
                    if (CRM_Utils_Array::value('import', $field)) {
                        if ($prefix) {
                            $GLOBALS['_CRM_CORE_DAO_ACTIVITYHISTORY']['_import']['ActivityHistory.'.$name] = &$field;
                        } else {
                            $GLOBALS['_CRM_CORE_DAO_ACTIVITYHISTORY']['_import'][$name] = &$field;
                        }
                    }
                }
            }
            return $GLOBALS['_CRM_CORE_DAO_ACTIVITYHISTORY']['_import'];
        }
    }
?>