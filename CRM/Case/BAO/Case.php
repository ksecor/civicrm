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

require_once 'CRM/Case/DAO/Case.php';
require_once 'CRM/Case/PseudoConstant.php';

/**
 * This class contains the funtions for Case Management
 *
 */
class CRM_Case_BAO_Case extends CRM_Case_DAO_Case
{
  
    /**
     * static field for all the case information that we can potentially export
     *
     * @var array
     * @static
     */
    static $_exportableFields = null;

    /**  
     * value seletor for multi-select
     **/ 
   
    const VALUE_SEPERATOR = "";
    
    function __construct()
    {
        parent::__construct();
    }
    

    /**
     * takes an associative array and creates a case object
     *
     * the function extract all the params it needs to initialize the create a
     * case object. the params array could contain additional unused name/value
     * pairs
     *
     * @param array  $params (reference ) an assoc array of name/value pairs
     * @param array $ids    the array that holds all the db ids
     *
     * @return object CRM_Case_BAO_Case object
     * @access public
     * @static
     */
    static function add( &$params ) 
    {
        $caseDAO =& new CRM_Case_DAO_Case();
        $caseDAO->copyValues($params);
        return $caseDAO->save();
    }

    /**
     * Given the list of params in the params array, fetch the object
     * and store the values in the values array
     *
     * @param array $params input parameters to find object
     * @param array $values output values of the object
     * @param array $ids    the array that holds all the db ids
     *
     * @return CRM_Case_BAO_Case|null the found object or null
     * @access public
     * @static
     */
    static function &getValues( &$params, &$values, &$ids ) 
    {
        $case =& new CRM_Case_BAO_Case( );

        $case->copyValues( $params );
        
        if ( $case->find(true) ) {
            $ids['case']    = $case->id;
            CRM_Core_DAO::storeValues( $case, $values );
            return $case;
        }
        return null;
    }

    /**
     * takes an associative array and creates a case object
     *
     * @param array $params (reference ) an assoc array of name/value pairs
     * @param array $ids    the array that holds all the db ids
     *
     * @return object CRM_Case_BAO_Case object 
     * @access public
     * @static
     */
    static function &create( &$params ) 
    {
        require_once 'CRM/Core/Transaction.php';
        $transaction = new CRM_Core_Transaction( ); 
        
        $case = self::add( $params );

        if ( is_a( $case, 'CRM_Core_Error') ) {
            $transaction->rollback( );
            return $case;
        }
        $session = & CRM_Core_Session::singleton();
        $id = $session->get('userID');
        if ( !$id ) {
            $id = $params['contact_id'];
        } 

        //handle custom data.
        if ( CRM_Utils_Array::value( 'custom', $params ) &&
             is_array( $params['custom'] ) ) {
            require_once 'CRM/Core/BAO/CustomValueTable.php';
            CRM_Core_BAO_CustomValueTable::store( $params['custom'], 'civicrm_case', $case->id );
        }

        // Log the information on successful add/edit of Case
        require_once 'CRM/Core/BAO/Log.php';
        $logParams = array(
                           'entity_table'  => 'civicrm_case',
                           'entity_id'     => $case->id,
                           'modified_id'   => $id,
                           'modified_date' => date('Ymd')
                           );
        
        CRM_Core_BAO_Log::add( $logParams );
        $transaction->commit( );
        
        return $case;
    }

    /**
     * Create case contact record
     *
     * @param array    case_id, contact_id
     *
     * @return object
     * @access public
     */
    function addCaseToContact( $params ) {
        require_once 'CRM/Case/DAO/CaseContact.php';
        $caseContact =& new CRM_Case_DAO_CaseContact();
        $caseContact->case_id = $params['case_id'];
        $caseContact->contact_id = $params['contact_id'];
        $caseContact->find(true);
        $caseContact->save();

        return $caseContact;
    }

    /**
     * Delet case contact record
     *
     * @param int    case_id
     *
     * @return Void
     * @access public
     */
    function deleteCaseContact( $caseID ) {
        require_once 'CRM/Case/DAO/CaseContact.php';
        $caseContact =& new CRM_Case_DAO_CaseContact();
        $caseContact->case_id = $caseID;
        $caseContact->delete();
    }

    /**
     * This function is used to convert associative array names to values
     * and vice-versa.
     *
     * This function is used by both the web form layer and the api. Note that
     * the api needs the name => value conversion, also the view layer typically
     * requires value => name conversion
     */
    static function lookupValue(&$defaults, $property, &$lookup, $reverse)
    {
        $id = $property . '_id';

        $src = $reverse ? $property : $id;
        $dst = $reverse ? $id       : $property;

        if (!array_key_exists($src, $defaults)) {
            return false;
        }

        $look = $reverse ? array_flip($lookup) : $lookup;
        
        if(is_array($look)) {
            if (!array_key_exists($defaults[$src], $look)) {
                return false;
            }
        }
        $defaults[$dst] = $look[$defaults[$src]];
        return true;
    }

    /**
     * Takes a bunch of params that are needed to match certain criteria and
     * retrieves the relevant objects. We'll tweak this function to be more
     * full featured over a period of time. This is the inverse function of
     * create.  It also stores all the retrieved values in the default array
     *
     * @param array $params   (reference ) an assoc array of name/value pairs
     * @param array $defaults (reference ) an assoc array to hold the name / value pairs
     *                        in a hierarchical manner
     * @param array $ids      (reference) the array that holds all the db ids
     *
     * @return object CRM_Case_BAO_Case object
     * @access public
     * @static
     */
    static function retrieve( &$params, &$defaults, &$ids ) 
    {
        $case = CRM_Case_BAO_Case::getValues( $params, $defaults, $ids );
        return $case;
    }

    /**
     * Function to process case activity add/delete
     * takes an associative array and
     *
     * @param array $params (reference ) an assoc array of name/value pairs
     *
     * @access public
     * @static
     */
    static function processCaseActivity( &$params ) 
    {
        require_once 'CRM/Case/DAO/CaseActivity.php';
        $caseActivityDAO =& new CRM_Case_DAO_CaseActivity();
        $caseActivityDAO->activity_id = $params['activity_id'];
        $caseActivityDAO->case_id = $params['case_id'];

        $caseActivityDAO->find( true );
        $caseActivityDAO->save();
    } 

    /**
     * Function to get the case subject for Activity
     *
     * @param int $activityId  activity id
     * @return  case subject or null
     * @access public
     * @static
     */
    static function getCaseSubject ( $activityId )
    {
        require_once 'CRM/Case/DAO/CaseActivity.php';
        $caseActivity =  new CRM_Case_DAO_CaseActivity();
        $caseActivity->activity_id = $activityId;
        if ( $caseActivity->find(true) ) {
            return CRM_Core_DAO::getFieldValue('CRM_Case_BAO_Case', $caseActivity->case_id,'subject' );
        }
        return null;
    }

   /**                                                           
     * Delete the record that are associated with this case 
     * record are deleted from case 
     * @param  int  $caseId id of the case to delete
     * 
     * @return void
     * @access public 
     * @static 
     */ 
    static function deleteCase( $caseId , $moveToTrash = false ) 
    {
        //delete activities
        $activities = self::getCaseActivity( $caseId, $params = array(), null, true );
        if ( $activities ) {
            require_once"CRM/Activity/BAO/Activity.php";
            foreach( $activities as $value ) {
                CRM_Activity_BAO_Activity::deleteActivity( $value, $moveToTrash );
            }
        }  
        
        if ( ! $moveToTrash ) {
            require_once 'CRM/Core/Transaction.php';
            $transaction = new CRM_Core_Transaction( );
        }
        require_once 'CRM/Case/DAO/Case.php';
        $case     = & new CRM_Case_DAO_Case( );
        $case->id = $caseId; 
        if ( ! $moveToTrash ) {  
            $case->delete( );
            $transaction->commit( );
            return true;
        } else {
                                    
            $case->is_deleted = 1;
            $case->save( );
            return true;
        }
        return false;
    }

   /**                                                           
     * Delete the activities related to case
     *
     * @param  int  $activityId id of the activity
     * 
     * @return void
     * @access public 
     * @static 
     */ 
    static function deleteCaseActivity( $activityId ) 
    {
        require_once 'CRM/Case/DAO/CaseActivity.php';
        $case              = & new CRM_Case_DAO_CaseActivity( );
        $case->activity_id = $activityId; 
        $case->delete( );
    }
    /* * Retrieve contact_id by case_id
     *
     * @param int    $caseId  ID of the case
     * 
     * @return array
     * 
     * @access public
     * 
     */
    
     function retrieveContactIdsByCaseId( $caseId , $contactID = null ) 
     {
         require_once 'CRM/Case/DAO/CaseContact.php';
         $caseContact =   & new CRM_Case_DAO_CaseContact( );
         $caseContact->case_id = $caseId;
         $caseContact->find();
         $contactArray = array();
         $count = 1;
         while ( $caseContact->fetch( ) ) {
             if ( $contactID != $caseContact->contact_id ) {
                 $contactArray[$count] = $caseContact->contact_id;
                 $count++;
             }
         }
         
         return $contactArray;
     }
      /**
     * Retrieve contact names by caseId
     *
     * @param int    $caseId  ID of the case
     * 
     * @return array
     * 
     * @access public
     * 
     */
    static function getcontactNames( $caseId ) 
    {
        $queryParam = array();
        $query = "SELECT contact_a.sort_name 
                  FROM civicrm_contact contact_a 
                  LEFT JOIN civicrm_case_contact 
                         ON civicrm_case_contact.contact_id = contact_a.id
                  WHERE civicrm_case_contact.case_id = {$caseId}";
        $dao = CRM_Core_DAO::executeQuery($query,$queryParam);
        $contactNames = array();
        while ( $dao->fetch() ) {
            $contactNames[] =  $dao->sort_name;
        }
        return $contactNames;
    }

    /* * Retrieve case_id by contact_id
     *
     * @param int    $contactId  ID of the contact
     * 
     * @return array
     * 
     * @access public
     * 
     */
    function retrieveCaseIdsByContactId( $contactID ) 
    {
         require_once 'CRM/Case/DAO/CaseContact.php';
         $caseContact =   & new CRM_Case_DAO_CaseContact( );
         $caseContact->contact_id = $contactID;
         $caseContact->find();
         $caseArray = array();
         $count = 1;
         while ( $caseContact->fetch( ) ) {
             $caseArray[$count] = $caseContact->case_id;
             $count++;
         }
         return $caseArray;
     }

   /**
     * Retrieve cases related to particular contact or whole contact
     * used in Dashboad and Tab
     *
     * @param boolean    $allCases  
     * 
     * @param int        $userID 
     *
     * @param String     $type /upcoming,recent,all/ 
     *
     * @return array     Array of Cases
     * 
     * @access public
     * 
     */

    function getCases( $allCases = true, $userID = null, $type = 'upcoming' )
    {

        $resultFields = array( 'contact_id',
                               'contact_type',
                               'sort_name',
                               'case_id',
                               'case_type',
                               'status_id',
                               'case_status',
                               'activity_type_id',
                               'case_role', );

        
        if ( $type == 'upcoming' ) {
            $resultFields[] = 'case_scheduled_activity_date';
            $resultFields[] = 'case_scheduled_activity_type';
        } else if ( $type == 'recent' ) {
            $resultFields[] = 'case_recent_activity_date';
            $resultFields[] = 'case_recent_activity_type';
        }
        
        $query = "SELECT
                  civicrm_case.id as case_id,
                  civicrm_contact.id as contact_id,
                  civicrm_contact.sort_name as sort_name,
                  civicrm_contact.contact_type as contact_type,
                  civicrm_activity.activity_type_id,
                  cov_type.label as case_type,
                  cov_status.label as case_status,
                  civicrm_activity.status_id,
                  case_relation_type.name_b_a as case_role, ";
        if ( $type == 'upcoming' ) {
            $query .=  " civicrm_activity.due_date_time as case_scheduled_activity_date,
                         aov.label as case_scheduled_activity_type ";       
        } else if ( $type == 'recent' || $type == 'all' ) {
            $query .=  " civicrm_activity.activity_date_time as case_recent_activity_date,
                         aov.label as case_recent_activity_type ";
        } 
        
        $query .= 
            " FROM civicrm_case
                  INNER JOIN civicrm_case_activity
                        ON civicrm_case_activity.case_id = civicrm_case.id  
            
                  LEFT JOIN civicrm_case_contact ON civicrm_case.id = civicrm_case_contact.case_id
                  LEFT JOIN civicrm_contact ON civicrm_case_contact.contact_id = civicrm_contact.id ";

        if ( $type == 'upcoming' ) {
            $query .= " LEFT JOIN civicrm_activity
                             ON ( civicrm_case_activity.activity_id = civicrm_activity.id
                                  AND civicrm_activity.is_current_revision = 1
                                  AND civicrm_activity.due_date_time <= DATE_ADD( NOW(), INTERVAL 14 DAY ) ) ";
        } else if ( $type == 'recent' || $type == 'all' ) {
            $query .= " LEFT JOIN civicrm_activity
                             ON ( civicrm_case_activity.activity_id = civicrm_activity.id
                                  AND civicrm_activity.is_current_revision = 1
                                  AND civicrm_activity.activity_date_time <= NOW() 
                                  AND civicrm_activity.activity_date_time >= DATE_SUB( NOW(), INTERVAL 14 DAY ) ) ";
        }
               
        $query .= "
                  LEFT JOIN civicrm_option_group aog  ON aog.name = 'activity_type'
                  LEFT JOIN civicrm_option_value aov
                        ON ( civicrm_activity.activity_type_id = aov.value
                             AND aog.id = aov.option_group_id )         

                  LEFT  JOIN  civicrm_relationship case_relationship 
                        ON ( case_relationship.contact_id_a = civicrm_case_contact.contact_id 
                             AND case_relationship.contact_id_b = {$userID} )
     
                  LEFT  JOIN civicrm_relationship_type case_relation_type 
                        ON ( case_relation_type.id = case_relationship.relationship_type_id 
                             AND case_relation_type.id = case_relationship.relationship_type_id )

                  LEFT JOIN civicrm_option_group cog_type ON cog_type.name = 'case_type'
                  LEFT JOIN civicrm_option_value cov_type
                        ON ( civicrm_case.case_type_id = cov_type.value
                             AND cog_type.id = cov_type.option_group_id )

                  LEFT JOIN civicrm_option_group cog_status ON cog_status.name = 'case_status'
                  LEFT JOIN civicrm_option_value cov_status 
                       ON ( civicrm_case.status_id = cov_status.value
                            AND cog_status.id = cov_status.option_group_id )

                  LEFT JOIN civicrm_option_group cog_actstatus ON cog_actstatus.name = 'activity_status'
                  LEFT JOIN civicrm_option_value cov_actstatus 
                       ON ( civicrm_activity.status_id = cov_actstatus.value
                            AND cog_actstatus.id = cov_actstatus.option_group_id )";

        $query .= "LEFT JOIN civicrm_activity ca2
                             ON ( ca2.id IN ( SELECT cca.activity_id FROM civicrm_case_activity cca 
                                              WHERE cca.case_id = civicrm_case.id )
                                  AND ca2.is_current_revision = 1 ";
        
        if ( $type == 'upcoming' ) {
            $query .= "AND ca2.due_date_time <= DATE_ADD( NOW(), INTERVAL 14 DAY ) 
                       AND ca2.status_id = cov_actstatus.value
                       AND civicrm_activity.due_date_time > ca2.due_date_time 
                        )";
        } else if ( $type == 'recent' || $type == 'all' ) {
            $query .= " AND ca2.activity_date_time <= NOW() 
                        AND ca2.activity_date_time >= DATE_SUB( NOW(), INTERVAL 14 DAY )
                        AND civicrm_activity.activity_date_time < ca2.activity_date_time )";
        }
        
        if ( $type == 'upcoming' ) {
            $query .= " WHERE cov_actstatus.name = 'Scheduled' AND ca2.id IS NULL "; 
        } else if ( $type == 'recent' || $type == 'all' ) {
            $query .= " WHERE cov_actstatus.name != 'Scheduled' AND ca2.id IS NULL";
        } 
        if ( $type == 'all') {
            $query .= " AND civicrm_case_contact.contact_id = {$userID} ";  
        }
                  
        if ( ! $allCases && $type != 'all' ) {
            $query .= " AND civicrm_case_contact.contact_id = {$userID}";
        }
        
        // make sure deleted activities and cases are not included
        $query .= " AND civicrm_activity.is_deleted = 0
                    AND civicrm_case.is_deleted = 0";

        if ( $type == 'upcoming' ) {
            $query .=" GROUP BY case_id
                       ORDER BY case_scheduled_activity_date ASC ";
        } else if ( $type == 'recent' || $type == 'all' ) {
            $query .= " GROUP BY case_id
                        ORDER BY case_recent_activity_date ASC ";
        }
        if ( $type == 'all' ) {
            //return if call is from tab, no need of further processing
            return $query;
        }
        return self::_getCasesList( $resultFields, $query );
    }


    /**
     * Function to get the summary of cases counts by type and status.
     */
    function getCasesSummary( )
    {
    
        require_once 'CRM/Core/OptionGroup.php';
        $caseStatuses = CRM_Core_OptionGroup::values( 'case_status' );
        $caseTypes    = CRM_Core_OptionGroup::values( 'case_type' );

        // get statuses as headers for the table
        $caseSummary['headers'] = $caseStatuses;

        // build rows with actual data
        $rows = array();
        foreach( $caseTypes as $typeId => $type ) {
            $rows[$typeId]['case_type'] = $type;

            $query = "select status_id, count(*) as case_count from civicrm_case" . 
            " where is_deleted = 0 AND case_type_id like '%" . $this->VALUE_SEPERATOR . $typeId . $this->VALUE_SEPERATOR . "%'" .
            " group by status_id";
            $res = CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );

            // make sure all the statuses are present, since we're not calculating 0 values
            foreach( $caseStatuses as $key => $dontCare ) {
                $q[$key] = '0';
            }
            
            while( $res->fetch() ) {
                $q[$res->status_id] = array( 'case_count' => $res->case_count,
                                             'url'        => CRM_Utils_System::url( 'civicrm/case/search',
                                                                                    "reset=1&force=1&status={$res->status_id}&type={$typeId}" )
                                             );
            }

            $rows[$typeId]['columns'] = $q;
        }

        $caseSummary['rows'] = $rows;

        return $caseSummary;
    }

    /**
     * Function to get Case roles
     *
     * @param int $contactID contact id
     * @param int $caseID case id
     *
     * @return returns case role / relationships
     *
     * @static
     */
    static function getCaseRoles( $contactID, $caseID, $relationshipID = null )
    {
        $query = '
SELECT civicrm_relationship.id as civicrm_relationship_id, civicrm_contact.sort_name as sort_name, civicrm_email.email as email, civicrm_phone.phone as phone, civicrm_relationship.contact_id_b as civicrm_contact_id, civicrm_relationship_type.name_b_a as relation, civicrm_relationship_type.id as relation_type 
FROM civicrm_relationship, civicrm_relationship_type, civicrm_contact 
LEFT OUTER JOIN civicrm_phone ON (civicrm_phone.contact_id = civicrm_contact.id AND civicrm_phone.is_primary = 1) 
LEFT JOIN civicrm_email ON (civicrm_email.contact_id = civicrm_contact.id ) 
WHERE civicrm_relationship.relationship_type_id = civicrm_relationship_type.id AND civicrm_relationship.contact_id_a = %1 AND civicrm_relationship.contact_id_b = civicrm_contact.id AND civicrm_relationship.case_id = %2
';

        $params = array( 1 => array( $contactID, 'Integer' ),
                         2 => array( $caseID, 'Integer' )
                         );

		if ( $relationshipID ) {
			$query .= ' AND civicrm_relationship.id = %3 ';
			$params[3] = array( $relationshipID, 'Integer' );
		}
        
        $dao =& CRM_Core_DAO::executeQuery( $query, $params );

        $values = array( );
        while ( $dao->fetch( ) ) {
            $rid = $dao->civicrm_relationship_id;
            $values[$rid]['cid']        = $dao->civicrm_contact_id;
            $values[$rid]['relation']   = $dao->relation;
            $values[$rid]['name']       = $dao->sort_name;
            $values[$rid]['email']      = $dao->email;
            $values[$rid]['phone']      = $dao->phone;
            $values[$rid]['relation_type']   = $dao->relation_type;
        }
        
        $dao->free( );
        return $values;
    }

    /**
     * Function to get Case Activities
     *
     * @param int    $caseID case id
     * @param array  $params posted params 
     * @param int    $contactID contact id
     *
     * @return returns case activities
     *
     * @static
     */
    static function getCaseActivity( $caseID, &$params, $contactID,  $skipDetails = false )
    {
        $values = array( );
        if ( $skipDetails ) {
            if ( !$caseID ) {
                return;
            }
            
            $query = "SELECT ca.id 
                      FROM civicrm_activity ca 
                      LEFT JOIN civicrm_case_activity cca ON cca.activity_id = ca.id LEFT JOIN civicrm_case cc ON cc.id = cca.case_id 
                      WHERE cc.id = %1";
            
            $params = array( 1 => array( $caseID, 'Integer' ) );
            $dao    =& CRM_Core_DAO::executeQuery( $query, $params );
            
            while ( $dao->fetch( ) ) {
                $values[$dao->id]['id']  = $dao->id;
            }
            $dao->free( );
            return $values;
        }

        $select = 'SELECT ca.id as id, 
                          ca.activity_type_id as type, 
                          cc.sort_name as reporter, 
                          ca.due_date_time as due_date, 
                          ca.activity_date_time actual_date, 
                          ca.status_id as status, 
                          ca.subject as subject,
                          ca.is_deleted as deleted ';

        $from  = 'FROM civicrm_case_activity cca, 
                       civicrm_activity ca, 
                       civicrm_contact cc '; 

        $where = 'WHERE cca.case_id= %1 
                    AND ca.id = cca.activity_id 
                    AND cc.id = ca.source_contact_id
                    AND ca.is_current_revision = 1';

        if ( $params['reporter_id'] ) {
            $where .= " AND ca.source_contact_id = ".CRM_Utils_Type::escape( $params['reporter_id'], 'Integer' );
        }

        if ( $params['status_id'] ) {
            $where .= " AND ca.status_id = ".CRM_Utils_Type::escape( $params['status_id'], 'Integer' );
        }

        if ( $params['is_current_revision'] ) {
            $where .= " AND ca.is_current_revision = 1";
        }
		
		if ( CRM_Utils_Array::value( 'activity_deleted', $params ) ) {
            $where .= " AND ca.is_deleted = 1";
        }

        if ( $params['activity_type_id'] ) {
            $where .= " AND ca.activity_type_id = ".CRM_Utils_Type::escape( $params['activity_type_id'], 'Integer' );
        }

        $fromDueDate = CRM_Utils_Type::escape( $params['activity_date_low'], 'Date' );
        $toDueDate   = CRM_Utils_Type::escape( $params['activity_date_high'], 'Date' );

        if ( $params['date_range'] == 0 ) {
            //pass
        } else if ( $params['date_range'] == 1 ) {
            $where .= " AND ( ca.due_date_time >= '{$fromDueDate}' AND ca.due_date_time <= '{$toDueDate}' ) ";
        } else if ( $params['date_range'] == 2 ) {
            $where .= " AND ( ca.activity_date_time >= '{$fromDueDate}' AND ca.activity_date_time <= '{$toDueDate}' ) ";
        } else {
            $fromDueDate = date( 'Ymd', mktime(0, 0, 0, date("m"), date("d")-14, date("Y")) );
            $toDueDate   = date( 'Ymd', mktime(0, 0, 0, date("m"), date("d")+14, date("Y")) );

            $where .= " AND ( ca.due_date_time >= '{$fromDueDate}' AND ca.due_date_time <= '{$toDueDate}' ) ";
        }

		// hack to handle to allow initial sorting to be done by query
		if ( $params['sortname'] == 'undefined' ) {
			$params['sortname'] = null;
		}

		if ( $params['sortorder'] == 'undefined' ) {
			$params['sortorder'] = null;
		}

        $sortname  = $params['sortname'];
        $sortorder = $params['sortorder'];
        
        // Default sort is status_id ASC, due_date_time ASC (so completed activities drop to bottom)
        if ( !$sortname AND !$sortorder ) {
            $orderBy = " ORDER BY status_id ASC, due_date_time ASC";
        } else {
			$orderBy = " ORDER BY {$sortname} {$sortorder}";
		}
        
        $page = $params['page'];
        $rp   = $params['rp'];
        
        if (!$page) $page = 1;
        if (!$rp) $rp = 10;

        $start = (($page-1) * $rp);
        
        $query  = $select . $from . $where . $orderBy;
		
        $params = array( 1 => array( $caseID, 'Integer' ) );
        $dao    =& CRM_Core_DAO::executeQuery( $query, $params );
        $params['total'] = $dao->N;

        //FIXME: need to optimize/cache these queries
        $limit  = " LIMIT $start, $rp";
        $query .= $limit;
        $dao    =& CRM_Core_DAO::executeQuery( $query, $params );

        $activityTypes  = CRM_Case_PseudoConstant::activityType( false );

        require_once "CRM/Utils/Date.php";
        require_once "CRM/Core/PseudoConstant.php";
        $activityStatus = CRM_Core_PseudoConstant::activityStatus( );
        
        $url = CRM_Utils_System::url( "civicrm/case/activity",
                                      "reset=1&cid={$contactID}&caseid={$caseID}", false, null, false ); 
        
        $editUrl     = "{$url}&action=update";
        $deleteUrl   = "{$url}&action=delete";
        $restoreUrl  = "{$url}&action=renew";
        $viewTitle = ts('View this activity.');
        
        require_once 'CRM/Case/BAO/Case.php';
        $caseDeleted = CRM_Core_DAO::getFieldValue( 'CRM_Case_DAO_Case', $caseID, 'is_deleted' );
        
        while ( $dao->fetch( ) ) { 
            $values[$dao->id]['id']                = $dao->id;
            $values[$dao->id]['type']              = $activityTypes[$dao->type]['label'];
            $values[$dao->id]['reporter']          = $dao->reporter;
            $values[$dao->id]['due_date']          = CRM_Utils_Date::customFormat( $dao->due_date );
            $values[$dao->id]['actual_date']       = CRM_Utils_Date::customFormat( $dao->actual_date );
            $values[$dao->id]['status']            = $activityStatus[$dao->status];
            $values[$dao->id]['subject']           = "<a href='javascript:viewActivity( {$dao->id}, {$contactID} );' title='{$viewTitle}'>{$dao->subject}</a>";
            
            $additionalUrl = "&id={$dao->id}";
            if ( !$dao->deleted ) {
                $url  = "<a href='" .$editUrl.$additionalUrl."'>". ts('Edit') . "</a>";
                $url .= " | <a href='" .$deleteUrl.$additionalUrl."'>". ts('Delete') . "</a>";
            } else if ( !$caseDeleted ) {
                $url  = "<a href='" .$restoreUrl.$additionalUrl."'>". ts('Restore') . "</a>";
            } else {
                $url = "";
            }
            
            $values[$dao->id]['links'] = $url;
            if ( $values[$dao->id]['status'] == 'Scheduled' && 
                 CRM_Utils_Date::overdue(  $dao->due_date ) ) {
                $values[$dao->id]['class']   = 'status-overdue';
            } else if ( $values[$dao->id]['status'] == 'Scheduled' ) {
                $values[$dao->id]['class']   = 'status-pending';
            } else if ( $values[$dao->id]['status'] == 'Completed' ) {
                $values[$dao->id]['class']   ="status-completed";
            }
        }

        $dao->free( );
        return $values;
    }

    
    /**
     * Function to get Case Related Contacts
     *
     * @param int    $caseID case id
     *
     * @return returns $searchRows array of returnproperties
     *
     * @static
     */
    static function getRelatedContacts( $caseID )
    {
        $values = array( );
        $query = 'SELECT cc.display_name as name, cc.id, crt.name_b_a as role, ce.email 
FROM civicrm_relationship cr 
LEFT JOIN civicrm_relationship_type crt ON crt.id = cr.relationship_type_id 
LEFT JOIN civicrm_contact cc ON cc.id = cr.contact_id_b 
LEFT JOIN civicrm_email   ce ON ce.contact_id = cc.id 
WHERE cr.case_id =  %1 AND ce.is_primary= 1';
        
        $params = array( 1 => array( $caseID, 'Integer' ) );
        $dao    =& CRM_Core_DAO::executeQuery( $query, $params );

        while ( $dao->fetch( ) ) {
            $values[$dao->id]['id']          = $dao->id;
            $values[$dao->id]['name']        = $dao->name;
            $values[$dao->id]['role']        = $dao->role;
            $values[$dao->id]['email']       = $dao->email;
        }
        $dao->free( );

        return $values;
    }

    /**
     * Function that send e-mail copy of activity
     * 
     * @param int     $activityId activity Id
     * @param array   $contacts array of related contact
     *
     * @return void
     * @access public
     */
    static function sendActivityCopy( $clientId, $activityId, $contacts )
    {   
        require_once 'CRM/Utils/Mail.php';
        require_once 'CRM/Core/BAO/Domain.php';        
        $template =& CRM_Core_Smarty::singleton( );

        $activityInfo = array( );
        $params       = array( 'id' => $activityId );

        require_once 'CRM/Case/XMLProcessor/Report.php';
        $xmlProcessor = new CRM_Case_XMLProcessor_Report( );
        $activityInfo = $xmlProcessor->getActivityInfo($clientId, $activityId);
        $template->assign('activity', $activityInfo );
       
        $emailTemplate  = 'CRM/Case/Form/ActivityMessage.tpl';
        $result         = array();

        list ($name, $address) = CRM_Core_BAO_Domain::getNameAndEmail();
        $receiptFrom = "\"$name\" <$address>";
            
        $template->assign( 'returnContent', 'subject' );
        $subject = $template->fetch( $emailTemplate );

        foreach ( $contacts as  $cid => $info ) {
            $template->assign( 'contact', $info );
            $template->assign( 'returnContent', 'textMessage' );
            $message = $template->fetch( $emailTemplate );
            
            $displayName = $info['name'];
            $email       = $info['email'];
            
            $result[] = CRM_Utils_Mail::send( $receiptFrom,
                                              $displayName,
                                              $email,
                                              $subject,
                                              $message
                                              );
        }
        return $result;
    }

    /**
     * Retrieve count of activities having a particular type, and
     * associated with a particular case.
     *
     * @param int    $caseId          ID of the case
     * @param int    $activityTypeId  ID of the activity type
     * 
     * @return array
     * 
     * @access public
     * 
     */
    static function getCaseActivityCount( $caseId, $activityTypeId ) 
    {
        $queryParam = array( 1 => array( $caseId, 'Integer' ),
                             2 => array( $activityTypeId, 'Integer' ) );
        $query = "SELECT count(ca.id) as countact 
FROM civicrm_activity ca
INNER JOIN civicrm_case_activity cca ON ca.id = cca.activity_id 
WHERE ca.activity_type_id = %2 AND cca.case_id = %1";
        
        $dao = CRM_Core_DAO::executeQuery($query, $queryParam);
        if ( $dao->fetch() ) {
            return $dao->countact;
        }
        
        return false;
    }
    
    
    /* * 
     *
     * Retrieve the list of cases given specific query and 
     * result fields.
     *
     * @param array  $resultFields the list of return properties
     * @param string $query the query
     * 
     * @return array
     * @access private
     * 
     */
     private static function _getCasesList( $resultFields, $query ) {

        $queryParams = array();
        $result = CRM_Core_DAO::executeQuery( $query,$queryParams );

        // we're going to use the usual actions, so doesn't make sense to duplicate definitions
        require_once( 'CRM/Case/Selector/Search.php');
        $actions = CRM_Case_Selector_Search::links();

        $filter = array();

        require_once( 'CRM/Contact/BAO/Contact/Utils.php' );

        $casesList = array();
        while ( $result->fetch() ) {
            foreach( $resultFields as $donCare => $field ) {
                $casesList[$result->case_id][$field] = $result->$field;
                if( $field = 'contact_type' ) {
                    $casesList[$result->case_id]['contact_type_icon'] = CRM_Contact_BAO_Contact_Utils::getImage( $result->contact_type );
                    $casesList[$result->case_id]['action'] = CRM_Core_Action::formLink( $actions, $mask,
                                                                                        array( 'id'  => $result->case_id,
                                                                                        'cid' => $result->contact_id,
                                                                                        'cxt' => 'dashboard' ) );
                    
                    
                    
                }
            }
        }
        return $casesList;        
        
     }    
    /**
     * Create an activity for a case via email
     * 
     * @param int    $file   email sent       
     *       
     * @return $activity object of newly creted activity via email
     * 
     * @access public
     * 
     */
    static function recordActivityViaEmail( $file ) 
    {
        if ( ! file_exists( $file ) ||
             ! is_readable( $file ) ) {
            return CRM_Core_Error::fatal( ts( 'File %1 does not exist or is not readable',
                                              array( 1 => $file ) ) );
        }
        
        require_once 'CRM/Utils/Mail/Incoming.php';
        $result = CRM_Utils_Mail_Incoming::parse( $file );
        if ( $result['is_error'] ) {
            return $result;
        }

        foreach( $result['to'] as $to ) {
            $caseId = null;

            $emailPattern = '/^([A-Z0-9._%+-]+)\+([\d]+)@[A-Z0-9.-]+\.[A-Z]{2,4}$/i';
            $replacement  = preg_replace ($emailPattern, '$2', $to['email']); 

            if ( $replacement !== $to['email'] ) {
                $caseId = $replacement;
            } else {
                continue;
            }

            $contactDetails = self::getRelatedContacts( $caseId );

            if ( CRM_Utils_Array::value( $result['from']['id'], $contactDetails ) ) {
                $params['subject']            = $result['subject'];
                $params['activity_date_time'] = $result['date'];
                $params['details']            = $result['body'];
                $params['source_contact_id']  = $result['from']['id'];
            
                $details = CRM_Case_PseudoConstant::activityType( );
                $matches = array( );
                preg_match( '/activity[ ]*type[ ]*=[ ]*\"([a-zA-Z0-9_ ]+)\"/i', 
                            $result['body'], $matches );
 
                if ( !empty($matches) && isset($matches[1]) ) {
                    $activityType = trim($matches[1]);
                    if ( isset($details[$activityType]) ) {
                        $params['activity_type_id'] = $details[$activityType]['id'];
                    }
                } else {
                    $params['activity_type_id'] = CRM_Core_OptionGroup::getValue('activity_type', 
                                                                                 'Inbound Email', 
                                                                                 'name' );
                }

                // create activity
                require_once "CRM/Activity/BAO/Activity.php";
                $activity = CRM_Activity_BAO_Activity::create( $params );

                $caseParams = array( 'activity_id' => $activity->id,
                                     'case_id'     => $caseId   );
                CRM_Case_BAO_Case::processCaseActivity( $caseParams );
            }
        } 
    }

    /**
     * Function to retrive the scheduled activity type and date
     * 
     * @param  array  $cases  Array of contact and case id        
     *       
     * @return array  $activityInfo Array of scheduled activity type and date
     * 
     * @access public
     *
     * @static
     */
    static function getNextScheduledActivity( $cases ) {
        
        $caseID    = implode ( ',', $cases['case_id']);
        
        $contactID = implode ( ',', $cases['contact_id'] );
        $query = "
             SELECT
                  civicrm_case.id as case_id, 
                  civicrm_activity.due_date_time as case_scheduled_activity_date,
                  aov.label                      as case_scheduled_activity_type  
             FROM civicrm_case
                  INNER JOIN civicrm_case_activity
                        ON civicrm_case_activity.case_id = civicrm_case.id              
                  LEFT JOIN civicrm_case_contact ON civicrm_case.id = civicrm_case_contact.case_id
                  LEFT JOIN civicrm_contact ON civicrm_case_contact.contact_id = civicrm_contact.id  
                  LEFT JOIN civicrm_activity
                       ON ( civicrm_case_activity.activity_id = civicrm_activity.id
                            AND civicrm_activity.is_current_revision = 1
                            AND civicrm_activity.due_date_time <= DATE_ADD( NOW(), INTERVAL 14 DAY ) ) 
                                             
                  LEFT JOIN civicrm_option_group cog_actstatus ON cog_actstatus.name = 'activity_status'
                  LEFT JOIN civicrm_option_value cov_actstatus 
                       ON ( civicrm_activity.status_id = cov_actstatus.value
                            AND cog_actstatus.id = cov_actstatus.option_group_id ) 
                         LEFT JOIN civicrm_option_group aog  ON aog.name = 'activity_type'
                  LEFT JOIN civicrm_option_value aov
                        ON ( civicrm_activity.activity_type_id = aov.value
                             AND aog.id = aov.option_group_id )  
                 
             WHERE cov_actstatus.name = 'Scheduled'  
                   AND civicrm_case_contact.contact_id IN( {$contactID} )
                   AND civicrm_activity.is_deleted = {$cases['case_deleted']}
                   AND civicrm_case.id IN( {$caseID})
                   AND civicrm_case.is_deleted = {$cases['case_deleted']} 
 
             GROUP BY civicrm_case.id
             ORDER BY case_scheduled_activity_date ASC"; 
        
        $res = CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );

        $activityInfo = array();
        while( $res->fetch() ) {
            $activityInfo[$res->case_id]['date']    = $res->case_scheduled_activity_date;
            $activityInfo[$res->case_id]['type']    = $res->case_scheduled_activity_type;
        } 
        return $activityInfo;
    }
    /**
     * combine all the exportable fields from the lower levels object
     *     
     * @return array array of exportable Fields
     * @access public
     */
    function &exportableFields( ) 
    {
        if ( ! self::$_exportableFields ) {
            if ( ! self::$_exportableFields ) {
                self::$_exportableFields = array();
            }
            require_once 'CRM/Case/DAO/Case.php';
            require_once 'CRM/Activity/DAO/Activity.php';
            
            $fields['case'] = CRM_Case_DAO_Case::import( );

            $fields['case']['case_start_date'] = array( 'title' => ts('Case Start Date') );
            $fields['case']['case_end_date']   = array( 'title' => ts('Case End Date') );
            //set title to activity fields
            $fields['activity'] = array( 
                                        'case_subject'                 => array( 'title' => ts('Activity Subject') ),
                                        'case_source_contact_id'       => array( 'title' => ts('Activity Reporter') ),
                                        'case_recent_activity_date'    => array( 'title' => ts('Activity Actual Date') ),
                                        'case_scheduled_activity_date' => array( 'title' => ts('Activity Due Date') ),
                                        'case_recent_activity_type'    => array( 'title' => ts('Activity Type') ),
                                        'case_activity_status_id'      => array( 'title' => ts('Activity Status') ),
                                        'case_activity_duration'       => array( 'title' => ts('Activity Duration') ),
                                        'case_activity_medium_id'      => array( 'title' => ts('Activity Medium') ),
                                        'case_activity_details'        => array( 'title' => ts('Activity Details') ),
                                        'case_activity_is_auto'        => array( 'title' => ts('Activity Auto-generated?') )
                                        );
            
            //$fields = array_merge($impFields, $expFieldsActivity );
            
            self::$_exportableFields = $fields;
        }
        return self::$_exportableFields;
    }

    /**                                                           
     * Restore the record that are associated with this case 
     * 
     * @param  int  $caseId id of the case to restore
     * 
     * @return true if success.
     * @access public 
     * @static 
     */ 
    static function restoreCase( $caseId ) 
    {
        //restore activities
        $activities = self::getCaseActivity( $caseId, $params = array(), null, true );
        if ( $activities ) {
            require_once"CRM/Activity/BAO/Activity.php";
            foreach( $activities as $value ) {
                CRM_Activity_BAO_Activity::restoreActivity( $value );
            }
        }  
        //restore case
        require_once 'CRM/Case/DAO/Case.php';
        $case     = & new CRM_Case_DAO_Case( );
        $case->id = $caseId; 
        $case->is_deleted = 0;
        $case->save( );
        return true;
    }
}

   
