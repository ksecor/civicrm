<?php
class Participant extends DrupalTestCase 
{
    /*
     * Helper function to create
     * a Participant
     *
     * @return $participant id of created Participant
     */
    function create( $contactId, $eventId )
    {        
        $params = array(
                        'send_receipt'     => 1,
                        'is_test'          => 0,
                        'is_pay_later'     => 0,
                        'event_id'         => $eventId,
                        'register_date'    => date('Y-m-d')." 00:00:00",
                        'role_id'          => 1,
                        'status_id'        => 1,
                        'source'           => 'Event_'.$eventId,
                        'contact_id'       => $contactId
                        );
        //crm_Core_error::debug($params);
        require_once 'CRM/Event/BAO/Participant.php';
        $participant = CRM_Event_BAO_Participant::add($params);
        return $participant->id;
    }
 
    /*
     * Helper function to delete a participant
     * 
     * @param  int  $participantID   id of the participant to delete
     * @return boolean true if participant deleted, false otherwise
     * 
     */
    function delete( $participantId ) {
        return CRM_Event_BAO_Participant::del( $participantId );
    }
}

?>