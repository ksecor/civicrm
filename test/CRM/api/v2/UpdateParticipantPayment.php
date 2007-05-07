<?php

require_once 'api/crm.php';

class TestOfUpdateParticipantPaymentAPIV2 extends UnitTestCase 
{
    protected $_participantPayment;
               
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }

    function testCreateParticipantPayment()
    {
        $params = array(
                        'participant_id'       => '2',
                        'payment_entity_table' => 'civicrm_contribute',           
                        'payment_entity_id'    => '2'
                        );
        
        $this->_participantPayment = & civicrm_participant_create_payment($params);
        $this->assertEqual($this->_participantPayment['participant_id'], '2');
        $this->assertEqual($this->_participantPayment['payment_entity_table'],'civicrm_contribute');
        $this->assertEqual($this->_participantPayment['payment_entity_id'],'2');
    }
    
    function testUpdateEmptyParticipantPayment()
    {
        $params = array();        
        $participantPayment = & civicrm_participant_update_payment($params);
        $this->assertEqual( $participantPayment['is_error'], 1 );
       
    }
    
    function testCreateErrorParticipantPaymentWithoutParticipantId()
    {
        $params = array(
                        'payment_entity_table' => 'civicrm_contribute',           
                        'payment_entity_id'    => '3'
                        );
        
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

    function testDeleteParticipantPayment()
    {
        $delete = & civicrm_participant_update_payment($this->_participantPayment['participant_id']);
        $this->assertNull($delete);
    }
}
?>
