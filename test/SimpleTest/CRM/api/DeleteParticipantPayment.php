<?php

require_once 'api/crm.php';

class TestOfDeleteParticipantPayment extends UnitTestCase 
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
        
        $this->_participantPayment = & crm_create_participant_payment($params);
        $this->assertEqual($this->_participantPayment['participant_id'], 2);
        $this->assertEqual($this->_participantPayment['payment_entity_table'],'civicrm_contribute');
        $this->assertEqual($this->_participantPayment['payment_entity_id'],1);
    }     
    
    function testDeleteEmptyParticipantPayment()
    {
        $params = array();        
        $delete = & crm_delete_participant_payment($params);
        $this->assertIsA($delete,'CRM_Core_Error');
    }
    
    function testDeleteErrorParticipantPaymentWithWrongId()
    {
        $id = -165;      
        $delete = & crm_delete_participant_payment($id);
        $this->assertIsA($delete,'CRM_Core_Error');
    }

    function testDeleteParticipantPayment()
    {
        $delete = & crm_delete_participant_payment($this->_participantPayment['id']);
        $this->assertNull($delete);
    }
}
?>
