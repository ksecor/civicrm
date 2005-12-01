<?php
  /*
   +--------------------------------------------------------------------+
   | CiviCRM version 1.3                                                |
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
   * Definition of the Custom Data of the CRM API. 
   * More detailed documentation can be found 
   * {@link http://objectledge.org/confluence/display/CRM/CRM+v1.0+Public+APIs
   * here}
   *
   * @package CRM
   * @author Donald A. Lobo <lobo@yahoo.com>
   * @copyright Donald A. Lobo 01/15/2005
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
    require_once "CRM/Contact/DAO/{$class_name}.php";
    $error = eval( '$fields = CRM_Contact_DAO_' .$class_name  . '::fields( );' );
    if($error) {
        return _crm_error($error);
    }
    $id = -1;
    
    foreach($fields as $key => $values) {
        $property_object[] = array("id"=>$id,
                                   "name"=>$key,
                                   "data_type"=>CRM_Utils_Type::typeToString($values['type']),
                                   "description"=>$values['title']);
    }
    
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
       
                $property_object[] = array("id"=>$values['id'],"name"=>$values['name'],"data_type"=>$values['data_type'] ,"description"=>$values['help_post']);
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
    if(! isset ($class_name) ) {
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
  
    $params['custom_group_id'] = $custom_group->id;
  
    if(! isset($custom_group->id) ) {
        return _crm_error("group id is not set in custom_group object");
    }
  
    $error = _crm_check_required_fields($params, 'CRM_Core_DAO_CustomField');
    if (is_a($error, 'CRM_Core_Error')) {
        return $error;
    }
    
    require_once 'CRM/Core/BAO/CustomField.php';
    $customField = CRM_Core_BAO_CustomField::create($params);

    return $customField;
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
        $data['value'] = implode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, $values);
    }
    
    $data['type'           ] = $custom_field->data_type;
    $data['custom_field_id'] = $custom_field->id;
    $data['entity_table'   ] = $entity_table;
    $data['entity_id'      ] = $entity_id;
 
    require_once 'CRM/Core/BAO/CustomValue.php';
    return CRM_Core_BAO_CustomValue::create( $data);
}