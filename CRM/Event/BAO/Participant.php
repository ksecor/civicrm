<?php
  /*
   +--------------------------------------------------------------------+
   | CiviCRM version 1.7                                                |
   +--------------------------------------------------------------------+
   | Copyright CiviCRM LLC (c) 2004-2007                                  |
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
   | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
   | about the Affero General Public License or the licensing  of       |
   | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
   | http://www.civicrm.org/licensing/                                 |
   +--------------------------------------------------------------------+
  */

  /**
   *
   *
   * @package CRM
   * @author Donald A. Lobo <lobo@civicrm.org>
   * @copyright CiviCRM LLC (c) 2004-2007
   * $Id$
   *
   */

require_once 'CRM/Event/DAO/Participant.php';

class CRM_Event_BAO_Participant extends CRM_Event_DAO_Participant
{
    /**
     * static field for all the membership information that we can potentially import
     *
     * @var array
     * @static
     */
    static $_importableFields = null;

    function __construct()
    {
        parent::__construct();
    }
    
   
    /**
     * Given the list of params in the params array, fetch the object
     * and store the values in the values array
     *
     * @param array $params input parameters to find object
     * @param array $values output values of the object
     *
     * @return CRM_Event_BAO_Participant|null the found object or null
     * @access public
     * @static
     */
    static function &getValues( &$params, &$values, &$ids ) 
    {
        $participant =& new CRM_Event_BAO_Participant( );
        $participant->copyValues( $params );
        
        if ( $participant->find(true) ) {
            CRM_Core_DAO::storeValues( $participant, $values );
            
        }
    }
    
    /**
     * takes an associative array and creates a participant object
     *
     * the function extract all the params it needs to initialize the create a
     * participant object. the params array could contain additional unused name/value
     * pairs
     *
     * @param array  $params (reference ) an assoc array of name/value pairs
     * @param array $ids    the array that holds all the db ids
     *
     * @return object CRM_Event_BAO_Participant object
     * @access public
     * @static
     */
    static function add(&$params, &$ids)
    {
        require_once 'CRM/Utils/Hook.php';
        
        if ( CRM_Utils_Array::value( 'participant', $ids ) ) {
            CRM_Utils_Hook::pre( 'edit', 'Participant', $ids['participant'], $params );
        } else {
            CRM_Utils_Hook::pre( 'create', 'Participant', null, $params ); 
        }
        
        // converting dates to mysql format
        $params['register_date']  = CRM_Utils_Date::isoToMysql($params['register_date']);
        
        $participantBAO =& new CRM_Event_BAO_Participant();
        $participantBAO->copyValues($params);
        $participantBAO->id = CRM_Utils_Array::value( 'participant', $ids );
        
        $result = $participantBAO->save();
        
        $session = & CRM_Core_Session::singleton();
        
        if ( CRM_Utils_Array::value( 'participant', $ids ) ) {
            CRM_Utils_Hook::post( 'edit', 'Participant', $participantBAO->id, $participantBAO );
        } else {
            CRM_Utils_Hook::post( 'create', 'Participant', $participantBAO->id, $participantBAO );
        }
        
        return $result;
    }
 
    /**
     * takes an associative array of modified participant object
     *
     * the function sets the activity history of the modified partcipant records
     *
     * @access public
     * @static
     */
    static function setActivityHistory( $participant ) 
    {
        $activitySummary = CRM_Event_BAO_Event::getEvents(true,$participant->event_id);
        $date = date( 'YmdHis' );
        $def['role_id'] = $participant->role_id;
        self::lookupValue($def, 'role', CRM_Event_PseudoConstant::participantRole(), false);
        require_once "api/History.php";
        $activityHistory = array('entity_table'     => 'civicrm_contact',
                                 'entity_id'        => $participant->contact_id,
                                 'activity_type'    => 'Event Registration',
                                 'module'           => 'CiviEvent',
                                 'callback'         => 'CRM_Event_BAO_Participant::showActivityDetails',
                                 'activity_id'      => $participant->id,
                                 'activity_summary' => $activitySummary[$participant->event_id].' ( '.$def['role'].' ) ',
                                 'activity_date'    => $date
                                 
                                 );

        if ( is_a( crm_create_activity_history($activityHistory), 'CRM_Core_Error' ) ) {
            return false;
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
        
        if ( $contactId ) {
            //            return CRM_Utils_System::url('civicrm/contact/view/activity', "cid=$contactId&action=view&id=$activityId&status=true&history=1&selectedChild=event"); 
            return CRM_Utils_System::url('civicrm/contact/view/participant', "reset=1&id=$id&cid=$contactId&action=view&context=participant&selectedChild=event&history=1"); 
        } else { 
            return CRM_Utils_System::url('civicrm' ); 
        } 
    }
    
    /**
     * takes an associative array and creates a participant object
     *
     * @param array $params (reference ) an assoc array of name/value pairs
     * @param array $ids    the array that holds all the db ids
     *
     * @return object CRM_Event_BAO_Participant object 
     * @access public
     * @static
     */

    static function &create(&$params, &$ids) 
    {
        require_once 'CRM/Utils/Date.php';

        CRM_Core_DAO::transaction('BEGIN');
        
        $participant = self::add($params, $ids);
        $groupTree =& CRM_Core_BAO_CustomGroup::getTree("Participant", $ids['id'], 0, $params['role_id']);
        
        CRM_Core_BAO_CustomGroup::postProcess( $groupTree, $params );
        CRM_Core_BAO_CustomGroup::updateCustomData($groupTree, "Participant", $participant->id); 

        if ( is_a( $participant, 'CRM_Core_Error') ) {
            CRM_Core_DAO::transaction( 'ROLLBACK' );
            return $participant;
        }

        $params['participant_id'] = $participant->id;
        
        CRM_Core_DAO::transaction('COMMIT');
        
        return $participant;
    }
   
    /**
     * combine all the importable fields from the lower levels object
     *
     * @return array array of importable Fields
     * @access public
     */
    function &importableFields( $contacType = 'Individual' ) 
    {
        if ( ! self::$_importableFields ) {
            if ( ! self::$_importableFields ) {
                self::$_importableFields = array();
            }
            if (!$status) {
                $fields = array( '' => array( 'title' => ts('- do not import -') ) );
            } else {
                $fields = array( '' => array( 'title' => ts('- Participant Fields -') ) );
            }
            
            $tmpFields     = CRM_Event_DAO_Participant::import( );
            
            $contactFields = CRM_Contact_BAO_Contact::importableFields( $contacType, null );
            if ($contacType == 'Individual') {
                require_once 'CRM/Core/DAO/DupeMatch.php';
                $dao = & new CRM_Core_DAO_DupeMatch();
                $dao->find(true);
                $fieldsArray = explode('AND',$dao->rule);
            } elseif ($contacType == 'Household') {
                $fieldsArray = array('household_name', 'email');
            } elseif ($contacType == 'Organization') {
                $fieldsArray = array('organization_name', 'email');
            }
            $tmpConatctField = array();
            if( is_array($fieldsArray) ) {
                foreach ( $fieldsArray as $value) {
                    $tmpConatctField[trim($value)] = $contactFields[trim($value)];
                    $tmpConatctField[trim($value)]['title'] = $tmpConatctField[trim($value)]['title']." (match to contact)" ;
                }
            }
            $fields = array_merge($fields, $tmpConatctField);
            $fields = array_merge($fields, $tmpFields);
            //$fields = array_merge($fields, $optionFields);
            
            $fields = array_merge($fields, CRM_Core_BAO_CustomField::getFieldsForImport('Participant'));
            self::$_importableFields = $fields;
        }
        return self::$_importableFields;
    }

    /**
     * function to get the event name/sort name for a particular participation / participant
     *
     * @param  int    $participantId  id of the participant

     * @return array $name associated array with sort_name and event title
     * @static
     * @access public
     */
    static function participantDetails( $participantId ) 
    {
        $query = "
SELECT civicrm_contact.sort_name as name, civicrm_event.title as title
FROM   civicrm_participant 
   LEFT JOIN civicrm_event   ON (civicrm_participant.event_id = civicrm_event.id)
   LEFT JOIN civicrm_contact ON (civicrm_participant.contact_id = civicrm_contact.id)
WHERE  civicrm_participant.id = {$participantId}
";
        $dao =& CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
        
        $details = array( );
        while ( $dao->fetch() ) {
            $details['name' ] = $dao->name;
            $details['title'] = $dao->title;
        }
        
        return $details;
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
        require_once 'CRM/Event/PseudoConstant.php';

        self::lookupValue($defaults, 'event', CRM_Event_PseudoConstant::event(), $reverse);
        self::lookupValue($defaults, 'status', CRM_Event_PseudoConstant::participantStatus(), $reverse);
        self::lookupValue($defaults, 'role', CRM_Event_PseudoConstant::participantRole(), $reverse);
       
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
     * Delete the record that are associated with this participation
     * @param  int  $id id of the participation to delete                                                                           
     * 
     * @return boolean  true if deleted, false otherwise
     * @access public 
     * @static 
     */ 
    static function deleteParticipant( $id ) 
    {    
        require_once 'CRM/Event/DAO/ParticipantPayment.php';
        $participantPayment = & new CRM_Event_DAO_ParticipantPayment( );
        $participantPayment->entity_table   = 'civicrm_contribution';
        $participantPayment->participant_id = $id;

        if ( $participantPayment->find( true ) ) {
            self::deleteParticipantSubobjects( $participantPayment->payment_entity_id );
            $participantPayment->delete( ); 
        }    
        
        require_once 'CRM/Event/DAO/Participant.php';
        $participant        = & new CRM_Event_DAO_Participant( );
        $participant->id    = $id;
        $participant->delete( );
        
        return true;
    }
    
    static function deleteParticipantSubobjects( $contribId ) 
    {
        require_once 'CRM/Contribute/BAO/Contribution.php';
        CRM_Contribute_BAO_Contribution::deleteContribution( $contribId );
        return;
    }

}
?>