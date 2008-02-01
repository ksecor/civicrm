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
        $participantPayment = & civicrm_participant_payment_create( $params );
        $this->assertEqual( $participantPayment['is_error'], 1 );
    }

    function testParticipantPaymentUpdateMissingParticipantId()
    {
        //WithoutParticipantId
        $params = array(
                        'contribution_id'    => '3'
                        );        
        $participantPayment = & civicrm_participant_payment_create( $params );
        $this->assertEqual( $participantPayment['is_error'], 1 );
    }

    function testParticipantPaymentUpdateMissingContributionId()
    {
        $params = array(
                        'participant_id'       => $this->_participantID,
                        );        
        $participantPayment = & civicrm_participant_payment_create( $params );
        $this->assertEqual( $participantPayment['is_error'], 1 );
    }
    
    function testParticipantPaymentUpdate()
    {
        //create Contribution type 

        $contributionTypeID = $this->contributionTypeCreate();
        
        //Create Contribution & get contribution ID
        $contributionID     = $this->contributionCreate( $this->_contactID , $contributionTypeID );
        $this->_participantPaymentID = $this->participantPaymentCreate( $this->_participantID );
        $params = array(
                        'id'              => $this->_participantPaymentID,
                        'participant_id'  => $this->_participantID,
                        'contribution_id' => $contributionID
                        );
        
        // Update Payment
        $participantPayment = & civicrm_participant_payment_create( $params );
        $this->assertEqual( $participantPayment['id'],$this->_participantPaymentID );
        $this->assertEqual( $participantPayment['is_error'], 0 );
        $this->assertTrue ( array_key_exists( 'id', $participantPayment ) );
        
        $params = array( 'id' => $this->_participantPaymentID );         
        $deletePayment = & civicrm_participant_payment_delete( $params );   
        $this->assertEqual( $deletePayment['is_error'], 0 );
        
        $this->contributionDelete( $contributionID );
        $this->contributionTypeDelete( $contributionTypeID );
        
    }
    
    function tearDown() 
    {
        $this->participantDelete( $this->_participantID );
        $this->contactDelete( $this->_contactID );
    }
}
?>