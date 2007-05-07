<?php

require_once 'api/crm.php';

class TestOfDeleteParticipantPaymentAPIV2 extends UnitTestCase 
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
                        'participant_id'       => 2,
                        'payment_entity_table' => 'civicrm_contribute',           
                        'payment_entity_id'    => 1
                        );
        
        $this->_participantPayment = & civicrm_participant_create_payment($params);
        $this->assertEqual($this->_participantPayment['participant_id'], 2);
        $this->assertEqual($this->_participantPayment['payment_entity_table'],'civicrm_contribute');
        $this->assertEqual($this->_participantPayment['payment_entity_id'],1);
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
