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
        $participantPayment = & civicrm_participant_update_payment( $params );
        $this->assertEqual( $participantPayment['is_error'], 1 );
    }

    function testParticipantPaymentUpdateMissingParticipantId()
    {
        //WithoutParticipantId
        $params = array(
                        'payment_entity_table' => 'civicrm_contribute',
                        'payment_entity_id'    => '3'
                        );        
        $participantPayment = & civicrm_participant_update_payment( $params );
        $this->assertEqual( $participantPayment['is_error'], 1 );
    }

     function testParticipantPaymentUpdateMissingPaymentEntityId()
     {
         $params = array(
                         'participant_id'       => $this->_participantID,
                         'payment_entity_table' => 'civicrm_contribute'                
                         );        
         $participantPayment = & civicrm_participant_update_payment( $params );
         $this->assertEqual( $participantPayment['is_error'], 1 );
     }
    
     function testUpdateParticipantPayment()
     {
         //Do payment
         $this->_participantPaymentID = $this->participantPaymentCreate( $this->_participantID );

         //Create contribution type & get contribution Type ID
         $contributionTypeID = $this->contributionTypeCreate();
         
         //Create Contribution & get entity ID
         $entityID = $this->contributionCreate( $this->_contactID , $contributionTypeID );

         $params = array(
                         'id'                   => $this->_participantPaymentID,
                         'participant_id'       => $this->_participantID,
                         'payment_entity_table' => 'civicrm_contribute',           
                         'payment_entity_id'    => $entityID
                         );
        
         // Update Payment
         $participantPayment = & civicrm_participant_update_payment( $params );
         
         $this->assertEqual( $participantPayment['is_error'], 0 );
         
         //delete created contribution
         $this->contributionDelete( $entityID );

         // delete created contribution type
         $this->contributionTypeDelete( $contributionTypeID );
     }

    function tearDown() 
    {
        $this->participantDelete( $this->_participantID );
        $this->contactDelete( $this->_contactID );
    }

}