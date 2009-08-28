<?php

require_once 'api/v2/Participant.php';
require_once 'CiviTest/CiviUnitTestCase.php';

class api_v2_ParticipantTest extends CiviUnitTestCase 
{
    protected $_contactID;
    protected $_createdParticipants;
    protected $_participantID;
    protected $_eventID;

    function get_info( )
    {
        return array(
                     'name'        => 'Participant Create',
                     'description' => 'Test all Participant Create API methods.',
                     'group'       => 'CiviCRM API Tests',
                     );
    } 
    
    function setUp() 
    {
        parent::setUp();
    
        $event = $this->eventCreate();
        $this->_eventID = $event['event_id'];
        
        $this->_contactID = $this->individualCreate( ) ;
        $this->_createdParticipants = array( );
        $this->_individualId = $this->individualCreate();        

        $this->_participantID = $this->participantCreate( array('contactID' => $this->_contactID,'eventID' => $this->_eventID  ));
        $this->_contactID2 = $this->individualCreate( ) ;
        $this->_participantID2 = $this->participantCreate( array('contactID' => $this->_contactID2,'eventID' => $this->_eventID ));
        $this->_participantID3 = $this->participantCreate( array ('contactID' => $this->_contactID, 'eventID' => $this->_eventID ) );        
        
        $this->_failureCase = 0;
    }
    
    function tearDown()
    {
        // Cleanup all created participant records.
        foreach ( $this->_createdParticipants as $id ) {
            $result = $this->participantDelete( $this->_participantID );
        }
        // Cleanup test contact
        $result = $this->contactDelete( $this->_contactID ); 

	// Cleanup test event
	if ( $this->_eventID ) {
	    $this->eventDelete( $this->_eventID );
	}
    }

    function testParticipantGetParticipantIdOnly()
    {
        $params = array(
                        'participant_id'      => $this->_participantID,
                        );
        $participant = & civicrm_participant_get($params);
        $this->assertEquals($participant['event_id'], $this->_eventID);
        $this->assertEquals($participant['participant_register_date'], '2007-02-19 00:00:00');
        $this->assertEquals($participant['participant_source'],'Wimbeldon');
    }

    function testParticipantGetContactIdOnly()
    {
        $params = array(
                        'contact_id'      => $this->_contactID,
                        );
        $participant = & civicrm_participant_get($params);
        $this->assertEquals($participant['participant_id'],$this->_participantID);
        $this->assertEquals($participant['event_id'], $this->_eventID);
        $this->assertEquals($participant['participant_register_date'], '2007-02-19 00:00:00');
        $this->assertEquals($participant['participant_source'],'Wimbeldon');
    }
    

    function testParticipantGetMultiMatchReturnFirst()
    {
        $params = array(
                        'event_id'      => $this->_eventID,
                        'returnFirst'   => 1,
                        );
      
        $participant = & civicrm_participant_get($params);
      
        $this->assertNotNull($participant['participant_id']);
       
    }

    // This should return an error because there will be at least 2 participants. 
    function testParticipantGetMultiMatchNoReturnFirst()
    {
        $params = array(
                        'event_id'      => $this->_eventID,
                        );
        $participant = & civicrm_participant_get($params);
      
        $this->assertEquals( $participant['is_error'],1 );
        $this->assertNotNull($participant['error_message']);
    }    


    function testParticipantSearchParticipantIdOnly()
    {
        $params = array(
                        'participant_id'      => $this->_participantID,
                        );
        $participant = & civicrm_participant_search($params);
        $this->assertEquals($participant[$this->_participantID]['event_id'], $this->_eventID);
        $this->assertEquals($participant[$this->_participantID]['participant_register_date'], '2007-02-19 00:00:00');
        $this->assertEquals($participant[$this->_participantID]['participant_source'],'Wimbeldon');
    }
    
    function testParticipantSearchContactIdOnly()
    {
        // Should get 2 participant records for this contact.
        $params = array(
                        'contact_id'      => $this->_contactID,
                        );
        $participant = & civicrm_participant_search($params);
        $this->assertEquals( count( $participant ), 3 );
    }
    
    
    function testParticipantSearchByEvent()
    {
        // Should get >= 3 participant records for this event. Also testing that last_name and event_title are returned.
        $params = array(
                        'event_id'      => $this->_eventID,
                        'return.last_name' => 1,
                        'return.event_title' => 1,
                        );
        $participant = & civicrm_participant_search($params);
        if ( count( $participant ) < 3 ) {
            $this->fail("Event search returned less than expected miniumum of 3 records.");
        }
        
        $this->assertEquals($participant[$this->_participantID]['last_name'],'Anderson');
        $this->assertEquals($participant[$this->_participantID]['event_title'],'Annual CiviCRM meet');        
    }
    

    function testParticipantSearchByEventWithLimit()
    {
        // Should 2 participant records since we're passing rowCount = 2.
        $params = array(
                        'event_id'      => $this->_eventID,
                        'rowCount'      => 3,
                        );
        $participant = & civicrm_participant_search($params);
               
        $this->assertEquals( count( $participant ), 3 );
    }


    function testParticipantCreateMissingContactID()
    {
        $params = array(
                        'event_id'      => $this->_eventID,
                        );
        $participant = & civicrm_participant_create($params);
        if ( CRM_Utils_Array::value('id', $participant) ) {
            $this->_createdParticipants[] = $participant['id'];
        }
        $this->assertEquals( $participant['is_error'],1 );
        $this->assertNotNull($participant['error_message']);
    }

    function testParticipantCreateMissingEventID()
    {
        $params = array(
                        'contact_id'    => $this->_contactID,
                        );
        $participant = & civicrm_participant_create($params); 
        if ( CRM_Utils_Array::value('id', $participant) ) {
            $this->_createdParticipants[] = $participant['id'];
        }
        $this->assertEquals( $participant['is_error'],1 );
        $this->assertNotNull($participant['error_message']);
    }
    
    function testParticipantCreateEventIdOnly()
    {
        $params = array(
                        'contact_id'    => $this->_contactID,
                        'event_id'      => $this->_eventID,
                        );
        $participant = & civicrm_participant_create($params);
        $this->assertNotEquals( $participant['is_error'],1 );
        $this->_participantID = $participant['result'];
        
        if ( ! $participant['is_error'] ) {
            $this->_createdParticipants[] = CRM_Utils_Array::value('result', $participant);
            // Create $match array with DAO Field Names and expected values
            $match = array(
                           'id'                         => CRM_Utils_Array::value('result', $participant)
                           );
            // assertDBState compares expected values in $match to actual values in the DB              
            $this->assertDBState( 'CRM_Event_DAO_Participant', $participant['result'], $match ); 
        }
    }
    
    function testParticipantCreateAllParams()
    {  
        $params = array(
                        'contact_id'    => $this->_contactID,
                        'event_id'      => $this->_eventID,
                        'status_id'     => 1,
                        'role_id'       => 1,
                        'register_date' => '2007-07-21',
                        'source'        => 'Online Event Registration: API Testing',
                        'event_level'   => 'Tenor'                        
                        );
        
        $participant = & civicrm_participant_create($params);
        $this->assertNotEquals( $participant['is_error'],1 );
        $this->_participantID = $participant['result'];
        if ( ! $participant['is_error'] ) {
            $this->_createdParticipants[] = CRM_Utils_Array::value('result', $participant);
            
            // Create $match array with DAO Field Names and expected values
            $match = array(
                           'id'         => CRM_Utils_Array::value('result', $participant)
                           );
            // assertDBState compares expected values in $match to actual values in the DB              
            $this->assertDBState( 'CRM_Event_DAO_Participant', $participant['result'], $match ); 
        }
    }

    function testParticipantUpdateEmptyParams()
    {
        $params = array();        
        $participant = & civicrm_participant_create($params);  
        $this->assertEquals( $participant['is_error'],1 );
        $this->assertEquals( $participant['error_message'],'Required parameter missing' );
    }

    function testParticipantUpdateWithoutEventId()
    {  
        $participantId = $this->participantCreate( array ('contactID' => $this->_individualId, 'eventID' => $this->_eventID  ) );
        $params = array(
                        'contact_id'    => $this->_individualId,
                        'status_id'     => 3,
                        'role_id'       => 3,
                        'register_date' => '2006-01-21',
                        'source'        => 'US Open',
                        'event_level'   => 'Donation'                        
                        );
        $participant = & civicrm_participant_create($params);
        $this->assertEquals( $participant['is_error'], 1 );
        $this->assertEquals( $participant['error_message'],'Required parameter missing' );
        // Cleanup created participant records.
        $result = $this->participantDelete( $participantId );
    }

    function testParticipantUpdate()
    {  
        $participantId = $this->participantCreate( array ('contactID' => $this->_individualId,'eventID' => $this->_eventID ) );
        $params = array(
                        'id'            => $participantId,
                        'contact_id'    => $this->_individualId,
                        'event_id'      => $this->_eventID,
                        'status_id'     => 3,
                        'role_id'       => 3,
                        'register_date' => '2006-01-21',
                        'source'        => 'US Open',
                        'event_level'   => 'Donation'                        
                        );
        $participant = & civicrm_participant_create($params);
        $this->assertNotEquals( $participant['is_error'],1 );
        
        if ( ! $participant['is_error'] ) {
            $params['id'] = CRM_Utils_Array::value('result', $participant);
            
            // Create $match array with DAO Field Names and expected values
            $match = array(
                           'id'         => CRM_Utils_Array::value('result', $participant)
                           );
            // assertDBState compares expected values in $match to actual values in the DB              
            $this->assertDBState( 'CRM_Event_DAO_Participant', $participant['result'], $match );
        }
        // Cleanup created participant records.
        $result = $this->participantDelete( $params['id'] );
    }

    function testParticipantDelete()
    {
        $params = array(
                        'id' => $this->_participantID,
                        );
        $participant = & civicrm_participant_delete($params);
        $this->assertNotEquals( $participant['is_error'],1 );
        $this->assertDBState( 'CRM_Event_DAO_Participant', $this->_participantID, NULL, true ); 

    }
    
   
    // This should return an error because required param is missing.. 
    function testParticipantDeleteMissingID()
    {
        $params = array(
                        'event_id'      => $this->_eventID,
                        );
        $participant = & civicrm_participant_delete($params);
        $this->assertEquals( $participant['is_error'],1 );
        $this->assertNotNull($participant['error_message']);
        $this->_failureCase = 1;
    }

    function testParticipantPaymentCreateWithEmptyParams( )
    {
        $params = array();        
        $participantPayment = & civicrm_participant_payment_create( $params );
        $this->assertEquals( $participantPayment['is_error'], 1 );
    }
    
    function testParticipantPaymentCreateMissingParticipantId( )
    {        
        //Create contribution type & get contribution Type ID
        $contributionTypeID = $this->contributionTypeCreate();
        
        //Create Contribution & get entity ID
        $contributionID = $this->contributionCreate( $this->_contactID , $contributionTypeID );
        
        //WithoutParticipantId
        $params = array(
                        'contribution_id'    => $contributionID
                        );        
        $participantPayment = & civicrm_participant_payment_create( $params );
        $this->assertEquals( $participantPayment['is_error'], 1 );
        
        //delete created contribution
        $this->contributionDelete( $contributionID );
        
        // delete created contribution type
        $this->contributionTypeDelete( $contributionTypeID );
    }
    
    function testParticipantPaymentCreateMissingContributionId( )
    {
        //Without Payment EntityID
        $params = array(
                        'participant_id'       => $this->_participantID,
                        );        
        $participantPayment = & civicrm_participant_payment_create( $params );
        $this->assertEquals( $participantPayment['is_error'], 1 );
    }
    
    function testParticipantPaymentCreate( )
    {  
        
        //Create contribution type & get contribution Type ID
        $contributionTypeID = $this->contributionTypeCreate();
        
        //Create Contribution & get contribution ID
        $contributionID = $this->contributionCreate( $this->_contactID , $contributionTypeID );
        
        //Create Participant Payment record With Values
        $params = array(
                        'participant_id'  => $this->_participantID,
                        'contribution_id' => $contributionID
                        );
        $participantPayment = & civicrm_participant_payment_create( $params );
        $this->assertEquals( $participantPayment['is_error'], 0 );
        $this->assertTrue( array_key_exists( 'id', $participantPayment ) );
        
        //delete created contribution
        $this->contributionDelete( $contributionID );
        
        // delete created contribution type
        $this->contributionTypeDelete( $contributionTypeID );
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

    function testParticipantPaymentDeleteWithEmptyParams()
    {
        $params = array();        
        $deletePayment = & civicrm_participant_payment_delete( $params ); 
        $this->assertEquals( $deletePayment['is_error'], 1 );
        $this->assertEquals( $deletePayment['error_message'], 'Invalid or no value for Participant payment ID' );
    }
    
    function testParticipantPaymentDeleteWithWrongID()
    {
        $params = array( 'id' => 0 );        
        $deletePayment = & civicrm_participant_payment_delete( $params ); 
        $this->assertEquals( $deletePayment['is_error'], 1 );
        $this->assertEquals( $deletePayment['error_message'], 'Invalid or no value for Participant payment ID' );
    }

    function testParticipantPaymentDelete()
    {
        // create contribution type 
        
        $contributionTypeID = $this->contributionTypeCreate();
        
        // create contribution
        $contributionID     = $this->contributionCreate( $this->_contactID , $contributionTypeID );
        
        $this->_participantPaymentID = $this->participantPaymentCreate( $this->_participantID, $contributionID );
        
        $params = array( 'id' => $this->_participantPaymentID );         
        $deletePayment = & civicrm_participant_payment_delete( $params );   
        $this->assertEquals( $deletePayment['is_error'], 0 );
        
        $this->contributionDelete( $contributionID );
        $this->contributionTypeDelete( $contributionTypeID );
    }    

    
}
