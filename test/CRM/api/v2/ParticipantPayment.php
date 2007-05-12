<?php

require_once 'api/v2/Participant.php';

class TestOfParticipantPaymentAPIV2 extends UnitTestCase 
{
    protected $_participantPayment;
            
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }

    function testCreateEmptyParticipantPayment()
    {
        $params = array();        
        $participantPayment = & civicrm_participant_create_payment($params);
        $this->assertEqual( $participantPayment['is_error'], 1 );
    }
    
    function testCreateErrorParticipantPaymentWithoutParticipantId()
    {
        $params = array(
                        'payment_entity_table' => 'civicrm_contribute',
                        'payment_entity_id'    => '3'
                        );        
        $participantPayment = & civicrm_participant_create_payment($params);
        $this->assertEqual( $participantPayment['is_error'], 1 );
    }


    function testCreateErrorParticipantPaymentWithoutPaymentEntityId()
    {
        $params = array(
                        'participant_id'       => '2',
                        'payment_entity_table' => 'civicrm_contribute'                
                        );        
        $participantPayment = & civicrm_participant_create_payment($params);
        $this->assertEqual( $participantPayment['is_error'], 1 );
    }
    
    function testCreateParticipantPayment()
    {
        $params = array(
                        'participant_id'       => '2',
                        'payment_entity_table' => 'civicrm_contribute',           
                        'payment_entity_id'    => '2'
                        );
       
        $this->_participantPayment = & civicrm_participant_create_payment($params);
        $this->assertNotNull( $this->_participantPayment['participant_id'] );
    }     
    function testUpdateEmptyParticipantPayment()
    {
        $params = array();        
        $participantPayment = & civicrm_participant_update_payment($params);
        $this->assertEqual( $participantPayment['is_error'], 1 );
       
    }
    
    function testUpdateParticipantPayment()
    {
        $params = array(
                        'id'                   => $this->_participantPayment['id'],
                        'participant_id'       => '2',
                        'payment_entity_table' => 'civicrm_event',           
                        'payment_entity_id'    => '4'
                        );
        
        $this->_participantPayment = & civicrm_participant_update_payment($params);
        $this->assertEqual($this->_participantPayment['participant_id'], '2');
        $this->assertEqual($this->_participantPayment['payment_entity_table'],'civicrm_event');
        $this->assertEqual($this->_participantPayment['payment_entity_id'],'4');
    }

    function testDeleteEmptyParticipantPayment()
    {
        $params = array();        
        $delete = & civicrm_participant_delete_payment($params);
        $this->assertEqual( $delete['is_error'], 1 );
    }
    
    function testDeleteErrorParticipantPaymentWithWrongId()
    {
        $id = -165;      
        $delete = & civicrm_participant_delete_payment($id);
        $this->assertEqual( $delete['is_error'], 1 );
    }

    function testDeleteParticipantPayment()
    {   
        $delete = & civicrm_participant_delete_payment($this->_participantPayment['participant_id']);
        $this->assertNull($delete);
    }
}
?>
