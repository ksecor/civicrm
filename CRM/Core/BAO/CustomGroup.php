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

require_once 'CRM/Core/DAO/CustomGroup.php';

class CRM_Core_BAO_CustomGroup extends CRM_Core_DAO_CustomGroup {

    /**
     * class constructor
     */
    function __construct( ) {
        parent::__construct( );
    }

    /**
     * Takes a bunch of params that are needed to match certain criteria and
     * retrieves the relevant objects. Typically the valid params are only
     * contact_id. We'll tweak this function to be more full featured over a period
     * of time. This is the inverse function of create. It also stores all the retrieved
     * values in the default array
     *
     * @param array $params   (reference ) an assoc array of name/value pairs
     * @param array $defaults (reference ) an assoc array to hold the flattened values
     *
     * @return object CRM_Core_BAO_CustomGroup object
     * @access public
     * @static
     */
    static function retrieve(&$params, &$defaults)
    {
        $customGroup = new CRM_Core_DAO_CustomGroup();
        $customGroup->copyValues($params);
        if ($customGroup->find(true)) {
            $customGroup->storeValues($defaults);
            return $customGroup;
        }
        return null;
    }

    /**
     * update the is_active flag in the db
     *
     * @param int      $id        id of the database record
     * @param boolean  $is_active value we want to set the is_active field
     *
     * @return Object             DAO object on sucess, null otherwise
     * @static
     */
    static function setIsActive($id, $is_active) {
        $customGroup = new CRM_Core_DAO_CustomGroup();
        $customGroup->id = $id;
        if ($customGroup->find(true)) {
            $customGroup->is_active = $is_active;
            return $customGroup->save();
        }
        return null;
    }

    /**
     * Get custom groups/fields for type of entity.
     *
     * An array containing all custom groups and their custom fields is returned.
     *
     * @param string $entity -  of the contact whose contact type is needed
     *
     * @return array $basicTree
     *
     * @access public
     *
     * @static
     *
     */
    public static function getBasicTree($entity)
    {
        // create a new tree
        $groupTree = array();
        $strSelect = $strFrom = $strWhere = $orderBy = ''; 

        $tableData = array();

        // using tableData to build the queryString 
        $tableData = array(
                           'crm_custom_field' => array('label'),
                           'crm_custom_group' => array('title'),
                           );

        // create select
        $strSelect = "SELECT"; 
        foreach ($tableData as $tableName => $tableColumn) {
            foreach ($tableColumn as $columnName) {
                $strSelect .= " $tableName.$columnName,";
            }
        }
        $strSelect = rtrim($strSelect, ',');

        // from, where, order by
        $strFrom = " FROM crm_custom_field, crm_custom_group";
        $strWhere = " WHERE crm_custom_group.extends = '$entity' AND
                            crm_custom_field.custom_group_id = crm_custom_group.id";
        $orderBy = " ORDER BY crm_custom_group.weight, crm_custom_field.weight";

        // final query string
        $queryString = $strSelect . $strFrom . $strWhere . $orderBy;

        // dummy dao needed
        $crmDAO = new CRM_Core_DAO();
        $crmDAO->query($queryString);

        // process records
        while($crmDAO->fetch()) {
            if (!array_key_exists($crmDAO->title, $groupTree)) {
                $groupTree[$crmDAO->title] = array();
            }
            $groupTree[$crmDAO->title][$crmDAO->label] = "";
        }
        return $groupTree;
    }

    /**
     * Get custom groups/fields for type of entity.
     *
     * An array containing all custom groups and their custom fields is returned.
     *
     * @param string $entity -  of the contact whose contact type is needed
     *
     * @return array $customGroup
     *
     * @access public
     *
     * @static
     *
     */
    public static function getTree($entity, $entityID=null)
    {
        // create a new tree
        $groupTree = array();
        $strSelect = $strFrom = $strWhere = $orderBy = ''; 

        $tableData = array();

        // using tableData to build the queryString 
        $tableData = array(
                           'crm_custom_field' => array('id', 'name', 'label', 'data_type', 'html_type', 'default_value', 
                                                       'is_required', 'attributes', 'label'),
                           'crm_custom_group' => array('id', 'title'),
                           );

        // create select
        $strSelect = "SELECT"; 
        foreach ($tableData as $tableName => $tableColumn) {
            foreach ($tableColumn as $columnName) {
                $alias = $tableName . "_" . $columnName;
                $strSelect .= " $tableName.$columnName as $alias,";
            }
        }
        $strSelect = rtrim($strSelect, ',');

        // from, where, order by
        $strFrom = " FROM crm_custom_field, crm_custom_group";
        $strWhere = " WHERE crm_custom_group.extends = '$entity' AND
                            crm_custom_field.custom_group_id = crm_custom_group.id";
        $orderBy = " ORDER BY crm_custom_group.weight, crm_custom_field.weight";

        // final query string
        $queryString = $strSelect . $strFrom . $strWhere . $orderBy;

        // dummy dao needed
        $crmDAO = new CRM_Core_DAO();
        $crmDAO->query($queryString);

        // process records
        while($crmDAO->fetch()) {

            $groupID = $crmDAO->crm_custom_group_id;
            $fieldID = $crmDAO->crm_custom_field_id;

            // create an array for groups if it does not exist
            if (!array_key_exists($groupID, $groupTree)) {
                $groupTree[$groupID] = array();
                $groupTree[$groupID]['id'] = $groupID;
                $groupTree[$groupID]['title'] = $crmDAO->crm_custom_group_title;
                $groupTree[$groupID]['fields'] = array();
            }
            
            // add the fields now
            $groupTree[$groupID]['fields'][$fieldID] = array();
            $groupTree[$groupID]['fields'][$fieldID]['id'] = $fieldID;

            foreach ($tableData['crm_custom_field'] as $v) {
                if ($v == 'id') {
                    continue;
                } else {
                    $fullField = "crm_custom_field_" . $v;
                    $groupTree[$groupID]['fields'][$fieldID][$v] = $crmDAO->$fullField;                    
                }
            }
        }

        if ($entityID) {
            // hack for now.. using only contacts custom data
            self::_populateCustomData($groupTree, $entityID);
        }

        return $groupTree;
    }


    /**
     * Get custom data for a contact.
     *
     * @param int $id - id of the contact whose custom data is needed
     *
     * @return array customData
     *
     * @access public
     *
     * @static
     *
     */
    private static function _populateCustomData(&$groupTree, $id)
    {
        $strSelect = $strFrom = $strWhere = $orderBy = ''; 

        $tableData = array();

        // using tableData to build the queryString 
        $tableData = array(
                           'crm_custom_value' => array('id', 'int_data', 'float_data', 'char_data', 'date_data', 'memo_data'),
                           'crm_custom_field' => array('id'),
                           'crm_custom_group' => array('id'),
                           );

        // create select
        $strSelect = "SELECT"; 
        foreach ($tableData as $tableName => $tableColumn) {
            foreach ($tableColumn as $columnName) {
                $alias = $tableName . '_' . $columnName;
                $strSelect .= " $tableName.$columnName as $alias,";
            }
        }
        $strSelect = rtrim($strSelect, ',');

        // from, where, order by
        $strFrom = " FROM crm_custom_value, crm_custom_field, crm_custom_group";
        $strWhere = " WHERE crm_custom_value.entity_id = $id AND
                            crm_custom_value.custom_field_id = crm_custom_field.id AND
                            crm_custom_field.custom_group_id = crm_custom_group.id";
        $orderBy = " ORDER BY crm_custom_group.weight, crm_custom_field.weight";

        // final query string
        $queryString = $strSelect . $strFrom . $strWhere . $orderBy;

        // dummy dao needed
        $crmDAO = new CRM_Core_DAO();
        $crmDAO->query($queryString);

        // process records
        while($crmDAO->fetch()) {
            $groupID = $crmDAO->crm_custom_group_id;
            $fieldID = $crmDAO->crm_custom_field_id;
            $valueID = $crmDAO->crm_custom_value_id;

            $groupTree[$groupID]['fields'][$fieldID]['customValue'] = array();
            $groupTree[$groupID]['fields'][$fieldID]['customValue']['id'] = $valueID;

            $dataType = $groupTree[$groupID]['fields'][$fieldID]['data_type'];

            switch ($dataType) {
            case 'String':
                $groupTree[$groupID]['fields'][$fieldID]['customValue']['data'] = $crmDAO->crm_custom_value_char_data;
                break;
            case 'Int':
            case 'Boolean':
                $groupTree[$groupID]['fields'][$fieldID]['customValue']['data'] = $crmDAO->crm_custom_value_int_data;
                break;
            case 'Float':
                $groupTree[$groupID]['fields'][$fieldID]['customValue']['data'] = $crmDAO->crm_custom_value_float_data;
                break;
            case 'Text':
                $groupTree[$groupID]['fields'][$fieldID]['customValue']['data'] = $crmDAO->crm_custom_value_memo_data;
                break;
            case 'Date':
                $groupTree[$groupID]['fields'][$fieldID]['customValue']['data'] = $crmDAO->crm_custom_value_date_data;
                break;
            }
        }
        return;
    }



    /**
     * Update custom data.
     * Add custom data to the contact table.
     *
     * @param int $id - id of the contact whose custom data is needed
     *
     * @return none
     *
     * @access public
     *
     * @static
     *
     */
    public static function updateCustomData(&$groupTree, $entityID)
    {
        CRM_Core_Error::le_method();
        
        foreach ($groupTree as $group) {
            $groupID = $group['id'];
            foreach ($group as $field) {
                $fieldID = $field['id'];
                if (isset($field['customValue'])) {
                    $customValueDAO = new CRM_Core_DAO_CustomValue();
                    
                    // check if it's an update or new one
                    if (isset($field['customValue']['id'])) {
                        $customValueDAO->id = $field['customValue']['id'];
                    }
                    switch ($field['data_type']) {
                    case 'String':
                        $customValueDAO->char_data = $field['customValue']['data'];
                        break;
                    case 'Int':
                    case 'Boolean':
                        $customValueDAO->int_data = $field['customValue']['data'];
                        break;
                    case 'Float':
                        $customValueDAO->float_data = $field['customValue']['data'];
                        break;
                    case 'Text':
                        $customValueDAO->memo_data = $field['customValue']['data'];
                        break;
                    case 'Date':
                        $customValueDAO->date_data = $field['customValue']['data'];
                        break;
                    }
                    // $customValueDAO->save();
                }
            }
        }
        CRM_Core_Error::ll_method();
    }
}

?>