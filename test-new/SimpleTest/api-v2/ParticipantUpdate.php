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
        $participant = & civicrm_participant_update($params);
        $this->assertEqual( $participant['is_error'],1 );
    }

    function testParticipantUpdateWithoutId()
    {  
        $contactId =$this->individualCreate(); 
        $participantId = $this->participantCreate( $contactId ); 
        $params = array(
                        'contact_id'    => $contactId,
                        'event_id'      => 2,
                        'status_id'     => 3,
                        'role_id'       => 3,
                        'register_date' => '2006-01-21',
                        'source'        => 'US Open',
                        'event_level'   => 'Donation'                        
                        );
        $participant = & civicrm_participant_update($params); 
        $this->assertEqual( $participant['is_error'], 1 );
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
       
        $participant = & civicrm_participant_update($params);
        $this->assertEqual($participant['event_id'],2);
        $this->assertEqual($participant['status_id'],3);
        $this->assertEqual($participant['role_id'],3);
        $this->assertEqual($participant['register_date'],20060121);
        $this->assertEqual($participant['source'],'US Open');
        $this->assertEqual($participant['event_level'],'Donation');
       
    }

    function tearDown() 
    {
    }
   
}
?>
