<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.0                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 * File for the CiviCRM APIv2 user framework group functions
 *
 * @package CiviCRM_APIv2
 * @subpackage API_UF
 * 
 * @copyright CiviCRM LLC (c) 2004-2009
 * @version $Id$
 *
 */


/**
 * Files required for this package
 */
require_once 'api/v2/utils.php'; 
require_once 'CRM/Core/BAO/UFGroup.php';

/**
 * Most API functions take in associative arrays ( name => value pairs
 * as parameters. Some of the most commonly used parameters are
 * described below
 *
 * @param array $params           an associative array used in construction
 *                                / retrieval of the object
 * @param array $returnProperties the limited set of object properties that
 *                                need to be returned to the caller
 *
 */


/**                
 * Get all the user framework groups 
 * 
 * @access public                                         
 * @return array - array reference of all groups. 
 * @static 
 */ 
function &civicrm_uf_profile_groups_get( ) {
    return CRM_Core_PseudoConstant::ufGroup( );
}

/** 
 * Get the form title. 
 * 
 * @param int $id id of uf_form 
 * @return string title 
 * 
 * @access public 
 * @static 
 * 
 */ 
function civicrm_uf_profile_title_get ( $id ) {
    return CRM_Core_BAO_UFGroup::getTitle( $id );
}

/** 
 * get all the fields that belong to the group with the named title 
 * 
 * @param int $id       the id of the UF group 
 * @param int $register are we interested in registration fields 
 * @param int $action   what action are we doing 
 * @param string $visibility visibility of fields we are interested in 
 * 
 * @return array the fields that belong to this title 
 * @static 
 * @access public 
 */ 
function civicrm_uf_profile_fields_get ( $id, $register = false, $action = null, $visibility = null ) {
    return CRM_Core_BAO_UFGroup::getFields( $id, $register, $action, null, $visibility, false, null, true );
}

/** 
 * get the html for the form that represents this particular group 
 * 
 * @param int     $userID   the user id that we are actually editing 
 * @param string  $title    the title of the group we are interested in 
 * @param int     $action   the action of the form 
 * @param boolean $register is this the registration form 
 * @param boolean $reset    should we reset the form? 
 * 
 * @return string       the html for the form 
 * @static 
 * @access public 
 */ 
function civicrm_uf_profile_html_get ( $userID, $title, $action = null, $register = false, $reset = false ) {
    return CRM_Core_BAO_UFGroup::getEditHTML( $userID, $title, $action, $register, $reset );
}

/** 
 * get the html for the form that represents this particular group 
 * 
 * @param int     $userID    the user id that we are actually editing 
 * @param int     $profileID the id of the group we are interested in 
 * @param int     $action    the action of the form 
 * @param boolean $register  is this the registration form 
 * @param boolean $reset     should we reset the form? 
 * 
 * @return string            the html for the form 
 * @static 
 * @access public 
 */ 
function civicrm_uf_profile_html_by_id_get ( $userID,
                                             $profileID,
                                             $action = null,
                                             $register = false,
                                             $reset = false ) {
    return CRM_Core_BAO_UFGroup::getEditHTML( $userID, null, $action, $register, $reset, $profileID );
}

 
/**  
 * get the html for the form for profile creation
 * @param int     $gid      group id
 * @param boolean $reset    should we reset the form?  
 *  
 * @return string       the html for the form  
 * @static  
 * @access public  
 */  
function civicrm_uf_create_html_get ( $gid, $reset = false ) {
    require_once 'CRM/Core/Controller/Simple.php';
    $session =& CRM_Core_Session::singleton( ); 
    $controller =& new CRM_Core_Controller_Simple( 'CRM_Profile_Form_Edit', '', CRM_Core_Action::ADD ); 
    if ( $reset ) { 
        unset( $_POST['_qf_default'] ); 
        unset( $_REQUEST['_qf_default'] );
    }
    $controller->set( 'gid', $gid );
    $controller->set( 'skipPermission', 1 );
    $controller->process( ); 
    $controller->setEmbedded( true ); 
    $controller->run( ); 
 
    $template =& CRM_Core_Smarty::singleton( ); 
    return trim( $template->fetch( 'CRM/Profile/Form/Dynamic.tpl' ) );
} 

/** 
 * get the contact_id given a uf_id 
 * 
 * @param int $ufID
 * 
 * @return int contact_id 
 * @access public    
 * @static 
 */ 
function civicrm_uf_match_id_get ( $ufID ) {
    require_once 'CRM/Core/BAO/UFMatch.php';
    return CRM_Core_BAO_UFMatch::getContactId( $ufID );
}

/**  
 * get the uf_id given a contact_id  
 *  
 * @param int $contactID
 *  
 * @return int ufID
 * @access public     
 * @static  
 */  
function civicrm_uf_id_get ( $contactID ) { 
    require_once 'CRM/Core/BAO/UFMatch.php'; 
    return CRM_Core_BAO_UFMatch::getUFId( $contactID ); 
} 

/*******************************************************************/


/**
 * Use this API to create a new group. See the CRM Data Model for uf_group property definitions
 *
 * @param $params  array   Associative array of property name/value pairs to insert in group.
 *
 * @return   Newly create $ufGroupArray array
 *
 * @access public 
 */
function civicrm_uf_group_create( $params ) {
    _civicrm_initialize( );
    
    if(! is_array($params) || ! isset($params['title']) ) {
        return civicrm_create_error("params is not an array or may be empty array ");
    }
    
    $ids = array();
    require_once 'CRM/Core/BAO/UFGroup.php';
    
    $ufGroup = CRM_Core_BAO_UFGroup::add( $params,$ids );
    _civicrm_object_to_array( $ufGroup, $ufGroupArray);
    
    return $ufGroupArray;
}

/**
 * Use this API to update  group. See the CRM Data Model for uf_group property definitions
 *
 * @param $params  array   Associative array of property name/value pairs to insert in group.
 *  
 * @param $groupId int  A valid UF Group ID that to be updated.   
 *  
 * @return  updated  $ufGroupArray array
 *
 * @access public 
 */
function civicrm_uf_group_update( $params , $groupId) {
    
    _civicrm_initialize( );
    
    if(! is_array( $params ) ) {
        return civicrm_create_error("params is not an array ");
    }
    
    if(! isset( $groupId ) ) {
        return civicrm_create_error("parameter $groupId  is not set ");
    }
    $ids = array();
    $ids['ufgroup'] = $groupId;
    
    require_once 'CRM/Core/BAO/UFGroup.php';
    
    $ufGroup = CRM_Core_BAO_UFGroup::add( $params,$ids );
    _civicrm_object_to_array( $ufGroup, $ufGroupArray);
    
    return $ufGroupArray;
}
/**
 * Defines 'uf field' within a group.
 *
 * @param $groupId int Valid uf_group id
 *
 * @param $params  array  Associative array of property name/value pairs to create new uf field.
 *
 * @return Newly created $ufFieldArray array
 *
 * @access public 
 *
 */
function civicrm_uf_field_create( $groupId , $params ) {
    _civicrm_initialize( );
    
    if(! isset( $groupId  ) ) {
        return civicrm_create_error("Group Id is not set.");
    }
    
    $field_type       = CRM_Utils_Array::value ( 'field_type'       , $params );
    $field_name       = CRM_Utils_Array::value ( 'field_name'       , $params );
    $location_type_id = CRM_Utils_Array::value ( 'location_type_id' , $params );
    $phone_type       = CRM_Utils_Array::value ( 'phone_type'       , $params );
    
    $params['field_name'] =  array( $field_type, $field_name, $location_type_id, $phone_type);
    
    if(! is_array( $params ) || $params['field_name'][1] == null || $params['weight'] == null ) {
        return civicrm_create_error("missing required fields ");
    }
    
    if ( !( CRM_Utils_Array::value('group_id', $params) ) ) {
        $params['group_id'] =  $groupId;
    }
    
    $ids = array();
    $ids['uf_group'] = $groupId;
    
    require_once 'CRM/Core/BAO/UFField.php';
    if (CRM_Core_BAO_UFField::duplicateField($params, $ids) ) {
        return civicrm_create_error("The field was not added. It already exists in this profile.");
    }
    
    $ufField = CRM_Core_BAO_UFField::add( $params,$ids );
    _civicrm_object_to_array( $ufField, $ufFieldArray);
    
    return $ufFieldArray;
} 

/**
 * Use this API to update uf field . See the CRM Data Model for uf_field property definitions
 *
 * @param $params  array   Associative array of property name/value pairs to update in field.
 *  
 * @param $fieldId int  A valid uf field id that to be updated.
 *  
 * @return  updated  $ufFieldArray array
 *
 * @access public 
 */
function civicrm_uf_field_update( $params , $fieldId ) {
    
    _civicrm_initialize( );
    
    if(! isset( $fieldId ) ) {
        return civicrm_create_error("parameter fieldId is not set");
    }
    
    if(! is_array( $params ) ) {
        return civicrm_create_error("params is not an array ");
    }   
    
    $field_type       = CRM_Utils_Array::value ( 'field_type'       , $params );
    $field_name       = CRM_Utils_Array::value ( 'field_name'       , $params );
    $location_type_id = CRM_Utils_Array::value ( 'location_type_id' , $params );
    $phone_type       = CRM_Utils_Array::value ( 'phone_type'       , $params );
    
    $params['field_name'] =  array( $field_type, $field_name, $location_type_id, $phone_type);
    
    require_once 'CRM/Core/BAO/UFField.php';
    $UFField = &new CRM_core_BAO_UFField();
    $UFField->id = $fieldId;
    
    if ( !( CRM_Utils_Array::value('group_id', $params) ) && $UFField->find(true) ) {
        $params['group_id'] =  $UFField->uf_group_id;
    }

    $ids = array();

    if ( $UFField->find(true) ) { 
        $ids['uf_group'] =  $UFField->uf_group_id;
    } else {
        return civicrm_create_error("there is no field for this fieldId");
    }
    $ids['uf_field'] = $fieldId;
    
    if (CRM_Core_BAO_UFField::duplicateField($params, $ids) ) {
        return civicrm_create_error("The field was not added. It already exists in this profile.");
    }
    
    $ufField = CRM_Core_BAO_UFField::add( $params,$ids );
    _civicrm_object_to_array( $ufField, $ufFieldArray);
    
    return $ufFieldArray;
}


/**
 * Delete uf group
 *  
 * @param $groupId int  Valid uf_group id that to be deleted
 *
 * @return true on successful delete or return error
 *
 * @access public
 *
 */
function civicrm_uf_group_delete( $groupId ) {
    _civicrm_initialize( );
    
    if(! isset( $groupId ) ) {
        return civicrm_create_error("provide a valid groupId.");
    }
    
    require_once 'CRM/Core/BAO/UFGroup.php';
    return CRM_Core_BAO_UFGroup::del($groupId);

}

/**
 * Delete uf field
 *  
 * @param $fieldId int  Valid uf_field id that to be deleted
 *
 * @return true on successful delete or return error
 *
 * @access public
 *
 */
function civicrm_uf_field_delete( $fieldId ) {
    _civicrm_initialize( );
    
    if(! isset( $fieldId ) ) {
        return civicrm_create_error("provide a valid fieldId.");
    }
    
    require_once 'CRM/Core/BAO/UFField.php';
    return CRM_Core_BAO_UFField::del($fieldId);
    
}

/**
 * check the data validity
 *
 * @param int    $userID    the user id 
 * @param string $title     the title of the group we are interested in
 * @param  boolean $register is this the registrtion form
 * @param int    $action  the action of the form
 *
 * @return error   if data not valid
 * 
 * @access public
 */
function civicrm_profile_html_validate($userID, $title, $action = null, $register = false) {
    return CRM_Core_BAO_UFGroup::isValid( $userID, $title, $register, $action );
}

/**
 * used to edit uf field
 *
 * @param array as key value pair

 * @return error   if updation fails else array of updated data
 * 
 * @access public
 */
function civicrm_uf_group_weight( $params ) {
    unset( $params['fnName'] );
    require_once 'CRM/Core/DAO/UFField.php';
    foreach ( $params as $key => $value ) {
        $value['is_active']  = 1;
        $result[] = civicrm_uf_field_update( $value, $key );
    }
    return $result;
}