<?php

require_once 'api/crm.php';

class TestOfUpdateEventAPIV2 extends UnitTestCase 
{
    protected $_event   = array();
       
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }

    function testCreateEvent()
    {
        $params = array(
                        'title'                    => 'Annual Function',
                        'description'              => 'Award ceremony and cultural events',
                        'event_type_id'            => '3',
                        'is_public'                => '1',
                        'start_date'               => '20070219',
                        'is_online_registration'   => '0',
                        'max_participants'         => '150',
                        'is_monetary'              => '0', 
                        'contribution_type_id'     => '0', 
                        'is_map'                   => '0', 
                        'is_active'                => '1' 
                        );
	
        $this->_event = & civicrm_event_create($params);
        $this->assertNotNull( $this->_event['event_id'] ); 
    }   
     
    function testUpdateWrongEventEmptyParams()
    {
        $params = array();                        
        $event = & civicrm_event_update($params);
        $this->assertEqual( $event['is_error'], 1 );
    }

    function testUpdateWrongEventWithoutId()
    {
        $params = array(
                        'title'                    => 'Annual Function 2007',
                        'description'              => 'Award ceremony and cultural events & lots of fun',
                        'event_type_id'            => '3',
                        'is_public'                => '1',
                        'start_date'               => '20070219',
                        'is_online_registration'   => '0',
                        'max_participants'         => '150',
                        'is_monetary'              => '0', 
                        'contribution_type_id'     => '0', 
                        'is_map'                   => '0', 
                        'is_active'                => '1' 
                        );
        
        $event = & civicrm_event_update($params);
        $this->assertEqual( $event['is_error'], 1 );
    }

    function testUpdateEvent()
    {
        $params = array(
                        'id'                        => $this->_event['event_id'],
                        'title'                    => 'Annual Function',
                        'summary'                  => 'Regular function', 
                        'description'              => 'Award ceremony and cultural events',
                        'event_type_id'            => '3',
                        'is_public'                => '1',
                        'start_date'               => '20070219',
                        'is_online_registration'   => '0',
                        'max_participants'         => '150',
                        'is_monetary'              => '0', 
                        'contribution_type_id'     => '0', 
                        'is_map'                   => '0', 
                        'is_active'                => '1' 
                        );
        
        $event = & civicrm_event_update($params);
        $this->assertEqual($event['title'],'Annual Function');
        $this->assertEqual($event['summary'],'Regular function');
        $this->assertEqual($event['description'],'Award ceremony and cultural events');
        $this->assertEqual($event['event_type_id'],'3');
        $this->assertEqual($event['is_public'],'1');
        $this->assertEqual($event['start_date'],'2007-02-19 00:00:00');
        $this->assertEqual($event['is_online_registration'],'0');
        $this->assertEqual($event['is_monetary'],'0');
        $this->assertEqual($event['contribution_type_id'],'0');
        $this->assertEqual($event['is_map'],'0');
        $this->assertEqual($event['is_active'],'1');
    }

    function testDeleteEvent()
    {
        $val = &civicrm_event_delete($this->_event['event_id']);
        $this->assertTrue($val);
    }

}
