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

/**
 * This class contains the funtions for Case Management
 *
 */
class CRM_Case_BAO_Case extends CRM_Case_DAO_Case
{
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
    static function deleteCase( $caseId ) 
    {
        require_once 'CRM/Core/Transaction.php';
        $transaction = new CRM_Core_Transaction( );

        require_once 'CRM/Case/DAO/Case.php';
        $case     = & new CRM_Case_DAO_Case( );
        $case->id = $caseId; 
        $case->delete( );

        $transaction->commit( );
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
     * function to get the amount details date wise.
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
            " where case_type_id like '%" . $this->VALUE_SEPERATOR . $typeId . $this->VALUE_SEPERATOR . "%'" .
            " group by status_id";
            $res = CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );

            // make sure all the statuses are present, since we're not calculating 0 values
            foreach( $caseStatuses as $key => $dontCare ) {
                $q[$key] = '0';
            }
            
            while( $res->fetch() ) {
                $q[$res->status_id] = array( 'case_count' => $res->case_count,
                                             'url'     => CRM_Utils_System::url( 'civicrm/case','reset=1')
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
    static function getCaseRoles( $contactID, $caseID )
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
    static function getCaseActivity( $caseID, $params, $contactID )
    {
        $select = '
SELECT ca.id as id, ca.activity_type_id as type, c.sort_name as reporter, ca.due_date_time as due_date, ca.activity_date_time actual_date, ca.status_id as status, cc2.label as category, ca.subject as subject ';

        $from  = 'FROM civicrm_case_activity cca, civicrm_activity ca, civicrm_contact c, civicrm_category cc1, civicrm_category cc2 ';

        $where = 'WHERE ca.id = cca.activity_id 
AND ca.activity_type_id = cc1.id
AND cc1.parent_id = cc2.id
AND ca.source_contact_id = c.id AND cca.case_id= %1';

        if ( $params['category_0'] ) {
            $where .= " AND cc1.parent_id = ".CRM_Utils_Type::escape( $params['category_0'], 'Integer' );
        }

        if ( $params['category_1'] ) {
            $where .= " AND ca.activity_type_id = ".CRM_Utils_Type::escape( $params['category_1'], 'Integer' );
        }

        if ( $params['reporter_id'] ) {
            $where .= " AND ca.source_contact_id = ".CRM_Utils_Type::escape( $params['reporter_id'], 'Integer' );
        }

        if ( $params['status_id'] ) {
            $where .= " AND ca.status_id = ".CRM_Utils_Type::escape( $params['status_id'], 'Integer' );
        }

        $fromDueDate = CRM_Utils_Type::escape( $params['activity_date_low'], 'Date' );
        $toDueDate   = CRM_Utils_Type::escape( $params['activity_date_high'], 'Date' );

        if ( $params['date_range'] == 1 ) {
            $where .= " AND ( ca.due_date_time >= '{$fromDueDate}' AND ca.due_date_time <= '{$toDueDate}' ) ";
        } else if ( $params['date_range'] == 2 ) {
            $where .= " AND ( ca.activity_date_time >= '{$fromDueDate}' AND ca.activity_date_time <= '{$toDueDate}' ) ";
        } else {
            $fromDueDate = date( 'Ymd', mktime(0, 0, 0, date("m"), date("d")-14, date("Y")) );
            $toDueDate   = date( 'Ymd', mktime(0, 0, 0, date("m"), date("d")+14, date("Y")) );

            $where .= " AND ( ca.due_date_time >= '{$fromDueDate}' AND ca.due_date_time <= '{$toDueDate}' ) ";
        }

        $sortname  = $params['sortname'];
        $sortorder = $params['sortorder'];
        
        if (!$sortname) $sortname = 'due_date_time';
        if (!$sortorder) $sortorder = 'desc';

        $orderBy = " ORDER BY $sortname $sortorder";

        $page = $params['page'];
        $rp   = $params['rp'];
        
        if (!$page) $page = 1;
        if (!$rp) $rp = 10;
        
        $start = (($page-1) * $rp);
        
        $limit = " LIMIT $start, $rp";

        $query = $select . $from . $where . $orderBy . $limit;
        
        $params = array( 1 => array( $caseID, 'Integer' ) );
        
        $dao =& CRM_Core_DAO::executeQuery( $query, $params );
        
        require_once "CRM/Case/PseudoConstant.php";
        $childCategories  = CRM_Case_PseudoConstant::category( false );

        require_once "CRM/Utils/Date.php";
        require_once "CRM/Core/PseudoConstant.php";
        $activityStatus = CRM_Core_PseudoConstant::activityStatus( );

        $values = array( );
        $url = CRM_Utils_System::url( "civicrm/case/activity?reset=1&cid={$contactID}&id={$caseID}",
                                      null, false, null, false ); 
        
        $editUrl   = "{$url}&action=add";
        $deleteUrl = "{$url}&action=delete";
              
        while ( $dao->fetch( ) ) {
            $values[$dao->id]['id']          = $dao->id;
            $values[$dao->id]['category']    = $dao->category;
            $values[$dao->id]['type']        = $childCategories[$dao->type];
            $values[$dao->id]['reporter']    = $dao->reporter;
            $values[$dao->id]['due_date']    = CRM_Utils_Date::customFormat( $dao->due_date );
            $values[$dao->id]['actual_date'] = CRM_Utils_Date::customFormat( $dao->actual_date );
            $values[$dao->id]['status']      = $activityStatus[$dao->status];
            $values[$dao->id]['subject']     = "<a href='javascript:viewActivity( {$dao->id} );'>{$dao->subject}</a>";
            
            $additionalUrl = "&atype={$dao->type}&aid={$dao->id}";
            
            $values[$dao->id]['links']       = "<a href='" .$editUrl.$additionalUrl."'>". ts('Edit') . "</a> | <a href='" .$deleteUrl.$additionalUrl."'>". ts('Delete') . "</a>";
        }
        
        $dao->free( );
        return $values;
    }

    static function getFileForActivityTypeId( $activityTypeId ) 
    {
        $actName = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_Category', $activityTypeId, 'name' );

        if ( $actName ) {
            $caseAction = trim(str_replace(' ', '', $actName));
        } else {
            return false;
        }

        global $civicrm_root;
        if ( !file_exists(rtrim($civicrm_root, '/') . "/CRM/Case/Form/Activity/{$caseAction}.php") ) {
            return false;
        }

        return $caseAction;
    }

}

   