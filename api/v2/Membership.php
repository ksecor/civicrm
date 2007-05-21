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
 * Definition of CRM API for Membership.
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
 * Create a Membership Type
 *  
 * This API is used for creating a Membership Type
 * 
 * @param   array  $params  an associative array of name/value property values of civicrm_membership_type
 * 
 * @return array of newly created membership type property values.
 * @access public
 */
function civicrm_membership_type_create($params) 
{
    _civicrm_initialize();
    if ( ! is_array($params) ) {
        return civicrm_create_error('Params is not an array.');
    }
    
    if (!$params["name"] && ! $params['duration_unit'] && ! $params['duration_interval']) {
        return civicrm_create_error('Missing require fileds ( name, duration unit,duration interval)');
    }
    
    if ( !$params['domain_id'] ) {
        require_once 'CRM/Core/Config.php';
        $config =& CRM_Core_Config::singleton();
        $params['domain_id'] = $config->domainID();
    }
   
    $error = _civicrm_check_required_fields( $params, 'CRM_Member_DAO_MembershipType' );
    if ($error['is_error']) {
        return civicrm_create_error( $error['error_message'] );
    }
    
    $ids['membershipType']   = $params['id'];
    $ids['memberOfContact']  = $params['member_of_contact_id'];
    $ids['contributionType'] = $params['contribution_type_id'];
    
    require_once 'CRM/Member/BAO/MembershipType.php';
    $membershipTypeBAO = CRM_Member_BAO_MembershipType::add($params, $ids);

    if ( is_a( $membershipTypeBAO, 'CRM_Core_Error' ) ) {
        return civicrm_create_error( "Membership is not created" );
    } else {
        $member = array();
        _civicrm_object_to_array($membershipTypeBAO, $member);//CRM_CORE_ERROR::DEBUG('membershipe',$member);
        $values = array( );
        $values['member_id'] = $member['id'];
        $values['is_error']   = 0;
    }
    
    return $values;
}

/**
 * Update an existing membership type
 *
 * This api is used for updating an existing membership type.
 * Required parrmeters : id of a membership type
 * 
 * @param  Array   $params  an associative array of name/value property values of civicrm_membership_type
 * 
 * @return array of updated membership type property values
 * @access public
 */
function &civicrm_membership_type_update( $params ) {
    if ( !is_array( $params ) ) {
        return civicrm_create_error( 'Params is not an array' );
    }
    
    if ( !isset($params['id']) ) {
        return civicrm_create_error( 'Required parameter missing' );
    }
    
    require_once 'CRM/Member/BAO/MembershipType.php';
    $membershipTypeBAO =& new CRM_Member_BAO_MembershipType( );
    $membershipTypeBAO->id = $params['id'];
    if ($membershipTypeBAO->find(true)) {
        $fields = $membershipTypeBAO->fields( );
        foreach ( $fields as $name => $field) {
            if (array_key_exists($name, $params)) {
                $membershipTypeBAO->$name = $params[$name];
            }
        }
        $membershipTypeBAO->save();
    }
    
    $membershipType = array();
    _civicrm_object_to_array( $membershipTypeBAO, $membershipType );
    $membershipTypeBAO->free( );
    return $membershipType;
}

/**
 * Deletes an existing membership type
 * 
 * This API is used for deleting a membership type
 * 
 * @param  Int  $membershipTypeID    ID of membership type to be deleted
 * 
 * @return boolean        true if success, else false
 * @access public
 */
function &civicrm_membership_type_delete( $membershipTypeID ) {
    if ( ! $membershipTypeID ) {
        return civicrm_create_error( 'Invalid value for membershipTypeID' );
    }
    require_once 'CRM/Member/BAO/MembershipType.php';
    $memberDelete = CRM_Member_BAO_MembershipType::del($membershipTypeID);
    return $memberDelete ? null : civicrm_create_error('Error while deleting membership type');
}

/**
 * Create a Membership Status
 *  
 * This API is used for creating a Membership Status
 * 
 * @param   array  $params  an associative array of name/value property values of civicrm_membership_status
 * @return array of newly created membership status property values.
 * @access public
 */
function civicrm_membership_status_create($params) 
{
    _civicrm_initialize();
    if ( ! is_array($params) ) {
        return _civicrm_create_error('Params is not an array.');
    }
    
    if ( empty($params) ) {
        return _civicrm_error('Params can not be empty.');
    }
    
    if (! $params["name"] ) {
        return _civicrm_error('Missing require fileds');
    }
    
    if ( !$params['domain_id'] ) {
        require_once 'CRM/Core/Config.php';
        $config =& CRM_Core_Config::singleton();
        $params['domain_id'] = $config->domainID();
    }
    
    require_once 'CRM/Member/BAO/MembershipStatus.php';
    $ids = array();
    $membershipStatusBAO = CRM_Member_BAO_MembershipStatus::add($params, $ids);
    $membershipStatus = array();
    _civicrm_object_to_array($membershipStatusBAO, $membershipStatus);
    
    return $membershipStatus;
}

/**
 * Get a membership status.
 * 
 * This api is used for finding an existing membership status.
 * Required parrmeters : id of a membership status
 * 
 * @params  array $params  an associative array of name/value property values of civicrm_membership_status
 *
 * @return  Array of all found membership status property values.
 * @access public
 */
function civicrm_membership_statuses_get($params) 
{
    _civicrm_initialize();
    if ( ! is_array($params) ) {
        return _civicrm_create_error('Params is not an array.');
    }
    
    if ( ! isset($params['id'])) {
        return _civicrm_create_error('Required parameters missing.');
    }
    
    require_once 'CRM/Member/BAO/MembershipStatus.php';
    $membershipStatusBAO = new CRM_Member_BAO_MembershipStatus();
    
    $properties = array_keys($membershipStatusBAO->fields());
    
    foreach ($properties as $name) {
        if (array_key_exists($name, $params)) {
            $membershipStatusBAO->$name = $params[$name];
        }
    }
    
    if ( $membershipStatusBAO->find() ) {
        $membershipStatus = array();
        while ( $membershipStatusBAO->fetch() ) {
            _civicrm_object_to_array( clone($membershipStatusBAO), $membershipStatus );
            $membershipStatuses[$membershipStatusBAO->id] = $membershipStatus;
        }
    } else {
        return _civicrm_error('Exact match not found');
    }
    return $membershipStatuses;
}

/**
 * Update an existing membership status
 *
 * This api is used for updating an existing membership status.
 * Required parrmeters : id of a membership status
 * 
 * @param  Array   $params  an associative array of name/value property values of civicrm_membership_status
 * 
 * @return array of updated membership status property values
 * @access public
 */
function &civicrm_membership_status_update( $params ) 
{
    _civicrm_initialize();
    if ( !is_array( $params ) ) {
        return _civicrm_create_error( 'Params is not an array' );
    }
    
    if ( !isset($params['id']) ) {
        return _civicrm_create_error( 'Required parameter missing' );
    }
    
    require_once 'CRM/Member/BAO/MembershipStatus.php';
    $membershipStatusBAO =& new CRM_Member_BAO_MembershipStatus( );
    $membershipStatusBAO->id = $params['id'];
    if ($membershipStatusBAO->find(true)) {
        $fields = $membershipStatusBAO->fields( );
        foreach ( $fields as $name => $field) {
            if (array_key_exists($name, $params)) {
                $membershipStatusBAO->$name = $params[$name];
            }
        }
        $membershipStatusBAO->save();
    }
    $membershipStatus = array();
    _civicrm_object_to_array( clone($membershipStatusBAO), $membershipStatus );
    return $membershipStatus;
}

/**
 * Deletes an existing membership status
 * 
 * This API is used for deleting a membership status
 * 
 * @param  Int  $membershipStatusID   Id of the membership status to be deleted
 * 
 * @return null if successfull, object of CRM_Core_Error otherwise
 * @access public
 */
function &civicrm_membership_status_delete( $membershipStatusID ) 
{
    _civicrm_initialize();
    if ( empty($membershipStatusID) ) {
        return _civicrm_create_error( 'Invalid value for membershipStatusID' );
    }
    
    require_once 'CRM/Member/BAO/MembershipStatus.php';
    CRM_Member_BAO_MembershipStatus::del($membershipStatusID);
}

/**
 * Create a Contct Membership
 *  
 * This API is used for creating a Membership for a contact.
 * Required parameters : membership_type_id and status_id.
 * 
 * @param   array  $params     an associative array of name/value property values of civicrm_membership
 * @param   int    $contactID  ID of a contact
 * 
 * @return array of newly created membership property values.
 * @access public
 */
function civicrm_contact_membership_create($params, $contactID)
{
    _civicrm_initialize();
    if ( !is_array( $params ) ) {
        return civicrm_create_error( 'Params is not an array' );
    }
    
    if ( !isset($params['membership_type_id']) || !isset($params['status_id']) || empty($contactID)) {
        return civicrm_create_error( 'Required parameter missing' );
    }
    
    $values  = array( );   
    $error = _civicrm_format_membership_params( $params, $values );
    if (is_a($error, 'CRM_Core_Error') ) {
        return $error;
    }
    $params = array_merge($values,$params);
    $params['contact_id'] = $contactID;
    
    require_once 'CRM/Member/BAO/Membership.php';
    $ids = array();
    $membershipBAO = CRM_Member_BAO_Membership::create($params, $ids);
    
    if ( ! is_a( $membershipBAO, 'CRM_Core_Error') ) {
        $relatedContacts = CRM_Member_BAO_Membership::checkMembershipRelationship( 
                                                            $membershipBAO->id,
                                                            $contactID,
                                                            CRM_Core_Action::ADD
                                                            );
    }
    
    foreach ( $relatedContacts as $contactId ) {
        $params['contact_id'         ] = $contactId;
        $params['owner_membership_id'] = $membershipBAO->id;
        unset( $params['id'] );
        
        CRM_Member_BAO_Membership::create( $params, CRM_Core_DAO::$_nullArray );
    }
    
    $membership = array();
    _civicrm_object_to_array($membershipBAO, $membership);
    return $membership;
}

?>
