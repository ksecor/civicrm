<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.6                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                  |
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
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/Event/DAO/Event.php';

class CRM_Event_BAO_ManageEvent extends CRM_Event_DAO_Event 
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
    static function add(&$params, $id) 
    {
        
        $event               =& new CRM_Event_DAO_Event( );
        $event->domain_id    = CRM_Core_Config::domainID( );
        $event->id = CRM_Utils_Array::value( 'event_id', $id );
       
        $event->copyValues( $params );
        $event->save( );
                
        return $event;
    }
    
    static function del ( $id ) {

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
        require_once 'CRM/Event/DAO/Event.php';
        $event           = & new CRM_Event_DAO_Event( );
        $event->id = $id; 
        $event->find();
        while ($event->fetch() ) {
            $event->delete();
        }
        return true;
    }
}
?>