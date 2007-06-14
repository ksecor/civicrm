<?php

require_once 'api/v2/Participant.php';

class TestOfParticipantPaymentUpdateAPIV2 extends CiviUnitTestCase 
{

    protected $_contactID;
    protected $_participantID;
    protected $_participantPaymentID;

    function setUp() 
    {
        $this->_contactID       = $this->organizationCreate( );
        $this->_participantID   = $this->participantCreate( $this->_contactID );
    }
    
    function testParticipantPaymentUpdateEmpty()
    {
        $params = array();        
        $participantPayment = & civicrm_participant_payment_update( $params );
        $this->assertEqual( $participantPayment['is_error'], 1 );
    }

    function testParticipantPaymentUpdateMissingParticipantId()
    {
        //WithoutParticipantId
        $params = array(
                        'payment_entity_table' => 'civicrm_contribute',
                        'payment_entity_id'    => '3'
                        );        
        $participantPayment = & civicrm_participant_payment_update( $params );
        $this->assertEqual( $participantPayment['is_error'], 1 );
    }

    function testParticipantPaymentUpdateMissingPaymentEntityId()
    {
        $params = array(
                        'participant_id'       => $this->_participantID,
                        'payment_entity_table' => 'civicrm_contribute'                
                        );        
        $participantPayment = & civicrm_participant_payment_update( $params );
        $this->assertEqual( $participantPayment['is_error'], 1 );
    }
    
    function testParticipantPaymentUpdate()
    {
        //Do payment
        $this->_participantPaymentID = $this->participantPaymentCreate( $this->_participantID );
        $params = array(
                        'id'                   => $this->_participantPaymentID,
                        'participant_id'       => $this->_participantID,
                        'payment_entity_table' => 'civicrm_event',           
                        'payment_entity_id'    => 3
                        );
        
        // Update Payment
        $participantPayment = & civicrm_participant_payment_update( $params );
       
        $this->assertEqual($participantPayment['id'],$this->_participantPaymentID );
        $this->assertEqual($participantPayment['participant_id'],$this->_participantID );
        $this->assertEqual($participantPayment['payment_entity_table'],'civicrm_event' );
        $this->assertEqual($participantPayment['payment_entity_id'],3 );
        
    }
    
    function tearDown() 
    {
        $this->participantDelete( $this->_participantID );
        $this->contactDelete( $this->_contactID );
    }
    
}