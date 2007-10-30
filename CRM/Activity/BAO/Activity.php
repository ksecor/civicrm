<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
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
     * Check if there is absolute minimum of data to add the object
     *
     * @param array  $params         (reference ) an assoc array of name/value pairs
     *
     * @return boolean
     * @access public
     */
    private function _dataExists( &$params ) 
    {
        if (CRM_Utils_Array::value( 'subject', $params) &&
            CRM_Utils_Array::value( 'source_contact_id', $params ) ) {
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
     */
    public function retrieveActivity( &$params, &$defaults, $activityType ) 
    {
        $activity =& new CRM_Activity_DAO_Activity( );
        $activity->copyValues( $params );
        if ( $activity->find( true ) ) {

            // TODO: at some stage we'll have to deal
            // TODO: with multiple values for assignees and targets, but
            // TODO: for now, let's just fetch first row
            require_once 'CRM/Activity/BAO/ActivityAssignment.php';
            $assignment =& new CRM_Activity_BAO_ActivityAssignment( );
            $assigneeContactId = $assignment->retrieveAssigneeIdByActivityId( $activity->id );
            if ( $assigneeContactId ) { 
                $defaults['assignee_contact_id'] = $assigneeContactId;
                $defaults['assignee_contact'] = CRM_Contact_BAO_Contact::sortName( $assigneeContactId );
            }
            
            require_once 'CRM/Activity/BAO/ActivityTarget.php';
            $target =& new CRM_Activity_BAO_ActivityTarget( );
            $targetContactId = $target->retrieveTargetIdByActivityId( $activity->id );
            if ( $targetContactId ) { 
                $defaults['target_contact_id'] = $targetContactId; 
                $defaults['target_contact'] = CRM_Contact_BAO_Contact::sortName( $targetContactId );
            }

            $defaults['source_contact'] = CRM_Contact_BAO_Contact::sortName( $activity->source_contact_id );

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
     *
     */
    public function removeActivity( $id , $activityType ) 
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
              
        $activity =& new CRM_Activity_DAO_Activity();
        $activity->id = $id;
        require_once 'CRM/Case/DAO/CaseActivity.php';
        $caseActivity =  new CRM_Case_DAO_CaseActivity();
        $caseActivity->activity_entity_table = $entityTable;
        $caseActivity->activity_entity_id = $activity->id ;
        if ($caseActivity->find(true)){
            require_once 'CRM/Case/BAO/Case.php';
            CRM_Case_BAO_Case::deleteCaseActivity( $caseActivity->id );
        }
        self::deleteActivityAssignment( $entityTable,$activity->id );
        return $activity->delete();
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
     */
    public function deleteContact($id)
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
     * @param boolean $record   true if it is Record Activity 
     * @access public
     * @return
     */
    public function createActivity( &$params ) 
    {
        return $this->_saveActivity( $params, 'create' );
    }


    public function updateActivity( &$params, &$ids )
    {
        $this->id = CRM_Utils_Array::value( 'id', $ids );
        return $this->_saveActivity( $params, 'update' );
    }    
    

    private function _saveActivity( &$params, $operation )
    {
        require_once 'CRM/Core/Transaction.php';

        // check required params
        if ( ! $this->_dataExists( $params ) ) {
            CRM_Core_Error::fatal( 'Not enough data to create activity object,' );
        }

        $this->copyValues( $params );

        // start transaction        
        $transaction = new CRM_Core_Transaction( );

        $result = $this->save( );        

        // attempt to save activity assignment
        if( CRM_Utils_Array::value( 'assignee_contact_id', $params ) ) {
            require_once 'CRM/Activity/BAO/ActivityAssignment.php';
            $assignment =& new CRM_Activity_BAO_ActivityAssignment();

            if( $operation === 'create' ) {
                if( ! is_a( $result, 'CRM_Core_Error' ) ) {
                    $resultAssignment = $assignment->createAssignment( $this->id, $params['assignee_contact_id'] );
                }

            } elseif( $operation === 'update' ) {
                $assignment->activity_id = $this->id;
                if( $assignment->find( true ) ) {
                    if( $assignment->assignee_contact_id != $params['assignee_contact_id'] ) {
                        $resultAssignment = $assignment->updateAssignment( $assignment->id, 
                                                                           $params['assignee_contact_id'] );
                    }
                } 
            }
        }        

        // attempt to save activity targets
        if( CRM_Utils_Array::value( 'target_contact_id', $params ) ) {
            require_once 'CRM/Activity/BAO/ActivityTarget.php';
            $target =& new CRM_Activity_BAO_ActivityTarget();

            if( $operation === 'create' ) {
                if( ! is_a( $result, 'CRM_Core_Error' ) ) {
                    $resultTarget = $target->createTarget( $this->id, $params['target_contact_id'] );
                }

            } elseif( $operation === 'update' ) {
                $target->activity_id = $this->id;
                if( $target->find( true ) ) {
                    if( $target->target_contact_id != $params['target_contact_id'] ) {
                        $resultTarget = $target->updateTarget( $target->id, $params['target_contact_id'] );
                    }
                }
            }
        }

        // write to changelog before transation is committed/rolled back (and prepare status to display)
        if( $operation === 'create' ) {
            $logMsg = "Activity created for ";
            $status = ts('Activity "%1"  has been saved.', array( 1 => $params['subject'] ) );
        } elseif( $operation === 'update' ) {
            $logMsg = "Activity (id: {$this->id} ) updated with ";
            $status = ts('Activity "%1"  has been saved.', array( 1 => $params['subject'] ) );
        }
        $logMsg .= "source = {$params['source_contact_id']}, target = {$params['target_contact_id']}, assignee ={$params['assignee_contact_id']}";
        $this->_logActivityAction( $result, $logMsg );

        // roll back if error occured
        if( is_a( $result, 'CRM_Core_Error' ) ) {
            $transaction->rollback( );
            return $result;
        } elseif( is_a( $resultAssignment, 'CRM_Core_Error' ) ) {
            $transaction->rollback( );
            return $resultAssignment;
        } elseif( is_a( $resultTarget, 'CRM_Core_Error' ) ) {
            $transaction->rollback( );
            return $resultTarget;
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

        require_once "CRM/Core/BAO/CustomQuery.php";
        $entityTable  = CRM_Core_BAO_CustomQuery::$extendsMap[$activityType];
        $customFields = CRM_Core_BAO_CustomField::getFields( 'Activity' );

        require_once 'CRM/Core/BAO/CustomValueTable.php';
        CRM_Core_BAO_CustomValueTable::postProcess( $params,
                                                    $customFields,
                                                    $entityTable,
                                                    $result->id,
                                                    $activityType );
        $transaction->commit( );            

        CRM_Core_Session::setStatus( $status );

        return $result;
        
    }
        
    /**
     * compose the url to show details of activity
     *
     * @param int $id
     * @param int $activityHistoryId
     *
     * @access public
     */
    public function showActivityDetails( $id, $activityHistoryId )
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

    private function _logActivityAction( $activity, $logMessage = null ) {
        $session = & CRM_Core_Session::singleton();
        $id = $session->get('userID');
        require_once 'CRM/Core/BAO/Log.php';
        $logParams = array(
                           'entity_table'  => 'civicrm_activity' ,
                           'entity_id'     => $activity->id,
                           'modified_id'   => $id,
                           'modified_date' => date('Ymd'),
                           'data'          => $logMessage
                           );
        CRM_Core_BAO_Log::add( $logParams );
        return true;
    }

}

?>
