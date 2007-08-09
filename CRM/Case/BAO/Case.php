<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.8                                                |
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
    static function add(&$params, &$ids) 
    {
        $caseDAO =& new CRM_Case_DAO_Case();
        
        $caseDAO->copyValues($params);
        $caseDAO->id = CRM_Utils_Array::value( 'case', $ids );
        $result = $caseDAO->save();
        
        return $result;
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
            $ids['domain' ] = $case->domain_id;

            CRM_Core_DAO::storeValues( $case, $values );
            
            return $cases;
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
    static function &create(&$params, &$ids) 
    {
        CRM_Core_DAO::transaction('BEGIN');
        
        $case = self::add($params, $ids);
        
        if ( is_a( $case, 'CRM_Core_Error') ) {
            CRM_Core_DAO::transaction( 'ROLLBACK' );
            return $case;
        }
        $session = & CRM_Core_Session::singleton();
        $id = $session->get('userID');
        if ( !$id ) {
            $id = $params['contact_id'];
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
        CRM_Core_DAO::transaction('COMMIT');
        
        return $case;

    }

    /**
     * Get the values for pseudoconstants for name->value and reverse.
     *
     * @param array   $defaults (reference) the default values, some of which need to be resolved.
     * @param boolean $reverse  true if we want to resolve the values in the reverse direction (value -> name)
     *
     * @return void
     * @access public
     * @static
     */
    static function resolveDefaults(&$defaults, $reverse = false)
    {
    
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
     * To fetch the contact id when display name is given
     * 
     * @param  sort name of the contact
     * @return id id of the corresponding display name
     *
     */ 
    static function retrieveCid( $params ) 
    {
        require_once 'CRM/Contact/DAO/Contact.php';
        $cid = new CRM_Contact_DAO_Contact();
        $cid->sort_name = $params;
        $cid->find(true);
        return $cid->id;
    }

    
    /**
     * takes an associative array and
     *
     * @param array $params (reference ) an assoc array of name/value pairs
     * @param array $ids    the array that holds all the db ids
     *
     * @access public
     * @static
     */
    static function &createCaseActivity(&$params , $ids ) 
    {
        if($params['subject']){ 
            require_once 'CRM/Case/DAO/Case.php';
            $caseDAO =& new CRM_Case_DAO_Case();
            $caseDAO->subject = $params['subject'];
            $caseDAO->find(true);
            $params['case_id'] = $caseDAO->id;
            require_once 'CRM/Case/DAO/CaseActivity.php';
            $caseActivityDAO =& new CRM_Case_DAO_CaseActivity();
            $caseActivityDAO->copyValues($params);
            $caseActivityDAO->id = CRM_Utils_Array::value( 'cid', $ids );
            $result = $caseActivityDAO->save();
        }
        if (!$params['subject'] && $ids['cid']){
            self::deleteCaseActivity( $ids['cid'] );
        }
    } 
    /*
     * @param Integer $activityType activity type id
     * @param Integer $id activity id
     * @return Integer case_id of CRM_Case_DAO_CaseActivity
     * @access public
     * @static
     */
     
    static function &getCaseID($activityType, $id)
    {
         if ( $activityType == 1) {
            $entityTable = "civicrm_meeting";
        } else if($activityType == 2) {
            $entityTable = "civicrm_phonecall";
        } else {
            $entityTable = "civicrm_activity";
        }
         
        require_once 'CRM/Case/DAO/CaseActivity.php';
        $caseActivity =  new CRM_Case_DAO_CaseActivity();
        $caseActivity->activity_entity_table = $entityTable;
        $caseActivity->activity_entity_id = $id;
        if ($caseActivity->find(true)){
            return $caseActivity->case_id;
        }
        return null;
         
    }


   /**                                                           
     * Delete the record that are associated with this case 
     * record are deleted from case 
     * @param  int  $id id of the case to delete
     * 
     * @return boolean  true if deleted, false otherwise
     * @access public 
     * @static 
     */ 
    static function deleteCase( $id ) 
    {
        require_once 'CRM/Case/DAO/Case.php';
        $case     = & new CRM_Case_DAO_Case( );
        $case->id = $id; 
        
        $case->find();
        while ($case->fetch() ) {
            return $case->delete();
        }
        return false;
    }

    /**                                                           
     * Delete the record that are associated with this case Activity 
     * record are deleted from case activity
     * @param  int  $id id of the caseActivity to delete
     * 
     * @return boolean  true if deleted, false otherwise
     * @access public 
     * @static 
     */ 
    static function deleteCaseActivity( $id ) 
    {
        require_once 'CRM/Case/DAO/CaseActivity.php';
        $caseActivity     = & new CRM_Case_DAO_CaseActivity( );
        $caseActivity->id = $id; 
        $caseActivity->find();
        while ($caseActivity->fetch() ) {
            return $caseActivity->delete();
        }
        return false;
    }
}

?>
