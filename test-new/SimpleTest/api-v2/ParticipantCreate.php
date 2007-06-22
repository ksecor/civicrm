<?php

require_once 'api/v2/Participant.php';

class TestOfParticipantCreateAPIV2 extends CiviUnitTestCase 
{
    protected $_contactID;
    protected $_createdParticipants;

    
    function setUp() 
    {
        $this->_contactID = $this->individualCreate( ) ;
	$this->_createdParticipants = array( );
    }

    function tearDown()
    {
        // Cleanup all created participant records.
        foreach ( $this->_createdParticipants as $id ) {
            $result = $this->participantDelete( $id );
        }
        // Cleanup test contact
        $result = $this->contactDelete( $this->_contactID ); 
    }
    

    function testParticipantCreateMissingContactID()
    {
        $params = array(
                        'event_id'      => 2,
                        );
        $participant = & civicrm_participant_create($params);
        if ( CRM_Utils_Array::value('id', $participant) ) {
            $this->_createdParticipants[] = $participant['id'];
        }
        $this->assertEqual( $participant['is_error'],1 );
        $this->assertNotNull($participant['error_message']);
    }

    function testParticipantCreateMissingEventID()
    {
        $params = array(
                        'contact_id'    => $this->_contactID,
                        );
        $participant = & civicrm_participant_create($params); 
        if ( CRM_Utils_Array::value('id', $participant) ) {
            $this->_createdParticipants[] = $participant['id'];
        }
        $this->assertEqual( $participant['is_error'],1 );
        $this->assertNotNull($participant['error_message']);
    }

    function testParticipantCreateEventIdOnly()
    {
        $params = array(
                        'contact_id'    => $this->_contactID,
                        'event_id'      => 1,
                        );
        $participant = & civicrm_participant_create($params); 
        $this->assertNotEqual( $participant['is_error'],1 );

        if ( ! $participant['is_error'] ) {
            $this->_createdParticipants[] = CRM_Utils_Array::value('id', $participant);
            
            // Create $match array with DAO Field Names and expected values
            $match = array(
                           'event_id'                   => 1,
                           'participant_status_id'      => 1,
                           );
            // assertDBState compares expected values in $match to actual values in the DB              
            $this->assertDBState( 'CRM_Event_DAO_Participant', $participant['id'], $match ); 
        }
    }
    
    function testParticipantCreateAllParams()
    {  
        $params = array(
                        'contact_id'    => $this->_contactID,
                        'event_id'      => 2,
                        'status_id'     => 1,
                        'role_id'       => 1,
                        'register_date' => '2007-07-21',
                        'source'        => 'Online Event Registration: API Testing',
                        'event_level'   => 'Tenor'                        
                        );
        
        $participant = & civicrm_participant_create($params);
        $this->assertNotEqual( $participant['is_error'],1 );

        if ( ! $participant['is_error'] ) {
            $this->_createdParticipants[] = CRM_Utils_Array::value('id', $participant);

            // Create $match array with DAO Field Names and expected values
            $match = array(
                       'event_id'                   => 2,
                       'participant_status_id'      => 1,
                       'participant_role_id'        => 1,
                       'participant_register_date'  => '2007-07-21 00:00:00',
                       'participant_source'         => 'Online Event Registration: API Testing',
                       'event_level'                => 'Tenor',
                       );
            // assertDBState compares expected values in $match to actual values in the DB              
            $this->assertDBState( 'CRM_Event_DAO_Participant', $participant['id'], $match ); 
        }
    }
    
}
?>
