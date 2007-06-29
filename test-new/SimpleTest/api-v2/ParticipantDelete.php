<?php

require_once 'api/v2/Participant.php';

class TestOfParticipantDeleteAPIV2 extends CiviUnitTestCase 
{
    protected $_contactID;
    protected $_participantID;
    protected $_failureCase;
    
    
    function setUp() 
    {
        $this->_contactID = $this->individualCreate( ) ;
        $this->_participantID = $this->participantCreate( $this->_contactID );
        $this->_failureCase = 0;
    }
    
    function tearDown()
    {       
        // Cleanup test contact.
        $result = $this->contactDelete( $this->_contactID );
        
    }
    
    
    function testParticipantDelete()
    {
        $params = array(
                        'id' => $this->_participantID,
                        );
        $participant = & civicrm_participant_delete($params);
        $this->assertDBState( 'CRM_Event_DAO_Participant', $this->_participantID, NULL, true ); 

    }
    
   
    // This should return an error because required param is missing.. 
    function testParticipantDeleteMissingID()
    {
        $params = array(
                        'event_id'      => 1,
                        );
        $participant = & civicrm_participant_delete($params);
        $this->assertEqual( $participant['is_error'],1 );
        $this->assertNotNull($participant['error_message']);
        $this->_failureCase = 1;
    }
    
    
}
?>
