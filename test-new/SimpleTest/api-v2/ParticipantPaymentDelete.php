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
        $id = -1;      
        $deletePayment = & civicrm_participant_payment_delete( $id );
        $this->assertEqual( $deletePayment['is_error'], 1 );
    }

    function testParticipantPaymentDelete()
    {
        $this->_participantPaymentID = $this->participantPaymentCreate( $this->_participantID );
        $deletePayment = & civicrm_participant_payment_delete( $this->_participantPaymentID );
        $this->assertEqual( $deletePayment['is_error'], 0 );
    }

    function tearDown() 
    {
        $this->participantDelete( $this->_participantID );
        $this->contactDelete( $this->_contactID );
    }

}
?>
