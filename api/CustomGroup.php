<?php
  /*
   +--------------------------------------------------------------------+
   | CiviCRM version 1.7                                                |
   +--------------------------------------------------------------------+
   | copyright CiviCRM LLC (c) 2004-2007                                  |
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
   | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
   | about the Affero General Public License or the licensing  of       |
   | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
   | http://www.civicrm.org/licensing/                                  |
   +--------------------------------------------------------------------+
  */

  /**
   * Definition of the Custom Data of the CRM API. 
   * More detailed documentation can be found 
   * {@link http://objectledge.org/confluence/display/CRM/CRM+v1.0+Public+APIs
   * here}
   *
   * @package CRM
   * @author Donald A. Lobo <lobo@civicrm.org>
   * @copyright CiviCRM LLC (c) 2004-2007
   * $Id$
   *
   */

  /**
   * Files required for this package
   */
require_once 'api/utils.php';

/**
 * Most API functions take in associative arrays ( name => value pairs
 * as parameters. Some of the most commonly used parameters are
 * described below
 *
 * @param array $params           an associative array used in construction
 / retrieval of the object
 * @param array $returnProperties the limited set of object properties that
 *                                need to be returned to the caller
 *
 */


/**
 *
 * Adds one or more option values for "enum" type properties 
 *
 * @param params         Array    Associative array of property name/value pairs to insert in group.
 * @param $custom_field  object   A valid custom field object 
 *
 * @return object of newly created custom_option.
 *
 * @access public 
 *
 */
function crm_create_option_value($params, $customField) 
{
    _crm_initialize( );
    
    if(! is_array($params) ) {
        return _crm_error( "params is not of array type" );
    }     
    
    if( ! isset ($customField->id) ) {
        return _crm_error( "customField is not valid custom_field object" );
    }
    
    $params['entity_id'   ] = $customField->id;
    $params['entity_table'] = 'civicrm_custom_field';
    
    $error = _crm_check_required_fields($params, 'CRM_Core_DAO_CustomOption');
    if (is_a($error, 'CRM_Core_Error')) {
        return $error;
    }

    require_once 'CRM/Core/BAO/CustomOption.php';
    return CRM_Core_BAO_CustomOption::create($params);
}

/**
 * 
 * Retrieves an array of valid values for "enum" type properties 
 *
 * @param  $customField  Object  custom fields object
 *
 * @return  Array of custom field options. 
 *
 * @access public
 *
 */
function crm_get_option_values($customField)
{
    _crm_initialize( );
    
    if( ! isset ($customField->id) ) {
        return _crm_error( "custom_field is not valid custom_field object" );
    }
    
    $fieldId = $customField->id;

    require_once 'CRM/Core/BAO/CustomOption.php';
    return CRM_Core_BAO_CustomOption::getCustomOption($fieldId);
}


/**
 *
 * updates one or more option values
 *
 * @param params         Array    Associative array of property name/value pairs to insert in option.
 * @param option         Object   A valid custom field option object 
 *
 * @return object of newly created custom_option.
 *
 * @access public 
 *
 */
function crm_update_option_value($params , $option )
{
    _crm_initialize( );
    
    if( ! isset ( $option->id ) ) {
        return _crm_error( "id of the custom option is not set." );
    }
    
    if( ! is_array($params) ) {
        return _crm_error( "params is not of array type" );
    }
    $params['id'] = $option->id;
    
    require_once 'CRM/Core/BAO/CustomOption.php';
    return CRM_Core_BAO_CustomOption::create($params);
    
} 

/**
 *
 * delete one or more option values
 *
 * @param $option  object   A valid custom field option object 
 *
 * @return null  if success
 *
 * @access public 
 *
 */
function crm_delete_option_value( $option ) {
    _crm_initialize( );
    
    if( ! isset ( $option->id ) ) {
        return _crm_error( "id of the custom option is not set." );
    }
    
    require_once 'CRM/Core/BAO/CustomOption.php';
    return CRM_Core_BAO_CustomOption::del( $option->id );
}



/**
 * Returns an array of property objects for the requested class.
 *
 * @param String      $class_name      'class_name' (string) A valid class name.
 * @param Striing     $filter           filter' (string) Limits properties returned ("core", "custom", "default", "all).
 *  
 * @return $property_object  Array of property objects containing the properties like id ,name ,data_type, description;
 *
 * @access public
 */
function crm_get_class_properties($class_name = 'Individual', $filter = 'all') {
    _crm_initialize( );

    $property_object = array(); 

    $id = -1;

    if($class_name =='Individual' || $class_name =='Organization' || $class_name =='Household') {
        eval( '$fields = CRM_Contact_DAO_Contact::fields( );' );
        foreach($fields as $key => $values) {
            $property_object[] = array("id"=>$id,
                                       "name"=>$key,
                                       "data_type"=>CRM_Utils_Type::typeToString($values['type']) ,
                                       "description"=>$values['title']);
        }
        $fields="";
    }
    
    if($filter == 'custom' || $filter == 'all' ) {
        require_once 'CRM/Core/BAO/CustomGroup.php';
        $groupTree = CRM_Core_BAO_CustomGroup::getTree($class_name, null, -1);
        foreach($groupTree as $node) {
            $fields = $node["fields"];
            foreach($fields as $key => $values) {
                $property_object[] = array("id"=>$values['id'],
                                           "name"=>$values['name'],
                                           "data_type"=>$values['data_type'] ,
                                           "description"=>$values['help_post']);
            }
        }
    }
    
    return $property_object;
}


/**
 * Use this API to create a new group. See the CRM Data Model for custom_group property definitions
 * 
 * @param $class_name String  Which class is being extended.
 *
 * @param $params     array   Associative array of property name/value pairs to insert in group.
 *
 * @return   Newly create custom_group object
 *
 * @access public 
 */
function crm_create_custom_group($class_name, $params)
{
    _crm_initialize( );
    if(! trim($class_name) ) {
        return _crm_error( "class_name is not set" );
    }
    
    if(! is_array($params) ) {
        return _crm_error( "params is not an array ");
    }

    $params['extends'] = $class_name;
    $error = _crm_check_required_fields($params, 'CRM_Core_DAO_CustomGroup');
    
    if (is_a($error, 'CRM_Core_Error')) {
        return $error;
    }
    require_once 'CRM/Core/BAO/CustomGroup.php';
    $customGroup = CRM_Core_BAO_CustomGroup::create($params);
    return $customGroup;
}

/**
 * Use this API to delete an existing group.
 *
 * @param $id  Int  id of the group to be deleted
 *
 * @return Null if success, object of crm_core_error otherwise
 * @access public
 **/
function crm_delete_custom_group($id)
{    
    _crm_initialize( );
    
    if ( !$id ) {
        return _crm_error( 'Invalid custom group id passed in' );
    }

    require_once 'CRM/Core/BAO/CustomGroup.php';
    $result = CRM_Core_BAO_CustomGroup::deleteGroup($id);
    return $result ? null : _crm_error('Error while deleting custom group');
}

/**
 * Updating Custom Group.
 * 
 * Use this API to update the custom group. See the CRM Data Model for custom_group property definitions.
 * Updating the extends enum value is not allowed.
 * 
 * @param $params      Array   Associative array of property name/value pairs of custom group.
 *
 * @param $customGroup Object  Object of (CRM_Core_BAO_CustomGroup) custom group to be updated.
 * 
 * @return   Updated custom_group object
 *
 * @access public 
 */
function crm_update_custom_group($params, $customGroup)
{
    _crm_initialize( );
    
    if( ! isset ( $customGroup->id ) ) {
        return _crm_error( "id of the custom group is not set." );
    }
    
    if( ! is_array($params) ) {
        return _crm_error( "params is not of array type" );
    }
    
    if( isset ( $params['extends'] ) ) {
        return _crm_error( "Can not update extends enum value" );
    }
    
    $params['id']      = $customGroup->id;
    $params['extends'] = $customGroup->extends;
    
    if ( ! isset( $params['domain_id'] ) ) {
        $params['domain_id'] = $customGroup->domain_id;
    }
    
    if ( ! isset( $params['weight'] ) ) {
        $params['weight']    = $customGroup->weight;
    }
    
    $checkFields = _crm_check_required_fields($params, 'CRM_Core_DAO_CustomGroup');
    
    if (is_a($checkFields, 'CRM_Core_Error')) {
        return $checkFields;
    }
    
    $updateObject = _crm_update_object($customGroup, $params);
    
    if( is_a( $updateObject, 'CRM_Core_Error' ) ) {
        return $updateObject;
    }
    
    return $customGroup;
    
    //require_once 'CRM/Core/BAO/CustomGroup.php';
    //return CRM_Core_BAO_CustomGroup::create($params);
}

/**
 * Defines 'custom field' within a group.
 *
 * @param $custom_group object Valid custom_group object
 *
 * @param $params       array  Associative array of property name/value pairs to create new custom field.
 *
 * @return Newly created custom_field object
 *
 * @access public 
 *
 */
function crm_create_custom_field(&$custom_group, $params)
{
    _crm_initialize( );
  
    if(! is_array($params) ) {
        return _crm_error("params is not an array ");
    }
    
    if(! isset($custom_group->id) ) {
        return _crm_error("group id is not set in custom_group object");
    }
    
    $params['custom_group_id'] = $custom_group->id;
    
    $error = _crm_check_required_fields($params, 'CRM_Core_DAO_CustomField');
    if (is_a($error, 'CRM_Core_Error')) {
        return $error;
    }
    
    require_once 'CRM/Core/BAO/CustomField.php';
    $customField = CRM_Core_BAO_CustomField::create($params);

    return $customField;
}

/**
 * Updating Custom Field.
 * 
 * Use this API to update the custom Field. See the CRM Data Model for custom_field property definitions.
 * Updating the html_type enum value and data_type enum value is not allowed.
 * 
 * @param $params      Array   Associative array of property name/value pairs of custom field.
 *
 * @param $customField Object  Object of (CRM_Core_BAO_CustomField) custom field to be updated.
 * 
 * @return   Updated custom_field object
 *
 * @access public 
 */
function crm_update_custom_field($params, $customField)
{
    _crm_initialize( );
    
    if ( ! isset ( $customField->id ) ) {
        return _crm_error( "id of the custom field is not set." );
    }
    
    if ( ! is_array($params) ) {
        return _crm_error( "params is not of array type" );
    }
    
    if ( isset($params['html_type'] ) || isset($params['data_type'] ) ) {
        return _crm_error( "Updating html_type and/or data_type is not allowed." );
    }
    
    $params['id'] = $customField->id;
    
    if ( ! isset($params['custom_group_id'] ) ) {
        $params['custom_group_id'] = $customField->custom_group_id;
    }
    
    if ( ! isset($params['weight'] ) ) {
        $params['weight'] = $customField->weight;
    }
    
    $checkFields = _crm_check_required_fields($params, 'CRM_Core_DAO_CustomField');
    
    if (is_a($checkFields, 'CRM_Core_Error')) {
        return $checkFields;
    }
    
    $updateObject = _crm_update_object($customField, $params);
    
    if( is_a( $updateObject, 'CRM_Core_Error' ) ) {
        return $updateObject;
    }
    
    return $customField;
    
    //require_once 'CRM/Core/BAO/CustomField.php';
    //return CRM_Core_BAO_CustomField::create($params);
}

/**
 * get 'custom field' 
 *
 * @param $params       array  Associative array of property name/value pairs to get custom field.
 *
 * @return  custom_field object
 *
 * @access public 
 *
 */
function crm_get_custom_field( $params ) {
    _crm_initialize( );
    
    if(! is_array($params) ) {
        return _crm_error("params is not an array ");
    }

    $dao = new CRM_Core_DAO_CustomField();
    $dao->id    = $params['id'];
    $dao->name  = $params['name'];
    $dao->label = $params['label'];
    $dao->find(true);
    return $dao;
}

/**
 * Use this API to delete an existing custom group field.
 *
 * @param $id Int     id of the field to be deleted
 *
 * @return Null if success, object of crm_core_error otherwise
 * @access public
 **/
function crm_delete_custom_field( $id ) {
    _crm_initialize( );
   
    if ( !$id ) {
        return _crm_error( 'Invalid custom field id passed in' );
    }
   
    require_once 'CRM/Core/BAO/CustomField.php';
    $result = CRM_Core_BAO_CustomField::deleteGroup( $id );
    return $result ? null : _crm_error('Error while deleting custom field');
}
/**
 *  Defines 'custom value' within a field for a specific entity table/id combination.
 *
 * @param $entity_table String  Name of the table that this value is attached to
 * 
 * @param $entity_id    int     ID of the object in the relevant table
 * 
 * @param $custom_field object  field type of the value
 *
 * @param $data         Array         data appropriate value for the above custom field
 *
 * @param $separator    String        separator for values for ckeckbox.
 *
 * @return newly created custom_value object
 *
 * @access public 
 *
 *
 */
function crm_create_custom_value($entity_table, $entity_id, &$custom_field, &$data ,$separator = null)
{
    _crm_initialize( );
    
    if(! isset($entity_table) ) {
        return _crm_error("parameter entity_table is not set ");
    }
    
    if(! isset($entity_id) ) {
        return _crm_error("parameter entity_id is not set ");
    }
    
    if(! isset($custom_field->id) && ! isset($custom_field->type) ) {
        return _crm_error("field id ot type is not set in custom_field object");
    }
    
    if ( $separator ) {
        $values        = explode($separator, $data['value']);
        require_once 'CRM/Core/BAO/CustomOption.php';
        $data['value'] = CRM_Core_BAO_CustomOption::VALUE_SEPERATOR.implode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, $values).CRM_Core_BAO_CustomOption::VALUE_SEPERATOR;
    }
    
    $data['type'           ] = $custom_field->data_type;
    $data['custom_field_id'] = $custom_field->id;
    $data['entity_table'   ] = $entity_table;
    $data['entity_id'      ] = $entity_id;
 
    require_once 'CRM/Core/BAO/CustomValue.php';
    return CRM_Core_BAO_CustomValue::create( $data);
}