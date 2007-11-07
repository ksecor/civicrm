<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * Definition of the Custom Data of the CRM API. 
 * More detailed documentation can be found 
 * {@link http://objectledge.org/confluence/display/CRM/CRM+v1.0+Public+APIs
 * here}
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

/**
 * Files required for this package
 */
require_once 'api/v2/utils.php';

/**
 * Most API functions take in associative arrays ( name => value pairs
 * as parameters. Some of the most commonly used parameters are
 * described below
 *
 * @param array $params           an associative array used in construction
 * retrieval of the object
 *
 *
 */

/**
 * Use this API to create a new group. See the CRM Data Model for custom_group property definitions
 * $params['class_name'] is a required field, class being extended.
 *
 * @param $params     array   Associative array of property name/value pairs to insert in group.
 *
 *
 * @return   Newly create custom_group object
 *
 * @access public 
 */
function civicrm_custom_group_create($params)
{
    _civicrm_initialize( );
    
    if(! is_array($params) ) { 
        return civicrm_create_error( "params is not an array");
    }   
    
    if(! trim($params['class_name']) ) {
        return civicrm_create_error( "class_name is not set" );
    }
    
    $params['extends'] = $params['class_name'];
    $error = _civicrm_check_required_fields($params, 'CRM_Core_DAO_CustomGroup');
    
    if (! trim($params['title'] ) ) {
        return civicrm_create_error( "Title is not set" );
    } else {
        $params['table_name'] = "civicrm_value_{$params['domain_id']}_{$params['title']}" ;
    }
    if (is_a($error, 'CRM_Core_Error')) {
        return civicrm_create_error( $error->_errors[0]['message'] );
    }
    
    require_once 'CRM/Core/BAO/CustomGroup.php';
    $customGroup = CRM_Core_BAO_CustomGroup::create($params);                             
    
    $customTable = CRM_Core_BAO_CustomGroup::createTable( $customGroup );
    
    _civicrm_object_to_array( $customGroup, $values );
    
    if ( is_a( $customGroup, 'CRM_Core_Error' ) && is_a( $customTable, 'CRM_Core_Error' ) ) { 
        return civicrm_create_error( $customGroup->_errors[0]['message'] );
    } else {
        $values['is_error']   = 0;
    }
    return $values;
}   


/**
 * Use this API to delete an existing group.
 *
 * @param array id of the group to be deleted
 *
 * @return Null if success
 * @access public
 **/
function civicrm_custom_group_delete($params)
{    
    _civicrm_initialize( );
          
    if ( !is_array( $params ) ) {
        return civicrm_create_error( 'Params is not an array' );
    }
    
    if ( ! CRM_Utils_Array::value( 'id', $params ) ) {
        return civicrm_create_error( 'Invalid or no value for Custom group ID' );
    }
    // convert params array into Object
    $values =& new CRM_Core_DAO_CustomGroup( );
    $values->id = $params['id'];
    $values->find(true);
    
    require_once 'CRM/Core/BAO/CustomGroup.php';
    $result = CRM_Core_BAO_CustomGroup::deleteGroup($values);  
    return $result ? civicrm_create_success( ): civicrm_error('Error while deleting custom group');
}

/**
 * Defines 'custom field' within a group.
 *
 *
 * @param $params       array  Associative array of property name/value pairs to create new custom field.
 *
 * @return Newly created custom_field id array
 *
 * @access public 
 *
 */

function civicrm_custom_field_create( $params )
{
    _civicrm_initialize( );
    
    if (! is_array($params) ) {                      
        return civicrm_create_error("params is not an array ");
    }
    
    if (!($params['fieldParams']['custom_group_id']) ) {                        
        return civicrm_create_error("Missing Required field :custom_group_id");
    }
    
    if (!($params['fieldParams']['label']) ) {                                     
        return civicrm_create_error("Missing Required field :custom_group_id");
    } else {             
        require_once 'CRM/Utils/String.php';
        $params['fieldParams']['column_name'] = 
            strtolower( CRM_Utils_String::munge( $params['fieldParams']['label'], '_', 32 ) );
    }
    
    $error = _civicrm_check_required_fields($params, 'CRM_Core_DAO_CustomField');
    if (is_a($error, 'CRM_Core_Error')) {
        return civicrm_create_error( $error->_errors[0]['message'] );
    }
    
    if ( $params['fieldParams']['html_type'] != 'Text' &&
         in_array( $params['fieldParams']['data_type'],
                   array( 'String', 'Int', 'Float', 'Money' ) ) ) {  
        
        // first create an option group for this custom group
        require_once 'CRM/Core/BAO/OptionGroup.php';
        $optionGroup            =& new CRM_Core_DAO_OptionGroup( );
        $optionGroup->domain_id = $params['optionGroup']['domain_id'];
        $optionGroup->name      = "{$params['customGroup']['table_name']}: {$params['fieldParams']['column_name']}";
        $optionGroup->label     = $params['fieldParams']['label'];
        $optionGroup->is_active = 1;
        $optionGroup->save( );
        
        _civicrm_object_to_array( $optionGroup, $optionGroupValues );
        
        $params['fieldParams']['option_group_id'] = $optionGroup->id;
        require_once 'CRM/Core/BAO/OptionValue.php';
        if ($params['optionValue']) {
            $optionValue                  =& new CRM_Core_DAO_OptionValue( );
            $optionValue->option_group_id =  $optionGroup->id;
            $optionValue->label           =  $params['optionValue']['label'];
            $optionValue->value           =  $params['optionValue']['value'];
            $optionValue->weight          =  $params['optionValue']['weight'];
            $optionValue->is_active       =  $params['optionValue']['is_active'];
            $optionValue->save( );
            
            _civicrm_object_to_array( $optionValue, $optionValues );
        }
    }
    
    require_once 'CRM/Core/BAO/CustomField.php';
    $customField = CRM_Core_BAO_CustomField::create($params['fieldParams']);  
    
    $column = CRM_Core_BAO_CustomField::createField( $customField, 'add' );
    
    _civicrm_object_to_array( $customField, $values );
    
    $values['optionGroupValues'] = $optionGroupValues;
    $values['optionValues'] = $optionValues;
    
    if ( is_a( $customField, 'CRM_Core_Error' ) && is_a( $column, 'CRM_Core_Error' )  ) {
        return civicrm_create_error( $customField->_errors[0]['message'] );
    } else {
        $values['is_error']   = 0;
    }
    return $values;
}

/**
 * Use this API to delete an existing custom group field.
 *
 * @param $params     Array id of the field to be deleted
 *
 *       
 * @access public
 **/
function civicrm_custom_field_delete( $params ) 
{
    _civicrm_initialize( );
    
    if ( !is_array( $params ) ) {
        return civicrm_create_error( 'Params is not an array' );
    }
    
    if ( ! CRM_Utils_Array::value( 'id', $params ) ) {
        return civicrm_create_error( 'Invalid or no value for Custom Field ID' );
    }
    
    if ( $params['optionValues'] ) {
        require_once 'CRM/Core/BAO/OptionValue.php';
        $optionValue =& new CRM_Core_DAO_OptionValue( );
        $optionValue->id = $params['optionValues']['id'];
        $optionValue->delete();
    }
    
    if ( $params['optionGroupValues'] ) {
        require_once 'CRM/Core/BAO/OptionGroup.php';
        $optionValue =& new CRM_Core_DAO_OptionValue( );
        $optionValue->id = $params['optionGroupValues']['id'];
        $optionValue->delete();
    }
    
    require_once 'CRM/Core/DAO/CustomField.php';
    $field =& new CRM_Core_DAO_CustomField( );
    $field->id = $params['id'];
    $field->find(true);
    
    require_once 'CRM/Core/BAO/CustomField.php';
    $customFieldDelete = CRM_Core_BAO_CustomField::deleteField( $field ); 
    return $customFieldDelete ?
        civicrm_create_error('Error while deleting custom field') :
        civicrm_create_success( );
}

?>
