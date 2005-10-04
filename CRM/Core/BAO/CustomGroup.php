<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.1                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Social Source Foundation                        |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the Affero General Public License Version 1,    |
 | March 2002.                                                        |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the Affero General Public License for more details.            |
 |                                                                    |
 | You should have received a copy of the Affero General Public       |
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Social Source Foundation (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Core/DAO/CustomGroup.php';

/**
 * Business object for managing custom data groups
 *
 */
class CRM_Core_BAO_CustomGroup extends CRM_Core_DAO_CustomGroup {

    /**
     * class constructor
     */
    function __construct( ) {
        parent::__construct( );
    }



    /**
     * takes an associative array and creates a custom group object
     *
     * This function is invoked from within the web form layer and also from the api layer
     *
     * @param array $params (reference) an assoc array of name/value pairs
     *
     * @return object CRM_Core_DAO_HistoryHistory object 
     * @access public
     * @static
     */
    static function create(&$params)
    {
        $customGroupBAO =& new CRM_Core_BAO_CustomGroup();
        $customGroupBAO->copyValues($params);
        return $customGroupBAO->save();
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
        return CRM_Core_DAO::commonRetrieve( 'CRM_Core_DAO_CustomGroup', $params, $defaults );
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
        return CRM_Core_DAO::setFieldValue( 'CRM_Core_DAO_CustomGroup', $id, 'is_active', $is_active );
    }

    /**
     * Get custom groups/fields for type of entity.
     *
     * An array containing all custom groups and their custom fields is returned.
     *
     * @param string $entityType - of the contact whose contact type is needed
     * @param int    $entityId   - optional - id of entity if we need to populate the tree with custom values. 
     * @param int    $groupId    - optional group id (if we need it for a single group only)
     *                           - if groupId is 0 it gets for inline groups only
     *                           - if groupId is -1 we get for all groups
     *
     * @return array $groupTree  - array consisting of all groups and fields and optionally populated with custom data values.
     *
     * @access public
     *
     * @static
     *
     */
    public static function getTree($entityType, $entityId=null, $groupId=0)
    {
        // create a new tree
        $groupTree = array();
        $strSelect = $strFrom = $strWhere = $orderBy = ''; 
        $tableData = array();

        // using tableData to build the queryString 
        $tableData = array(
                           'civicrm_custom_field' => array('id', 'name', 'label', 'data_type', 'html_type', 'default_value', 'attributes',
                                                       'is_required', 'help_post','options_per_line'),
                           'civicrm_custom_group' => array('id', 'title', 'help_pre', 'collapse_display'),
                           );

        // since we have an entity id, lets get it's custom values too.
        if ($entityId) {
            $tableData['civicrm_custom_value'] = array('id', 'int_data', 'float_data', 'decimal_data', 'char_data', 'date_data', 'memo_data');
        }

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
        $strFrom = " FROM civicrm_custom_group LEFT JOIN civicrm_custom_field ON (civicrm_custom_field.custom_group_id = civicrm_custom_group.id)";
        if ($entityId) {
            $tableName = self::_getTableName($entityType);
            $strFrom .= " LEFT JOIN civicrm_custom_value
                                 ON ( civicrm_custom_value.custom_field_id = civicrm_custom_field.id 
                                AND   civicrm_custom_value.entity_table = '$tableName' 
                                AND   civicrm_custom_value.entity_id = $entityId )";
        }

        // if entity is either individual, organization or household pls get custom groups for 'contact' too.
        if ($entityType == "Individual" || $entityType == 'Organization' || $entityType == 'Household') {
            $in = "'$entityType', 'Contact'";
        } else {
            $in = "'$entityType'";
        }

        $strWhere = " WHERE civicrm_custom_group.is_active = 1 AND civicrm_custom_field.is_active = 1 AND civicrm_custom_group.extends IN ($in)";

        if ($groupId > 0) {
            // since we want a specific group id we add it to the where clause
            $strWhere .= " AND civicrm_custom_group.style = 'Tab' AND civicrm_custom_group.id = " 
                      .  CRM_Utils_Type::escape($groupId, 'Integer');
        } else if ($groupId == 0){
            // since groupId is 0 we need to show all Inline groups
            $strWhere .= " AND civicrm_custom_group.style = 'Inline'";
        }
        $orderBy = " ORDER BY civicrm_custom_group.weight, civicrm_custom_group.title, civicrm_custom_field.weight, civicrm_custom_field.label ";

        // final query string
        $queryString = $strSelect . $strFrom . $strWhere . $orderBy;

        // dummy dao needed
        $crmDAO =& CRM_Core_DAO::executeQuery( $queryString );

        // process records
        while($crmDAO->fetch()) {

            // get the id's 
            $groupId = $crmDAO->civicrm_custom_group_id;
            $fieldId = $crmDAO->civicrm_custom_field_id;

            // create an array for groups if it does not exist
            if (!array_key_exists($groupId, $groupTree)) {
                $groupTree[$groupId] = array();
                $groupTree[$groupId]['id'] = $groupId;

                // populate the group information
                foreach ($tableData['civicrm_custom_group'] as $fieldName) {
                    $fullFieldName = 'civicrm_custom_group_' . $fieldName;
                    if ($fieldName == 'id' || is_null($crmDAO->$fullFieldName)) {
                        continue;
                    }
                    $groupTree[$groupId][$fieldName] = $crmDAO->$fullFieldName;
                }
                $groupTree[$groupId]['fields'] = array();
            }

            // add the fields now (note - the query row will always contain a field)
            $groupTree[$groupId]['fields'][$fieldId] = array();
            $groupTree[$groupId]['fields'][$fieldId]['id'] = $fieldId;
            
            // populate information for a custom field
            foreach ($tableData['civicrm_custom_field'] as $fieldName) {
                $fullFieldName = "civicrm_custom_field_" . $fieldName;
                if ($fieldName == 'id' || is_null($crmDAO->$fullFieldName)) {
                        continue;
                } 
                $groupTree[$groupId]['fields'][$fieldId][$fieldName] = $crmDAO->$fullFieldName;                    
            }

            // check for custom values please
            if ($crmDAO->civicrm_custom_value_id) {
                $valueId = $crmDAO->civicrm_custom_value_id;

                // create an array for storing custom values for that field
                $groupTree[$groupId]['fields'][$fieldId]['customValue'] = array();
                $groupTree[$groupId]['fields'][$fieldId]['customValue']['id'] = $valueId;
                
                $dataType = $groupTree[$groupId]['fields'][$fieldId]['data_type'];
                
                switch ($dataType) {
                case 'String':
                    $groupTree[$groupId]['fields'][$fieldId]['customValue']['data'] = $crmDAO->civicrm_custom_value_char_data;
                    break;
                case 'Int':
                case 'Boolean':
                    $groupTree[$groupId]['fields'][$fieldId]['customValue']['data'] = $crmDAO->civicrm_custom_value_int_data;
                    break;
                case 'Float':
                    $groupTree[$groupId]['fields'][$fieldId]['customValue']['data'] = $crmDAO->civicrm_custom_value_float_data;
                    break;
                case 'Money':
                    $groupTree[$groupId]['fields'][$fieldId]['customValue']['data'] = $crmDAO->civicrm_custom_value_decimal_data;
                    break;
                case 'Memo':
                    $groupTree[$groupId]['fields'][$fieldId]['customValue']['data'] = $crmDAO->civicrm_custom_value_memo_data;
                    break;
                case 'Date':
                    $groupTree[$groupId]['fields'][$fieldId]['customValue']['data'] = $crmDAO->civicrm_custom_value_date_data;
                    break;
                case 'StateProvince':
                    $groupTree[$groupId]['fields'][$fieldId]['customValue']['data'] = $crmDAO->civicrm_custom_value_int_data;
                    break;
                case 'Country':
                    $groupTree[$groupId]['fields'][$fieldId]['customValue']['data'] = $crmDAO->civicrm_custom_value_int_data;
                    break;
                }
            }
        }
            
        return $groupTree;
    }


    /**
     * Update custom data.
     *
     *  - custom data is modified as follows
     *    - if custom data is changed it's updated.
     *    - if custom data is newly entered in field, it's inserted into db, finally
     *    - if existing custom data is cleared, it is deleted from the table.
     *
     * @param  array  &$groupTree - array of all custom groups, fields and values.
     * @param  string $entityType - type of entity being extended
     * @param  int    $entityId   - id of the contact whose custom data is to be updated
     * @return void
     *
     * @access public
     * @static
     *
     */
    public static function updateCustomData(&$groupTree, $entityType, $entityId)
    {

        $tableName = self::_getTableName($entityType);

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
                    $customValueDAO =& new CRM_Core_DAO_CustomValue();
                    $customValueDAO->entity_table = $tableName;
                    $customValueDAO->custom_field_id = $fieldId;
                    $customValueDAO->entity_id = $entityId;
                    
                    // check if it's an update or new one
                    if (isset($field['customValue']['id'])) {
                        // get the id of row in civicrm_custom_value
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
                        $customValueDAO->float_data = $data;
                        break;
                    case 'Money':
                        $customValueDAO->decimal_data = number_format( $data, 2, '.', '' ); 
                        break;
                    case 'Memo':
                        $customValueDAO->memo_data = $data;
                        break;
                    case 'Date':
                        $customValueDAO->date_data = $data;
                        break;
                    case 'StateProvince':
                        $customValueDAO->int_data = $data;
                        break;
                    case 'Country':
                        $customValueDAO->int_data = $data;
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
     * This method returns the number of entries in the civicrm_custom_value table for this particular group.
     *
     * @param int $groupId - id of group.
     * @return int $numValue - number of custom data values for this group.
     *
     * @access public
     * @static
     *
     */
    public static function getNumValue($groupId)
    {
         $query = "SELECT count(*) 
                   FROM   civicrm_custom_value, civicrm_custom_field 
                   WHERE  civicrm_custom_value.custom_field_id = civicrm_custom_field.id AND
                          civicrm_custom_field.custom_group_id = " 
                 . CRM_Utils_Type::escape($groupId, 'Integer');

         return CRM_Core_DAO::singleValueQuery( $query );
    }


    /**
     * Get the group title.
     *
     * @param int $id id of group.
     * @return string title 
     *
     * @access public
     * @static
     *
     */
    public static function getTitle( $id )
    {
        return CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_CustomGroup', $id, 'title' );
    }


    /**
     * Get custom group details for a group.
     *
     * An array containing custom group details (including their custom field) is returned.
     *
     * @param int    $groupId  - group id whose details are needed
     * @return array $groupTree - array consisting of all group and field details
     *
     * @access public
     *
     * @static
     *
     */
    public static function getGroupDetail($groupId)
    {
        // create a new tree
        $groupTree = array();
        $strSelect = $strFrom = $strWhere = $orderBy = ''; 

        $tableData = array();

        // using tableData to build the queryString 
        $tableData = array(
                           'civicrm_custom_field' => array('id', 'name', 'label', 'data_type', 'html_type', 'default_value', 'attributes',
                                                       'is_required', 'help_post','options_per_line'),
                           'civicrm_custom_group' => array('id', 'title', 'help_pre'),
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
        $strFrom = " FROM civicrm_custom_field, civicrm_custom_group";
        $strWhere = " WHERE civicrm_custom_field.custom_group_id = civicrm_custom_group.id
                            AND civicrm_custom_group.is_active = 1
                            AND civicrm_custom_field.is_active = 1
                            AND civicrm_custom_group.id = " .
                            CRM_Utils_Type::escape($groupId, 'Integer');
        $orderBy = " ORDER BY civicrm_custom_group.weight, civicrm_custom_field.weight";

        // final query string
        $queryString = $strSelect . $strFrom . $strWhere . $orderBy;

        // dummy dao needed
        $crmDAO =& new CRM_Core_DAO();
        $crmDAO->query($queryString);

        // process records
        while($crmDAO->fetch()) {

            $groupId = $crmDAO->civicrm_custom_group_id;
            $fieldId = $crmDAO->civicrm_custom_field_id;

            // create an array for groups if it does not exist
            if (!array_key_exists($groupId, $groupTree)) {
                $groupTree[$groupId] = array();
                $groupTree[$groupId]['id'] = $groupId;
                $groupTree[$groupId]['title'] = $crmDAO->civicrm_custom_group_title;
                $groupTree[$groupId]['help_pre'] = $crmDAO->civicrm_custom_group_help_pre;
                $groupTree[$groupId]['fields'] = array();
            }
            
            // add the fields now (note - the query row will always contain a field)
            $groupTree[$groupId]['fields'][$fieldId] = array();
            $groupTree[$groupId]['fields'][$fieldId]['id'] = $fieldId;

            foreach ($tableData['civicrm_custom_field'] as $v) {
                //if ($v == 'id') {
                $fullField = "civicrm_custom_field_" . $v;
                if ($v == 'id' || is_null($crmDAO->$fullField)) {
                    continue;
                } else {
                    $groupTree[$groupId]['fields'][$fieldId][$v] = $crmDAO->$fullField;                    
                }
            }
        }

        return $groupTree;
    }

    /**
     * Get custom group details for groups whose fields are searchable.
     *
     * An array containing custom group details (including their custom field) is returned.
     *
     * @return array $groupTree - array consisting of all group and field details
     *
     * @access public
     *
     * @static
     *
     */
    public static function getGroupDetailForSearch()
    {
        // create a new tree
        $groupTree = array();
        $strSelect = $strFrom = $strWhere = $orderBy = ''; 

        $tableData = array();

        // using tableData to build the queryString 
        $tableData = array(
                           'civicrm_custom_field' => array('id', 'name', 'label', 'data_type', 'html_type', 'attributes', 'is_searchable'),
                           'civicrm_custom_group' => array('id', 'title'),
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
        $strFrom = " FROM civicrm_custom_field, civicrm_custom_group";
        $strWhere = " WHERE civicrm_custom_field.custom_group_id = civicrm_custom_group.id
                            AND civicrm_custom_group.is_active = 1
                            AND civicrm_custom_field.is_active = 1
                            AND civicrm_custom_field.is_searchable = 1";
        $orderBy = " ORDER BY civicrm_custom_group.weight, civicrm_custom_field.weight";

        // final query string
        $queryString = $strSelect . $strFrom . $strWhere . $orderBy;

        // dummy dao needed
        $crmDAO =& new CRM_Core_DAO();
        $crmDAO->query($queryString);

        // process records
        while($crmDAO->fetch()) {

            $groupId = $crmDAO->civicrm_custom_group_id;
            $fieldId = $crmDAO->civicrm_custom_field_id;

            // create an array for groups if it does not exist
            if (!array_key_exists($groupId, $groupTree)) {
                $groupTree[$groupId] = array();
                $groupTree[$groupId]['id'] = $groupId;
                $groupTree[$groupId]['title'] = $crmDAO->civicrm_custom_group_title;
                $groupTree[$groupId]['fields'] = array();
            }
            
            // add the fields now (note - the query row will always contain a field)
            $groupTree[$groupId]['fields'][$fieldId] = array();
            $groupTree[$groupId]['fields'][$fieldId]['id'] = $fieldId;

            foreach ($tableData['civicrm_custom_field'] as $v) {
                $fullField = "civicrm_custom_field_" . $v;
                if ($v == 'id' || is_null($crmDAO->$fullField)) {
                    continue;
                } else {
                    $groupTree[$groupId]['fields'][$fieldId][$v] = $crmDAO->$fullField;                    
                }
            }
        }

        return $groupTree;
    }

    /**
     *
     * This function does 2 things - 
     *   1 - Create menu tabs for all custom groups with style 'Tab'
     *   2 - Updates tab for custom groups with style 'Inline'. If there
     *       are no inline groups it removes the 'Custom Data' tab
     *
     *
     * @param string $entityType  - what entity are we extending here ?
     * @param string $path        - what should be the starting path for the new menus ?
     * @param int    $startWeight - weight to start the local menu tabs
     *
     * @return void
     *
     * @access public
     * @static
     *
     */
    public static function addMenuTabs($entityType, $path, $startWeight)
    {
        $customGroupDAO =& new CRM_Core_DAO_CustomGroup();
        $menus = array();

        // get only 'Inline' groups
        $customGroupDAO->whereAdd("style = 'Inline'");
        $customGroupDAO->whereAdd("is_active = 1");
        // add whereAdd for entity type
        self::_addWhereAdd($customGroupDAO, $entityType);

        // order by weight
        $customGroupDAO->orderBy('weight, title');
        if ($customGroupDAO->find(true)) {
            $menu = array();
            $menu['path']    = $path;
            $menu['title']   = "$customGroupDAO->title";
            $menu['qs']      = 'reset=1&gid=0&cid=%%cid%%';
            $menu['type']    = CRM_Utils_Menu::CALLBACK;
            $menu['crmType'] = CRM_Utils_Menu::LOCAL_TASK;
            $menu['weight']  = $startWeight++;
            $menu['extra' ]  = array( 'gid' => 0 );
            $menus[] = $menu;
        }

        // for Tab's
        $customGroupDAO =& new CRM_Core_DAO_CustomGroup();

        // get only 'Tab' groups
        $customGroupDAO->whereAdd("style = 'Tab'");
        $customGroupDAO->whereAdd("is_active = 1");

        // add whereAdd for entity type
        self::_addWhereAdd($customGroupDAO, $entityType);

        // order by weight
        $customGroupDAO->orderBy('weight');
        $customGroupDAO->find();

        // process each group with menu tab
        while($customGroupDAO->fetch()) {
            $menu = array();
            $menu['path']    = $path;
            $menu['title']   = "$customGroupDAO->title";
            $menu['qs']      = 'reset=1&gid=' . $customGroupDAO->id . '&cid=%%cid%%';
            $menu['type']    = CRM_Utils_Menu::CALLBACK;
            $menu['crmType'] = CRM_Utils_Menu::LOCAL_TASK;
            $menu['weight']  = $startWeight++;
            $menu['extra' ]  = array( 'gid' => $customGroupDAO->id );
            $menus[] = $menu;
        }
        
        foreach($menus as $menu) {
            CRM_Utils_Menu::add($menu);
        }
    }



    /**
     * Get the table name for the entity type
     * currently if entity type is 'Contact', 'Individual', 'Household', 'Organization'
     * tableName is 'civicrm_contact'; 
     * 
     * @param string $entityType - what entity are we extending here ?
     *
     * @return void
     *
     * @access private
     * @static
     *
     */
    private static function _getTableName($entityType)
    {
        $tableName = '';
        switch($entityType) {
        case 'Contact':
        case 'Individual':
        case 'Household':
        case 'Organization':
            $tableName = 'civicrm_contact';
            break;
            // need to add cases for Location, Address
        }

        return $tableName;
    }

    /**
     * Add the whereAdd clause for the DAO depending on the type of entity
     * the custom group is extending.
     *
     * @param object CRM_Core_DAO_CustomGroup (reference) - Custom Group DAO.
     * @param string $entityType    - what entity are we extending here ?
     *
     * @return void
     *
     * @access private
     * @static
     *
     */
    private static function _addWhereAdd(&$customGroupDAO, $entityType)
    {
        switch($entityType) {
        case 'Contact':
            // if contact, get all related to contact
            $customGroupDAO->whereAdd("extends IN ('Contact', 'Individual', 'Household', 'Organization')");
            break;
        case 'Individual':
        case 'Household':
        case 'Organization':
            // is I/H/O then get I/H/O and contact
            $customGroupDAO->whereAdd("extends IN ('Contact', '$entityType')");
            break;
        case 'Location':
        case 'Address':
            $customGroupDAO->whereAdd("extends IN ('$entityType')");
            break;
        }
    }


    /**
     * Delete the Custom Group.
     *
     * @param int    id  Group Id 
     * 
     * @return void
     *
     * @access public
     * @static
     *
     */

  public static function deleteGroup($id) 
    { 
        //check wheter this contain any custom fields
        $custonField = & new CRM_Core_DAO_CustomField();
        $custonField->custom_group_id = $id;
        $custonField->find();
        while($custonField->fetch()) {
            return false;
            
        }
        //delete  custom group
        $group = & new CRM_Core_DAO_CustomGroup();
        $group->id = $id; 
        $group->delete();
        return true;
    }


}

?>
