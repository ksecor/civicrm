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
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Event/DAO/Event.php';

class CRM_Event_BAO_Event extends CRM_Event_DAO_Event 
{

    /**
     * class constructor
     */
    function __construct( ) 
    {
        parent::__construct( );
    }
    
    /**
     * Takes a bunch of params that are needed to match certain criteria and
     * retrieves the relevant objects. Typically the valid params are only
     * contact_id. We'll tweak this function to be more full featured over a period
     * of time. This is the inverse function of create. It also stores all the retrieved
     * values in the default array
     *
     * @param array $params   (reference ) an assoc array of name/value pairs
     * @param array $defaults (reference ) an assoc array to hold the flattened values
     *
     * @return object CRM_Event_BAO_ManageEvent object
     * @access public
     * @static
     */
    static function retrieve( &$params, &$defaults ) 
    {
        $event  = new CRM_Event_DAO_Event( );
        $event->copyValues( $params );
        if ( $event->find( true ) ) {
            CRM_Core_DAO::storeValues( $event, $defaults );
            return $event;
        }
        return null;
    }

    /**
     * update the is_active flag in the db
     *
     * @param int      $id        id of the database record
     * @param boolean  $is_active value we want to set the is_active field
     *
     * @return Object             DAO object on sucess, null otherwise
     * @static
     */
    static function setIsActive( $id, $is_active ) 
    {
        return CRM_Core_DAO::setFieldValue( 'CRM_Event_DAO_Event', $id, 'is_active', $is_active );
    }

    /**
     * function to add the eventship types
     *
     * @param array $params reference array contains the values submitted by the form
     * @param array $ids    reference array contains the id
     * 
     * @access public
     * @static 
     * @return object
     */
    static function add(&$params, &$id) 
    {
        $event =& new CRM_Event_DAO_Event( );
        $event->domain_id = CRM_Core_Config::domainID( );
        $event->id = CRM_Utils_Array::value( 'event_id', $id );
        
        $event->copyValues( $params );
        $event->save( );
                
        return $event;
    }
    
    /**
     * function to create the event
     *
     * @param array $params reference array contains the values submitted by the form
     * @param array $ids    reference array contains the id
     * 
     * @access public
     * @static 
     * 
     */
   
    public static function create( &$params, &$ids) 
    {
        $event = self::add($params, $ids);

        $groupTree =& CRM_Core_BAO_CustomGroup::getTree("Event", $ids['id'], 0,$params["event_type_id"]);
       
        CRM_Core_BAO_CustomGroup::postProcess( $groupTree, $params );
        CRM_Core_BAO_CustomGroup::updateCustomData($groupTree, "Event", $event->id); 
        
    }
     
    /**
     * Function to delete the event
     *
     * @param int    $id           event id
     *
     * @access public
     * @static
     *
     */
    static function del( $id )
    {
        CRM_Core_BAO_Location::deleteContact( $id );
      
        require_once 'CRM/Event/DAO/EventPage.php';
        $registration           = & new CRM_Event_DAO_EventPage( );
        $registration->event_id = $id; 
        $registration->find();
        while ($registration->fetch() ) {
            $registration->delete();
        }
        require_once 'CRM/Core/DAO/CustomOption.php';
        $customOption = & new CRM_Core_DAO_CustomOption( );
        $customOption->entity_id    = $id; 
        $customOption->entity_table = 'civicrm_event'; 
        $customOption->find();
        while ($customOption->fetch() ) {
            $customOption->delete();
        }
        require_once 'CRM/Event/DAO/Participant.php';
        require_once 'CRM/Event/DAO/ParticipantPayment.php';
        $participant = & new CRM_Event_DAO_Participant( );
        $participant->entity_id    = $id; 
        $participant->entity_table = 'civicrm_event'; 
        $participant->find();
        while ($participant->fetch() ) {
            $payment = & new CRM_Event_DAO_ParticipantPayment( );
            $payment->participant_id = $participant->id;
            $payment->find();
            while( $payment->fetch() ) {
                $payment->delete();
            }
            $participant->delete();
        }
        require_once 'CRM/Core/DAO/UFJoin.php';
        $ufJoin = & new CRM_Core_DAO_UFJoin( );
        $ufJoin->entity_id    = $id; 
        $ufJoin->entity_table = 'civicrm_event'; 
        $ufJoin->find();
        while ($ufJoin->fetch() ) {
            $ufJoin->delete();
        }
        require_once 'CRM/Event/DAO/Event.php';
        $event           = & new CRM_Event_DAO_Event( );
        $event->id = $id; 
        $event->find();
        while ($event->fetch() ) {
            $event->delete();
        }
        return true;
    }
    
    /**
     * Function to get current/future Events 
     *
     * @param $all boolean true if events all are required else returns current and future events
     *
     * @static
     */
    static function getEvents( $all = false, $id = false) 
    {
        $query = "SELECT `id`, `title`, `start_date` FROM `civicrm_event`";
        
        if ( !$all ) {
            $query .= " WHERE `start_date` >= now();";
        }
        if ( $id ) {
            $query .= " WHERE `id` = {$id};";
        }

        $events = array( );
        $dao =& CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
        while ( $dao->fetch( ) ) {
            $events[$dao->id] = $dao->title . ' - '.CRM_Utils_Date::customFormat($dao->start_date);
        }
        
        return $events;
    }
    
    /**
     * Function to get events Summary
     *
     * @static
     * @return array Array of event summary values
     */
    static function getEventSummary( )
    {
        $eventSummary = array( );
        
        $query = "SELECT count(id) as total_events
                  FROM   civicrm_event 
                  WHERE  is_active=1";
        
        $dao =& CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
        
        if ( $dao->fetch( ) ) {
            $eventSummary['total_events'] = $dao->total_events;
        }
        
        // Get the Id of Option Group for Event
        require_once 'CRM/Core/DAO/OptionGroup.php';
        $optionGroupDAO = new CRM_Core_DAO_OptionGroup();
        $optionGroupDAO->name = 'event_type';
        $optionGroupId = null;
        if ($optionGroupDAO->find(true) ) {
            $optionGroupId = $optionGroupDAO->id;
        }
        
        $query = "SELECT     civicrm_event.id id, civicrm_event.title event_title, civicrm_event.is_public is_public, 
                             civicrm_event.max_participants max_participants, civicrm_event.start_date start_date, 
                             civicrm_event.end_date end_date, civicrm_event.is_map is_map, 
                             civicrm_option_value.label event_type, count(civicrm_participant.id) participants
                  FROM       civicrm_event 
                  LEFT JOIN  civicrm_participant  ON (civicrm_event.id=civicrm_participant.event_id ) 
                  LEFT JOIN  civicrm_option_value ON (civicrm_event.event_type_id=civicrm_option_value.value AND civicrm_option_value.option_group_id=" . CRM_Utils_Type::escape( $optionGroupId, 'Integer' ) . ") 
                  WHERE      civicrm_event.is_active=1 AND civicrm_event.domain_id =" . CRM_Utils_Type::escape( CRM_Core_Config::domainID(), 'Integer' ) . "
                  GROUP BY   civicrm_participant.event_id
                  ORDER BY   civicrm_event.end_date DESC
                  LIMIT      0 , 10";
        
        $dao =& CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
        
        $properties = array( 'eventTitle'      => 'event_title',      'isPublic'     => 'is_public', 
                             'maxParticipants' => 'max_participants', 'startDate'    => 'start_date', 
                             'endDate'         => 'end_date',         'eventType'    => 'event_type', 
                             'isMap'           => 'is_map',           'participants' => 'participants' );
        
        while ( $dao->fetch( ) ) {
            foreach ( $properties as $property => $name ) {
                if (( $name == 'start_date' ) || 
                    ( $name == 'end_date' ) ) {
                    $eventSummary['events'][$dao->id][$property] = CRM_Utils_Date::customFormat($dao->$name, '%B %d%f %Y');
                } else if ( $name == 'participants' ) {
                    $eventSummary['events'][$dao->id][$property] = $dao->$name;
                    if ( $dao->$name ) {
                        $set = CRM_Utils_System::url( 'civicrm/event/search',"reset=1&force=1&event=$dao->id" );
                    } else {
                        $set = null;
                    }
                    $eventSummary['events'][$dao->id]['participant_url'] = $set;
                } else if ( $name == 'is_public' ) {
                    if ( $dao->$name ) {
                        $set = 'Yes';
                    } else {
                        $set = 'No';
                    }
                    $eventSummary['events'][$dao->id][$property] = $set;
                } else if ( $name == 'is_map' ) {
                    if ( $dao->$name) {
                        $set = CRM_Utils_System::url( 'civicrm/event/search',"reset=1" );
                    } else {
                        $set = null;
                    }
                    $eventSummary['events'][$dao->id][$property] = $set;
                    $eventSummary['events'][$dao->id]['configure'] = CRM_Utils_System::url( "civicrm/admin/event", "action=update&id=$dao->id&reset=1" );
                } else {
                    $eventSummary['events'][$dao->id][$property] = $dao->$name;
                }
            }
        }
        
        return $eventSummary;
    }
}
?>