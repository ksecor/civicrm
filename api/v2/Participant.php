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
 * Definition of CRM API for Participant.
 * More detailed documentation can be found 
 * {@link http://objectledge.org/confluence/display/CRM/CRM+v1.0+Public+APIs
 * here}
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

/**
 * Files required for this package
 */
require_once 'api/v2/utils.php';

/**
 * Create a Event Participants
 *  
 * This API is used for creating a Participants of Event.
 * Required parameters : event_id OR contact_id.
 * 
 * @param   array  $params     an associative array of name/value property values of civicrm_participant
 * 
 * @return array participant id if participant is created otherwise is_error = 1
 * @access public
 */
function civicrm_participant_create($params)
{
    _civicrm_initialize();
    $contactID = CRM_Utils_Array::value( 'contact_id', $params );

    if ( !is_array( $params ) ) {
        return civicrm_create_error( 'Parameters is not an array' );
    }
    

    if ( !isset($params['event_id']) || !isset($params['contact_id'])) {
        return civicrm_create_error( 'Required parameter missing' );
    }
    if ( !isset($params['status_id'] )) {
        $params['status_id'] = 1;
    } 
    
    if ( !isset($params['register_date'] )) {
             $params['register_date']= date( 'YmdHis' );
    }

    $ids= array();

    require_once 'CRM/Event/BAO/Participant.php';
    $participant = CRM_Event_BAO_Participant::create($params, $ids);

    if ( is_a( $participant, 'CRM_Core_Error' ) ) {
        return civicrm_create_error( "Participant is not created" );
    } else {
        $values = array( );
        $values['participant_id'] = $participant->id;
        $values['is_error']   = 0;
    }
    return $values;
}


/**
 * Retrieve a specific event, given a set of input params
 * If more than one event exists, return an error, unless
 * the client has requested to return the first found contact
 *
 * @param  array   $params           (reference ) input parameters
 *
 * @return array (reference )        array of properties, if error an array with an error id and error message
 * @static void
 * @access public
 */
function &civicrm_participant_get( &$params ) {
    _civicrm_initialize( );

    $values = array( );
    if ( empty( $params ) ) {
        return civicrm_create_error( ts( 'No input parameters present' ) );
    }
    
    if ( ! is_array( $params ) ) {
        return civicrm_create_error( ts( 'Input parameters is not an array' ) );
    }

    $participant  =& civicrm_participant_search( $params );


    if ( count( $participant ) != 1 &&
         ! $participant['returnFirst'] ) {
        return civicrm_create_error( ts( '%1 participant matching input params', array( 1 => count( $participant ) ) ) );
    }

    if ( civicrm_error( $participant ) ) {
        return $participant;
    }

    if ( count( $participant ) != 1 &&
         ! $params['returnFirst'] ) {
        return civicrm_create_error( ts( '%1 participants matching input params', array( 1 => count( $participant ) ) ) );
    }

    $participant = array_values( $participant )
;
    return $participant[0];
}

/**
 * Get contact participant record.
 * 
 * This api is used for finding an existing participant record.
 *
 * @params  array  $params     an associative array of name/value property values of civicrm_participant
 *
 * @return  Array of all found participant property values.
 * @access public
 */  

function civicrm_participant_search( $params ) {

    $inputParams      = array( );
    $returnProperties = array( );
    $otherVars = array( 'sort', 'offset', 'rowCount' );
    
    $sort     = null;
    $offset   = 0;
    $rowCount = 25;
    foreach ( $params as $n => $v ) {
        if ( substr( $n, 0, 7 ) == 'return.' ) {
            $returnProperties[ substr( $n, 7 ) ] = $v;
        } elseif ( array_key_exists( $n, $otherVars ) ) {
            $$n = $v;
        } else {
            $inputParams[$n] = $v;
        }
    }
    require_once 'CRM/Contact/BAO/Query.php';
    require_once 'CRM/Event/BAO/Query.php';  
    if ( empty( $returnProperties ) ) {
        $returnProperties = CRM_Event_BAO_Query::defaultReturnProperties( CRM_Contact_BAO_Query::MODE_EVENT );
    }

    $newParams =& CRM_Contact_BAO_Query::convertFormValues( $params);
    $query =& new CRM_Contact_BAO_Query( $newParams, $returnProperties, null );
    list( $select, $from, $where ) = $query->query( );
    
    $sql = "$select $from $where";  

    if ( ! empty( $sort ) ) {
        $sql .= " ORDER BY $sort ";
    }
    $dao =& CRM_Core_DAO::executeQuery( $sql, CRM_Core_DAO::$_nullArray );
    
    $participant = array( );
    while ( $dao->fetch( ) ) {
        $participant[$dao->participant_id] = $query->store( $dao );
    }
    $dao->free( );
    
    return $participant;

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
function civicrm_participant_update($params)
{
    _civicrm_initialize();
    if ( !is_array( $params ) ) {
        return civicrm_create_error( 'Parameters is not an array' );
    }
    
    if ( !isset($params['id']) ) {
        return civicrm_create_error( 'Required parameter missing' );
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
    _civicrm_object_to_array( $participantBAO, $participant );
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
function civicrm_participant_delete($participantID)
{
    _civicrm_initialize();
    
    if (empty($participantID)) {
        return civicrm_create_error('Invalid value for participantID');
    }
    require_once 'CRM/Event/BAO/Participant.php';
    $participant = new CRM_Event_BAO_Participant();
    $result = $participant->deleteParticipant($participantID);
    
    return $result ? null : civicrm_create_error('Error while deleting participant');
}


/**
 * Create a Event Participant Payment
 *  
 * This API is used for creating a Participant Payment of Event.
 * Required parameters : participant_id, contribution_id.
 * 
 * @param   array  $params     an associative array of name/value property values of civicrm_participant_payment
 * 
 * @return array of newly created payment property values.
 * @access public
 */
function civicrm_participant_create_payment($params)
{
    _civicrm_initialize();
    if ( !is_array( $params ) ) {
        return civicrm_create_error( 'Params is not an array' );
    }
    
    if ( !isset($params['participant_id']) || !isset($params['payment_entity_id']) ) {
        return civicrm_create_error( 'Required parameter missing' );
    }

    require_once 'CRM/Event/DAO/ParticipantPayment.php';
    $participantPaymentDAO =& new CRM_Event_DAO_ParticipantPayment();
    $participantPaymentDAO->copyValues($params);
    $participantPaymentDAO = $participantPaymentDAO->save();
    
    $participantPayment = array();
    _civicrm_object_to_array($participantPaymentDAO, $participantPayment);
    return $participantPayment;
}

/**
 * Update an existing contact participant payment
 *
 * This api is used for updating an existing contact participant payment
 * Required parrmeters : id of a participant_payment
 * 
 * @param  Array   $params  an associative array of name/value property values of civicrm_participant_payment
 * 
 * @return array of updated participant_payment property values
 * @access public
 */
function civicrm_participant_update_payment($params)
{
    _civicrm_initialize();
    if ( !is_array( $params ) ) {
        return civicrm_create_error( 'Params is not an array' );
    }
    
    if ( !isset($params['id']) ) {
        return civicrm_create_error( 'Required parameter missing' );
    }
    
    require_once 'CRM/Event/DAO/ParticipantPayment.php';
    $participantPaymentBAO =& new CRM_Event_DAO_ParticipantPayment( );
    $participantPaymentBAO->id = $params['id'];
    $fields = $participantPaymentBAO->fields( );   
    if ($participantPaymentBAO->find(true)) {
        foreach ( $fields as $name => $field) {
            if (array_key_exists($name, $params)) {                
                $participantPaymentBAO->$name = $params[$name];
            }
        }
        
        $participantPaymentBAO->save();
    }
    
    $participantPayment = array();
    _civicrm_object_to_array( $participantPaymentBAO, $participantPayment );
    return $participantPayment;
}

/**
 * Deletes an existing Participant Payment
 * 
 * This API is used for deleting a Participant Payment
 * 
 * @param  Int  $participantPaymentID   Id of the Participant Payment to be deleted
 * 
 * @return null if successfull, array with is_error=1 otherwise
 * @access public
 */
function civicrm_participant_delete_payment($participantPaymentID)
{
    _civicrm_initialize();
    
    if (empty($participantPaymentID)) {
        return civicrm_create_error('Invalid value for participantPaymentID');
    }
    require_once 'CRM/Event/BAO/ParticipantPayment.php';
    $participant = new CRM_Event_BAO_ParticipantPayment();
    
    $params = array( 'id' => $participantPaymentID );
    
    return $participant->deleteParticipantPayment( $params ) ? null : civicrm_create_error('Error while deleting participantPayment');
}

?>
