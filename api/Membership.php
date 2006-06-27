<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.4                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                  |
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
 | at http://www.openngo.org/faqs/licensing.html                      |
 +--------------------------------------------------------------------+
*/

/**
 * Definition of CRM API for Membership.
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
 * Create a Membership Type
 *  
 * This API is used for creating a Membership Type
 * 
 * @param   array  $params  an associative array of name/value property values of civicrm_membership_type
 * 
 * @return array of newly created membership type property values.
 * @access public
 */
function crm_create_membership_type($params) 
{
    _crm_initialize();
    if ( ! is_array($params) ) {
        return _crm_error('Params is not an array.');
    }
    
    if ( !$params['domain_id'] ) {
        require_once 'CRM/Core/Config.php';
        $config =& CRM_Core_Config::singleton();
        $params['domain_id'] = $config->domainID();
    }
    
    $error = _crm_check_required_fields( $params, 'CRM_Member_DAO_MembershipType');
    if ( is_a($error, 'CRM_Core_Error')  ) {
        return $error;
    }
    
    $ids['membershipType']   = $params['id'];
    $ids['memberOfContact']  = $params['member_of_contact_id'];
    $ids['contributionType'] = $params['contribution_type_id'];
    
    require_once 'CRM/Member/BAO/MembershipType.php';
    $membershipTypeBAO = CRM_Member_BAO_MembershipType::add($params, $ids);
    
    $membershipType = array();
    _crm_object_to_array($membershipTypeBAO, $membershipType);
    
    return $membershipType;
}

/**
 * Get a Membership Type.
 * 
 * This api is used for finding an existing membership type.
 * Required parameters : id of membership type
 * 
 * @params  array $params  an associative array of name/value property values of civicrm_membership_type
 * 
 * @return  Array of all found membership type property values.
 * @access public
 */
function crm_get_membership_types($params) 
{
    _crm_initialize();
    if ( ! is_array($params) ) {
        return _crm_error('Params is not an array.');
    }
    
    if ( ! isset($params['id'])) {
        return _crm_error('Required parameters missing.');
    }
    
    require_once 'CRM/Member/BAO/MembershipType.php';
    $membershipTypeBAO = new CRM_Member_BAO_MembershipType();
    
    $properties = array_keys($membershipTypeBAO->fields());
    
    foreach ($properties as $name) {
        if (array_key_exists($name, $params)) {
            $membershipTypeBAO->$name = $params[$name];
        }
    }
    
    if ( $membershipTypeBAO->find() ) {
        $membershipType = array();
        while ( $membershipTypeBAO->fetch() ) {
            _crm_object_to_array( clone($membershipTypeBAO), $membershipType );
            $membershipTypes[$membershipTypeBAO->id] = $membershipType;
        }
    } else {
        return _crm_error('Exact match not found');
    }
    return $membershipTypes;
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
function &crm_update_membership_type( $params ) {
    if ( !is_array( $params ) ) {
        return _crm_error( 'Params is not an array' );
    }
    
    if ( !isset($params['id']) ) {
        return _crm_error( 'Required parameter missing' );
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
    _crm_object_to_array( $membershipTypeBAO, $membershipType );
    return $membershipType;
}

/**
 * Deletes an existing membership type
 * 
 * This API is used for deleting a membership type
 * 
 * @param  Int  $membershipTypeID    ID of membership type to be deleted
 * 
 * @return null if successfull, object of CRM_Core_Error otherwise
 * @access public
 */
function &crm_delete_membership_type( $membershipTypeID ) {
    if ( empty($membershipTypeID) ) {
        return _crm_error( 'Invalid value for membershipTypeID' );
    }
    
    require_once 'CRM/Member/BAO/MembershipType.php';
    CRM_Member_BAO_MembershipType::del($membershipTypeID);
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
function crm_create_membership_status($params) 
{
    _crm_initialize();
    if ( ! is_array($params) ) {
        return _crm_error('Params is not an array.');
    }
    
    if ( empty($params) ) {
        return _crm_error('Params can not be empty.');
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
    _crm_object_to_array($membershipStatusBAO, $membershipStatus);
    
    return $membershipStatus;
}

/**
 * Get a membership status.
 * 
 * This api is used for finding an existing membership status.
 * 
 * @params  array $params  an associative array of name/value property values of civicrm_membership_status
 *
 * @return  Array of all found membership status property values.
 * @access public
 */
function crm_get_membership_statuses($params) 
{
    _crm_initialize();
    if ( ! is_array($params) ) {
        return _crm_error('Params is not an array.');
    }
    
    if ( empty($params) ) {
        return _crm_error('Params can not be empty.');
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
            _crm_object_to_array( clone($membershipStatusBAO), $membershipStatus );
            $membershipStatuses[$membershipStatusBAO->id] = $membershipStatus;
        }
    } else {
        return _crm_error('Exact match not found');
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
function &crm_update_membership_status( $params ) 
{
    _crm_initialize();
    if ( !is_array( $params ) ) {
        return _crm_error( 'Params is not an array' );
    }
    
    if ( !isset($params['id']) ) {
        return _crm_error( 'Required parameter missing' );
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
    _crm_object_to_array( clone($membershipStatusBAO), $membershipStatus );
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
function &crm_delete_membership_status( $membershipStatusID ) 
{
    _crm_initialize();
    if ( empty($membershipStatusID) ) {
        return _crm_error( 'Invalid value for membershipStatusID' );
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
function crm_create_contact_membership($params, $contactID)
{
    _crm_initialize();
    if ( !is_array( $params ) ) {
        return _crm_error( 'Params is not an array' );
    }
    
    if ( !isset($params['membership_type_id']) || !isset($params['status_id']) || empty($contactID)) {
        return _crm_error( 'Required parameter missing' );
    }
    
    $params['contact_id'] = $contactID;
    
    require_once 'CRM/Member/BAO/Membership.php';
    $ids = array();
    $membershipBAO = CRM_Member_BAO_Membership::add($params, $ids);
    
    $membership = array();
    _crm_object_to_array($membershipBAO, $membership);
    return $membership;
}

/**
 * Update an existing contact membership
 *
 * This api is used for updating an existing contact membership.
 * Required parrmeters : id of a membership
 * 
 * @param  Array   $params  an associative array of name/value property values of civicrm_membership
 * 
 * @return array of updated membership property values
 * @access public
 */
function crm_update_contact_membership($params)
{
    _crm_initialize();
    if ( !is_array( $params ) ) {
        return _crm_error( 'Params is not an array' );
    }
    
    if ( !isset($params['id']) ) {
        return _crm_error( 'Required parameter missing' );
    }
    
    require_once 'CRM/Member/BAO/Membership.php';
    $membershipBAO =& new CRM_Member_BAO_Membership( );
    $membershipBAO->id = $params['id'];
    
    if ($membershipBAO->find(true)) {
        $fields = $membershipBAO->fields( );
        foreach ( $fields as $name => $field) {
            if (array_key_exists($name, $params)) {
                $membershipBAO->$name = $params[$name];
            }
            if ($field['type'] & CRM_Utils_Type::T_DATE) {
                $dropArray = array('-' => '', ':' => '', ' ' => '');
                $membershipBAO->$name = strtr($membershipBAO->$name, $dropArray);
            }
        }
        $membershipBAO->save();
    }
    
    $membership = array();
    _crm_object_to_array( $membershipBAO, $membership );
    return $membership;
}

/**
 * Get conatct membership record.
 * 
 * This api is used for finding an existing membership record.
 * This api will also return the mebership records for the contacts
 * having mebership based on the relationship with the direct members.
 * 
 * @params  Int  $contactID  ID of a contact
 *
 * @return  Array of all found membership property values.
 * @access public
 */
function crm_get_contact_memberships($contactID)
{
    _crm_initialize();
    if ( empty($contactID) ) {
        return _crm_error( 'Invalid value for ContactID.' );
    }
    
    // get the membership for the given contact ID
    require_once 'CRM/Member/BAO/Membership.php';
    $membership = array('contact_id' => $contactID);
    $membershipValues = $ids = array();
    CRM_Member_BAO_Membership::getValues($membership, $membershipValues, $ids);
    
    if ( empty( $membershipValues ) ) {
        return _crm_error('No memberships for this contact.');
    }
    
    //CRM_Core_Error::debug('Membership Values 1', $membershipValues);
    
    // populate the membership type name for the membership type id
    require_once 'CRM/Member/BAO/MembershipType.php';
    $membershipType = CRM_Member_BAO_MembershipType::getMembershipTypeDetails($membershipValues['membership_type_id']);
    
    $membershipValues['membership_name']      = $membershipType['name'];
    
    // populating relationship type name.
    require_once 'CRM/Contact/BAO/RelationshipType.php';
    $relationshipType = new CRM_Contact_BAO_RelationshipType();
    $relationshipType->id =  $membershipType['relationship_type_id'];
    if ( $relationshipType->find(true) ) {
        $membershipValues['relationship_name'] = $relationshipType->name_a_b;
    }
    
    //CRM_Core_Error::debug('Membership Values 2', $membershipValues);
    
    $members[$membershipValues['contact_id']] = $membershipValues;
    
    // populating contacts in members array based on their relationship with direct members.
    require_once 'CRM/Contact/BAO/Relationship.php';
    $relationship = new CRM_Contact_BAO_Relationship();
    $relationship->contact_id_b            = $membershipValues['contact_id'];
    $relationship->relationship_type_id    = $membershipType['relationship_type_id'];
    if ($relationship->find()) {
        while ($relationship->fetch()) {
            clone($relationship);
            $membershipValues['contact_id'] = $relationship->contact_id_a;
            $members[$relationship->contact_id_a] = $membershipValues;
        }
    }
    //CRM_Core_Error::debug('Memberships', $members);
    return $members;
}

/**
 * Deletes an existing contact membership
 * 
 * This API is used for deleting a contact membership
 * 
 * @param  Int  $membershipID   Id of the contact membership to be deleted
 * 
 * @return null if successfull, object of CRM_Core_Error otherwise
 * @access public
 */
function crm_delete_membership($membershipID)
{
    _crm_initialize();
    
    if (empty($membershipID)) {
        return _crm_error('Invalid value for membershipID');
    }
    
    require_once 'CRM/Member/BAO/Membership.php';
    $membership = new CRM_Member_BAO_Membership();
    $result = $membership->deleteMembership($membershipID);
    
    return $result ? null : _crm_error('Error while deleting Membership');
}

/**
 * Derives the Membership Status of a given Membership Reocrd
 * 
 * This API is used for deriving Membership Status of a given Membership 
 * record using the rules encoded in the membership_status table.
 * 
 * @param  Int     $membershipID  Id of a membership
 * @param  String  $statusDate    
 * 
 * @return Array  Array of status id and status name 
 * @public
 */
function crm_calc_membership_status(  $membershipID, $statusDate = 'today' )
{
    if ( empty( $membershipID ) ) {
        return _crm_error( 'Invalid value for membershipID' );
    }
    require_once 'CRM/Member/BAO/Membership.php';
    $params['id'] = $membershipID;
    $values = $ids = array();
    CRM_Member_BAO_Membership::getValues($params, $values, $ids);
    
    require_once 'CRM/Member/BAO/MembershipStatus.php';
    return CRM_Member_BAO_MembershipStatus::getMembershipStatusByDate($values['start_date'], $values['end_date'], $values['join_date'], $statusDate);
}
?>