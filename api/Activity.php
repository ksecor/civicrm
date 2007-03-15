<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | copyright CiviCRM LLC (c) 2004-2007                                |
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions      |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 * Definition of the Contact part of the CRM API. 
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
require_once 'api/utils.php';

require_once 'CRM/Activity/BAO/Activity.php';

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

    $activityName = trim( $activityName );

    // return error if we do not get any params
    if ( empty( $params ) ) {
        return _crm_error( "Input Parameters empty" );
    }

    if ( empty( $activityName ) ) {
        return _crm_error( "Missing Activity Name" );
    }   

    // Check for Activityy name
    _crm_check_activity_name( $activityName, 'CRM_Core_DAO_ActivityType' );
    
    $ids = array();
    
    //check the type of activity
    if ( $activityName == 'Meeting' ) {
        $activityType = $activityName;
    } elseif ( $activityName == 'Phone Call' ) {
        $activityType = 'Phonecall';
    } else {
        $activityType = 'Activity';
    }
    
    $activity = CRM_Activity_BAO_Activity::add( $params, $ids, $activityType );

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
    
    if ( empty( $contactID ) ) {
        return _crm_error( "Required parameter not found" );
    }

    $activity = array( );

    // get all the activities of a contact with $contactID
    $activity['meeting'  ]  =& _crm_get_activities( $contactID, 'CRM_Activity_DAO_Meeting'   );
    $activity['phonecall']  =& _crm_get_activities( $contactID, 'CRM_Activity_DAO_Phonecall' );
    $activity['activity' ]  =& _crm_get_activities( $contactID, 'CRM_Activity_DAO_Activity'  );
    
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
    if ( ! is_array( $params ) ) {
        return _crm_error( 'Params is not an array' );
    }
    
    if ( ! isset($params['id'] ) ) {
        return _crm_error( 'Required parameter "id" missing' );
    }

    $activityName = trim( $activityName );
    if ( empty( $activityName ) ) {
        return _crm_error( "Missing Activity Name" );
    }   
    
    _crm_check_activity_name( $activityName, 'CRM_Core_DAO_ActivityType' );
    
    if ( $activityName == 'Meeting' ) {
        $activity = _crm_update_activity( $params, 'CRM_Activity_DAO_Meeting'   );
    } elseif ( $activityName == 'PhoneCall') {
        $activity = _crm_update_activity( $params, 'CRM_Activity_DAO_Phonecall' );
    } else {
        $activity = _crm_update_activity($params, 'CRM_Activity_DAO_Activity');
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
        return _crm_error( 'Required parameter "id" not found' );
    }
    
    $activityName = trim( $activityName );
    if ( empty( $activityName ) ) {
        return _crm_error( "Missing Activity Name" );
    }   
    
    _crm_check_activity_name( $activityName, 'CRM_Core_DAO_ActivityType' );
    
    //check the type of activity
     
    //check the type of activity
    if ( $activityName == 'Meeting' ) {
        $activityType = $activityName;
    } elseif ( $activityName == 'Phone Call' ) {
        $activityType = 'Phonecall';
    } else {
        $activityType = 'Activity';
    }
    
    $activity = CRM_Activity_BAO_Activity::del( $params['id'], $activityType );
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
    if ( $dao->find( true ) ) {
        $dao->copyValues( $params );
        $dao->save( );
    }
    
    $activity = array();
    _crm_object_to_array( $dao, $activity );
    
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
function &_crm_get_activities( $contactID, $daoName ) {

    require_once(str_replace('_', DIRECTORY_SEPARATOR, $daoName) . ".php");
    eval('$dao =& new $daoName( );');
    $dao->target_entity_id = $contactID;
    $activities = array();

    if ($dao->find()) {
        while ( $dao->fetch() ) {
            _crm_object_to_array( $dao, $activity );
            $activities[$dao->id] = $activity;
        }
    }

    return $activities;
}

function _crm_check_activity_name($activityName, $daoName) {
    require_once(str_replace('_', DIRECTORY_SEPARATOR, $daoName) . ".php");
    $dao       =& new $daoName( );
    $dao->name = $activityName;
    
    if (! $dao->find( true ) ) {
        return _crm_error( "Invalid Activity Name" );
    }
    return true;
}
