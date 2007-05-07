<?php

require_once 'api/crm.php';

class TestOfCreateParticipantPaymentAPIV2 extends UnitTestCase 
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

    function testDeleteParticipantPayment()
    {
        $delete = & crm_delete_participant_payment($this->_participantPayment['participant_id']);
        $this->assertNull($delete);
    }
}
?>
