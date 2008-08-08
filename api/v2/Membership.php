<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
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
function civicrm_membership_type_create(&$params) 
{
    _civicrm_initialize();
    if ( ! is_array($params) ) {
        return civicrm_create_error('Params is not an array.');
    }
    
    if (!$params["name"] && ! $params['duration_unit'] && ! $params['duration_interval']) {
        return civicrm_create_error('Missing require fileds ( name, duration unit,duration interval)');
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
        $membershipType = array();
        _civicrm_object_to_array( $membershipTypeBAO, $membershipType );
        $values = array( );
        $values['id']       = $membershipType['id'];
        $values['is_error'] = 0;
    }
    
    return $values;
}

/**
 * Get a Membership Type.
 * 
 * This api is used for finding an existing membership type.
 * 
 * @params  array $params  an associative array of name/value property values of civicrm_membership_type
 * 
 * @return  Array of all found membership type property values.
 * @access public
 */
function civicrm_membership_types_get(&$params) 
{
    _civicrm_initialize();

    if ( ! is_array($params) ) {
        return civicrm_create_error('Params is not an array.');
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
            _civicrm_object_to_array( clone($membershipTypeBAO), $membershipType );
            $membershipTypes[$membershipTypeBAO->id] = $membershipType;
        }
    } else {
        return civicrm_create_error('Exact match not found');
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
function &civicrm_membership_type_update( &$params ) {
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
 * Required parrmeters : id of a membership type
 * 
 * @param  Array   $params  an associative array of name/value property values of civicrm_membership_type
 * 
 * @return boolean        true if success, else false
 * @access public
 */
function &civicrm_membership_type_delete( &$params ) {
    if ( !is_array( $params ) ) {
        return civicrm_create_error( 'Params is not an array' );
    }
    
    if ( ! CRM_Utils_Array::value( 'id', $params ) ) {
        return civicrm_create_error( 'Invalid or no value for membershipTypeID' );
    }

    require_once 'CRM/Member/BAO/MembershipType.php';
    $memberDelete = CRM_Member_BAO_MembershipType::del( $params['id'] );
    return $memberDelete ?
        civicrm_create_error('Error while deleting membership type') : 
        civicrm_create_success( );
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
function civicrm_membership_status_create(&$params) 
{
    _civicrm_initialize();
    if ( ! is_array($params) ) {
        return civicrm_create_error('Params is not an array.');
    }
    
    if ( empty($params) ) {
        return civicrm_create_error('Params can not be empty.');
    }
    
    if (! $params["name"] ) {
        return civicrm_create_error('Missing required fields');
    }
    
    require_once 'CRM/Member/BAO/MembershipStatus.php';
    $ids = array();
    $membershipStatusBAO = CRM_Member_BAO_MembershipStatus::add($params, $ids);
    if ( is_a( $membershipStatusBAO, 'CRM_Core_Error' ) ) {
        return civicrm_create_error( "Membership is not created" );
    } else {
        $values             = array( );
        $values['id']       = $membershipStatusBAO->id;
        $values['is_error'] = 0;
        return $values;
    }
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
function civicrm_membership_statuses_get(&$params) 
{
    _civicrm_initialize();
    if ( ! is_array($params) ) {
        return civicrm_create_error('Params is not an array.');
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
        return civicrm_create_error('Exact match not found');
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
function &civicrm_membership_status_update( &$params ) 
{
    _civicrm_initialize();
    if ( !is_array( $params ) ) {
        return civicrm_create_error( 'Params is not an array' );
    }
    
    if ( !isset($params['id']) ) {
        return civicrm_create_error( 'Required parameter missing' );
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
function &civicrm_membership_status_delete( &$params ) {
    if ( ! is_array( $params ) ) {
        return civicrm_create_error( 'Params is not an array' );
    }
    
    if ( ! CRM_Utils_Array::value( 'id', $params ) ) {
        return civicrm_create_error( 'Invalid or no value for membershipStatusID' );
    }

    require_once 'CRM/Member/BAO/MembershipStatus.php';
    $memberStatusDelete = CRM_Member_BAO_MembershipStatus::del( $params['id'] );
    return $memberStatusDelete ?
        civicrm_create_error('Error while deleting membership type Status') :
        civicrm_create_success( );
}


/**
 * Create a Contact Membership
 *  
 * This API is used for creating a Membership for a contact.
 * Required parameters : membership_type_id and status_id.
 * 
 * @param   array  $params     an associative array of name/value property values of civicrm_membership
 * 
 * @return array of newly created membership property values.
 * @access public
 */
function civicrm_contact_membership_create(&$params)
{
    _civicrm_initialize();
    if ( !is_array( $params ) ) {
        return civicrm_create_error( 'Params is not an array' );
    }
    
    if ( !isset($params['membership_type_id']) || !isset($params['contact_id'] ) ||
         ( $params['is_override'] && ! $params['status_id'] )) {
        return civicrm_create_error( ts('Required parameter missing') );
    }
    
    $values  = array( );   
    $error = _civicrm_membership_format_params( $params, $values );
    if (is_a($error, 'CRM_Core_Error') ) {
        return civicrm_create_error( 'Membership is not created' );
    }
     
    $params = array_merge($values,$params);
    require_once 'CRM/Member/BAO/Membership.php';
    //for edit membership id should be present
    if ( $params['id'] ) {
        $ids = array( 'membership' => $params['id'],
                      'user_id'    => $params['contact_id'] );
    }
    
    $membershipBAO = CRM_Member_BAO_Membership::create($params, $ids, true);
    
    if ( array_key_exists( 'is_error', $membershipBAO ) ) {
        // In case of no valid status for given dates, $membershipBAO
        // is going to contain 'is_error' => "Error Message"
        return civicrm_create_error( ts( 'The membership can not be saved, no valid membership status for given dates' ) );
    }
    
    if ( ! is_a( $membershipBAO, 'CRM_Core_Error') ) {
      require_once 'CRM/Core/Action.php';
        $relatedContacts = CRM_Member_BAO_Membership::checkMembershipRelationship( 
                                                                   $membershipBAO->id,
                                                                   $params['contact_id'],
                                                                   CRM_Core_Action::ADD
                                                                   );
    }
    
    foreach ( $relatedContacts as $contactId => $relationshipStatus ) {
        $params['contact_id'         ] = $contactId;
        $params['owner_membership_id'] = $membershipBAO->id;
        unset( $params['id'] );
        
        CRM_Member_BAO_Membership::create( $params, CRM_Core_DAO::$_nullArray );
    }
    
    $membership = array();
    _civicrm_object_to_array($membershipBAO, $membership);
    $values = array( );
    $values['id'] = $membership['id'];
    $values['is_error']   = 0;
    
    return $values;
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
function civicrm_contact_memberships_get(&$contactID)
{
    _civicrm_initialize();
    if ( empty($contactID) ) {
        return civicrm_create_error( 'Invalid value for ContactID.' );
    }
    
    // get the membership for the given contact ID
    require_once 'CRM/Member/BAO/Membership.php';
    $membership = array('contact_id' => $contactID);
    $membershipValues = array();
    CRM_Member_BAO_Membership::getValues($membership, $membershipValues);
    
    if ( empty( $membershipValues ) ) {
        return civicrm_create_error('No memberships for this contact.');
    }
    
    $members[$contactID] = array( );
    
    foreach ($membershipValues as $membershipId => $values) {
        // populate the membership type name for the membership type id
        require_once 'CRM/Member/BAO/MembershipType.php';
        $membershipType = CRM_Member_BAO_MembershipType::getMembershipTypeDetails($values['membership_type_id']);
        
        $membershipValues[$membershipId]['membership_name'] = $membershipType['name'];
        
        $relationships[$membershipType['relationship_type_id']] = $membershipId;
        
        // populating relationship type name.
        require_once 'CRM/Contact/BAO/RelationshipType.php';
        $relationshipType = new CRM_Contact_BAO_RelationshipType();
        $relationshipType->id = $membershipType['relationship_type_id'];
        if ( $relationshipType->find(true) ) {
            $membershipValues[$membershipId]['relationship_name'] = $relationshipType->name_a_b;
        }
       
        require_once 'CRM/Core/BAO/CustomGroup.php';
        $groupTree =& CRM_Core_BAO_CustomGroup::getTree( 'Membership', $membershipId, false,
                                                         $values['membership_type_id']);
        $defaults = array( );
        CRM_Core_BAO_CustomGroup::setDefaults( $groupTree, $defaults );  
        
        if ( !empty( $defaults ) ) {
            foreach ( $defaults as $key => $val ) {
                $membershipValues[$membershipId][$key] = $val;
            }
        }
    }
    
    $members[$contactID] = $membershipValues;
    
    // populating contacts in members array based on their relationship with direct members.
    require_once 'CRM/Contact/BAO/Relationship.php';
    foreach ($relationships as $relTypeId => $membershipId) {
        // As members are not direct members, there should not be
        // membership id in the result array.
        unset($membershipValues[$membershipId]['id']);
        $relationship = new CRM_Contact_BAO_Relationship();
        $relationship->contact_id_b            = $contactID;
        $relationship->relationship_type_id    = $relTypeId;
        if ($relationship->find()) {
            while ($relationship->fetch()) {
                clone($relationship);
                $membershipValues[$membershipId]['contact_id'] = $relationship->contact_id_a;
                $members[$contactID][$relationship->contact_id_a] = $membershipValues[$membershipId];
            }
        }
    }
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
function civicrm_membership_delete(&$membershipID)
{
    _civicrm_initialize();
    
    if (empty($membershipID)) {
        return civicrm_create_error('Invalid value for membershipID');
    }
    
    require_once 'CRM/Member/BAO/Membership.php';
    CRM_Member_BAO_Membership::deleteRelatedMemberships( $membershipID );
    
    $membership = new CRM_Member_BAO_Membership();
    $result = $membership->deleteMembership($membershipID);
    
    return $result ? civicrm_create_success( ) : civicrm_create_error('Error while deleting Membership');
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
function civicrm_membership_status_calc( $membershipParams )
{
    if ( ! is_array( $membershipParams ) ) {
        return civicrm_create_error( ts( 'membershipParams is not an array' ) );
    }
    
    if ( ! ( $membershipID = CRM_Utils_Array::value( 'membership_id', $membershipParams ) ) ) {
        return civicrm_create_error( 'membershipParams do not contain membership_id' );
    }
    
    $query = "
SELECT start_date, end_date, join_date
  FROM civicrm_membership
 WHERE id = %1
";
    $params = array( 1 => array( $membershipID, 'Integer' ) );
    $dao =& CRM_Core_DAO::executeQuery( $query, $params );
    if ( $dao->fetch( ) ) {
        require_once 'CRM/Member/BAO/MembershipStatus.php';
        $result =&
            CRM_Member_BAO_MembershipStatus::getMembershipStatusByDate( $dao->start_date,
                                                                        $dao->end_date,
                                                                        $dao->join_date );
    } else {
        $result = null;
    }
    $dao->free( );
    return $result;
}

/**
 * take the input parameter list as specified in the data model and 
 * convert it into the same format that we use in QF and BAO object
 *
 * @param array  $params       Associative array of property name/value
 *                             pairs to insert in new contact.
 * @param array  $values       The reformatted properties that we can use internally
 *
 * @param array  $create       Is the formatted Values array going to
 *                             be used for CRM_Member_BAO_Membership:create()
 *
 * @return array|error
 * @access public
 */
function _civicrm_membership_format_params( &$params, &$values, $create=false) 
{
    require_once "CRM/Member/DAO/Membership.php";
    $fields =& CRM_Member_DAO_Membership::fields( );
    _civicrm_store_values( $fields, $params, $values );
    
    foreach ($params as $key => $value) {
        // ignore empty values or empty arrays etc
        if ( CRM_Utils_System::isNull( $value ) ) {
            continue;
        }
               
        switch ($key) {
        case 'membership_contact_id':
            if (!CRM_Utils_Rule::integer($value)) {
                return civicrm_create_error("contact_id not valid: $value");
            }
            $dao =& new CRM_Core_DAO();
            $qParams = array();
            $svq = $dao->singleValueQuery("SELECT id FROM civicrm_contact WHERE id = $value",
                                          $qParams);
            if (!$svq) {
                return civicrm_create_error("Invalid Contact ID: There is no contact record with contact_id = $value.");
            }
            $values['contact_id'] = $values['membership_contact_id'];
            unset($values['membership_contact_id']);
            break;
        case 'join_date':
        case 'membership_start_date':
        case 'membership_end_date':
            if (!CRM_Utils_Rule::date($value)) {
                return civicrm_create_error("$key not a valid date: $value");
            }
            break;
        case 'membership_type_id':
            $id = CRM_Core_DAO::getFieldValue( "CRM_Member_DAO_MembershipType", $value, 'id', 'name' );
            $values[$key] = $id;
            break;
        case 'status_id':
            $id = CRM_Core_DAO::getFieldValue( "CRM_Member_DAO_MembershipStatus", $value, 'id', 'name' );
            $values[$key] = $id;
            break;
        default:
            break;
        }
    }

    _civicrm_custom_format_params( $params, $values, 'Membership' );
      
    
    if ( $create ) {
        // CRM_Member_BAO_Membership::create() handles membership_start_date,
        // membership_end_date and membership_source. So, if $values contains
        // membership_start_date, membership_end_date  or membership_source,
        // convert it to start_date, end_date or source
        $changes = array('membership_start_date' => 'start_date',
                         'membership_end_date'   => 'end_date',
                         'membership_source'     => 'source',
                         );
        
        foreach ($changes as $orgVal => $changeVal) {
            if ( isset($values[$orgVal]) ) {
                $values[$changeVal] = $values[$orgVal];
                unset($values[$orgVal]);
            }
        }
    }
    
    return null;
}




