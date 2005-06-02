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
    $GLOBALS['_CRM_CONTACT_DAO_GROUPCONTACT']['_tableName'] =  'crm_group_contact';
$GLOBALS['_CRM_CONTACT_DAO_GROUPCONTACT']['_fields'] = '';
$GLOBALS['_CRM_CONTACT_DAO_GROUPCONTACT']['_links'] = '';
$GLOBALS['_CRM_CONTACT_DAO_GROUPCONTACT']['_import'] = '';

require_once 'CRM/Core/DAO.php';
require_once 'CRM/Utils/Array.php';
require_once 'CRM/Core/DAO.php';
    require_once 'CRM/Utils/Type.php';
    class CRM_Contact_DAO_GroupContact extends CRM_Core_DAO {

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
        * FK to crm_group
        *
        * @var int unsigned
        */
        var $group_id;

        /**
        * FK to crm_contact
        *
        * @var int unsigned
        */
        var $contact_id;

        /**
        * status of contact relative to membership in group
        *
        * @var enum('Pending', 'In', 'Out')
        */
        var $status;

        /**
        * when was contact status for this group set to Pending
        *
        * @var date
        */
        var $pending_date;

        /**
        * when was contact status for this group set to In
        *
        * @var date
        */
        var $in_date;

        /**
        * when was contact status for this group set to Out
        *
        * @var date
        */
        var $out_date;

        /**
        * Interface by which contact was set to status = Pending
        *
        * @var enum('Admin', 'Email', 'Web', 'API')
        */
        var $pending_method;

        /**
        * Interface by which contact was set to status = In
        *
        * @var enum('Admin', 'Email', 'Web', 'API')
        */
        var $in_method;

        /**
        * Interface by which contact was set to status = Out
        *
        * @var enum('Admin', 'Email', 'Web', 'API')
        */
        var $out_method;

        /**
        * class constructor
        *
        * @access public
        * @return crm_group_contact
        */
        function CRM_Contact_DAO_GroupContact() 
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
            if (!isset($GLOBALS['_CRM_CONTACT_DAO_GROUPCONTACT']['_links'])) {
                $GLOBALS['_CRM_CONTACT_DAO_GROUPCONTACT']['_links'] = array(
                    'group_id'=>'crm_group:id',
                    'contact_id'=>'crm_contact:id',
                );
            }
            return $GLOBALS['_CRM_CONTACT_DAO_GROUPCONTACT']['_links'];
        }
        /**
        * returns all the column names of this table
        *
        * @access public
        * @return array
        */
        function &fields() 
        {
            if (!isset($GLOBALS['_CRM_CONTACT_DAO_GROUPCONTACT']['_fields'])) {
                $GLOBALS['_CRM_CONTACT_DAO_GROUPCONTACT']['_fields'] = array(
                    'id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'required'=>true,
                    ) ,
                    'group_id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'required'=>true,
                    ) ,
                    'contact_id'=>array(
                        'type'=>CRM_UTILS_TYPE_T_INT,
                        'required'=>true,
                    ) ,
                    'status'=>array(
                        'type'=>CRM_UTILS_TYPE_T_ENUM,
                        'title'=>ts('Status') ,
                    ) ,
                    'pending_date'=>array(
                        'type'=>CRM_UTILS_TYPE_T_DATE,
                        'title'=>ts('Pending Date') ,
                    ) ,
                    'in_date'=>array(
                        'type'=>CRM_UTILS_TYPE_T_DATE,
                        'title'=>ts('In Date') ,
                    ) ,
                    'out_date'=>array(
                        'type'=>CRM_UTILS_TYPE_T_DATE,
                        'title'=>ts('Out Date') ,
                    ) ,
                    'pending_method'=>array(
                        'type'=>CRM_UTILS_TYPE_T_ENUM,
                        'title'=>ts('Pending Method') ,
                    ) ,
                    'in_method'=>array(
                        'type'=>CRM_UTILS_TYPE_T_ENUM,
                        'title'=>ts('In Method') ,
                    ) ,
                    'out_method'=>array(
                        'type'=>CRM_UTILS_TYPE_T_ENUM,
                        'title'=>ts('Out Method') ,
                    ) ,
                );
            }
            return $GLOBALS['_CRM_CONTACT_DAO_GROUPCONTACT']['_fields'];
        }
        /**
        * returns the names of this table
        *
        * @access public
        * @return string
        */
        function getTableName() 
        {
            return $GLOBALS['_CRM_CONTACT_DAO_GROUPCONTACT']['_tableName'];
        }
        /**
        * returns the list of fields that can be imported
        *
        * @access public
        * return array
        */
        function &import($prefix = false) 
        {
            if (!isset($GLOBALS['_CRM_CONTACT_DAO_GROUPCONTACT']['_import'])) {
                $GLOBALS['_CRM_CONTACT_DAO_GROUPCONTACT']['_import'] = array();
                $fields = &CRM_Contact_DAO_GroupContact::fields();
                foreach($fields as $name=>$field) {
                    if (CRM_Utils_Array::value('import', $field)) {
                        if ($prefix) {
                            $GLOBALS['_CRM_CONTACT_DAO_GROUPCONTACT']['_import']['GroupContact.'.$name] = &$field;
                        } else {
                            $GLOBALS['_CRM_CONTACT_DAO_GROUPCONTACT']['_import'][$name] = &$field;
                        }
                    }
                }
            }
            return $GLOBALS['_CRM_CONTACT_DAO_GROUPCONTACT']['_import'];
        }
    }
?>