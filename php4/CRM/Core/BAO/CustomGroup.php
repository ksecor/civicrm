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
require_once 'CRM/Core/DAO.php';
require_once 'CRM/Core/DAO/CustomValue.php';
require_once 'CRM/Core/DAO/CustomGroup.php';

/**
 * Business object for managing custom data groups
 *
 */
class CRM_Core_BAO_CustomGroup extends CRM_Core_DAO_CustomGroup {

    /**
     * class constructor
     */
    function CRM_Core_BAO_CustomGroup( ) {
        parent::CRM_Core_DAO_CustomGroup( );
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
     function retrieve(&$params, &$defaults)
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
     function setIsActive($id, $is_active) {
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
     * @param string $entity   - of the contact whose contact type is needed
     * @param int    $entityId - optional - id of entity if we need to populate the tree with custom values. 
     * @return array $groupTree - array consisting of all groups and fields and optionally populated with custom data values.
     *
     * @access public
     *
     * @static
     *
     */
      function getTree($entity, $entityId=null)
    {
        // create a new tree
        $groupTree = array();
        $strSelect = $strFrom = $strWhere = $orderBy = ''; 

        $tableData = array();

        // using tableData to build the queryString 
        $tableData = array(
                           'crm_custom_field' => array('id', 'name', 'label', 'data_type', 'html_type', 'default_value', 'attributes',
                                                       'is_required', 'help_post'),
                           'crm_custom_group' => array('id', 'title', 'help_pre'),
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

        // if entity is either individual, organization or household pls get
        // custom groups for 'contact' too.
        if ($entity == "Individual" || $entity == 'Organization' || $entity == 'Household') {
            $in = "'$entity', 'Contact'";
        } else {
            $in = "'$entity'";
        }

        $strWhere = " WHERE crm_custom_field.custom_group_id = crm_custom_group.id
                            AND crm_custom_group.is_active = 1
                            AND crm_custom_field.is_active = 1
                            AND crm_custom_group.extends IN ($in)";

        $orderBy = " ORDER BY crm_custom_group.weight, crm_custom_field.weight";

        // final query string
        $queryString = $strSelect . $strFrom . $strWhere . $orderBy;

        // dummy dao needed
        $crmDAO = new CRM_Core_DAO();
        $crmDAO->query($queryString);

        // process records
        while($crmDAO->fetch()) {

            $groupId = $crmDAO->crm_custom_group_id;
            $fieldId = $crmDAO->crm_custom_field_id;

            // create an array for groups if it does not exist
            if (!array_key_exists($groupId, $groupTree)) {
                $groupTree[$groupId] = array();
                $groupTree[$groupId]['id'] = $groupId;
                $groupTree[$groupId]['title'] = $crmDAO->crm_custom_group_title;
                $groupTree[$groupId]['help_pre'] = $crmDAO->crm_custom_group_help_pre;
                $groupTree[$groupId]['fields'] = array();
            }
            
            // add the fields now (note - the query row will always contain a field)
            $groupTree[$groupId]['fields'][$fieldId] = array();
            $groupTree[$groupId]['fields'][$fieldId]['id'] = $fieldId;

            foreach ($tableData['crm_custom_field'] as $v) {
                //if ($v == 'id') {
                $fullField = "crm_custom_field_" . $v;
                if ($v == 'id' || is_null($crmDAO->$fullField)) {
                    continue;
                } else {
                    $groupTree[$groupId]['fields'][$fieldId][$v] = $crmDAO->$fullField;                    
                }
            }
        }

        if ($entityId) {
            // hack for now.. using only contacts custom data
            CRM_Core_BAO_CustomGroup::_populateCustomData($groupTree, $entityId);
        }

        return $groupTree;
    }

    /**
     * Populates the GroupTree with custom data for the specified entity id
     *
     * @param array reference $groupTree
     * @param int $id - id of the entity.
     * @return none
     *
     * @access public
     * @static
     *
     */
      function _populateCustomData(&$groupTree, $id)
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
        $strWhere = " WHERE crm_custom_value.entity_id = $id
                            AND crm_custom_value.custom_field_id = crm_custom_field.id
                            AND crm_custom_field.custom_group_id = crm_custom_group.id
                            AND crm_custom_group.is_active = 1
                            AND crm_custom_field.is_active = 1";
        $orderBy = " ORDER BY crm_custom_group.weight, crm_custom_field.weight";

        // final query string
        $queryString = $strSelect . $strFrom . $strWhere . $orderBy;

        // dummy dao needed
        $crmDAO = new CRM_Core_DAO();
        $crmDAO->query($queryString);

        // process records
        while($crmDAO->fetch()) {
            $groupId = $crmDAO->crm_custom_group_id;
            $fieldId = $crmDAO->crm_custom_field_id;
            $valueId = $crmDAO->crm_custom_value_id;

            // create an array for storing custom values for that field
            $groupTree[$groupId]['fields'][$fieldId]['customValue'] = array();
            $groupTree[$groupId]['fields'][$fieldId]['customValue']['id'] = $valueId;

            $dataType = $groupTree[$groupId]['fields'][$fieldId]['data_type'];

            switch ($dataType) {
            case 'String':
                $groupTree[$groupId]['fields'][$fieldId]['customValue']['data'] = $crmDAO->crm_custom_value_char_data;
                break;
            case 'Int':
            case 'Boolean':
                $groupTree[$groupId]['fields'][$fieldId]['customValue']['data'] = $crmDAO->crm_custom_value_int_data;
                break;
            case 'Float':
            case 'Money':
                $groupTree[$groupId]['fields'][$fieldId]['customValue']['data'] = $crmDAO->crm_custom_value_float_data;
                break;
            case 'Memo':
                $groupTree[$groupId]['fields'][$fieldId]['customValue']['data'] = $crmDAO->crm_custom_value_memo_data;
                break;
            case 'Date':
                $groupTree[$groupId]['fields'][$fieldId]['customValue']['data'] = $crmDAO->crm_custom_value_date_data;
                break;
            }
        }
        return;
    }


    /**
     * Update custom data.
     *
     *  - custom data is modified as follows
     *    - if custom data is changed it's updated.
     *    - if custom data is newly entered in field, it's inserted into db, finally
     *    - if existing custom data is cleared, it is deleted from the table.
     *
     * @param array reference &$groupTree - array of all custom groups, fields and values.
     * @param int $id - id of the contact whose custom data is to be updated
     * @return none
     *
     * @access public
     * @static
     *
     */
      function updateCustomData(&$groupTree, $entityId)
    {
        // traverse the group tree
        foreach ($groupTree as $group) {
            $groupId = $group['id'];

            // traverse fields
            foreach ($group['fields'] as $field) {
                $fieldId = $field['id'];

                /**
                 * $field['customValue'] is set in the tree in the following cases
                 *     - data existed in db for that field
                 *     - new data was entered in the "form" for that field
                */
                if (isset($field['customValue'])) {
                    // customValue exists hence we need a DAO.
                    $customValueDAO = new CRM_Core_DAO_CustomValue();
                    $customValueDAO->entity_table = 'contact'; // hard coded for now.
                    $customValueDAO->custom_field_id = $fieldId;
                    $customValueDAO->entity_id = $entityId;
                    
                    // check if it's an update or new one
                    if (isset($field['customValue']['id'])) {
                        // get the id of row in crm_custom_value
                        $customValueDAO->id = $field['customValue']['id'];
                    }

                    $data = $field['customValue']['data'];

                    // since custom data is empty, delete it from db.
                    // note we cannot use a plain if($data) since data could have a value "0"
                    // which will result in a !false expression
                    // and accidental deletion of data.
                    // especially true in the case of radio buttons where we are using the values
                    // 1 - true and 0 for false.
                    if (! strlen(trim($data) ) ) {
                        $customValueDAO->delete();
                        continue;
                    }
                    
                    // data is not empty
                    switch ($field['data_type']) {
                    case 'String':
                        $customValueDAO->char_data = $data;
                        break;
                    case 'Int':
                    case 'Boolean':
                        $customValueDAO->int_data = $data;
                        break;
                    case 'Float':
                    case 'Money':
                        $customValueDAO->float_data = $data;
                        break;
                    case 'Memo':
                        $customValueDAO->memo_data = $data;
                        break;
                    case 'Date':
                        $customValueDAO->date_data = $data;
                        break;
                    }

                    // insert/update of custom value
                    $customValueDAO->save();
                }
            }
        }
    }


    /**
     * Get number of elements for a particular group.
     *
     * This method returns the number of entries in the crm_custom_value table for this particular group.
     *
     * @param int $groupId - id of group.
     * @return int $numValue - number of custom data values for this group.
     *
     * @access public
     * @static
     *
     */
      function getNumValue($groupId)
    {
         $queryString = "SELECT count(*) 
                         FROM   crm_custom_value, crm_custom_field 
                         WHERE  crm_custom_value.custom_field_id = crm_custom_field.id AND
                                crm_custom_field.custom_group_id = $groupId";

         // this might be faster
         // $queryString = "SELECT count(*) 
         // FROM   crm_custom_value
         // WHERE  crm_custom_value.custom_field_id IN (SELECT id FROM crm_custom_field WHERE custom_group_id = $groupId)";

        // dummy dao needed
        $crmDAO = new CRM_Core_DAO();
        $crmDAO->query($queryString);
        // does not work for php4
        //$row = $crmDAO->getDatabaseResult()->fetchRow();
        $result = $crmDAO->getDatabaseResult();
        $row    = $result->fetchRow();
        return $row[0];
    }


    /**
     * Get the group title.
     *
     * @param int $groupId - id of group.
     * @return string $groupTitle
     *
     * @access public
     * @static
     *
     */
      function getTitle($groupId)
    {
        $dao = new CRM_Core_DAO_CustomGroup();
        $dao->id = $groupId;
        $dao->find(1);
        return $dao->title;
    }
}
?>