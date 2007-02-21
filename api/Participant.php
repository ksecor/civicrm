<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | copyright CiviCRM LLC (c) 2004-2007                                  |
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
 * Definition of CRM API for Participant.
 * More detailed documentation can be found 
 * {@link http://objectledge.org/confluence/display/CRM/CRM+v1.0+Public+APIs
 * here}
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

/**
 * Files required for this package
 */
require_once 'api/utils.php';

/**
 * Create a Event Participants
 *  
 * This API is used for creating a Participants of Event.
 * Required parameters : event_id OR contact_id.
 * 
 * @param   array  $params     an associative array of name/value property values of civicrm_participant
 * @param   int    $contactID  ID of a contact
 * 
 * @return array of newly created membership property values.
 * @access public
 */
function crm_create_participant($params, $contactID)
{
    _crm_initialize();
    if ( !is_array( $params ) ) {
        return _crm_error( 'Params is not an array' );
    }
    
    if ( !isset($params['event_id']) || empty($contactID)) {
        return _crm_error( 'Required parameter missing' );
    }
    if ( !isset($params['status_id'] )) {
        $params['status_id'] = 1;
    } 
    
    if ( !isset($params['register_date'] )) {
             $params['register_date']= date( 'YmdHis' );
         }
    
    $params['contact_id'] = $contactID;    
    require_once 'CRM/Event/BAO/Participant.php';
    $ids = array();
    $participantBAO = CRM_Event_BAO_Participant::create($params, $ids);

    $participant = array();
    _crm_object_to_array($participantBAO, $participant);
    return $participant;
}

/**
 * Get conatct participant record.
 * 
 * This api is used for finding an existing participant record.
 *
 * @params  array  $params     an associative array of name/value property values of civicrm_participant
 *
 * @return  Array of all found participant property values.
 * @access public
 */  
function crm_get_participants( $params )
{
    _crm_initialize();
     if ( !is_array( $params ) ) {
        return _crm_error( 'Params is not an array' );
    }
    
     if ( !isset($params['event_id']) && !isset($params['contact_id'])) {
        return _crm_error( 'Required parameter missing' );
    }
       
    // get the participants for the given contact ID
    require_once 'CRM/Event/BAO/Participant.php';
    $participant = $params;
    $participantValues = $ids = array();
    CRM_Event_BAO_Participant::getValues($participant, $participantValues, $ids);   
    if ( empty( $participantValues ) ) {
        return _crm_error('No participants for this contact.');
    }
    return $participantValues;
}



/**
 * Update an existing contact participant
 *
 * This api is used for updating an existing contact participant.
 * Required parrmeters : id of a participant
 * 
 * @param  Array   $params  an associative array of name/value property values of civicrm_participant
 * 
 * @return array of updated participant property values
 * @access public
 */
function crm_update_participant($params)
{
    _crm_initialize();
    if ( !is_array( $params ) ) {
        return _crm_error( 'Params is not an array' );
    }
    
    if ( !isset($params['id']) ) {
        return _crm_error( 'Required parameter missing' );
    }
    
    require_once 'CRM/Event/BAO/Participant.php';
    $participantBAO =& new CRM_Event_BAO_Participant( );
    $participantBAO->id = $params['id'];
    $fields = $participantBAO->fields( );
    $datefields = array("register_date" => "register_date");    
    foreach ( array('source','status_id','register_date') as $v) {    
        $fields[$v] = $fields['event_'.$v];
        unset( $fields['event_'.$v] );
    }

    if ($participantBAO->find(true)) {
        foreach ( $fields as $name => $field) {
            if (array_key_exists($name, $params)) {                
                $participantBAO->$name = $params[$name];
            }
        }
        
        //fix the dates 
        foreach ( $datefields as $key => $value ) {
            $participantBAO->$key  = CRM_Utils_Date::customFormat($participantBAO->$key,'%Y%m%d');
        }
        $participantBAO->save();
    }
    
    $participant = array();
    _crm_object_to_array( $participantBAO, $participant );
    return $participant;
}



/**
 * Deletes an existing contact participant
 * 
 * This API is used for deleting a contact participant
 * 
 * @param  Int  $participantID   Id of the contact participant to be deleted
 * 
 * @return null if successfull, object of CRM_Core_Error otherwise
 * @access public
 */
function crm_delete_participant($participantID)
{
    _crm_initialize();
    
    if (empty($participantID)) {
        return _crm_error('Invalid value for participantID');
    }
    require_once 'CRM/Event/BAO/Participant.php';
    $participant = new CRM_Event_BAO_Participant();
    $result = $participant->deleteParticipant($participantID);
    
    return $result ? null : _crm_error('Error while deleting participant');
}
?>