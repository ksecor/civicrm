<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.0                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

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

    function tearDown() 
    {
        if ( $this->_eventId ) {
            $this->eventDelete( $this->_eventId );
        }        
        $this->eventDelete( $this->_event['event_id'] );	
    }

///////////////// civicrm_event_get methods

    function testGetWrongParamsType()
    {
        $params = 'Annual CiviCRM meet';
        $result = civicrm_event_get( $params );

        $this->assertEquals( $result['is_error'], 1 );
        $this->assertEquals( $result['error_message'], 'Params is not an array' );
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

///////////////// civicrm_event_create methods
    
    function testCreateEventParamsNotArray( )
    {
        $params = null;
        $result = civicrm_event_create( $params );
        $this->assertEquals( $result['is_error'], 1 );
        $this->assertEquals( $result['error_message'], 'Params is not an array');
    }    
    
    function testCreateEventEmptyParams( )
    {
        $params = array( );
        $result = civicrm_event_create( $params );
        $this->assertEquals( $result['is_error'], 1 );
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

        $this->assertEquals( $result['is_error'], 0 );
        $this->assertArrayHasKey( 'event_id', $result );
    }

///////////////// civicrm_event_delete methods

    function testDeleteWrongParamsType()
    {
        $params = 'Annual CiviCRM meet';
        $result =& civicrm_event_delete($params);

        $this->assertEquals($result['is_error'], 1);        
        $this->assertEquals( $result['error_message'], 'Invalid value for eventID');
    }

    function testDeleteEmptyParams( )
    {
        $params = array( );
        $result =& civicrm_event_delete($params);
        $this->assertEquals($result['is_error'], 1);        
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

///////////////// civicrm_event_search methods

    /**
     *  Test civicrm_event_search with wrong params type
     */
    function testSearchWrongParamsType()
    {
        $this->markTestIncomplete();
    }

    /**
     *  Test civicrm_event_search with empty params
     */
     function testSearchEmptyParams()
     {
         $this->markTestIncomplete();
     }

    /**
     *  Test civicrm_event_search. Success expected.
     */
     function testSearch()
     {
         $this->markTestIncomplete();
     }

    /**
     *  Test civicrm_event_search. Success expected.
     *  return.offset and return.max_results test (CRM-5266)
     */
     function testSearchEmptyParams()
     {
         $this->markTestIncomplete();
     }

}

