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
require_once 'api/crm.php';

/**
 * Create a Membership Type
 *  
 * This API is used for creating a Membership Type
 * 
 * @param   array  $params  an associative array of name/value property values of civicrm_file
 * @return array of newly created file property values.
 * @access public
 */
function crm_create_membership_type($params) 
{
    _crm_initialize();
    if ( ! is_array($params) ) {
        return _crm_error('Params is not an array.');
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
 * Get a file.
 * 
 * This api is used for finding an existing file.
 * Required parameters : id OR file_type_id of a file
 * 
 * @params  array $params  an associative array of name/value property values of civicrm_file
 *
 * @return  Array of all found file object property values.
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
 * Update an existing file
 *
 * This api is used for updating an existing file.
 * Required parrmeters : id of a file
 * 
 * @param  Array   $params  an associative array of name/value property values of civicrm_file
 * 
 * @return array of updated file object property values
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
        $membershipTypeBAO->copyValues( $params );
        $membershipTypeBAO->save();
    }
    
    $membershipType = array();
    _crm_object_to_array( $membershipTypeBAO, $membershipType );
    return $membershipType;
}

/**
 * Deletes an existing file
 * 
 * This API is used for deleting a file
 * Required parameters : id of a file
 * 
 * @param  Int  $fileId  Id of the file to be deleted
 * 
 * @return null if successfull, object of CRM_Core_Error otherwise
 * @access public
 */
function &crm_delete_membership_type( $membershipTypeID ) {
    if ( empty($membershipTypeID) ) {
        return _crm_error( 'Required parameter missing' );
    }
    
    require_once 'CRM/Member/BAO/MembershipType.php';
    CRM_Member_BAO_MembershipType::del($membershipTypeID);
}

/**
 * Create a Membership Type
 *  
 * This API is used for creating a Membership Type
 * 
 * @param   array  $params  an associative array of name/value property values of civicrm_file
 * @return array of newly created file property values.
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
    
    require_once 'CRM/Member/BAO/MembershipStatus.php';
    $ids = array();
    $membershipStatusBAO = CRM_Member_BAO_MembershipStatus::add($params, $ids);
    $membershipStatus = array();
    _crm_object_to_array($membershipStatusBAO, $membershipStatus);
    
    return $membershipStatus;
}

/**
 * Get a file.
 * 
 * This api is used for finding an existing file.
 * Required parameters : id OR file_type_id of a file
 * 
 * @params  array $params  an associative array of name/value property values of civicrm_file
 *
 * @return  Array of all found file object property values.
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
    
    require_once 'CRM/Member/DAO/MembershipStatus.php';
    $membershipStatusDAO = new CRM_Member_DAO_MembershipStatus();
    
    $properties = array_keys($membershipStatusDAO->fields());
    
    foreach ($properties as $name) {
        if (array_key_exists($name, $params)) {
            $membershipStatusDAO->$name = $params[$name];
        }
    }
    
    if ( $membershipStatusDAO->find() ) {
        $membershipStatus = array();
        while ( $membershipStatusDAO->fetch() ) {
            _crm_object_to_array( clone($membershipStatusDAO), $membershipStatus );
            $membershipStatuses[$membershipStatusDAO->id] = $membershipStatus;
        }
    } else {
        return _crm_error('Exact match not found');
    }
    return $membershipStatuses;
}

/**
 * Update an existing file
 *
 * This api is used for updating an existing file.
 * Required parrmeters : id of a file
 * 
 * @param  Array   $params  an associative array of name/value property values of civicrm_file
 * 
 * @return array of updated file object property values
 * @access public
 */
function &crm_update_membership_status( $params ) {
    if ( !is_array( $params ) ) {
        return _crm_error( 'Params is not an array' );
    }
    
    if ( !isset($params['id']) ) {
        return _crm_error( 'Required parameter missing' );
    }
    
    require_once 'CRM/Member/DAO/MembershipStatus.php';
    $membershipStatusDAO =& new CRM_Member_DAO_MembershipStatus( );
    $membershipStatusDAO->id = $params['id'];
    if ($membershipStatusDAO->find(true)) {
        $membershipStatusDAO->copyValues( $params );
        $membershipStatusDAO->save();
    }
    $membershipStatus = array();
    _crm_object_to_array( clone($membershipStatusDAO), $membershipStatus );
    return $membershipStatus;
}

/**
 * Deletes an existing file
 * 
 * This API is used for deleting a file
 * Required parameters : id of a file
 * 
 * @param  Int  $fileId  Id of the file to be deleted
 * 
 * @return null if successfull, object of CRM_Core_Error otherwise
 * @access public
 */
function &crm_delete_membership_status( $membershipStatus ) {
    if ( empty($membershipStatus) ) {
        return _crm_error( 'Required parameter missing' );
    }
    
    require_once 'CRM/Member/BAO/MembershipStatus.php';
    CRM_Member_BAO_MembershipStatus::del($membershipStatus['id']);
}

?>