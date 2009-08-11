<?php

require_once 'api/v2/Event.php';
require_once 'CiviTest/CiviUnitTestCase.php';

class api_v2_EventGetTest extends CiviUnitTestCase 
{
    private $_event;
   
    function get_info( )
    {
        return array(
                     'name'        => 'Event Get',
                     'description' => 'Test all Event Get API methods.',
                     'group'       => 'CiviCRM API Tests',
                     );
    }  

    function setUp( )
    {
        parent::setUp();

        $this->_event = $this->eventCreate( );
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
    
    function tearDown( )
    {
        $this->eventDelete( $this->_event['event_id'] );
    }
}
