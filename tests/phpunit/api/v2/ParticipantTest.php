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
            $result = $this->participantDelete( $id );
        }
        // Cleanup test contact
        $result = $this->contactDelete( $this->_contactID ); 
        
        // Cleanup test event
        if ( $this->_eventID ) {
            $this->eventDelete( $this->_eventID );
        }
    }

///////////////// civicrm_participant_get methods
    
    /**
     * check with participant_id
     */
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
    
    
    /**
     * check with contact_id
     */
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
    
    /**
     * check with event_id
     * fetch first record
     */
    function testParticipantGetMultiMatchReturnFirst()
    {
        $params = array(
                        'event_id'      => $this->_eventID,
                        'returnFirst'   => 1,
                        );
      
        $participant = & civicrm_participant_get($params);
      
        $this->assertNotNull($participant['participant_id']);
       
    }
    
    /**
     * check with event_id
     * This should return an error because there will be at least 2 participants. 
     */ 
    function testParticipantGetMultiMatchNoReturnFirst()
    {
        $params = array(
                        'event_id'      => $this->_eventID,
                        );
        $participant = & civicrm_participant_get($params);
      
        $this->assertEquals( $participant['is_error'],1 );
        $this->assertNotNull($participant['error_message']);
    }    

///////////////// civicrm_participant_search methods
    
    /**
     * check with participant_id
     */
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
    
    /**
     * check with contact_id
     */
    function testParticipantSearchContactIdOnly()
    {
        // Should get 2 participant records for this contact.
        $params = array(
                        'contact_id'      => $this->_contactID,
                        );
        $participant = & civicrm_participant_search($params);
        $this->assertEquals( count( $participant ), 3 );
    }
    
    /**
     * check with event_id
     */
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
    
    /**
     * check with event_id
     * fetch with limit
     */
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

///////////////// civicrm_participant_create methods
    
    /**
     * check with event_id
     */
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

    /**
     * check with contact_id
     * without event_id
     */
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

    /**
     * check with contact_id & event_id
     */
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
                           'id' => CRM_Utils_Array::value('result', $participant)
                           );
            // assertDBState compares expected values in $match to actual values in the DB              
            $this->assertDBState( 'CRM_Event_DAO_Participant', $participant['result'], $match ); 
        }
    }
    
    /**
     * check with complete array
     */
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
    
    /**
     * check with empty array
     */
    function testParticipantCreateWithEmptyParams()
    {
        $params = array( );
        $result =& civicrm_participant_create($params);
        $this->assertEquals( $result['is_error'], 1, "In line " . __LINE__ );
    }

    /**
     * check without array
     */    
    function testParticipantCreateWithWrongParams()
    {
        $params = 'a string';
        $result =& civicrm_participant_create($params);
        $this->assertEquals( $result['is_error'], 1, "In line " . __LINE__ );
    }
    
///////////////// civicrm_participant_update methods

    /**
     * check with empty array
     */
    function testParticipantUpdateEmptyParams()
    {
        $params = array();        
        $participant = & civicrm_participant_create($params);  
        $this->assertEquals( $participant['is_error'],1 );
        $this->assertEquals( $participant['error_message'],'Required parameter missing' );
    }
    
    /**
     * check without event_id
     */
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

    /**
     * check with complete array
     */
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

    /**
     * check without array
     */
    function testParticipantUpdateWithWrongParams()
    {
        $params = 'a string';
        $participant = & civicrm_participant_create($params);
        $this->assertEquals( $participant['is_error'],1 );
        $this->assertEquals( $participant['error_message'],'Parameters is not an array' );
    }

///////////////// civicrm_participant_delete methods

    /**
     * check with participant_id
     */    
    function testParticipantDelete()
    {
        $params = array(
                        'id' => $this->_participantID,
                        );
        $participant = & civicrm_participant_delete($params);
        $this->assertNotEquals( $participant['is_error'],1 );
        $this->assertDBState( 'CRM_Event_DAO_Participant', $this->_participantID, NULL, true ); 

    }
    
    /**
     * check without participant_id
     * and with event_id
     * This should return an error because required param is missing.. 
     */
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
    
    /**
     * check without array
     */
    function testParticipantDeleteWithWrongParams()
    {
        $params = 'a string';
        $participant = & civicrm_participant_delete($params);
        $this->assertEquals( $participant['is_error'], 1 );
        $this->assertNotNull($participant['error_message'], 'Params is not an array');
    }

///////////////// civicrm_participant_payment_create methods

    /**
     * check with empty array
     */
    function testParticipantPaymentCreateWithEmptyParams( )
    {
        $params = array();        
        $participantPayment = & civicrm_participant_payment_create( $params );
        $this->assertEquals( $participantPayment['is_error'], 1 );
    }

    /**
     * check without participant_id
     */
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
    
    /**
     * check without contribution_id
     */
    function testParticipantPaymentCreateMissingContributionId( )
    {
        //Without Payment EntityID
        $params = array(
                        'participant_id'       => $this->_participantID,
                        );        
        $participantPayment = & civicrm_participant_payment_create( $params );
        $this->assertEquals( $participantPayment['is_error'], 1 );
    }
    
    /**
     * check with valid array
     */
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
    
    /**
     * check without array
     */
    function testParticipantPaymentCreateWithWrongParams( )
    {  
        $params = 'a string';        
        $participantPayment = & civicrm_participant_payment_create( $params );
        $this->assertEquals( $participantPayment['is_error'], 1 );
        $this->assertNotNull($participantPayment['error_message'], 'Params is not an array');
    }

///////////////// civicrm_participant_payment_update methods 
    
    /**
     * check with empty array
     */
    function testParticipantPaymentUpdateEmpty()
    {
        $params = array();        
        $participantPayment = & civicrm_participant_payment_update( $params );
        $this->assertEquals( $participantPayment['is_error'], 1 );
    }

    /**
     * check without array
     */
    function testParticipantPaymentUpdateEmptyWithWrongParams()
    {
        $params = 'a string';        
        $participantPayment = & civicrm_participant_payment_update( $params );
        $this->assertEquals( $participantPayment['is_error'], 1 );
        $this->assertNotNull($participantPayment['error_message'], 'Params is not an array');
    }
    
    /**
     * check with missing participant_id
     */
    function testParticipantPaymentUpdateMissingParticipantId()
    {
        //WithoutParticipantId
        $params = array(
                        'contribution_id' => '3'
                        );        
        $participantPayment = & civicrm_participant_payment_update( $params );
        $this->assertEquals( $participantPayment['is_error'], 1 );
    }

    /**
     * check with missing contribution_id
     */
    function testParticipantPaymentUpdateMissingContributionId()
    {
        $params = array(
                        'participant_id' => $this->_participantID,
                        );        
        $participantPayment = & civicrm_participant_payment_update( $params );
        $this->assertEquals( $participantPayment['is_error'], 1 );
    }

    /**
     * check with complete array
     */
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
        $participantPayment = & civicrm_participant_payment_update( $params );
        $this->assertEquals( $participantPayment['id'],$this->_participantPaymentID );
        $this->assertTrue ( array_key_exists( 'id', $participantPayment ) );
        
        $params = array( 'id' => $this->_participantPaymentID );
        $deletePayment = & civicrm_participant_payment_delete( $params );
        $this->assertEquals( $deletePayment['is_error'], 0 );
        
        $this->contributionDelete( $contributionID );
        $this->contributionTypeDelete( $contributionTypeID );
    }

///////////////// civicrm_participant_payment_delete methods 
    
    /**
     * check with empty array
     */
    function testParticipantPaymentDeleteWithEmptyParams()
    {
        $params = array();        
        $deletePayment = & civicrm_participant_payment_delete( $params ); 
        $this->assertEquals( $deletePayment['is_error'], 1 );
        $this->assertEquals( $deletePayment['error_message'], 'Invalid or no value for Participant payment ID' );
    }

    /**
     * check with wrong id
     */
    function testParticipantPaymentDeleteWithWrongID()
    {
        $params = array( 'id' => 0 );        
        $deletePayment = & civicrm_participant_payment_delete( $params ); 
        $this->assertEquals( $deletePayment['is_error'], 1 );
        $this->assertEquals( $deletePayment['error_message'], 'Invalid or no value for Participant payment ID' );
    }

    /**
     * check with valid array
     */
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

    /**
     * check without array
     */
    function testParticipantPaymentDeleteWithWrongParams()
    {
        $params = 'a string';        
        $deletePayment = & civicrm_participant_payment_delete( $params ); 
        $this->assertEquals( $deletePayment['is_error'], 1 );
        $this->assertEquals( $deletePayment['error_message'], 'Params is not an array' );
    }
}

