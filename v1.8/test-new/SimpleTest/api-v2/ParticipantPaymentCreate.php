<?php

require_once 'api/v2/Participant.php';

class TestOfParticipantPaymentCreateAPIV2 extends CiviUnitTestCase 
{
    protected $_contactID;
    protected $_participantID;
    
    function setUp( ) 
    { 
        $this->_contactID     = $this->organizationCreate( );
        $this->_participantID = $this->participantCreate( $this->_contactID );
    }
    
    function testParticipantPaymentCreateWithEmptyParams( )
    {
        $params = array();        
        $participantPayment = & civicrm_participant_payment_create( $params );
        $this->assertEqual( $participantPayment['is_error'], 1 );
    }
    
    function testParticipantPaymentCreateMissingParticipantId( )
    {
        
        //Create contribution type & get contribution Type ID
        $contributionTypeID = $this->contributionTypeCreate();
        
        //Create Contribution & get entity ID
        $entityID = $this->contributionCreate( $this->_contactID , $contributionTypeID );
        
        //WithoutParticipantId
        $params = array(
                        'payment_entity_table' => 'civicrm_contribute',
                        'payment_entity_id'    => $entityID
                        );        
        $participantPayment = & civicrm_participant_payment_create( $params );
        $this->assertEqual( $participantPayment['is_error'], 1 );
        
        //delete created contribution
        $this->contributionDelete( $entityID );
        
        // delete created contribution type
        $this->contributionTypeDelete( $contributionTypeID );
    }
    
    function testParticipantPaymentCreateMissingPaymentEntityId( )
    {
        //Without Payment EntityID
        $params = array(
                        'participant_id'       => $this->_participantID,
                        'payment_entity_table' => 'civicrm_contribute'                
                        );        
        $participantPayment = & civicrm_participant_payment_create( $params );
        $this->assertEqual( $participantPayment['is_error'], 1 );
    }
    
    function testParticipantPaymentCreate( )
    {  
        
        //Create contribution type & get contribution Type ID
        $contributionTypeID = $this->contributionTypeCreate();
        
        //Create Contribution & get entity ID
        $entityID = $this->contributionCreate( $this->_contactID , $contributionTypeID );
        
        //Create Participant Payment record With Values
        $params = array(
                        'participant_id'       => $this->_participantID,
                        'payment_entity_table' => 'civicrm_contribute',           
                        'payment_entity_id'    => $entityID
                        );
        
        $participantPayment = & civicrm_participant_payment_create( $params );
        
        $this->assertEqual( $participantPayment['is_error'], 0 );
        $this->assertTrue( array_key_exists( 'id', $participantPayment ) );
        
        //delete created contribution
        $this->contributionDelete( $entityID );
        
        // delete created contribution type
        $this->contributionTypeDelete( $contributionTypeID );
    }
    
    function tearDown( ) 
    {
        $this->participantDelete( $this->_participantID );
        $this->contactDelete( $this->_contactID );
    }
}    
?>
