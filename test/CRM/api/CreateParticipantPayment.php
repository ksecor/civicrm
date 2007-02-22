<?php

require_once 'api/crm.php';

class TestOfCreateParticipantPayment extends UnitTestCase 
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
        $participantPayment = & crm_create_participant_payment($params);
        $this->assertIsA($participantPayment,'CRM_Core_Error');
    }
    
    function testCreateErrorParticipantPaymentWithoutParticipantId()
    {
        $params = array(
                        'payment_entity_table' => 'civicrm_contribute',
                        'payment_entity_id'    => '3'
                        );        
        $participantPayment = & crm_create_participant_payment($params);
        $this->assertIsA($participantPayment,'CRM_Core_Error');
    }


    function testCreateErrorParticipantPaymentWithoutPaymentEntityId()
    {
        $params = array(
                        'participant_id'       => '2',
                        'payment_entity_table' => 'civicrm_contribute'                
                        );        
        $participantPayment = & crm_create_participant_payment($params);
        $this->assertIsA($participantPayment,'CRM_Core_Error');
    }
    
    function testCreateParticipantPayment()
    {
        $params = array(
                        'participant_id'       => '2',
                        'payment_entity_table' => 'civicrm_contribute',           
                        'payment_entity_id'    => '2'
                        );
       
        $this->_participantPayment = & crm_create_participant_payment($params);
        $this->assertEqual($this->_participantPayment['participant_id'], '2');
        $this->assertEqual($this->_participantPayment['payment_entity_table'],'civicrm_contribute');
        $this->assertEqual($this->_participantPayment['payment_entity_id'],'2');
    }     

    function testDeleteParticipantPayment()
    {
        $delete = & crm_delete_participant_payment($this->_participantPayment['id']);
        $this->assertNull($delete);
    }
}
?>