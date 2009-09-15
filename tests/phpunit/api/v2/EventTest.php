<?php

require_once 'api/v2/Event.php';
require_once 'CiviTest/CiviUnitTestCase.php';

class api_v2_EventTest extends CiviUnitTestCase 
{
    protected $_params;
    
    function get_info( )
    {
        return array(
                     'name'        => 'Event Create',
                     'description' => 'Test all Event Create API methods.',
                     'group'       => 'CiviCRM API Tests',
                     );
    }  

    function setUp() 
    {
        parent::setUp();
    
        $this->_params = array(
            'title'                   => 'Annual CiviCRM meet',
            'summary'                 => 'If you have any CiviCRM realted issues or want to track where CiviCRM is heading, Sign up now',
            'description'             => 'This event is intended to give brief idea about progess of CiviCRM and giving solutions to common user issues',
            'event_type_id'           => 1,
            'is_public'               => 1,
            'start_date'              => 20081021,
            'end_date'                => 20081023,
            'is_online_registration'  => 1,
            'registration_start_date' => 20080601,
            'registration_end_date'   => 20081015,
            'max_participants'        => 100,
            'event_full_text'         => 'Sorry! We are already full',
            'is_monetory'             => 0, 
            'is_active'               => 1,
            'is_show_location'        => 0,
        );

        $params = array(
                        'title'         => 'Annual CiviCRM meet',
                        'event_type_id' => 1,
                        'start_date'    => 20081021,
                        );

        $this->_event   = civicrm_event_create($params);
        $this->_eventId = $this->_event['event_id'];
    }


    function testGetEventEmptyParams( )
    {
        $params = array( );
        
        $result = civicrm_event_get( $params );
        
        $this->assertEquals( $result['is_error'], 1 );
        $this->assertEquals( $result['error_message'], 'Params is not an array' );
    }
    
    function testGetEventById( )
    {
        $params = array( 'id' => $this->_event['event_id'] );
        
        $result = civicrm_event_get( $params );
        $this->assertEquals( $result['event_title'], 'Annual CiviCRM meet' );
    }
    
    function testGetEventByEventTitle( )
    {
        $params = array( 'title' => 'Annual CiviCRM meet' );
        
        $result = civicrm_event_get( $params );
        $this->assertEquals( $result['id'], $this->_event['event_id'] );
    }
    
    function testCreateEventParamsNotArray( )
    {
        $params = null;
        $result = civicrm_event_create( $params );
        $this->assertEquals( $result['is_error'], 1 );
        $this->assertNotEquals( $result['error_message'], 'Missing require fields ( title, event type id,start date)');
        $this->assertEquals( $result['error_message'], 'Params is not an array');
    }    
    
    function testCreateEventEmptyParams( )
    {
        $params = array( );
        $result = civicrm_event_create( $params );
        $this->assertEquals( $result['is_error'], 1 );
        $this->assertFalse( array_key_exists( 'event_id', $result ) );
        $this->assertEquals( $result['error_message'], 'Missing require fields ( title, event type id,start date)');
    }
    
    function testCreateEventParamsWithoutTitle( )
    {
        unset($this->_params['title']);
        $result = civicrm_event_create( $this->_params );
        $this->assertEquals( $result['is_error'], 1 );
        $this->assertEquals( $result['error_message'], 'Missing require fields ( title, event type id,start date)');
    }
    
    function testCreateEventParamsWithoutEventTypeId( )
    {
        unset($this->_params['event_type_id']);
        $result = civicrm_event_create( $this->_params );
        $this->assertEquals( $result['is_error'], 1 );
        $this->assertEquals( $result['error_message'], 'Missing require fields ( title, event type id,start date)');
    }
    
    function testCreateEventParamsWithoutStartDate( )
    {
        unset($this->_params['start_date']);
        $result = civicrm_event_create( $this->_params );
        $this->assertEquals( $result['is_error'], 1 );
        $this->assertEquals( $result['error_message'], 'Missing require fields ( title, event type id,start date)');
    }
    
    function testCreateEvent( )
    {
        $result = civicrm_event_create( $this->_params );
        
        $this->assertNotEquals( $result['is_error'], 1 );
        $this->assertTrue( array_key_exists( 'event_id', $result ) );
        
        civicrm_event_delete( $result );
    }

    function testDeleteWithoutEventId( )
    {
        $params = array( );
        $result =& civicrm_event_delete($params);
        $this->assertEquals($result['is_error'], 1);
        
        // delete the event created for testing
        $event  = array( 'event_id' => $this->_eventId );
        $result = civicrm_event_delete( $event );
    }
    
    function testDelete( )
    {
        $params = array('event_id' => $this->_eventId);
        $result =& civicrm_event_delete($params);
        $this->assertNotEquals($result['is_error'], 1);
    }
    
    function testDeleteWithWrongEventId( )
    {
        $params = array('event_id' => $this->_eventId);
        $result =& civicrm_event_delete($params);
        // try to delete again - there's no such event anymore
        $params = array('event_id' => $this->_eventId);
        $result =& civicrm_event_delete($params);
        $this->assertEquals($result['is_error'], 1);
    }
    
    function tearDown() 
    {
        if ( $this->_eventId ) {
            $this->eventDelete( $this->_eventId );
        }        
        $this->eventDelete( $this->_event['event_id'] );	
    }
}

