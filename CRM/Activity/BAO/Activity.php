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
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 * this BAO handles all activities(Meetings/PhoneCall/OtherActivities)
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Activity/DAO/Meeting.php';
require_once 'CRM/Activity/DAO/Phonecall.php';
require_once 'CRM/Activity/DAO/Activity.php';

/**
 * This class is for activity functions
 *
 */
class CRM_Activity_BAO_Activity extends CRM_Activity_DAO_Activity
{
    /**
     * class constructor
     */
    function __construct( ) 
    {
        parent::__construct( );
    }

    /**
     * funtion to add the activities based on the activity type
     *
     * @param array  $params       (reference ) an assoc array of name/value pairs
     * @param array  $ids          (reference ) the array that holds all the db ids
     * @param array  $activityType activity type  
     *
     * @return object activity type of object that is added
     * @access public
     * @static
     */
    static function add( &$params, &$ids, $activityType ) 
    {
        if ( ! self::dataExists( $params ) ) {
            return null;
        }
        
        eval ('$activity =& new CRM_Activity_DAO_' . $activityType .'( );');
        
        $activity->copyValues($params);
        
        $activity->id = CRM_Utils_Array::value( 'id', $ids );

        return $activity->save( );
        
    }

    /**
     * Check if there is data to add the object
     *
     * @param array  $params         (reference ) an assoc array of name/value pairs
     *
     * @return boolean
     * @access public
     * @static
     */
    static function dataExists( &$params ) 
    {
        if (CRM_Utils_Array::value( 'subject', $params)) {
            return true;
        }
        return false;
    }


    /**
     * Takes a bunch of params that are needed to match certain criteria and
     * retrieves the relevant objects. Typically the valid params are only
     * contact_id. We'll tweak this function to be more full featured over a period
     * of time. This is the inverse function of create. It also stores all the retrieved
     * values in the default array
     *
     * @param array  $params   (reference ) an assoc array of name/value pairs
     * @param array  $defaults (reference ) an assoc array to hold the flattened values
     * @param string $activityType activity type
     *
     * @return object CRM_Core_BAO_Meeting object
     * @access public
     * @static
     */
    static function retrieve( &$params, &$defaults, $activityType ) 
    {
        eval ( '$activity =& new CRM_Activity_DAO_' . $activityType . '( );' );
        $activity->copyValues( $params );
        if ( $activity->find( true ) ) {
            CRM_Core_DAO::storeValues( $activity, $defaults );
            return $activity;
        }
        return null;
    }

    /**
     * Function to delete the activity
     *
     * @param int    $id           activity id
     * @param string $activityType activity type
     *
     * @return null
     * @access public
     * @static
     *
     */
    static function del ( $id , $activityType ) 
    {
        eval ('$activity =& new CRM_Activity_DAO_' .$activityType. '( );');
        $activity->id = $id;
        $activity->delete();
    }


    /**
     * delete all records for this contact id
     *
     * @param int    $id  ID of the contact for which the records needs to be deleted.
     * @param string $activityType activity type 
     * 
     * @return void
     * 
     * @access public
     * @static
     */
    public static function deleteContact($id)
    {
        $activity = array("Meeting", "Phonecall", "Activity");
        foreach ($activity as $key) {
            
            // need to delete for both source and target
            eval ('$dao =& new CRM_Activity_DAO_' . $key . '();');
            $dao->source_contact_id = $id;
            $dao->delete();

            eval ('$dao =& new CRM_Activity_DAO_' . $key . '();');
            $dao->target_entity_table = 'civicrm_contact';
            $dao->target_entity_id    = $id;        
            $dao->delete();

        }
    }

    /**
     * Function to process the activities
     *
     * @param object $form         form object
     * @param array  $params       associated array of the submitted values
     * @param array  $ids          array of ids
     * @param string $activityType activity Type
     *
     * @access public
     * @return
     */
    public static function createActivity( &$params, &$ids, $activityType = 'Meeting') 
    {
        $activity = self::add($params, $ids, $activityType);

        $groupTree =& CRM_Core_BAO_CustomGroup::getTree($activityType, $ids['id'], 0);
        CRM_Core_BAO_CustomGroup::postProcess( $groupTree, $params );
        
        // do the updates/inserts
        CRM_Core_BAO_CustomGroup::updateCustomData($groupTree, $activityType, $activity->id); 
        
        if ( $activityType == 'Phonecall' ) {
            $title = ts('Phone Call');
        } else if ( $activityType == 'Activity' ) {
            $activityType = CRM_Core_PseudoConstant::activityType(true);
            $title        = $activityType[$params['activity_type_id']];
        } else {
            $title = ts($activityType);
        }

        if ( $activity->status == 'Completed' ) {
            // we need to insert an activity history record here
            $params = array('entity_table'     => 'civicrm_contact',
                            'entity_id'        => $activity->source_contact_id,
                            'activity_type'    => $title,
                            'module'           => 'CiviCRM',
                            'callback'         => 'CRM_Activity_BAO_Activity::showActivityDetails',
                            'activity_id'      => $activity->id,
                            'activity_summary' => $activity->subject,
                            'activity_date'    => $activity->scheduled_date_time
                            );
            
            if ( is_a( crm_create_activity_history($params), 'CRM_Core_Error' ) ) {
                return false;
            }

            // now set activity history for the target cid
            $params['entity_id'] = $activity->target_entity_id;
            if ( is_a( crm_create_activity_history($params), 'CRM_Core_Error' ) ) {
                return false;
            }
        }
        
        if( $activity->status=='Completed' ) {
            CRM_Core_Session::setStatus( ts( $title .' "%1" has been logged to Activity History.', array( 1 => $activity->subject)) );
        } else {
            CRM_Core_Session::setStatus( ts( $title . ' "%1" has been saved.', array( 1 => $activity->subject)) );
        }
    }

    /**
     * compose the url to show details of activity
     *
     * @param int $id
     * @param int $activityHistoryId
     *
     * @static
     * @access public
     */
    static function showActivityDetails( $id, $activityHistoryId )
    {
        $params   = array( );
        $defaults = array( );
        $params['id'          ] = $activityHistoryId;
        $params['entity_table'] = 'civicrm_contact';
        
        require_once 'CRM/Core/BAO/History.php'; 
        $history    = CRM_Core_BAO_History::retrieve($params, $defaults);
        $contactId  = CRM_Utils_Array::value('entity_id', $defaults);
        $activityId = $history->activity_id;

        if ($history->activity_type == 'Meeting') {
            $activityTypeId = 1;
        } else if ($history->activity_type == 'Phone Call') {
            $activityTypeId = 2;
        } else {
            $activityTypeId = 5;
        }

        if ( $contactId ) {
            return CRM_Utils_System::url('civicrm/contact/view/activity', "activity_id=$activityTypeId&cid=$contactId&action=view&id=$activityId&status=true&history=1"); 
        } else { 
            return CRM_Utils_System::url('civicrm' ); 
        } 
    }

}

?>
