<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
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
 * Definition of the Contact part of the CRM API. 
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

require_once 'CRM/Core/BAO/OtherActivity.php';
require_once 'CRM/Core/BAO/Meeting.php';
require_once 'CRM/Core/BAO/Phonecall.php';
/**
 * Create a new Activity.
 *
 * Creates a new Activity record and returns the newly created
 * activity object (including the contact_id property). Minimum
 * required data values for the various contact_type are:
 *
 * Properties which have administratively assigned sets of values
 * If an unrecognized value is passed, an error
 * will be returned. 
 *
 * Modules may invoke crm_get_contact_values($contactID) to
 * retrieve a list of currently available values for a given
 * property.
 * @param array  $params       Associative array of property name/value
 *                             pairs to insert in new contact.
 * @param string $activity_type Which class of contact is being created.
 *            Valid values = 'SMS', 'Meeting', 'Event', 'PhoneCall'.
 *                            '
 *
 * @return CRM_Activity|CRM_Error Newly created Activity object
 * 
 */
 
function &crm_create_activity( &$params, $activityName) {
    _crm_initialize( );

    // return error if we do not get any params
    if (empty($params)) {
        return _crm_error( "Input Parameters empty" );
    }
    if (!trim($activityName)){
        return _crm_error( "Missing Activity Name" );
    }   
    //Check for Activityy name
    _crm_check_activity_name($activityName, 'CRM_Core_DAO_ActivityType');
    
    $ids = array();
    
    //check the type of activity
    if( $activityName == 'Meeting'){
        $activity = CRM_Core_BAO_Meeting::add( $params, $ids );
    }elseif( trim($activityName) == 'Phone Call' ) {
        $activity = CRM_Core_BAO_Phonecall::add( $params, $ids );
    }else{
        $activity = CRM_Core_BAO_OtherActivity::add( $params, $ids );
    }
    
    $activityArray = array(); 
    _crm_object_to_array( $activity, $activityArray);
    
    return $activityArray;
}

/**
 * 
 * Retrieves an array of valid values for "enum" 
 *
 * @contactID 
 *
 * @return  Array of $activity Values  
 *
 * @access public
 *
 */
function &crm_get_contact_activities($contactID)
{
    _crm_initialize( );
    
    if( empty($contactID) ) {
        return _crm_error( "Required parameter not found" );
    }
    //get all the activities of a contact with $contactID
    $activity['meeting']   = _crm_get_activities($contactID, 'CRM_Core_DAO_Meeting');
    $activity['phonecall'] = _crm_get_activities($contactID, 'CRM_Core_DAO_Phonecall');
    $activity['activity']  = _crm_get_activities($contactID, 'CRM_Core_DAO_Activity');
    
    return $activity;
}

/**
 * Update a specified activity.
 *
 * Updates activity with the values passed in the 'params' array. An
 * error is returned if an invalid id or activity Name is passed 
 * @param CRM_Activity $activity A valid Activity object
 * @param array       $params  Associative array of property
 *                             name/value pairs to be updated. 
 *  
 * @return CRM_Activity|CRM_Core_Error  Return the updated ActivtyType Object else
 *                                Error Object (if integrity violation)
 *
 * @access public
 *
 */
function &crm_update_activity( &$params,$activityName ) {
    if ( !is_array( $params ) ) {
        return _crm_error( 'Params is not an array' );
    }
    
    if ( !isset($params['id']) ) {
        return _crm_error( 'Required parameter missing' );
    }
    
    if (!trim($activityName)){
        return _crm_error( "Missing Activity Name" );
    }   
    
    _crm_check_activity_name($activityName, 'CRM_Core_DAO_ActivityType');
    
    if ( $activityName == 'Meeting') {
        $activity = _crm_update_activity($params, 'CRM_Core_DAO_Meeting');
    } elseif ( trim($activityName) == 'PhoneCall') {
        $activity = _crm_update_activity($params, 'CRM_Core_DAO_Phonecall');
    } else {
        $activity = _crm_update_activity($params, 'CRM_Core_DAO_Activity');
    }
    
    return $activity;
}
/**
 * Delete a specified Activity.
 * @param CRM_Activity $activity Activity object to be deleted
 *
 * @return void|CRM_Core_Error  An error if 'activityName or ID' is invalid,
 *                         permissions are insufficient, etc.
 *
 * @access public
 *
 */
function crm_delete_activity($params, $activityName) {
    _crm_initialize( );
    
    if ( ! isset( $params['id'] )) {
        return _crm_error( 'Required parameter not found' );
    }
    
    if (!trim($activityName)){
        return _crm_error( "Missing Activity Name" );
    }   
    
    _crm_check_activity_name($activityName, 'CRM_Core_DAO_ActivityType');
    
    //check the type of activity
    
    if( $activityName == 'Meeting'){
        CRM_Core_BAO_Meeting::del( $params['id'] );
    }elseif( trim($activityName) == 'PhoneCall'){
        CRM_Core_BAO_Phonecall::del( $params['id'] );
    }else{
        CRM_Core_BAO_OtherActivity::del( $params['id'] );
    }
}
/**
 * Function to update activities
 * @param CRM_Activity $activity Activity object to be deleted
 *
 * @return void|CRM_Core_Error  An error if 'activityName or ID' is invalid,
 *                         permissions are insufficient, etc.
 *
 * @access public
 *
 */
function _crm_update_activity($params, $daoName) {
    require_once(str_replace('_', DIRECTORY_SEPARATOR, $daoName) . ".php");
    $dao =& new $daoName();
    $dao->id = $params['id'];
    if ($dao->find(true)) {
        $dao->copyValues( $params );
        $dao->save();
    }
    
    $activity = array();
    _crm_object_to_array( clone($dao), $activity );
    
    return $activity;
}

/**
 * Delete a specified Activity.
 * @param CRM_Activity $activity Activity object to be deleted
 *
 * @return void|CRM_Core_Error  An error if 'activityName or ID' is invalid,
 *                         permissions are insufficient, etc.
 *
 * @access public
 *
 */
function _crm_get_activities($contactID, $daoName) {
    require_once(str_replace('_', DIRECTORY_SEPARATOR, $daoName) . ".php");
    eval('$dao = new $daoName();');
    $dao->target_entity_id = $contactID;
    if ($dao->find()) {
        $activity = array();
        while ( $dao->fetch() ) {
            _crm_object_to_array(clone($dao), $activity);
            $activities[$dao->id] = $activity;
        }
    }
    return $activities;
}

function _crm_check_activity_name($activityName, $daoName) {
    //Check if ActivityName Already Exist in the database
    require_once(str_replace('_', DIRECTORY_SEPARATOR, $daoName) . ".php");
    $dao =& new $daoName();
    $dao->name = $activityName;
    //$ids     = $params['id'];
    
    if(! $dao->find(true)){
        return _crm_error( "Invalid Activity Name" );
    }
}