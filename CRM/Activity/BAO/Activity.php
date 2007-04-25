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
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Activity/DAO/Meeting.php';
require_once 'CRM/Activity/DAO/Phonecall.php';
require_once 'CRM/Activity/DAO/Activity.php';
require_once 'api/History.php';

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
        if ( $activityType == 1) {
            $activityType = "Meeting";
        } else if($activityType == 2) {
            $activityType = "Phonecall";
        } else {
            $activityType = "Activity";
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
        if ( $activityType == 1) {
            $activityType = "Meeting";
        } else if($activityType == 2) {
            $activityType = "Phonecall";
        } else {
            $activityType = "Activity";
        }
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
        //delete Custom Data, if any
        require_once 'CRM/Core/BAO/CustomQuery.php';
        $entityTable = CRM_Core_BAO_CustomQuery::$extendsMap[$activityType];

        require_once 'CRM/Core/BAO/CustomValue.php';
        $cutomDAO = & new CRM_Core_DAO_CustomValue();
        $cutomDAO->entity_id = $id;
        $cutomDAO->entity_table = $entityTable;
        $cutomDAO->find( );
        while( $cutomDAO->fetch( )) {
            $cutomDAO->delete();
        }
        
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

        // do the updates/inserts
        if ( $activityType == 1) {
            $activityType = 'Meeting';
        } else if($activityType == 2) {
            $activityType = 'Phonecall';
        } else {
            $activityType = 'Activity';
        }
      
        // format custom data
        // get mime type of the uploaded file
        if ( !empty($_FILES) ) {
            foreach ( $_FILES as $key => $value) {
                $files = array( );
                if ( $params[$key] ) {
                    $files['name'] = $params[$key];
                }
                if ( $value['type'] ) {
                    $files['type'] = $value['type']; 
                }
                $params[$key] = $files;
            }
        }

        $customData = array( );
        require_once "CRM/Core/BAO/CustomField.php";
        foreach ( $params as $key => $value ) {
            if ( $customFieldId = CRM_Core_BAO_CustomField::getKeyID($key) ) {
                CRM_Core_BAO_CustomField::formatCustomField( $customFieldId, $customData,
                                                             $value, $activityType, null, $activity->id);
            }
        }

        //special case to handle if all checkboxes are unchecked
        $customFields = CRM_Core_BAO_CustomField::getFields( 'Activity' );

        if ( !empty($customFields) ) {
            foreach ( $customFields as $k => $val ) {
                if ( in_array ( $val[3], array ('CheckBox','Multi-Select') )&&
                     ! CRM_Utils_Array::value( $k, $customData ) ) {
                    CRM_Core_BAO_CustomField::formatCustomField( $k, $customData,
                                                                 '', $activityType, null, $activity->_id);
                }
            }
        }

        if ( !empty($customData) ) {
            //get the entity table for the custom field
            require_once "CRM/Core/BAO/CustomQuery.php";
            $entityTable = CRM_Core_BAO_CustomQuery::$extendsMap[$activityType];

            // add custom field values
            foreach ($customData as $customValue) {
                $cvParams = array(
                                  'entity_table'    => $entityTable,
                                  'entity_id'       => $activity->id,
                                  'value'           => $customValue['value'],
                                  'type'            => $customValue['type'],
                                  'custom_field_id' => $customValue['custom_field_id'],
                                  'file_id'         => $customValue['file_id'],
                                  );
                
                if ($customValue['id']) {
                    $cvParams['id'] = $customValue['id'];
                }
                CRM_Core_BAO_CustomValue::create($cvParams);
            }
        }
        
        if ( $activityType == 'Phonecall' ) {
            $title = 'Phone Call';
        } else if ( $activityType == 'Activity' ) {
            $activityType = CRM_Core_PseudoConstant::activityType(false);
            $title        = $activityType[$params['activity_type_id']];
        } else {
            $title = $activityType;
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
            CRM_Core_Session::setStatus("$title " . ts('"%1" has been logged to Activity History.', array(1 => $activity->subject)));
        } else {
            CRM_Core_Session::setStatus("$title " . ts('"%1" has been saved.', array(1 => $activity->subject)));
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
            $activityTypes = array( );
            $activityTypes = CRM_Core_PseudoConstant::activityType();
            $activityTypeId = array_search( $history->activity_type, $activityTypes );
        }

        if ( $contactId ) {
            return CRM_Utils_System::url('civicrm/contact/view/activity', "activity_id=$activityTypeId&cid=$contactId&action=view&id=$activityId&status=true&history=1&selectedChild=activity&context=activity"); 
        } else { 
            return CRM_Utils_System::url('civicrm' ); 
        } 
    }

}

?>
