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
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Activity/DAO/Activity.php';
require_once 'CRM/Activity/BAO/ActivityTarget.php';
require_once 'CRM/Activity/BAO/ActivityAssignment.php';

/**
 * This class is for activity functions
 *
 */
class CRM_Activity_BAO_Activity extends CRM_Activity_DAO_Activity
{
    
    /**
     * static field for all the activity information that we can potentially import
     *
     * @var array
     * @static
     */
    static $_importableFields = null;

    /**
     * Check if there is absolute minimum of data to add the object
     *
     * @param array  $params         (reference ) an assoc array of name/value pairs
     *
     * @return boolean
     * @access public
     */
    public function dataExists( &$params ) 
    {
        if ( CRM_Utils_Array::value('source_contact_id', $params) || 
             CRM_Utils_Array::value('id', $params) ) {
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
    public function retrieve ( &$params, &$defaults ) 
    {
        $activity =& new CRM_Activity_DAO_Activity( );
        $activity->copyValues( $params );

        if ( $activity->find( true ) ) {
            require_once "CRM/Contact/BAO/Contact.php";
            // TODO: at some stage we'll have to deal
            // TODO: with multiple values for assignees and targets, but
            // TODO: for now, let's just fetch first row
            $defaults['assignee_contact'] = CRM_Activity_BAO_ActivityAssignment::retrieveAssigneeIdsByActivityId( $activity->id );
            $assignee_contact_names = CRM_Activity_BAO_ActivityAssignment::getAssigneeNames( $activity->id );
      
            $defaults['assignee_contact_value'] = null;
            foreach( $assignee_contact_names as $key => $name ) {
                $defaults['assignee_contact_value'] .= $defaults['assignee_contact_value']?",\"$name\"":"\"$name\"";
            }
            require_once 'CRM/Activity/BAO/ActivityTarget.php';
            $defaults['target_contact'] = CRM_Activity_BAO_ActivityTarget::retrieveTargetIdsByActivityId( $activity->id );
            $target_contact_names = CRM_Activity_BAO_ActivityTarget::getTargetNames( $activity->id );

            $defaults['target_contact_value'] = null;
            foreach ( $target_contact_names as $key => $name ) {
                $defaults['target_contact_value'] .= $defaults['target_contact_value']?",\"$name\"":"\"$name\"";
            }
            if ( $activity->source_contact_id ) {
                $defaults['source_contact'] = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact',
                                                                           $activity->source_contact_id,
                                                                           'sort_name' );
            }
            
            //get case subject
            require_once "CRM/Case/BAO/Case.php";
            $defaults['case_subject'] = CRM_Case_BAO_Case::getCaseSubject( $activity->id );

            CRM_Core_DAO::storeValues( $activity, $defaults );

            return $activity;
        }
        return null;
    }

    /**
     * Function to delete the activity
     * @param array  $params  associated array 
     *
     * @return void
     * @access public
     *
     */
    public function deleteActivity( &$params, $moveToTrash = false ) 
    {
        require_once 'CRM/Core/Transaction.php';
        $transaction = new CRM_Core_Transaction( );
               
        $activity    =& new CRM_Activity_DAO_Activity( );
        $activity->copyValues( $params );
        
        if ( ! $moveToTrash ) {  
            $result = $activity->delete( );
        } else {
            $activity->is_deleted = 1;
            $result = $activity->save( );
        }
        $transaction->commit( );
        return $result;
    }
    
    /**
     * Delete activity assignment record
     *
     * @param int    $id  activity id
     *
     * @return null
     * @access public
     */
    public function deleteActivityAssignment( $activityId ) 
    {
        require_once 'CRM/Activity/BAO/ActivityAssignment.php';
        $assignment              =& new CRM_Activity_BAO_ActivityAssignment( );
        $assignment->activity_id = $activityId;
        $assignment->delete( );
    }

    /**
     * Delete activity target record
     *
     * @param int    $id  activity id
     *
     * @return null
     * @access public
     */
    public function deleteActivityTarget( $activityId ) 
    {
        require_once 'CRM/Activity/BAO/ActivityTarget.php';
        $target              =& new CRM_Activity_BAO_ActivityTarget( );
        $target->activity_id = $activityId;
        $target->delete( );
    }

    /**
     * Create activity target record
     *
     * @param array    activity_id, target_contact_id
     *
     * @return null
     * @access public
     */
    public function createActivityTarget( $params ) 
    {
        if ( !$params['target_contact_id'] ) {
            return;
        }
        require_once 'CRM/Activity/BAO/ActivityTarget.php';
        $target              =& new CRM_Activity_BAO_ActivityTarget( );
        $target->activity_id = $params['activity_id'];
        $target->target_contact_id = $params['target_contact_id'];
        $target->save( );
    }
    
    /**
     * Create activity assignment record
     *
     * @param array    activity_id, assignee_contact_id
     *
     * @return null
     * @access public
     */
    public function createActivityAssignment( $params ) 
    {
        if ( !$params['assignee_contact_id'] ) {
            return;
        }
        require_once 'CRM/Activity/BAO/ActivityAssignment.php';
        $assignee              =& new CRM_Activity_BAO_ActivityAssignment( );
        $assignee->activity_id = $params['activity_id'];
        $assignee->assignee_contact_id = $params['assignee_contact_id'];
        $assignee->save( );
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
    public function create( &$params )
    {
        // check required params
        if ( ! self::dataExists( $params ) ) {
            CRM_Core_Error::fatal( 'Not enough data to create activity object,' );
        }
        
        $activity =& new CRM_Activity_DAO_Activity( );

        if ( ! CRM_Utils_Array::value( 'status_id', $params ) ) {
            if ( isset( $params['activity_date_time'] ) &&
                 $params['activity_date_time'] < date('Ymd') ) {
                $params['status_id'] = 2;
            } else {
                $params['status_id'] = 1;
            }
        }
        if ( empty( $params['id'] ) ) {
            unset( $params['id'] );
        }

        $activity->copyValues( $params );

        // start transaction        
        require_once 'CRM/Core/Transaction.php';
        $transaction = new CRM_Core_Transaction( );

        $result = $activity->save( );        
        
        if ( is_a( $result, 'CRM_Core_Error' ) ) {
            $transaction->rollback( );
            return $result;
        }

        $activityId = $activity->id;

        // check and attach and files as needed
        require_once 'CRM/Core/BAO/File.php';
        CRM_Core_BAO_File::processAttachment( $params,
                                              'civicrm_activity',
                                              $activityId );
        
        // attempt to save activity assignment
        $resultAssignment = null;
        if ( CRM_Utils_Array::value( 'assignee_contact_id', $params ) ) {
            require_once 'CRM/Activity/BAO/ActivityAssignment.php';
            
            $assignmentParams = array( 'activity_id'         => $activityId );

            if ( is_array( $params['assignee_contact_id'] ) ) {
                // first delete existing assignments if any
                self::deleteActivityAssignment( $activityId );

                foreach ( $params['assignee_contact_id'] as $acID ) {
                    if ( $acID ) {
                        $assignmentParams['assignee_contact_id'] = $acID;
                        $resultAssignment = CRM_Activity_BAO_ActivityAssignment::create( $assignmentParams );
                        if( is_a( $resultAssignment, 'CRM_Core_Error' ) ) {
                            $transaction->rollback( );
                            return $resultAssignment;
                        }
                    }
                }
            } else {
                $assignmentParams['assignee_contact_id'] = $params['assignee_contact_id'];
            
                if ( CRM_Utils_Array::value( 'id', $params ) ) {
                    $assignment =& new CRM_Activity_BAO_ActivityAssignment( );
                    $assignment->activity_id = $activityId;
                    $assignment->find( true );

                    if ( $assignment->assignee_contact_id != $params['assignee_contact_id'] ) {
                        $assignmentParams['id'] = $assignment->id;
                        $resultAssignment       = CRM_Activity_BAO_ActivityAssignment::create( $assignmentParams );
                    }
                } else {
                    $resultAssignment = CRM_Activity_BAO_ActivityAssignment::create( $assignmentParams );
                }
            }
        } else {       
            self::deleteActivityAssignment( $activityId );
        }

        if( is_a( $resultAssignment, 'CRM_Core_Error' ) ) {
            $transaction->rollback( );
            return $resultAssignment;
        }

        // attempt to save activity targets
        $resultTarget = null;
        if ( CRM_Utils_Array::value( 'target_contact_id', $params ) ) {

            $targetParams = array( 'activity_id'       => $activityId );
            $resultTarget = array( );
            if ( is_array( $params['target_contact_id'] ) ) {
                // first delete existing targets if any
                self::deleteActivityTarget( $activityId );

                foreach ( $params['target_contact_id'] as $tid ) {
                    if ( $tid ) {
                        $targetParams['target_contact_id'] = $tid;
                        $resultTarget = CRM_Activity_BAO_ActivityTarget::create( $targetParams );
                        if ( is_a( $resultTarget, 'CRM_Core_Error' ) ) {
                            $transaction->rollback( );
                            return $resultTarget;
                        }
                    }
                }
            } else {
                $targetParams['target_contact_id'] = $params['target_contact_id'];

                if ( CRM_Utils_Array::value( 'id', $params ) ) {
                    $target =& new CRM_Activity_BAO_ActivityTarget( );
                    $target->activity_id = $activityId;
                    $target->find( true );
                
                    if ( $target->target_contact_id != $params['target_contact_id'] ) {
                        $targetParams['id'] = $target->id;
                        $resultTarget       = CRM_Activity_BAO_ActivityTarget::create( $targetParams );
                    }
                } else {
                    $resultTarget = CRM_Activity_BAO_ActivityTarget::create( $targetParams );
                }
            }
        } else {
            self::deleteActivityTarget( $activityId );
        }

        // write to changelog before transation is committed/rolled
        // back (and prepare status to display)
        if ( CRM_Utils_Array::value( 'id', $params ) ) {
            $logMsg = "Activity (id: {$result->id} ) updated with ";
        } else {
            $logMsg = "Activity created for ";
        }
        
        $msgs = array( );
        if ( isset( $params['source_contact_id'] ) ) {
            $msgs[] = "source={$params['source_contact_id']}";
        } 

        if ( isset( $params['target_contact_id'] ) ) {
            if ( is_array( $params['target_contact_id'] ) ) {
                $msgs[] = "target=" . implode( ',', $params['target_contact_id'] );
            } else {
                $msgs[] = "target={$params['target_contact_id']}";
            }
        }

        if ( isset( $params['assignee_contact_id'] ) ) {
            if ( is_array( $params['assignee_contact_id'] ) ) {
                $msgs[] = "assignee=" . implode( ',', $params['assignee_contact_id'] );
            } else {
                $msgs[] = "assignee={$params['assignee_contact_id']}";
            }
        }
        $logMsg .= implode( ', ', $msgs );

        self::logActivityAction( $result, $logMsg );

        if ( CRM_Utils_Array::value( 'custom', $params ) &&
             is_array( $params['custom'] ) ) {
            require_once 'CRM/Core/BAO/CustomValueTable.php';
            CRM_Core_BAO_CustomValueTable::store( $params['custom'], 'civicrm_activity', $result->id );
        }

        $transaction->commit( );  

        return $result;
    }
        
    public function logActivityAction( $activity, $logMessage = null ) 
    {
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

    /**
     * function to get the list of open Actvities
     *
     * @param array reference $params  array of parameters 
     * @param int     $offset          which row to start from ?
     * @param int     $rowCount        how many rows to fetch
     * @param object|array  $sort      object or array describing sort order for sql query.
     * @param type    $type            type of activity we're interested in
     * @param boolean $admin           if contact is admin
     * @param int     $caseId          case id
     * @param string  $context         context , page on which selector is build
     *
     * @return array (reference)      $values the relevant data object values of open activitie
     *
     * @access public
     * @static
     */
    static function &getActivities( &$data, $offset = null, $rowCount = null, $sort = null,
                                    $type ='Activity', $admin = false, $caseId = null, $context = null ) 
    {
        $dao =& new CRM_Core_DAO();

        $params = array( );
        $clause = 1 ;

        if ( !$admin ) {
            $clause = " ( source_contact_id = %1 or target_contact_id = %1 or assignee_contact_id = %1 or civicrm_case_contact.contact_id = %1 ) ";
            $params = array( 1 => array( $data['contact_id'], 'Integer' ) );
        }
        
        $statusClause = 1 ;
        if ( $context == 'home' ) {
            $statusClause = " civicrm_activity.status_id = 1 "; 
        }

        // Exclude Contribution-related activity records if user doesn't have 'access CiviContribute' permission
        $contributionFilter = 1;
        if ( ! CRM_Core_Permission::check('access CiviContribute') ) {
            $contributionFilter = " civicrm_activity.activity_type_id != 6 ";
        }

        // Filter on case ID if looking at activities for a specific case
        $case = 1;
        if ( $caseId ) {
            $case = " civicrm_case_activity.case_id = $caseId ";
        }
        
        // Filter on component IDs.
        $componentClause = "civicrm_option_value.component_id IS NULL";

        $compInfo        = CRM_Core_Component::getEnabledComponents();
        foreach ( $compInfo as $compObj ) {
            if ( $compObj->info['showActivitiesInCore'] ) {
                $componentsIn = $componentsIn ? 
                    ($componentsIn . ', ' . $compObj->componentID) : $compObj->componentID;
            }
        }
        if ( $componentsIn ) {
            $componentClause = "($componentClause OR civicrm_option_value.component_id IN ($componentsIn))";
        }

        $query = "select DISTINCT(civicrm_activity.id), civicrm_activity.activity_date_time,
                         civicrm_activity.status_id, civicrm_activity.subject,
                         civicrm_activity.source_contact_id,civicrm_activity.source_record_id,
                         sourceContact.sort_name as source_contact_name,
                         civicrm_activity_target.target_contact_id,
			             targetContact.sort_name as target_contact_name,
                         civicrm_activity_assignment.assignee_contact_id,
			             assigneeContact.sort_name as assignee_contact_name,
                         civicrm_option_value.value as activity_type_id,
                         civicrm_option_value.label as activity_type,
                         civicrm_case_activity.case_id as case_id,
                         civicrm_case.subject as case_subject
                  from civicrm_activity 
                  left join civicrm_activity_target on 
                            civicrm_activity.id = civicrm_activity_target.activity_id 
                  left join civicrm_activity_assignment on 
                            civicrm_activity.id = civicrm_activity_assignment.activity_id 
                  left join civicrm_contact sourceContact on 
                            source_contact_id = sourceContact.id 
		          left join civicrm_contact targetContact on 
                            target_contact_id = targetContact.id 
                  left join civicrm_contact assigneeContact on 
                            assignee_contact_id = assigneeContact.id
                  left join civicrm_option_value on
                            ( civicrm_activity.activity_type_id = civicrm_option_value.value )
                  left join civicrm_option_group on  
                            civicrm_option_group.id = civicrm_option_value.option_group_id
                  left join civicrm_case_activity on
                            civicrm_case_activity.activity_id = civicrm_activity.id
                  left join civicrm_case on
                            civicrm_case_activity.case_id = civicrm_case.id
                  left join civicrm_case_contact on
                            civicrm_case_contact.case_id = civicrm_case.id
                  where {$clause}
                        and civicrm_option_group.name = 'activity_type'
                        and {$componentClause}
                        and is_test = 0  and {$contributionFilter} and {$case} and {$statusClause} 
                        GROUP BY id";

        $order = '';

        if ($sort) {
            $orderBy = $sort->orderBy();
            if ( ! empty( $orderBy ) ) {
                $order = " ORDER BY $orderBy";
            }
        }

        if ( empty( $order ) ) {
            if ( $context == 'activity' ) {
                $order = " ORDER BY activity_date_time desc ";
            } else {
                $order = " ORDER BY status_id asc, activity_date_time asc ";
            }
        }

        if ( $rowCount > 0 ) {
            $limit = " LIMIT $offset, $rowCount ";
        }

        $queryString = $query . $order . $limit;
        $dao =& CRM_Core_DAO::executeQuery( $queryString, $params );
        
        $selectorFields = array( 'activity_type_id',
                                 'activity_type',
                                 'id',
                                 'activity_date_time',
                                 'status_id',
                                 'subject',                                 
                                 'source_contact_name',
                                 'source_contact_id',
                                 'target_contact_name',
                                 'target_contact_id',
                                 'assignee_contact_name',
                                 'assignee_contact_id',
                                 'source_record_id',
                                 'case_id',
                                 'case_subject' );

        $values =array();
        $rowCnt = 0;
        while($dao->fetch()) {
            foreach( $selectorFields as $dc => $field ) {
                if ( isset($dao->$field ) ) {
                    $values[$rowCnt][$field] = $dao->$field;
                }
            }
            $rowCnt++;
        }

        return $values;
    }

    /**
     * Get total number of open activities
     *
     * @param  int $id id of the contact
     * @param string  $context         context , page on which selector is build
     *
     * @return int $numRow - total number of open activities    
     *
     * @static
     * @access public
     */
    static function getNumOpenActivity( $id, $admin = false, $context = null, $caseId = null ) 
    {
        $params = array( );
        $clause = 1 ;

        if ( !$admin ) {
            $clause = " ( source_contact_id = %1 or target_contact_id = %1 or assignee_contact_id = %1 ) ";
            $params = array( 1 => array( $id, 'Integer' ) );
        }
        
        $statusClause = 1 ;
        if ( $context == 'home' ) {
            $statusClause = " civicrm_activity.status_id = 1 "; 
        }
        //handle case related activity if $case is set
        $case = 1;
        if ( $caseId ) {
            $case = "civicrm_case_activity.case_id = {$caseId}";
            $caseJoin = "LEFT JOIN civicrm_case_activity ON civicrm_activity.id = civicrm_case_activity.activity_id";
        } 

        // Exclude Contribution-related activity records if user doesn't have 'access CiviContribute' permission
        $contributionFilter = 1;
        if ( ! CRM_Core_Permission::check('access CiviContribute') ) {
            $contributionFilter = " civicrm_activity.activity_type_id != 6 ";
        }
        
        $query = "select count(civicrm_activity.id) from civicrm_activity
                  left join civicrm_activity_target on 
                            civicrm_activity.id = civicrm_activity_target.activity_id
                  left join civicrm_activity_assignment on 
                            civicrm_activity.id = civicrm_activity_assignment.activity_id
                  {$caseJoin}
                  where {$clause}
                  and is_test = 0 and {$contributionFilter} and {$statusClause} and {$case}";

        return CRM_Core_DAO::singleValueQuery( $query, $params );
    }
    
    /**
     * send the message to all the contacts and also insert a
     * contact activity in each contacts record
     *
     * @param array  $contactIds   the array of contact ids to send the email
     * @param string $subject      the subject of the message
     * @param string $message      the message contents
     * @param string $emailAddress use this 'to' email address instead of the default Primary address
     * @param int    $userID        use this userID if set
     * @param string $from
     * @param array  $attachments   the array of attachments if any
     * @return array             (total, added, notAdded) count of emails sent
     * @access public
     * @static
     */
    static function sendEmail( &$contactIds,
                               &$subject,
                               &$text,
                               &$html,
                               $emailAddress,
                               $userID = null,
                               $from = null,
                               $attachments = null ) 
    {        
        if ( $userID == null ) {
            $session =& CRM_Core_Session::singleton( );
            $userID  =  $session->get( 'userID' );
        }
        list( $fromDisplayName, $fromEmail, $fromDoNotEmail ) = CRM_Contact_BAO_Contact::getContactDetails( $userID );
        if ( ! $fromEmail ) {
            return array( count($contactIds), 0, count($contactIds) );
        }
        if ( ! trim($fromDisplayName) ) {
            $fromDisplayName = $fromEmail;
        }
        
        $matches = array();
        preg_match_all( '/(?<!\{|\\\\)\{(\w+\.\w+)\}(?!\})/',
                        $text,
                        $matches,
                        PREG_PATTERN_ORDER);
        
        if ( $matches[1] ) {
            foreach ( $matches[1] as $token ) {
                list($type,$name) = split( '\.', $token, 2 );
                if ( $name ) {
                    if ( ! isset( $messageToken['contact'] ) ) {
                        $messageToken['contact'] = array( );
                    }
                    $messageToken['contact'][] = $name;
                }
            }
        }
        
        $matches = array();
        preg_match_all( '/(?<!\{|\\\\)\{(\w+\.\w+)\}(?!\})/',
                        $subject,
                        $matches,
                        PREG_PATTERN_ORDER);
        
        if ( $matches[1] ) {
            foreach ( $matches[1] as $token ) {
                list($type,$name) = split( '\.', $token, 2 );
                if ( $name ) {
                    if ( ! isset( $subjectToken['contact'] ) ) {
                        $subjectToken['contact'] = array( );
                    }
                    $subjectToken['contact'][] = $name;
                }
            }
        }
        
        $matches = array();
        preg_match_all( '/(?<!\{|\\\\)\{(\w+\.\w+)\}(?!\})/',
                        $html,
                        $matches,
                        PREG_PATTERN_ORDER);
        
        if ( $matches[1] ) {
            foreach ( $matches[1] as $token ) {
                list($type,$name) = split( '\.', $token, 2 );
                if ( $name ) {
                    if ( ! isset( $messageToken['contact'] ) ) {
                        $messageToken['contact'] = array( );
                    }
                    $messageToken['contact'][] = $name;
                }
            }
        }
        require_once 'CRM/Utils/Mail.php';
        if (!$from ) {
            $from = CRM_Utils_Mail::encodeAddressHeader($fromDisplayName, $fromEmail);
        }
        
        //create the meta level record first
        
        $activityTypeID = CRM_Core_OptionGroup::getValue( 'activity_type',
                                                          'Email',
                                                          'name' );
        
        $activityParams = array('source_contact_id'    => $userID,
                                'activity_type_id'     => $activityTypeID,
                                'activity_date_time'   => date('YmdHis'),
                                'subject'              => $subject,
                                'details'              => $text,
                                'status_id'            => 2
                                );

        // add the attachments to activity params here
        if ( $attachments ) {
            // first process them
            $activityParams = array_merge( $activityParams,
                                           $attachments );
        }

        $activity = self::create($activityParams);

        // get the set of attachments from where they are stored
        $attachments =& CRM_Core_BAO_File::getEntityFile( 'civicrm_activity',
                                                          $activity->id );
        $sent = $notSent = array();
        $returnProperties = array();
        if( isset( $messageToken['contact'] ) ) { 
            foreach ( $messageToken['contact'] as $key => $value ) {
                $returnProperties[$value] = 1; 
            }
        }
        
        if( isset( $subjectToken['contact'] ) ) { 
            foreach ( $subjectToken['contact'] as $key => $value ) {
                if ( !isset( $returnProperties[$value] ) ) {
                    $returnProperties[$value] = 1;
                }
            }
        }
        
        require_once 'CRM/Mailing/BAO/Mailing.php';
        $mailing   = & new CRM_Mailing_BAO_Mailing();
        $details   = $mailing->getDetails($contactIds, $returnProperties );
        
        $tokens = array( );
        CRM_Utils_Hook::tokens( $tokens );
        $categories = array_keys( $tokens );

        if ( defined( 'CIVICRM_MAIL_SMARTY' ) ) {
            $smarty =& CRM_Core_Smarty::singleton( );

            require_once 'CRM/Core/Smarty/resources/String.php';
            civicrm_smarty_register_string_resource( );
        }

        require_once 'api/v2/Contact.php';
        foreach ( $contactIds as $contactId ) {
            //fix for CRM-3798
            $params  = array( 'contact_id'  => $contactId, 
                              'is_deceased' => 0, 
                              'on_hold'     => 0 );
            
            $contact = civicrm_contact_get( $params );
            
            if ( civicrm_error( $contact ) ) {
                $notSent[] = $contactId;
                continue;
            }
            
            if( is_array( $details[0]["{$contactId}"] ) ) {
                $contact = array_merge( $contact, $details[0]["{$contactId}"] );
            }

            $tokenSubject = CRM_Utils_Token::replaceContactTokens( $subject     , $contact, false, $subjectToken);
            $tokenSubject = CRM_Utils_Token::replaceHookTokens   ( $tokenSubject, $contact, $categories, false );

            $tokenText    = CRM_Utils_Token::replaceContactTokens( $text     , $contact, false, $messageToken);
            $tokenText    = CRM_Utils_Token::replaceHookTokens   ( $tokenText, $contact, $categories, false );

            $tokenHtml    = CRM_Utils_Token::replaceContactTokens( $html     , $contact, true , $messageToken);
            $tokenHtml    = CRM_Utils_Token::replaceHookTokens   ( $tokenHtml, $contact, $categories, true );

            if ( defined( 'CIVICRM_MAIL_SMARTY' ) ) {
                // also add the contact tokens to the template
                $smarty->assign_by_ref( 'contact', $contact );

                $tokenText = $smarty->fetch( "string:$tokenText" );
                $tokenHtml = $smarty->fetch( "string:$tokenHtml" );
            }

            if ( self::sendMessage( $from,
                                    $userID,
                                    $contactId,
                                    $tokenSubject,
                                    $tokenText,
                                    $tokenHtml,
                                    $emailAddress,
                                    $activity->id,
                                    $attachments ) ) {
                $sent[] =  $contactId;
            } else {
                $notSent[] = $contactId;
            } 
        }
        
        return array( count($contactIds), $sent, $notSent, $activity->id );
    }
    
    /**
     * send the message to a specific contact
     *
     * @param string $from         the name and email of the sender
     * @param int    $toID         the contact id of the recipient       
     * @param string $subject      the subject of the message
     * @param string $message      the message contents
     * @param string $emailAddress use this 'to' email address instead of the default Primary address 
     * @param int    $activityID   the activity ID that tracks the message
     *
     * @return boolean             true if successfull else false.
     * @access public
     * @static
     */
    static function sendMessage( $from, $fromID, $toID, &$subject, &$text_message, &$html_message, $emailAddress, $activityID, $attachments = null ) 
    {
        list( $toDisplayName, $toEmail, $toDoNotEmail ) = CRM_Contact_BAO_Contact::getContactDetails( $toID );
        if ( $emailAddress ) {
            $toEmail = trim( $emailAddress );
        }
        
        // make sure both email addresses are valid
        // and that the recipient wants to receive email
        if ( empty( $toEmail ) or $toDoNotEmail ) {
            return false;
        }
        if ( ! trim($toDisplayName) ) {
            $toDisplayName = $toEmail;
        }
        
        if ( ! CRM_Utils_Mail::send( $from,
                                     $toDisplayName, $toEmail,
                                     $subject,
                                     $text_message,
                                     null,
                                     null,
                                     null,
                                     $html_message,
                                     $attachments ) ) {
            return false;
        }

        // add activity target record for every mail that is send
        $activityTargetParams = array( 
                                      'activity_id'       => $activityID,
                                      'target_contact_id' => $toID
                                      );
        self::createActivityTarget( $activityTargetParams );
        return true;
    }
    
    /**
     * combine all the importable fields from the lower levels object
     *
     * The ordering is important, since currently we do not have a weight
     * scheme. Adding weight is super important and should be done in the
     * next week or so, before this can be called complete.
     *
     * @param NULL
     * 
     * @return array    array of importable Fields
     * @access public
     */
    function &importableFields( ) 
    {
        if ( ! self::$_importableFields ) {
            if ( ! self::$_importableFields ) {
                self::$_importableFields = array();
            }
            if (!$status) {
                $fields = array( '' => array( 'title' => ts('- do not import -') ) );
            } else {
                $fields = array( '' => array( 'title' => ts('- Activity Fields -') ) );
            }
            
            require_once 'CRM/Activity/DAO/Activity.php';
            $tmpFields     = CRM_Activity_DAO_Activity::import( );
            require_once 'CRM/Contact/BAO/Contact.php';
            $contactFields = CRM_Contact_BAO_Contact::importableFields('Individual', null );
             
            // Using new Dedupe rule.
            $ruleParams = array(
                                'contact_type' => 'Individual',
                                'level' => 'Strict'
                                );
            require_once 'CRM/Dedupe/BAO/Rule.php';
            $fieldsArray = CRM_Dedupe_BAO_Rule::dedupeRuleFields($ruleParams);
            
            $tmpConatctField = array();
            if( is_array($fieldsArray) ) {
                foreach ( $fieldsArray as $value) {
                    $tmpConatctField[trim($value)] = $contactFields[trim($value)];
                    $tmpConatctField[trim($value)]['title'] = $tmpConatctField[trim($value)]['title']." (match to contact)" ;
                }
            }
            $tmpConatctField['external_identifier'] = $contactFields['external_identifier'];
            $tmpConatctField['external_identifier']['title'] = $contactFields['external_identifier']['title'] . " (match to contact)";
            $fields = array_merge($fields, $tmpConatctField);
            $fields = array_merge($fields, $tmpFields);
            $fields = array_merge($fields, CRM_Core_BAO_CustomField::getFieldsForImport('Activity'));
            self::$_importableFields = $fields;
        }
        return self::$_importableFields;
    }

 /**
     * To get the Activities of a target contact
     *
     * @param $contactId    Integer  ContactId of the contact whose activities
     *                               need to find
     * 
     * @return array    array of activity fields
     * @access public
     */
    
    function getContactActivity( $contactId )
    {
        $activities = array();
        
        // First look for activities where contactId is one of the targets
        $query = "SELECT activity_id FROM civicrm_activity_target
                  WHERE  target_contact_id = $contactId";
        $dao = CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
        while ( $dao->fetch( ) ) {
            $activities[$dao->activity_id]['targets'][] = $contactId;
        }
        
        // Then get activities where contactId is an asignee
        $query = "SELECT activity_id FROM civicrm_activity_assignment
                  WHERE  assignee_contact_id = $contactId";
        $dao = CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
        while ( $dao->fetch( ) ) {
            $activities[$dao->activity_id]['asignees'][] = $contactId;
        }
        
        // Then get activities that contactId created
        $query = "SELECT id AS activity_id FROM civicrm_activity
                  WHERE  source_contact_id = $contactId";
        $dao = CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
        while ( $dao->fetch( ) ) {
            $activities[$dao->activity_id]['source_contact_id'][] = $contactId;
        }         
        
        // Then look up the activity details for each activity_id we saw above
        require_once 'CRM/Core/OptionGroup.php';
        foreach ( $activities as $activityId => $dummy ) {
            $query = "SELECT * FROM civicrm_activity activity
                      LEFT JOIN     civicrm_activity_target target
                      ON            activity.id = target.activity_id
                      LEFT JOIN     civicrm_activity_assignment assignment
                      ON            activity.id = assignment.activity_id
                      WHERE         activity.id = $activityId";
                
            $dao = CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
            
            while ( $dao->fetch( ) ) {
                $activities[$activityId]['source_contact_id'] = $dao->source_contact_id;
                if ( $dao->target_contact_id ) {
                    $activities[$activityId]['targets'][]     = $dao->target_contact_id;
                }
                if ( $dao->asignee_contact_id ) {
                    $activities[$activityId]['asignees'][]    = $dao->asignee_contact_id;
                }
                $activities[$activityId]['activity_type_id']  = $dao->activity_type_id;
                $activities[$activityId]['subject']           = $dao->subject;
                $activities[$activityId]['location']          = $dao->location;
                $activities[$activityId]['activity_date_time']= $dao->activity_date_time;
                $activities[$activityId]['details']           = $dao->details;
                $activities[$activityId]['status_id']         = $dao->status_id;
                $activities[$activityId]['activity_name']     = CRM_Core_OptionGroup::getLabel('activity_type',
                                                                                                     $dao->activity_type_id );
                $activities[$activityId]['status']            = CRM_Core_OptionGroup::getLabel('activity_status',
                                                                                                     $dao->status_id );
            }                                                                              
        }
        return $activities;
    }

    /**
     * Function to add activity for Membership/Event/Contribution
     *
     * @param object  $activity   (reference) perticular component object
     * @param string  $activityType for Membership Signup or Renewal
     *
     *  
     * @static
     * @access public
     */
    static function addActivity( &$activity, $activityType = 'Membership Signup' )
    { 
        if ( $activity->__table == 'civicrm_membership' ) {
            require_once "CRM/Member/PseudoConstant.php";
            $membershipType = CRM_Member_PseudoConstant::membershipType( $activity->membership_type_id );
            
            if ( ! $membershipType ) {
                $membershipType = ts('Membership');
            }
            
            $subject = "{$membershipType}";
            
            if ( $activity->source != 'null' ) {
                $subject .= " - {$activity->source}";
            }
            
            if ( $activity->owner_membership_id ) {
                $cid         = CRM_Core_DAO::getFieldValue( 
                                                           'CRM_Member_DAO_Membership', 
                                                           $activity->owner_membership_id,
                                                           'contact_id' );
                $displayName = CRM_Core_DAO::getFieldValue( 
                                                           'CRM_Contact_DAO_Contact',
                                                           $cid, 'display_name' );
                $subject .= " (by {$displayName})";
            }
            
            require_once 'CRM/Member/DAO/MembershipStatus.php';
            $subject .= " - Status: " . CRM_Core_DAO::getFieldValue( 'CRM_Member_DAO_MembershipStatus', $activity->status_id );
            $date = $activity->start_date;
            $component = 'Membership';

        } else if ( $activity->__table == 'civicrm_participant' ) {
            require_once "CRM/Event/BAO/Event.php";
            $event = CRM_Event_BAO_Event::getEvents( true, $activity->event_id );
            
            require_once "CRM/Event/PseudoConstant.php";
            $roles  = CRM_Event_PseudoConstant::participantRole( );
            $status = CRM_Event_PseudoConstant::participantStatus( );
            
            $subject = $event[$activity->event_id];
            if ( CRM_Utils_Array::value( $activity->role_id, $roles ) ) {
                $subject .= ' - ' . $roles[$activity->role_id]; 
            }
            if ( CRM_Utils_Array::value( $activity->status_id, $status ) ) {
                $subject .= ' - ' . $status[$activity->status_id]; 
            }
            $date = date( 'YmdHis' );
            $activityType = 'Event Registration';
            $component = 'Event';
            
        } else if ( $activity->__table == 'civicrm_contribution' ) {
            //create activity record only for Completed Contributions
            if ( $activity->contribution_status_id != 1 ) {
                return;
            }
            
            $subject = null;
                       
            require_once "CRM/Utils/Money.php";
            $subject .= CRM_Utils_Money::format($activity->total_amount, $activity->currency);
            if ( $activity->source != 'null' ) {
                $subject .= " - {$activity->source}";
                
            } 
            $date = CRM_Utils_Date::isoToMysql($activity->receive_date);
            $activityType = $component = 'Contribution';
        } 
        require_once "CRM/Core/OptionGroup.php";
        $activityParams = array( 'source_contact_id' => $activity->contact_id,
                                 'source_record_id'  => $activity->id,
                                 'activity_type_id'  => CRM_Core_OptionGroup::getValue( 'activity_type',
                                                                                        $activityType,
                                                                                        'name' ),
                                 'subject'            => $subject,
                                 'activity_date_time' => $date,
                                 'is_test'            => $activity->is_test,
                                 'status_id'          => 2
                                 );
        
        require_once 'api/v2/Activity.php';
        if ( is_a( civicrm_activity_create( $activityParams ), 'CRM_Core_Error' ) ) {
            CRM_Core_Error::fatal("Failed creating Activity for $component of id {$activity->id}");
            return false;
        }
    }

    /**
     * Function to get Parent activity for currently viewd activity
     *
     * @param int  $activityId   current activity id
     *
     * @return int $parentId  Id of parent acyivity otherwise false.
     * @access public
     */
    static function getParentActivity( $activityId )
    {
        static $parentActivities = array( );

        $activityId = CRM_Utils_Type::escape($activityId, 'Integer');

        if ( ! array_key_exists($activityId, $parentActivities) ) {
            $parentActivities[$activityId] = array( );

            $parentId = CRM_Core_DAO::getFieldValue( 'CRM_Activity_DAO_Activity',
                                                     $activityId,
                                                     'parent_id' );

            $parentActivities[$activityId] = $parentId ? $parentId : false;
        }

        return $parentActivities[$activityId];
    }

    /**
     * Function to get total count of prior revision of currently viewd activity
     *
     * @param int  $activityId   current activity id
     *
     * @return int $params  count of prior acyivities otherwise false.
     * @access public
     */
    static function getPriorCount( $activityID )
    {
        static $priorCounts = array( );

        $activityID = CRM_Utils_Type::escape($activityID, 'Integer');

        if ( ! array_key_exists($activityID, $priorCounts) ) {
            $priorCounts[$activityID] = array( );
            $originalID = 
                CRM_Core_DAO::getFieldValue( 'CRM_Activity_DAO_Activity',
                                             $activityID,
                                             'original_id' );
            if ( $originalID ) {
                $query  = "
SELECT count( id ) AS cnt
FROM civicrm_activity
WHERE ( id = {$originalID} OR original_id = {$originalID} )
AND is_current_revision = 0
AND id < {$activityID} 
";
                $params = array( 1 => array( $originalID, 'Integer' ) );
                $count  = CRM_Core_DAO::singleValueQuery( $query, $params );
            }
            $priorCounts[$activityID] = $count ? $count : 0;
        }

        return $priorCounts[$activityID];
    }

    /**
     * Function to get all prior activities of currently viewd activity
     *
     * @param int  $activityId   current activity id
     *
     * @return array $result  prior acyivities info.
     * @access public
     */
    static function getPriorAcitivities( $activityID, $onlyPriorRevisions = false ) 
    {
        static $priorActivities = array( );

        $activityID = CRM_Utils_Type::escape($activityID, 'Integer');
        $index      = $activityID . '_' . (int) $onlyPriorRevisions;

        if ( ! array_key_exists($index, $priorActivities) ) {
            $priorActivities[$index] = array( );

            $originalID = CRM_Core_DAO::getFieldValue( 'CRM_Activity_DAO_Activity',
                                                       $activityID,
                                                       'original_id' );
            if ( $originalID ) {
                $query = "
SELECT c.display_name as name, cl.modified_date as date, ca.id as activityID
FROM civicrm_log cl, civicrm_contact c, civicrm_activity ca
WHERE (ca.id = %1 OR ca.original_id = %1)
AND cl.entity_table = 'civicrm_activity'
AND cl.entity_id    = ca.id
AND cl.modified_id  = c.id
";
                if ( $onlyPriorRevisions ) {
                    $query .= " AND ca.id < {$activityID}";
                }
                $query .= " ORDER BY ca.id DESC";

                $params = array( 1 => array( $originalID, 'Integer' ) );
                $dao    =& CRM_Core_DAO::executeQuery( $query, $params );
            
                while ( $dao->fetch( ) ) {
                    $priorActivities[$index][$dao->activityID]['id']   = $dao->activityID;
                    $priorActivities[$index][$dao->activityID]['name'] = $dao->name;
                    $priorActivities[$index][$dao->activityID]['date'] = $dao->date;
                    $priorActivities[$index][$dao->activityID]['link'] = 'javascript:viewActivity( $dao->activityID );';
                }
                $dao->free( );
            }
        }
        return $priorActivities[$index];
    }

    /**
     * Function to find the latest revision of a given activity
     *
     * @param int  $activityId    prior activity id
     *
     * @return int $params  current activity id.
     * @access public
     */
    static function getLatestActivityId( $activityID )
    {
        static $latestActivityIds = array( );

        $activityID = CRM_Utils_Type::escape($activityID, 'Integer');

        if ( ! array_key_exists($activityID, $latestActivityIds) ) {
            $latestActivityIds[$activityID] = array();

            $originalID = CRM_Core_DAO::getFieldValue( 'CRM_Activity_DAO_Activity',
                                                       $activityID,
                                                       'original_id' );
            if ( $originalID ) {
                $activityID = $originalID;
            }
            $params =  array( 1 => array( $activityID, 'Integer' ) );
            $query  = "SELECT id from civicrm_activity where original_id = %1 and is_current_revision = 1";

            $latestActivityIds[$activityID] = CRM_Core_DAO::singleValueQuery( $query, $params );
        }

        return $latestActivityIds[$activityID];
    }
    /**
     * Function to create a follow up a given activity
     *
     * @activityId int activity id of parent activity 
     * @param array  $activity details
     * @caseId int Case id
     * 
     * @access public
     */
    static function createFollowupActivity( $activityId, $params )
    { 
        if ( !$activityId ) {
            return;
        }
       
        $followupParams                      = array( );
        $followupParams['parent_id']         = $activityId;
        $followupParams['source_contact_id'] = $params['source_contact_id'];
        $followupParams['status_id']         = 
            CRM_Core_OptionGroup::getValue( 'activity_status', 'Scheduled', 'name' );
        
        $activityTypes = CRM_Case_PseudoConstant::activityType( );
        $followupParams['activity_type_id']  = $activityTypes[$params['followup_activity']]['id'];
        
        CRM_Utils_Date::getAllDefaultValues( $currentDate );
        $followupParams['due_date_time']        = 
            CRM_Utils_Date::intervalAdd($params['interval_unit'], 
                                        $params['interval'], $currentDate); 
        $followupParams['due_date_time']     =  CRM_Utils_Date::format($followupParams['due_date_time']);
        
        return self::create( $followupParams );
    }

    /**
     * Function to get Activity specific File according activity type Id.
     *
     * @param int  $activityTypeId  activity id
     *
     * @return if file exists returns $caseAction activity filename otherwise false.
     *
     * @static
     */
    static function getFileForActivityTypeId( $activityTypeId, $crmDir = 'Activity' ) 
    {
        $activityTypes  = CRM_Case_PseudoConstant::activityType( false );
        
        if ( $activityTypes[$activityTypeId]['name'] ) {
            require_once 'CRM/Utils/String.php';
            $caseAction = CRM_Utils_String::munge( ucwords($activityTypes[$activityTypeId]['name']), '', 0 );
        } else {
            return false;
        }
        
        global $civicrm_root;
        if ( !file_exists(rtrim($civicrm_root, '/') . "/CRM/{$crmDir}/Form/Activity/{$caseAction}.php") ) {
            return false;
        }

        return $caseAction;
    }

    /**
     * Function to restore the activity
     * @param array  $params  associated array 
     *
     * @return void
     * @access public
     *
     */
    public function restoreActivity( &$params ) 
    {
        $activity    =& new CRM_Activity_DAO_Activity( );
        $activity->copyValues( $params );

        $activity->is_deleted = 0;
        $result = $activity->save( );

        return $result;
    }
}
