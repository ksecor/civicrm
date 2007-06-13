<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
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
 *
 * Adds one or more option values for "enum" type properties 
 *
 * @param params      Array    Associative array of property name/value pairs to insert in group.
 *
 * @return array of newly created custom_option_id.
 *
 * @access public 
 *
 */
function civicrm_option_value_create( $params ) 
{
    _civicrm_initialize( );
    
    if(! is_array($params) ) {
        return civicrm_create_error( "params is not of array type" );
    }     
    
    if(!($params['custom_field_id']) ) {
        return civicrm_create_error( "Missing required Field : Custom Field ID" );
    }
    
    $params['entity_id'   ] = $params['custom_field_id'];
    $params['entity_table'] = 'civicrm_custom_field';
    
    $error = _civicrm_check_required_fields($params, 'CRM_Core_DAO_CustomOption');
    if (is_a($error, 'CRM_Core_Error')) {
        return civicrm_create_error( $error->_errors[0]['message'] );
    }

    require_once 'CRM/Core/BAO/CustomOption.php';
    $customOption = CRM_Core_BAO_CustomOption::create($params);
   
    if ( is_a( $customOption, 'CRM_Core_Error' ) ) {
        return civicrm_create_error( $customOption->_errors[0]['message'] );
    } else {
        $values = array( );
        $values['custom_option_id'] = $customOption->id;
        $values['is_error']   = 0;
    }
    return $values;
   
}

/**
 *
 * delete one or more option values
 *
 * @param $param  array   A valid custom field id 
 *
 * @return null  if success
 *
 * @access public 
 *
 */

function civicrm_option_value_delete( $params ) 
{
    if ( !is_array( $params ) ) {
        return civicrm_create_error( 'Params is not an array' );
    }
    
    if ( ! CRM_Utils_Array::value( 'id', $params ) ) {
        return civicrm_create_error( 'Invalid or no value for Custom option ID' );
    }
    
    require_once 'CRM/Core/BAO/CustomOption.php';
    $optionDelete = CRM_Core_BAO_CustomOption::del( $params['id'] );
    return $optionDelete ?
        civicrm_create_error('Error while deleting custom option') : 
        civicrm_create_success( );
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
  
    if(! is_array($params) ) {
        return civicrm_create_error("params is not an array ");
    }
    
    if(!($params['custom_group_id']) ) {
        return civicrm_create_error("Missing Required field :custom_group_id");
    }
    
    $error = _civicrm_check_required_fields($params, 'CRM_Core_DAO_CustomField');
    if (is_a($error, 'CRM_Core_Error')) {
        return civicrm_create_error( $error->_errors[0]['message'] );
    }
    
    require_once 'CRM/Core/BAO/CustomField.php';
    $customField = CRM_Core_BAO_CustomField::create($params);
    
    if ( is_a( $customField, 'CRM_Core_Error' ) ) {
        return civicrm_create_error( $customField->_errors[0]['message'] );
    } else {
        $values = array( );
        $values['custom_field_id'] = $customField->id;
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
function civicrm_custom_field_delete( $params ) {
    _civicrm_initialize( );
   
     if ( !is_array( $params ) ) {
        return civicrm_create_error( 'Params is not an array' );
    }
    
    if ( ! CRM_Utils_Array::value( 'id', $params ) ) {
        return civicrm_create_error( 'Invalid or no value for Custom Field ID' );
    }

    require_once 'CRM/Core/BAO/CustomField.php';
    $customFieldDelete = CRM_Core_BAO_CustomField::deleteGroup( $params['id'] );
    return $customFieldDelete ?
         civicrm_create_success( ) : civicrm_create_error('Error while deleting custom field');
}

?>