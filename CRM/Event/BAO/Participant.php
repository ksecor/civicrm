<?php

  /*
   +--------------------------------------------------------------------+
   | CiviCRM version 2.2                                                |
   +--------------------------------------------------------------------+
   | Copyright CiviCRM LLC (c) 2004-2009                                |
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
   *
   * @package CRM
   * @copyright CiviCRM LLC (c) 2004-2009
   * $Id$
   *
   */

require_once 'CRM/Event/DAO/Participant.php';

class CRM_Event_BAO_Participant extends CRM_Event_DAO_Participant
{
    /**
     * static field for all the participant information that we can potentially import
     *
     * @var array
     * @static
     */
    static $_importableFields = null;

    /**
     * static field for all the participant information that we can potentially export
     *
     * @var array
     * @static
     */
    static $_exportableFields = null;

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
    static function &add(&$params)
    {
        require_once 'CRM/Utils/Hook.php';
        
        if ( CRM_Utils_Array::value( 'id', $params ) ) {
            CRM_Utils_Hook::pre( 'edit', 'Participant', $params['id'], $params );
        } else {
            CRM_Utils_Hook::pre( 'create', 'Participant', null, $params ); 
        }
        
        // converting dates to mysql format
        if ( CRM_Utils_Array::value( 'register_date', $params ) ) {
            $params['register_date']  = CRM_Utils_Date::isoToMysql($params['register_date']);
        }

        if ( CRM_Utils_Array::value( 'participant_fee_amount', $params ) ) {
            $params['participant_fee_amount'] = CRM_Utils_Rule::cleanMoney( $params['participant_fee_amount'] );
        }

        $participantBAO =& new CRM_Event_BAO_Participant();
        $participantBAO->id = CRM_Utils_Array::value( 'id', $params );
        $participantBAO->find(true);
        $participantBAO->register_date = CRM_Utils_Date::isoToMysql($participantBAO->register_date);
        $participantBAO->copyValues($params);
        $participantBAO->save();
        
        $session = & CRM_Core_Session::singleton();
        
        // reset the group contact cache for this group
        require_once 'CRM/Contact/BAO/GroupContactCache.php';
        CRM_Contact_BAO_GroupContactCache::remove( );
        
        if ( CRM_Utils_Array::value( 'id', $params ) ) {
            CRM_Utils_Hook::post( 'edit', 'Participant', $participantBAO->id, $participantBAO );
        } else {
            CRM_Utils_Hook::post( 'create', 'Participant', $participantBAO->id, $participantBAO );
        }
        
        return $participantBAO;
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
        $query = "
SELECT li.label, li.qty, li.unit_price, li.line_total
  FROM civicrm_participant AS p,
       civicrm_participant_payment AS pp,
       civicrm_line_item AS li
 WHERE p.id = %1
   AND pp.participant_id = p.id
   AND li.entity_id = pp.contribution_id
   AND li.entity_table = 'civicrm_contribution'";

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

    static function &create(&$params) 
    { 
        require_once 'CRM/Utils/Date.php';

        require_once 'CRM/Core/Transaction.php';
        $transaction = new CRM_Core_Transaction( );
        
        if ( CRM_Utils_Array::value( 'id', $params ) ) {
            $status = CRM_Core_DAO::getFieldValue( 'CRM_Event_DAO_Participant', $params['id'], 'status_id' );
        }
        
        $participant =& self::add($params);
        
        if ( is_a( $participant, 'CRM_Core_Error') ) {
            $transaction->rollback( );
            return $participant;
        }
        
        if ( ( ! CRM_Utils_Array::value( 'id', $params ) ) ||
             ( $params['status_id'] != $status ) ) {
            require_once 'CRM/Activity/BAO/Activity.php';
            CRM_Activity_BAO_Activity::addActivity( $participant );
        }
        
        $session = & CRM_Core_Session::singleton();
        $id = $session->get('userID');
        if ( !$id ) {
            $id = $params['contact_id'];
        }
        
        // add custom field values       
         if ( CRM_Utils_Array::value( 'custom', $params ) &&
             is_array( $params['custom'] ) ) {
            require_once 'CRM/Core/BAO/CustomValueTable.php';
            CRM_Core_BAO_CustomValueTable::store( $params['custom'], 'civicrm_participant', $participant->id );
        }
        
        if ( CRM_Utils_Array::value('note', $params) || CRM_Utils_Array::value('participant_note', $params)) {
            if ( CRM_Utils_Array::value('note', $params) ) {
                $note = CRM_Utils_Array::value('note', $params);
            } else {
                $note = CRM_Utils_Array::value('participant_note', $params);
            }
        
            $noteDetails  = CRM_Core_BAO_Note::getNote( $participant->id, 'civicrm_participant' );
            $noteIDs      = array( );
            if ( ! empty( $noteDetails ) ) {
                $noteIDs['id'] = array_pop( array_flip( $noteDetails ) );
            }

            if ( $note ) {
                require_once 'CRM/Core/BAO/Note.php';
                $noteParams = array(
                                    'entity_table'  => 'civicrm_participant',
                                    'note'          => $note,
                                    'entity_id'     => $participant->id,
                                    'contact_id'    => $id,
                                    'modified_date' => date('Ymd')
                                    );
                
                CRM_Core_BAO_Note::add( $noteParams, $noteIDs );
            }
        }

        // Log the information on successful add/edit of Participant data.
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
        
        $transaction->commit( );
        
        require_once 'CRM/Utils/Recent.php';
        require_once 'CRM/Event/PseudoConstant.php';
        require_once 'CRM/Contact/BAO/Contact.php';
        $url = CRM_Utils_System::url( 'civicrm/contact/view/participant', 
               "action=view&reset=1&id={$participant->id}&cid={$participant->contact_id}" );
               
        $participantRoles = CRM_Event_PseudoConstant::participantRole();
        $eventTitle = CRM_Core_DAO::getFieldValue( 'CRM_Event_DAO_Event', $participant->event_id, 'title' );
        $title = CRM_Contact_BAO_Contact::displayName( $participant->contact_id ) . ' (' . $participantRoles[$participant->role_id] . ' - ' . $eventTitle . ')' ;

//        CRM_Core_Error::debug( 't', $eventTitle);
        
        // add the recently created Activity
        CRM_Utils_Recent::add( $title,
                               $url,
                               $participant->id,
                               'Participant',
                               $participant->contact_id,
                               null );
        
        return $participant;
    }
    
    /**
     * Check whether the event is full for participation and return as
     * per requirements. 
     *
     * @param int      $eventId            event id.
     * @param boolean  $returnEmptySeats   are we require number if empty seats. 
     * @param boolean  $includeWaitingList consider waiting list in event full 
     *                 calculation or not. (it is for cron job  purpose)
     *
     * @return         
     * 1. false                 => If event having some empty spaces.
     * 2. null                  => If no registration yet or no limit.
     * 3. Event Full Message    => If event is full.
     * 4. Number of Empty Seats => If we are interested in empty spaces.( w/ include/exclude waitings. )
     *
     * @static
     * @access public
     */
    static function eventFull( $eventId, $returnEmptySeats = false, $includeWaitingList = true )
    {
        // consider event is full when. 
        // 1. (count(is_counted) >= event_size) or 
        // 2. (count(participants-with-status-on-waitlist) > 0)
        // It might be case there are some empty spaces and still event
        // is full, as waitlist might represent group require spaces > empty.
        
        require_once 'CRM/Event/PseudoConstant.php';
        $countedStatuses    = CRM_Event_PseudoConstant::participantStatus( null, "is_counted = 1" );
        $waitingStatuses    = CRM_Event_PseudoConstant::participantStatus( null, "class = 'Waiting'" );
        $countedStatusIds   = implode( ',', array_keys( $countedStatuses ) );
        $onWaitlistStatusId = array_search( 'On waitlist', $waitingStatuses );
        
        if ( !$countedStatusIds ) {
            $countedStatusIds = 0;
        }
        
        if ( !$onWaitlistStatusId ) {
            $onWaitlistStatusId = 0;
        }
        
        //if waiting straight forward consider event as full.
        if ( $includeWaitingList ) {
            $waitingQuery = "
  SELECT  waiting.id as waiting_participant,
          civicrm_event.event_full_text as event_full_text
    FROM  civicrm_participant waiting, civicrm_event 
   WHERE  waiting.event_id = civicrm_event.id
     AND  waiting.status_id = {$onWaitlistStatusId}
     AND  waiting.is_test = 0
     AND  waiting.event_id = {$eventId}
";
            $waiting =& CRM_Core_DAO::executeQuery( $waitingQuery, CRM_Core_DAO::$_nullArray );
            if ( $waiting->fetch( ) ) { 
                //get the event full message.
                $eventFullmsg = ts( "This event is full !!!" );
                if ( $waiting->event_full_text ) {
                    $eventFullmsg = $waiting->event_full_text;
                }
                
                return $eventFullmsg;
            }
        }
        
        // participant has to have is_counted true for event to be full
        $query = " 
  SELECT  count(counted.id) as counted_participants,
          civicrm_event.max_participants as max_participants,
          civicrm_event.event_full_text as event_full_text  
    FROM  civicrm_participant counted, civicrm_event 
   WHERE  counted.event_id = civicrm_event.id
     AND  counted.status_id IN ( {$countedStatusIds} )
     AND  counted.is_test = 0
     AND  counted.event_id = {$eventId}
GROUP BY  counted.event_id
";
        $counted =& CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
        
        if ( $counted->fetch( ) ) {
            if ( $counted->max_participants == NULL ) {
                return null;
            }
            
            //get the event full message.
            $eventFullmsg = ts( "This event is full !!!" );
            if ( $counted->event_full_text ) {
                $eventFullmsg = $counted->event_full_text;
            }
            
            if ( $counted->counted_participants >= $counted->max_participants ) {
                return $eventFullmsg;
            }      
            
            //return the difference ( exclude waitings. )
            if ( $returnEmptySeats ) {
                return $counted->max_participants - $counted->counted_participants; 
            }
        }
        
        // return size/false/full message as there is no participant register yet.
        $maxParticipants = CRM_Core_DAO::getFieldValue( 'CRM_Event_DAO_Event', $eventId, 'max_participants' );
        
        // no limit for registration.
        if ( $maxParticipants == null ) {
            return null;
        }
        
        if ( $maxParticipants ) {
            if ( $returnEmptySeats ) {
                return $maxParticipants;
            }
            return false;
        }
        
        $evenFullText = CRM_Core_DAO::getFieldValue( 'CRM_Event_DAO_Event', $eventId, 'event_full_text' );
        if ( !$evenFullText ) {
            $evenFullText = ts( "This event is full !!!" );
        }
        
        return $evenFullText;
    }
    
    /**
     * Get the empty spaces for event those we can allocate
     * to pending participant to become confirm.
     *
     * @param int  $eventId event id.
     *
     * @return int $spaces  Number of Empty Seats/null.
     * @static
     * @access public
     */
    static function pendingToConfirmSpaces( $eventId )
    {
        $emptySeats = 0;
        if ( !$eventId ) {
            return $emptySeats;
        }
        
        $positiveStatuses = CRM_Event_PseudoConstant::participantStatus( null, "class = 'Positive'"  ); 
        $statusIds = "(" . implode( ',', array_keys( $positiveStatuses ) ) . ")";
        
        $query ="
  SELECT  count(participant.id) as registered,
          civicrm_event.max_participants
    FROM  civicrm_participant participant, civicrm_event
   WHERE  participant.event_id = {$eventId}
     AND  civicrm_event.id = participant.event_id
     AND  participant.status_id IN {$statusIds}
GROUP BY  participant.event_id
";
        $dao =& CRM_Core_DAO::executeQuery( $query ); 
        if ( $dao->fetch( ) ) { 
            
            //unlimited space.
            if ( $dao->max_participants == NULL || $dao->max_participants <= 0  ) {
                return null;
            }
            
            //no space.
            if ( $dao->registered >= $dao->max_participants ) {
                return $emptySeats;
            }
            
            //difference.
            return $dao->max_participants - $dao->registered; 
        }
        
        //space in case no registeration yet.
        return CRM_Core_DAO::getFieldValue( 'CRM_Event_DAO_Event', $eventId, 'max_participants' );
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

            $note          = array( 'participant_note' => array( 'title'         => 'Participant Note',
                                                                 'name'          => 'participant_note',
                                                                 'headerPattern' => '/(participant.)?note$/i'));

            $tmpConatctField = array( );
            if ( !$onlyParticipant ) {
                require_once 'CRM/Contact/BAO/Contact.php';
                $contactFields = CRM_Contact_BAO_Contact::importableFields( $contactType, null );

                // Using new Dedupe rule.
                $ruleParams = array(
                                    'contact_type' => $contactType,
                                    'level' => 'Strict'
                                    );
                require_once 'CRM/Dedupe/BAO/Rule.php';
                $fieldsArray = CRM_Dedupe_BAO_Rule::dedupeRuleFields($ruleParams);
                
                if( is_array($fieldsArray) ) {
                    foreach ( $fieldsArray as $value) {
                        $tmpContactField[trim($value)] = CRM_Utils_Array::value(trim($value),$contactFields);
                        if (!$status) {
                            $title = $tmpContactField[trim($value)]['title']." (match to contact)" ;
                        } else {
                            $title = $tmpContactField[trim($value)]['title'];
                        }
                        
                        $tmpContactField[trim($value)]['title'] = $title;
                    }
                }
            }
            $tmpContactField['external_identifier'] = CRM_Utils_Array::value('external_identifier',$contactFields);
            $tmpContactField['external_identifier']['title'] = $contactFields['external_identifier']['title'] . " (match to contact)";
            $tmpFields['participant_contact_id']['title']    = $tmpFields['participant_contact_id']['title'] . " (match to contact)";

            $fields = array_merge($fields, $tmpContactField);
            $fields = array_merge($fields, $tmpFields);
            $fields = array_merge($fields, $note);
            //$fields = array_merge($fields, $optionFields);
            
            $fields = array_merge($fields, CRM_Core_BAO_CustomField::getFieldsForImport('Participant'));
            self::$_importableFields = $fields;
        }

        return self::$_importableFields;
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
            
            $fields = array( );
            
            require_once 'CRM/Core/DAO/Note.php';
            $participantFields = CRM_Event_DAO_Participant::export( );
            $noteField         = array( 'participant_note' => array( 'title' => 'Participant Note',
                                                                     'name'  => 'participant_note'));
            require_once 'CRM/Core/DAO/Discount.php';
            $discountFields  = CRM_Core_DAO_Discount::export( );
            
            $fields = array_merge( $noteField, $discountFields );
            $fields = array_merge( $fields, $participantFields );
            // add custom data
            $fields = array_merge($fields, CRM_Core_BAO_CustomField::getFieldsForImport('Participant'));
            self::$_exportableFields = $fields;
        }

        return self::$_exportableFields;
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
     * 
     * @param  int  $id id of the participation to delete                                                                           
     * 
     * @return void
     * @access public 
     * @static 
     */ 
    static function deleteParticipant( $id ) 
    {
        require_once 'CRM/Core/Transaction.php';
        $transaction = new CRM_Core_Transaction( );

        //delete activity record
        require_once "CRM/Activity/BAO/Activity.php";
        $params = array( 'source_record_id' => $id,
                         'activity_type_id' => 5 );// activity type id for event registration

        CRM_Activity_BAO_Activity::deleteActivity( $params );

        // delete the participant payment record
        // we need to do this since the cascaded constraints
        // dont work with join tables
        require_once 'CRM/Event/BAO/ParticipantPayment.php';
        $p = array( 'participant_id' => $id );
        CRM_Event_BAO_ParticipantPayment::deleteParticipantPayment( $p );

        $participant = new CRM_Event_DAO_Participant( );
        $participant->id = $id;
        $participant->delete( );

        $transaction->commit( );

        // delete the recently created Activity
        require_once 'CRM/Utils/Recent.php';
        CRM_Utils_Recent::del( $id );
        
        return $participant;
    }
    
    /**
     *Checks duplicate participants
     *
     * @param array  $duplicates (reference ) an assoc array of name/value pairs
     * @param array $input an assosiative array of name /value pairs
     * from other function
     * @return object CRM_Contribute_BAO_Contribution object    
     * @access public
     * @static
     */
    static function checkDuplicate( $input, &$duplicates ) 
    {    
        $eventId         = CRM_Utils_Array::value( 'event_id'  , $input );
        $contactId      = CRM_Utils_Array::value( 'contact_id', $input );
        
        $clause = array( );
        $input = array( );
        
        if ( $eventId ) {
            $clause[]  = "event_id = %1";
            $input[1]  = array( $eventId, 'Integer' );
        }
        
        if ( $contactId ) {
            $clause[]  = "contact_id = %2";
            $input[2]  = array( $contactId, 'Integer' );
        }
        
        if ( empty( $clause ) ) {
            return false;
        }
        
        $clause = implode( ' AND ', $clause );
        
        $query = "SELECT id FROM civicrm_participant WHERE $clause";
        $dao =& CRM_Core_DAO::executeQuery( $query, $input );
        $result = false;
        while ( $dao->fetch( ) ) {
            $duplicates[] = $dao->id;
            $result = true;
        }
        return $result;
    }
    
    /**
     * fix the event level
     *
     * When price sets are used as event fee, fee_level is set as ^A
     * seperated string. We need to change that string to comma
     * separated string before using fee_level in view mode.
     *
     * @param string  $eventLevel  event_leval string from db
     * 
     * @static
     * @return void
     */
    static function fixEventLevel( &$eventLevel )
    {
        if ( ( substr( $eventLevel, 0, 1) == CRM_Core_BAO_CustomOption::VALUE_SEPERATOR ) &&
             ( substr( $eventLevel, -1, 1) == CRM_Core_BAO_CustomOption::VALUE_SEPERATOR ) ) {
            $eventLevel = implode( ', ', explode( CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, 
                                                  substr( $eventLevel, 1, -1) ) );
        }
    }
    
    /**
     * get the additional participant ids.
     *
     * @param int     $primaryParticipantId  primary partycipant Id
     * @param boolean $excludeCancel         do not include participant those are cancelled.
     *
     * @return array $additionalParticipantIds
     * @static
     */
    static function getAdditionalParticipantIds( $primaryParticipantId, $excludeCancel = true )
    {
        $additionalParticipantIds = array( );
        if ( !$primaryParticipantId ) {
            return $additionalParticipantIds;
        }
        
        $where = "participant.registered_by_id={$primaryParticipantId}";
        if ( $excludeCancel ) {
            $cancelStatusId = 0;
            $negativeStatuses = CRM_Event_PseudoConstant::participantStatus( null, "class = 'Negative'"  ); 
            $cancelStatusId = array_search( 'Cancelled', $negativeStatuses );
            $where .= " AND participant.status_id != {$cancelStatusId}";
        }
        
        $query = "
  SELECT  participant.id
    FROM  civicrm_participant participant
   WHERE  {$where}"; 
        
        $dao = CRM_Core_DAO::executeQuery( $query );
        $cnt = 1;
        while ( $dao->fetch( ) ) {
            $additionalParticipantIds[$cnt] = $dao->id;
            $cnt++;
        }
        
        return $additionalParticipantIds;
    }
    
    /**
     * Function for update primary and additional participant status 
     *      
     * @param  int $participantID primary participant's id 
     * @param  int $statusId status id for participant
     * return void
     * @access public
     * @static
     */
    static function updateParticipantStatus( $participantID, $statusID ) 
    {    
        if ( !$participantID ) {
            return;
        }
        
        $query = "UPDATE civicrm_participant cp SET cp.status_id = %1 WHERE ( cp.id = %2 OR cp.registered_by_id = %2 )";

        $params = array( 1 => array( $statusID, 'Integer' ), 2 => array( $participantID, 'Integer' ) );
        
        $dao = CRM_Core_DAO::executeQuery( $query, $params );
    }
    
    /**
     * Function for update status for given participant ids
     *      
     * @param  int     $participantIds      array of participant ids
     * @param  int     $statusId status     id for participant
     * @params boolean $updateRegisterDate  way to track when status changed.
     *
     * return void
     * @access public
     * @static
     */
    static function updateStatus( $participantIds, $statusId, $updateRegisterDate = true ) 
    {    
        if ( !is_array( $participantIds ) || empty( $participantIds ) || !$statusId ) {
            return;
        }
        
        //lets update register date as we update status to keep track
        //when we did update status, useful for moving participant
        //from pending to expired.
        $setClause = "status_id = {$statusId}";
        if ( $updateRegisterDate ) {
            $setClause .= ", register_date = NOW()";
        }
        
        $participantIdClause = "( " . implode( ',', $participantIds ) . " )";
        
        $query = "
UPDATE  civicrm_participant 
   SET  {$setClause} 
 WHERE  id IN {$participantIdClause}";
        
        $dao = CRM_Core_DAO::executeQuery( $query );
    }
    
    /*
     * Function takes participant ids and statuses
     * update status from $fromStatusId to $toStatusId 
     * and send mail + create activities.
     *      
     * @param  array $participantIds   participant ids.
     * @param  int   $toStatusId       update status id.
     * @param  int   $fromStatusId     from status id
     *
     * return  void
     * @access public
     * @static
     */
    static function transitionParticipants( $participantIds, $toStatusId, $fromStatusId = null, $returnResult = false )
    {   
        if ( !is_array( $participantIds ) || empty( $participantIds ) ) {
            return;
        }
        
        //thumb rule is if we triggering  primary participant need to triggered additional
        $allParticipantIds = $primaryANDAdditonalIds = array( );
        require_once 'CRM/Event/BAO/Participant.php';
        foreach ( $participantIds as $id ) {
            $allParticipantIds[] = $id; 
            $additionalIds = CRM_Event_BAO_Participant::getAdditionalParticipantIds( $id );
            if ( !empty( $additionalIds ) ) {
                $primaryANDAdditonalIds[$id] = $additionalIds;
                $allParticipantIds = array_merge( $allParticipantIds, $additionalIds );
            }
        }
        
        //get the unique participant ids,
        $allParticipantIds = array_unique( $allParticipantIds );
        
        //pull required participants, contacts, events  data, if not in hand
        static $eventDetails   = array( );
        static $domainValues   = array( );
        static $contactDetails = array( );
        
        $contactIds = $eventIds = $participantDetails = array( );
        
        require_once 'CRM/Event/PseudoConstant.php';
        $statusTypes = CRM_Event_PseudoConstant::participantStatus( );
        $participantRoles = CRM_Event_PseudoConstant::participantRole( );
        
        //first thing is pull all necessory data from db.
        $participantIdClause = "(" . implode( ',', $allParticipantIds ) . ")";  
        
        //get all participants data.
        $query = "SELECT * FROM civicrm_participant WHERE id IN {$participantIdClause}";
        $dao   =& CRM_Core_DAO::executeQuery( $query );
        while ( $dao->fetch( ) ) {
            $participantDetails[$dao->id] = array( 'id'               => $dao->id,
                                                   'role'             => $participantRoles[$dao->role_id],
                                                   'is_test'          => $dao->is_test,
                                                   'event_id'         => $dao->event_id,
                                                   'status_id'        => $dao->status_id,
                                                   'fee_amount'       => $dao->fee_amount, 
                                                   'contact_id'       => $dao->contact_id,
                                                   'register_date'    => $dao->register_date,
                                                   'registered_by_id' => $dao->registered_by_id
                                                   );
            if ( !array_key_exists( $dao->contact_id, $contactDetails ) ) {
                $contactIds[$dao->contact_id] = $dao->contact_id; 
            }
            
            if ( !array_key_exists( $dao->event_id, $eventDetails ) ) {
                $eventIds[$dao->event_id] = $dao->event_id;
            }
        }
        
        //get the domain values.
        if ( empty( $domainValues ) ) { 
            // making all tokens available to templates.
            require_once 'CRM/Core/BAO/Domain.php';
            require_once 'CRM/Core/SelectValues.php';
            $domain =& CRM_Core_BAO_Domain::getDomain( );
            $tokens = array ( 'domain'  => array( 'name', 'phone', 'address', 'email'),
                              'contact' => CRM_Core_SelectValues::contactTokens( ));
            
            require_once 'CRM/Utils/Token.php';
            foreach( $tokens['domain'] as $token ){ 
                $domainValues[$token] = CRM_Utils_Token::getDomainTokenReplacement( $token, $domain );
            }
        }
        
        //get all required contacts detail.
        if ( !empty( $contactIds ) ) {
            // get the contact details.
            require_once 'CRM/Mailing/BAO/Mailing.php';
            list( $currentContactDetails ) = CRM_Mailing_BAO_Mailing::getDetails( $contactIds, null, false, false );
            foreach ( $currentContactDetails as $contactId => $contactValues ) {
                $contactDetails[$contactId] = $contactValues;
            }
        }
        
        //get all required events detail.
        if ( !empty( $eventIds ) ) {
            foreach ( $eventIds as $eventId ) {
                //retrieve event information
                require_once 'CRM/Event/BAO/Event.php';
                $eventParams = array( 'id' => $eventId );
                CRM_Event_BAO_Event::retrieve( $eventParams, $eventDetails[$eventId] );
                
                //get the location info
                $locParams = array( 'entity_id' => $eventId ,'entity_table' => 'civicrm_event');
                require_once 'CRM/Core/BAO/Location.php';
                CRM_Core_BAO_Location::getValues( $locParams, $eventDetails[$eventId], true );
            }
        }
        
        //now we are ready w/ all required data.
        //take a decision as per statuses. 
        
        $emailType  = null;
        $toStatus   = $statusTypes[$toStatusId];
        $fromStatus = CRM_Utils_Array::value( $fromStatusId, $statusTypes );
        
        switch ( $toStatus ) { 
        case 'Pending from waitlist' :
        case 'Pending from approval':
            switch ( $fromStatus ) {
            case 'On waitlist':
            case 'Awaiting approval':
                $emailType = 'Confirm';
                break;
            }
            break;
            
        case 'Expired' :
            //no matter from where u come send expired mail.
            $emailType = $toStatus;
            break;
            
        case 'Cancelled':
            //no matter from where u come send cancel mail.
            $emailType = $toStatus;
            break;
        }
        
        //as we process additional w/ primary, there might be case if user
        //select primary as well as additionals, so avoid double processing.
        $processedParticipantIds = array( );
        $mailedParticipants      = array( );
        
        //send mails and update status.
        foreach ( $participantDetails as $participantId => $participantValues ) {
            $updateParticipantIds = array( );
            if ( in_array( $participantId,  $processedParticipantIds ) ) {
                continue;
            }
            
            //check is it primary and has additional.
            if ( array_key_exists( $participantId, $primaryANDAdditonalIds ) ) {
                foreach ( $primaryANDAdditonalIds[$participantId] as $additonalId ) {
                    if ( $emailType ) {
                        $mail = self::sendTransitionParticipantMail( $additonalId, 
                                                                     $participantDetails[$additonalId],
                                                                     $eventDetails[$participantDetails[$additonalId]['event_id']],
                                                                     $contactDetails[$participantDetails[$additonalId]['contact_id']],
                                                                     $domainValues,
                                                                     $emailType );
                        
                        //get the mail participant ids
                        if ( $mail ) {
                            $mailedParticipants[$additonalId] = 
                                $contactDetails[$participantDetails[$additonalId]['contact_id']]['display_name'];
                        }
                    }
                    $updateParticipantIds[] = $additonalId;
                    $processedParticipantIds[] = $additonalId;
                }
                
                //hack to get total amount(additonal + primary)
                $query = "
SELECT  civicrm_contribution.total_amount 
  FROM  civicrm_contribution, civicrm_participant_payment
 WHERE  civicrm_contribution.id = civicrm_participant_payment.contribution_id
   AND  civicrm_participant_payment.participant_id = {$participantId}";
                $totalAmount = CRM_Core_DAO::singleValueQuery( $query );
                if ( $totalAmount ) {
                    $participantValues['fee_amount'] = $totalAmount;
                }
            }
            
            //now send email appropriate mail to primary.
            if ( $emailType ) {
                $mail = self::sendTransitionParticipantMail( $participantId, 
                                                             $participantValues, 
                                                             $eventDetails[$participantValues['event_id']],
                                                             $contactDetails[$participantValues['contact_id']],
                                                             $domainValues,
                                                             $emailType );
                
                //get the mail participant ids
                if ( $mail ) {
                    $mailedParticipants[$participantId] = $contactDetails[$participantValues['contact_id']]['display_name'];
                }
            }
            
            //now update status of group/one at once.
            $updateParticipantIds[] = $participantId;
            self::updateStatus( $updateParticipantIds, $toStatusId );
            $processedParticipantIds[] = $participantId;
        }
        
        //return result for cron.
        if ( $returnResult ) {
            $results = array( 'mailedParticipants'    => $mailedParticipants,
                              'updatedParticipantIds' => $processedParticipantIds ); 
            
            return $results;
        }
    }
    
    /**
     * Function to send mail and create activity 
     * when participant status changed.
     *      
     * @param  int     $participantId      participant id.
     * @param  array   $participantValues  participant detail values. status id for participants 
     * @param  array   $eventDetails       required event details
     * @param  array   $contactDetails     required contact details
     * @param  array   $domainValues       required domain values.
     * @param  string  $mailType           (eg 'approval', 'confirm', 'expired' ) 
     *
     * return  void
     * @access public
     * @static
     */
    function sendTransitionParticipantMail( $participantId, 
                                            $participantValues, 
                                            $eventDetails, 
                                            $contactDetails, 
                                            &$domainValues,
                                            $mailType ) {
        //send emails.
        $mailSent = false;
        if ( $toEmail = CRM_Utils_Array::value( 'email', $contactDetails ) ) {
            
            $contactId       = $participantValues['contact_id'];
            $participantName = $contactDetails['display_name'];
            
            //assign contact value to templates.
            $template =& CRM_Core_Smarty::singleton( );
            $template->assign( 'contact', $contactDetails );
            
            //assign values to template.
            $template =& CRM_Core_Smarty::singleton( );
            
            // assign domain values to template
            $template->assign( 'domain', $domainValues );
            
            //assign participant values to templates.
            $template->assign( 'participant', $participantValues );
            
            //assign event values to templates.
            $template->assign( 'event', $eventDetails );
            
            //is it paid event
            $template->assign( 'paidEvent', CRM_Utils_Array::value( 'is_monetary', $eventDetails ) );
            
            //is show location
            $template->assign( 'isShowLocation', CRM_Utils_Array::value( 'is_show_location', $eventDetails ) );
            
            //is it primary participant.
            $template->assign( 'isAdditional', $participantValues['registered_by_id'] );
            
            //is expiraed registration mail
            $isExpiredMail = false;
            if ( $mailType == 'Expired' ) {
                $isExpiredMail = true;
            }
            $template->assign( 'isExpired', $isExpiredMail );
            
            //is it confirm mail.
            $isConfirmMail = false;
            if ( $mailType == 'Confirm' ) {
                $template->assign( 'isConfirm', $isConfirmMail );
            }
            
            //calculate the checksum value.
            $checksumValue = null;
            if ( $mailType == 'Confirm' && !$participantValues['registered_by_id'] ) {
                require_once 'CRM/Utils/Date.php';
                require_once 'CRM/Contact/BAO/Contact/Utils.php';
                $checksumLife = 'inf';
                if ( $endDate = CRM_Utils_Array::value( 'end_date',  $eventDetails )  ) {
                    $checksumLife = (CRM_Utils_Date::unixTime( $endDate )-time())/(60*60);
                }
                $checksumValue = CRM_Contact_BAO_Contact_Utils::generateChecksum( $contactId, null, $checksumLife );
            }
            $template->assign( 'checksumValue', $checksumValue );
            
            //support different templates for different mails.
            $subject = $message = '';
            if ( $mailType == 'Expired' ) {
                $subject = $template->fetch( 'CRM/Event/Form/ParticipantExpiredSubject.tpl' );
                $message = $template->fetch( 'CRM/Event/Form/ParticipantExpiredMessage.tpl' );
            } else if ( $mailType == 'Confirm' )  {
                $subject = $template->fetch( 'CRM/Event/Form/ParticipantConfirmSubject.tpl' );
                $message = $template->fetch( 'CRM/Event/Form/ParticipantConfirmMessage.tpl' );
            } else if ( 'Cancelled' ) {
                $subject = $template->fetch( 'CRM/Event/Form/ParticipantCancelledSubject.tpl' );
                $message = $template->fetch( 'CRM/Event/Form/ParticipantCancelledMessage.tpl' );
            }
            
            //take a receipt from as event else domain.
            if ( CRM_Utils_Array::value('confirm_from_name',  $eventDetails ) && 
                 CRM_Utils_Array::value('confirm_from_email', $eventDetails ) ) {
                $receiptFrom = '"' . $eventDetails['confirm_from_name'] . '" <' . $eventDetails['confirm_from_email'] . '>';
            } else {
                $receiptFrom = '"' . $domainValues['name'] . '" <' . $domainValues['email'] . '>';
            }
            
            //send mail to participant.
            require_once 'CRM/Utils/Mail.php';
            $mailSent = CRM_Utils_Mail::send( $receiptFrom,
                                              $participantName,
                                              $toEmail,
                                              $subject,
                                              $message,
                                              CRM_Utils_Array::value( 'cc_confirm',  $eventDetails ), 
                                              CRM_Utils_Array::value( 'bcc_confirm', $eventDetails ) 
                                              );
            
            // 3. create activity record.
            if ( $mailSent ) {
                $now = date( 'YmdHis' );
                $activityType = 'Event Registration';
                $activityParams = array( 'subject'            => $subject,
                                         'source_contact_id'  => $contactId,
                                         'source_record_id'   => $participantId,
                                         'activity_type_id'   => CRM_Core_OptionGroup::getValue( 'activity_type',
                                                                                                 $activityType,
                                                                                                 'name' ),
                                         'activity_date_time' => CRM_Utils_Date::isoToMysql( $now ),
                                         'due_date_time'      => CRM_Utils_Date::isoToMysql( $participantValues['register_date'] ),
                                         'is_test'            => $participantValues['is_test'],
                                         'status_id'          => 2
                                         );
                
                require_once 'api/v2/Activity.php';
                if ( is_a( civicrm_activity_create( $activityParams ), 'CRM_Core_Error' ) ) {
                    CRM_Core_Error::fatal("Failed creating Activity for expiration mail");
                }
            }
        }
        
        return $mailSent;
    }
    
}

