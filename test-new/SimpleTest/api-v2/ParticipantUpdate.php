<?php

require_once 'api/v2/Participant.php';

class TestOfParticipantUpdateAPIV2 extends CiviUnitTestCase 
{
    protected $_participant;
               
    function setUp() 
    {
    }
    
    function testParticipantUpdateEmptyParams()
    {
        $params = array();        
        $participant = & civicrm_participant_create($params);  
        $this->assertEqual( $participant['is_error'],1 );
        $this->assertEqual( $participant['error_message'],'Required parameter missing' );
    }

    function testParticipantUpdateWithoutEventId()
    {  
        $contactId =$this->individualCreate(); 
        $participantId = $this->participantCreate( $contactId ); 
        $params = array(
                        'contact_id'    => $contactId,
                        'status_id'     => 3,
                        'role_id'       => 3,
                        'register_date' => '2006-01-21',
                        'source'        => 'US Open',
                        'event_level'   => 'Donation'                        
                        );
        $participant = & civicrm_participant_create($params);  
        $this->assertEqual( $participant['is_error'], 1 );
        $this->assertEqual( $participant['error_message'],'Required parameter missing' );
    }

    function testParticipantUpdate()
    {  
        $contactId =$this->individualCreate(); 
        $participantId = $this->participantCreate( $contactId ); 
        $params = array(
                        'id'            => $participantId,
                        'contact_id'    => $contactId,
                        'event_id'      => 2,
                        'status_id'     => 3,
                        'role_id'       => 3,
                        'register_date' => '2006-01-21',
                        'source'        => 'US Open',
                        'event_level'   => 'Donation'                        
                        );
        $participant = & civicrm_participant_create($params);
        $this->assertNotEqual( $participant['is_error'],1 );

        if ( ! $participant['is_error'] ) {
            $params['id'] = CRM_Utils_Array::value('id', $participant);
            
            // Create $match array with DAO Field Names and expected values
            $match = array(
                           'id'         => CRM_Utils_Array::value('id', $participant)
                           );
            // assertDBState compares expected values in $match to actual values in the DB              
            $this->assertDBState( 'CRM_Event_DAO_Participant', $participant['id'], $match );
        }
    }
    
    function tearDown() 
    {
    
    }

}
?>
