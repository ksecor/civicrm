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
   *
   * @package CRM
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
    static function getValues( &$params, &$values, &$ids ) 
    {
        $participant =& new CRM_Event_BAO_Participant( );
        $participant->copyValues( $params );
        $participant->find();
        $participants = array();
        while ( $participant->fetch() ) {
            $ids['participant'] = $participant->id;
            CRM_Core_DAO::storeValues( $participant, $values[$participant->id] );
            $participants[$participant->id] = $participant;
        }       
        return $participants;
    }

    /**
     * Given a participant id, return contribution/fee line items
     *
     * @param $id int|array participant id
     *
     * @return array line items
     */
    static function getLineItems( $id ) {
        $lineItems = array();
        require_once 'CRM/Core/DAO.php';
        $query = "SELECT li.label, li.qty, li.unit_price, li.line_total";
        $query .= " FROM civicrm_participant AS p,";
        $query .= " civicrm_participant_payment AS pp,";
        $query .= " civicrm_line_item AS li";
        $query .= " WHERE p.id=%1 AND pp.participant_id=p.id";
        $query .= " AND li.entity_id=pp.payment_entity_id";
        $query .= " AND li.entity_table=pp.payment_entity_table";
        $params = array( 1 => array( $id, 'Integer' ) );
        $dao = CRM_Core_DAO::executeQuery( $query, $params );
        while ( $dao->fetch() ) {
            $lineItems[] = array(
                'label' => $dao->label,
                'qty' => $dao->qty,
                'unit_price' => $dao->unit_price,
                'line_total' => $dao->line_total
            );
        }
        return $lineItems;
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
        
        if ( CRM_Utils_Array::value( 'participant', $ids ) ) {
            $status = CRM_Core_DAO::getFieldValue( 'CRM_Event_DAO_Participant', $ids['participant'], 'status_id' );
        }
        
        $participant = self::add($params, $ids);
        
        if ( is_a( $participant, 'CRM_Core_Error') ) {
            CRM_Core_DAO::transaction( 'ROLLBACK' );
            return $participant;
        }
        
        if ( ( ! CRM_Utils_Array::value( 'participant', $ids ) ) ||
             ( $params['status_id'] != $status ) ) {
            self::setActivityHistory($participant);
        }
        
        $session = & CRM_Core_Session::singleton();
        $id = $session->get('userID');
        if ( !$id ) {
            $id = $params['contact_id'];
        }
        
        // add custom field values
        if (CRM_Utils_Array::value('custom', $params)) {
            foreach ($params['custom'] as $customValue) {
                $cvParams = array(
                                  'entity_table'    => 'civicrm_participant',
                                  'entity_id'       => $participant->id,
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
        
        if ( CRM_Utils_Array::value('note', $params) ) {
            require_once 'CRM/Core/BAO/Note.php';
            $noteParams = array(
                                'entity_table'  => 'civicrm_participant',
                                'note'          => $params['note'],
                                'entity_id'     => $participant->id,
                                'contact_id'    => $id,
                                'modified_date' => date('Ymd')
                                );
            
            CRM_Core_BAO_Note::add( $noteParams, $ids['note'] );
        }
        // Log the information on successful add/edit of Participant
        // data.
        require_once 'CRM/Core/BAO/Log.php';
        require_once 'CRM/Event/PseudoConstant.php' ;
        $logParams = array(
                        'entity_table'  => 'civicrm_participant',
                        'entity_id'     => $participant->id,
                        'data'          => CRM_Event_PseudoConstant::participantStatus($participant->status_id),
                        'modified_id'   => $id,
                        'modified_date' => date('Ymd')
                        );
        
        CRM_Core_BAO_Log::add( $logParams );
        
        $params['participant_id'] = $participant->id;
        
        CRM_Core_DAO::transaction('COMMIT');
        
        return $participant;
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
        require_once "CRM/Event/BAO/Event.php";
        $event = CRM_Event_BAO_Event::getEvents(true,$participant->event_id);
        $date = date( 'YmdHis' );
        require_once "CRM/Event/PseudoConstant.php";
        $roles  = CRM_Event_PseudoConstant::participantRole( );
        $status = CRM_Event_PseudoConstant::participantStatus( );

        $activitySummary = $event[$participant->event_id].' - '.$roles[$participant->role_id].' - ' .$status[$participant->status_id];
        $activityHistory = array('entity_table'     => 'civicrm_contact',
                                 'entity_id'        => $participant->contact_id,
                                 'activity_type'    => 'Event Registration',
                                 'module'           => 'CiviEvent',
                                 'callback'         => 'CRM_Event_BAO_Participant::showActivityDetails',
                                 'activity_id'      => $participant->id,
                                 'activity_summary' => $activitySummary,
                                 'activity_date'    => $date,
                                 'is_test'          => $participant->is_test
                                 );

        require_once "api/History.php";
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
            return CRM_Utils_System::url('civicrm/contact/view/participant', "reset=1&id=$id&cid=$contactId&hid={$activityHistoryId}&action=view&context=participant&selectedChild=event&history=1"); 
        } else { 
            return CRM_Utils_System::url('civicrm' ); 
        } 
    }
    
    /**
     * check whether the event is 
     * full for participation
     *
     * @param int $eventId
     *
     * @static
     * @access public
     */
    static function eventFull( $eventId )
    {
        $query = "SELECT   count(civicrm_participant.id) as total_participants,
                           civicrm_event.max_participants as max_participants,
                           civicrm_event.event_full_text as event_full_text  
                  FROM     civicrm_participant, civicrm_event 
                  WHERE    civicrm_participant.event_id = civicrm_event.id
                     AND   civicrm_participant.status_id!=4 
                     AND   civicrm_participant.is_test=0 
                     AND   civicrm_participant.event_id={$eventId} 
                  GROUP BY civicrm_participant.event_id";
        
        $dao =& CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
        

        while ( $dao->fetch( ) ) {
            if( $dao->max_participants == NULL ) {
                return false;
            }
            if( $dao->total_participants >= $dao->max_participants ) {
                if( $dao->event_full_text ) {
                    return $dao->event_full_text;
                } else {
                    return ts( "This event is full !!!" );
                }
            }
        }
        return false;
    }

    /**
     * return the last modified date for participation
     *
     * @param int $contactId      contact id
     * @param int $participantId  participant id
     *
     * @static
     * @access public
     */
    static function getModifiedDate( $contactId, $participantId )
    {
        $query = "SELECT   civicrm_log.id as id, civicrm_log.modified_date as modified_date
                  FROM     civicrm_log
                  WHERE    civicrm_log.entity_table='civicrm_participant' 
                     AND   civicrm_log.entity_id={$participantId} 
                     AND   civicrm_log.modified_id={$contactId} 
                  ORDER BY id DESC LIMIT 1";
        
        $dao =& CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
        if ( $dao->fetch( ) ) {
             return $dao->modified_date;
        }
        return false;
    }
        
    /**
     * combine all the importable fields from the lower levels object
     *
     * @return array array of importable Fields
     * @access public
     */
    function &importableFields( $contactType = 'Individual', $status = true, $onlyParticipant = false ) 
    {
        if ( ! self::$_importableFields ) {
            if ( ! self::$_importableFields ) {
                self::$_importableFields = array();
            }

            if ( !$onlyParticipant ) {
                if ( !$status ) {
                    $fields = array( '' => array( 'title' => ts('- do not import -') ) );
                } else {
                    $fields = array( '' => array( 'title' => ts('- Participant Fields -') ) );
                }
            } else {
                $fields = array( );
            }
            
            require_once 'CRM/Core/DAO/Note.php';
            $tmpFields     = CRM_Event_DAO_Participant::import( );
            $note          = CRM_Core_DAO_Note::import( );
            
            $tmpConatctField = array( );
            if ( !$onlyParticipant ) {
                $contactFields = CRM_Contact_BAO_Contact::importableFields( $contactType, null );
                if ($contactType == 'Individual') {
                    require_once 'CRM/Core/DAO/DupeMatch.php';
                    $dao = & new CRM_Core_DAO_DupeMatch();
                    $dao->find(true);
                    $fieldsArray = explode('AND',$dao->rule);
                } elseif ($contactType == 'Household') {
                    $fieldsArray = array('household_name', 'email');
                } elseif ($contactType == 'Organization') {
                    $fieldsArray = array('organization_name', 'email');
                }

                if( is_array($fieldsArray) ) {
                    foreach ( $fieldsArray as $value) {
                        $tmpConatctField[trim($value)] = CRM_Utils_Array::value(trim($value),$contactFields);
                        if (!$status) {
                            $title = $tmpConatctField[trim($value)]['title']." (match to contact)" ;
                        } else {
                            $title = $tmpConatctField[trim($value)]['title'];
                        }
                        
                        $tmpConatctField[trim($value)]['title'] = $title;
                    }
                }
            }
            
            $fields = array_merge($fields, $tmpConatctField);
            $fields = array_merge($fields, $tmpFields);
            $fields = array_merge($fields, $note);
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
     * Delete the Participation records for this contact.
     * 
     * @param  int  $cid   Contact Id                                                              
     * 
     * @access public 
     * @static 
     */ 
    static function deleteContact( $cid ) 
    {
        $participants = array();
        
        $participantDAO = new CRM_Event_BAO_Participant();
        $participantDAO->contact_id = $cid;
        $participantDAO->find();
        while ( $participantDAO->fetch( ) ) {
            $participants[] = $participantDAO->id;
        }
        
        foreach( $participants as $id) {
            self::deleteParticipant( $id );
        }
    }
    
    /**                          
     * Delete the record that are associated with this participation
     * 
     * @param  int  $id id of the participation to delete                                                                           
     * 
     * @return Int      Count of no of participant deleted.
     * @access public 
     * @static 
     */ 
    static function deleteParticipant( $id ) 
    {
        //check dependencies
        
        $dependencies = array(
                        'CRM_Core_DAO_Log'                 => array(
                                                      'entity_id'    => $id,
                                                      'entity_table' => 'civicrm_participant'
                                                      ),
                        'CRM_Core_DAO_Note'                => array(
                                                      'entity_id'    => $id,
                                                      'entity_table' => 'civicrm_participant'
                                                      ),
                        'CRM_Core_DAO_CustomValue'         => array(
                                                      'entity_id'    => $id,
                                                      'entity_table' => 'civicrm_participant'
                                                      ),
                        'CRM_Event_BAO_ParticipantPayment' => array(
                                                      'deleteParticipantPayment' => array (
                                                              'params' => array (
                                                                          'entity_table'   => 'civicrm_contribution',
                                                                          'participant_id' => $id
                                                                          ) 
                                                              ) 
                                                      )
                        );
        
        foreach ( $dependencies as $daoName => $values ) {
            require_once ( str_replace('_', DIRECTORY_SEPARATOR, $daoName) . '.php' );
            eval('$dao = new ' . $daoName . '( );');
            
            $externalMethodCall = true;
            foreach ( $values as $field => $value ) {
                if ( !is_array( $value ) ) {
                    $dao->$field        = $value;
                    $externalMethodCall = false;
                } else {
                    $methodName = $field;
                    $params     = $value;
                }
            }
            
            if ( ! $externalMethodCall ) {
                $dao->find( );
                
                while( $dao->fetch( ) ) {
                    $dao->delete( );
                    
                }
            } else {
                eval( $daoName . '::$methodName( $params["params"] );' );
            }
        }
      
        require_once 'CRM/Event/DAO/Participant.php';
        $participant        = & new CRM_Event_DAO_Participant( );
        $participant->id    = $id;
        
        if ( $participant->find( ) ) {
            return $participant->delete( );
        }
        
        return false;
    }
    
    static function deleteParticipantSubobjects( $contribId ) 
    {
        require_once 'CRM/Contribute/BAO/Contribution.php';
        CRM_Contribute_BAO_Contribution::deleteContribution( $contribId );
        return;
    }

}
?>
