<?php

require_once 'api/crm.php';

class TestOfGetEvent extends UnitTestCase 
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
                        'max_participants'         => '15',
                        'is_monetary'              => '0', 
                        'contribution_type_id'     => '0', 
                        'is_map'                   => '0', 
                        'is_active'                => '1' 
                        );
	
        $this->_event = & crm_create_event($params);
    }

    function testGetWrongEventEmptyParams()
    {
        $params = array();                        
        $event = & crm_get_event($params);
        $this->assertIsA($event,'CRM_Core_Error');
    }
    
    function testGetWrongEventWithoutId()
    {
        $params = array(
                        'title'                    => 'Annual Function',
                        'description'              => 'Award ceremony and cultural events',
                        'event_type_id'            => '3',
                        'is_public'                => '1',
                        'start_date'               => '20070219',
                        'is_online_registration'   => '0',
                        'max_participants'         => '15',
                        'is_monetary'              => '0', 
                        'contribution_type_id'     => '0', 
                        'is_map'                   => '0', 
                        'is_active'                => '1' 
                        );
        
        $event = & crm_get_event($params);
        $this->assertIsA($event,'CRM_Core_Error');
    }

    function testGetEvent()
    {
        $id = $this->_event['id'];
        $params = array(
                        'id'          => $id,
                        'description' => 'Award ceremony and cultural events',
                        );
        
        $event = & crm_get_event($params);

        $this->assertEqual($event[$id]['title'],'Annual Function');
        $this->assertEqual($event[$id]['description'],'Award ceremony and cultural events');
        $this->assertEqual($event[$id]['event_type_id'],'3');
        $this->assertEqual($event[$id]['is_public'],'1');
        $this->assertEqual($event[$id]['start_date'],'2007-02-19 00:00:00');
        $this->assertEqual($event[$id]['is_online_registration'],'0');
        $this->assertEqual($event[$id]['max_participants'],'15');
        $this->assertEqual($event[$id]['is_monetary'],'0');
        $this->assertEqual($event[$id]['contribution_type_id'],'0');
        $this->assertEqual($event[$id]['is_map'],'0');
        $this->assertEqual($event[$id]['is_active'],'1');                               
    }    
    
    function testDeleteEvent()
    {
        $val = &crm_delete_event($this->_event['id']);
        $this->assertTrue($val);
    }
}
