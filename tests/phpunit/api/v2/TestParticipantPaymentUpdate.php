<?php

require_once 'api/v2/Participant.php';

class api_v2_TestParticipantPaymentUpdate extends CiviUnitTestCase 
{

    protected $_contactID;
    protected $_participantID;
    protected $_participantPaymentID;
    protected $_eventID;

    function get_info( )
    {
        return array(
                     'name'        => 'Participant Payment Update',
                     'description' => 'Test all Participant Payment Update API methods.',
                     'group'       => 'CiviCRM API Tests',
                     );
    }

    function setUp() 
    {
        $event = $this->eventCreate();
        $this->_eventID = $event['event_id'];

        $this->_contactID       = $this->organizationCreate( );
        $this->_participantID   = $this->participantCreate( array ('contactID' => $this->_contactID,'eventID' =>$this->_eventID ) );
    }
    
    function testParticipantPaymentUpdateEmpty()
    {
        $params = array();        
        $participantPayment = & civicrm_participant_payment_create( $params );
        $this->assertEquals( $participantPayment['is_error'], 1 );
    }

    function testParticipantPaymentUpdateMissingParticipantId()
    {
        //WithoutParticipantId
        $params = array(
                        'contribution_id'    => '3'
                        );        
        $participantPayment = & civicrm_participant_payment_create( $params );
        $this->assertEquals( $participantPayment['is_error'], 1 );
    }

    function testParticipantPaymentUpdateMissingContributionId()
    {
        $params = array(
                        'participant_id'       => $this->_participantID,
                        );        
        $participantPayment = & civicrm_participant_payment_create( $params );
        $this->assertEquals( $participantPayment['is_error'], 1 );
    }
    
    function testParticipantPaymentUpdate()
    {
        //create contribution type 
        
        $contributionTypeID = $this->contributionTypeCreate();
        
        // create contribution
        $contributionID     = $this->contributionCreate( $this->_contactID , $contributionTypeID );
        
        $this->_participantPaymentID = $this->participantPaymentCreate( $this->_participantID, $contributionID );
        $params = array(
                        'id'              => $this->_participantPaymentID,
                        'participant_id'  => $this->_participantID,
                        'contribution_id' => $contributionID
                        );
        
        // Update Payment
        $participantPayment = & civicrm_participant_payment_create( $params );
        $this->assertEquals( $participantPayment['id'],$this->_participantPaymentID );
        $this->assertEquals( $participantPayment['is_error'], 0 );
        $this->assertTrue ( array_key_exists( 'id', $participantPayment ) );
        
        $params = array( 'id' => $this->_participantPaymentID );         
        $deletePayment = & civicrm_participant_payment_delete( $params );   
        $this->assertEquals( $deletePayment['is_error'], 0 );
        
        $this->contributionDelete( $contributionID );
        $this->contributionTypeDelete( $contributionTypeID );
    }
    
    function tearDown() 
    {
        $this->participantDelete( $this->_participantID );
        $this->contactDelete( $this->_contactID );

        // Cleanup test event.
        $result = $this->eventDelete($this->_eventID);
    }
}