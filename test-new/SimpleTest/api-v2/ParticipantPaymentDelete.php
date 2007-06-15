<?php

require_once 'api/v2/Participant.php';

class TestOfParticipantPaymentDeleteAPIV2 extends CiviUnitTestCase 
{
    protected $_contactID;
    protected $_participantID;
    protected $_participantPaymentID;
           
    function setUp() 
    {
        $this->_contactID     = $this->organizationCreate( );
        $this->_participantID = $this->participantCreate( $this->_contactID );
    }
    
    function testParticipantPaymentDeleteWithEmptyParams()
    {
        $params = array();        
        $deletePayment = & civicrm_participant_payment_delete( $params );
        $this->assertEqual( $deletePayment['is_error'], 1 );
    }
    
    function testParticipantPaymentDeleteWithWrongID()
    {
        $params = array( 'id' => -1 );        
        $deletePayment = & civicrm_participant_payment_delete( $params );
        $this->assertEqual( $deletePayment['is_error'], 1 );
    }

    function testParticipantPaymentDelete()
    {
        $this->_participantPaymentID = $this->participantPaymentCreate( $this->_participantID );
       
        $params = array( 'id' => $this->_participantPaymentID );        
        $deletePayment = & civicrm_participant_payment_delete( $params );
        $this->assertEqual( $deletePayment['is_error'], 0 );
    }

    function tearDown() 
    {
        $this->participantDelete( $this->_participantID );
        $this->contactDelete( $this->_contactID );
    }

}
?>
