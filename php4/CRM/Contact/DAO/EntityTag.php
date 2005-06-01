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
    $GLOBALS['_CRM_CONTACT_DAO_ENTITYTAG']['_tableName'] =  'crm_entity_tag';
$GLOBALS['_CRM_CONTACT_DAO_ENTITYTAG']['_fields'] = '';
$GLOBALS['_CRM_CONTACT_DAO_ENTITYTAG']['_links'] = '';
$GLOBALS['_CRM_CONTACT_DAO_ENTITYTAG']['_import'] = '';

require_once 'CRM/Core/DAO.php';
require_once 'CRM/Utils/Array.php';
require_once 'CRM/Core/DAO.php';
    require_once 'CRM/Utils/Type.php';
    class CRM_Contact_DAO_EntityTag extends CRM_Core_DAO {

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
        * primary key
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
        * FK to entity table specified in entity_table column.
        *
        * @var int unsigned
        */
        var $entity_id;

        /**
        * FK to crm_tag
        *
        * @var int unsigned
        */
        var $tag_id;

        /**
        * class constructor
        *
        * @access public
        * @return crm_entity_tag
        */
        function CRM_Contact_DAO_EntityTag() 
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
            if (!isset($GLOBALS['_CRM_CONTACT_DAO_ENTITYTAG']['_links'])) {
                $GLOBALS['_CRM_CONTACT_DAO_ENTITYTAG']['_links'] = array(
                    'tag_id'=>'crm_tag:id',
                );
            }
            return $GLOBALS['_CRM_CONTACT_DAO_ENTITYTAG']['_links'];
        }
        /**
        * returns all the column names of this table
        *
        * @access public
        * @return array
        */
        function &fields() 
        {
            if (!isset($GLOBALS['_CRM_CONTACT_DAO_ENTITYTAG']['_fields'])) {
                $GLOBALS['_CRM_CONTACT_DAO_ENTITYTAG']['_fields'] = array(
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
                    'tag_id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'required'=>true,
                    ) ,
                );
            }
            return $GLOBALS['_CRM_CONTACT_DAO_ENTITYTAG']['_fields'];
        }
        /**
        * returns the names of this table
        *
        * @access public
        * @return string
        */
        function getTableName() 
        {
            return $GLOBALS['_CRM_CONTACT_DAO_ENTITYTAG']['_tableName'];
        }
        /**
        * returns the list of fields that can be imported
        *
        * @access public
        * return array
        */
        function &import($prefix = false) 
        {
            if (!isset($GLOBALS['_CRM_CONTACT_DAO_ENTITYTAG']['_import'])) {
                $GLOBALS['_CRM_CONTACT_DAO_ENTITYTAG']['_import'] = array();
                $fields = &CRM_Contact_DAO_EntityTag::fields();
                foreach($fields as $name=>$field) {
                    if (CRM_Utils_Array::value('import', $field)) {
                        if ($prefix) {
                            $GLOBALS['_CRM_CONTACT_DAO_ENTITYTAG']['_import']['EntityTag.'.$name] = &$field;
                        } else {
                            $GLOBALS['_CRM_CONTACT_DAO_ENTITYTAG']['_import'][$name] = &$field;
                        }
                    }
                }
            }
            return $GLOBALS['_CRM_CONTACT_DAO_ENTITYTAG']['_import'];
        }
    }
?>