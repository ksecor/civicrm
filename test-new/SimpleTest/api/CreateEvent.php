<?php

require_once 'api/crm.php';

class TestOfCreateEvent extends UnitTestCase 
{
    protected $_event    = array();
    protected $_event1   = array();
    protected $_event2   = array();
        
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }

    function testCreateWrongEvent()
    {
        $params = array();        

        $event = & crm_create_event($params);
        $this->assertIsA($event,'CRM_Core_Error');
    
    }
    
    function testCreateWrongEventWithoutTitle()
    {
    
        $params = array(
                        'summary'                  => 'Regular function',
                        'description'              => 'Award ceremony and cultural events',
                        'event_type_id'            => '3',
                        'is_public'                => '1',
                        'start_date'               => '20070219',
                        'end_date'                 => '20071019',
                        'is_online_registration'   => '0',
                        'registration_link_text'   => 'link',
                        'max_participants'         => '150',
                        'event_full_text'          => 'efull', 
                        'is_monetary'              => '0', 
                        'contribution_type_id'     => '0', 
                        'is_map'                   => '0', 
                        'is_active'                => '1' 
                        );
        
        $event = & crm_create_event($params);
        $this->assertIsA($event,'CRM_Core_Error');
    
    }

    function testCreateWrongEventWithoutEventTypeId()
    {
        
        $params = array(
                        'title'                    => 'Annual Function',
                        'summary'                  => 'Regular function',
                        'description'              => 'Award ceremony and cultural events',
                        'is_public'                => '1',
                        'start_date'               => '20070219',
                        'is_online_registration'   => '1',
                        'max_participants'         => '150',
                        'is_active'                => '1' 
                        );

        $event = & crm_create_event($params);
        $this->assertIsA($event,'CRM_Core_Error');
    
    }

    function testCreateWrongEventWithoutStartDate()
    {
        
        $params = array(
                        'title'                    => 'Annual Function',
                        'summary'                  => 'Regular function',
                        'description'              => 'Award ceremony and cultural events',
                        'event_type_id'            => '3',
                        'is_public'                => '1',
                        'is_active'                => '1' 
                        );

        $event = & crm_create_event($params);
        $this->assertIsA($event,'CRM_Core_Error');
        
    }

    function testCreateEventWithoutSummary()
    {
        
        $params = array(
                        'title'                    => 'Annual Function',
                        'description'              => 'Award ceremony and cultural events',
                        'event_type_id'            => '3',
                        'is_public'                => '1',
                        'start_date'               => '20070219',
                        'end_date'                 => '20071019',
                        'is_online_registration'   => '0',
                        'registration_link_text'   => 'link',
                        'max_participants'         => '150',
                        'event_full_text'          => 'efull', 
                        'is_monetary'              => '0', 
                        'contribution_type_id'     => '0', 
                        'is_map'                   => '0', 
                        'is_active'                => '1' 
                        );
        
        $event = & crm_create_event($params);               
        $this->assertEqual($event['title'],'Annual Function');
        //$this->assertEqual($event['summary'],'Regular function');
        $this->assertEqual($event['description'],'Award ceremony and cultural events');
        $this->assertEqual($event['event_type_id'],'3');
        $this->assertEqual($event['is_public'],'1');
        $this->assertEqual($event['start_date'],'20070219');
        $this->assertEqual($event['end_date'],'20071019');
        $this->assertEqual($event['is_online_registration'],'0');
        $this->assertEqual($event['registration_link_text'],'link');
        $this->assertEqual($event['max_participants'],'150');
        $this->assertEqual($event['event_full_text'],'efull');
        $this->assertEqual($event['is_monetary'],'0');
        $this->assertEqual($event['contribution_type_id'],'0');
        $this->assertEqual($event['is_map'],'0');
        $this->assertEqual($event['is_active'],'1');
        $this->_event1 = $event;
    
    }
    
    function testCreateEventWithoutEndDate()
    {
    
        $params = array(
                        'title'                    => 'Annual Function',
                        'summary'                  => 'Regular function',
                        'description'              => 'Award ceremony and cultural events',
                        'event_type_id'            => '3',
                        'is_public'                => '1',
                        'start_date'               => '20070219',
                        'is_online_registration'   => '0',
                        'registration_link_text'   => 'link',
                        'max_participants'         => '150',
                        'event_full_text'          => 'efull', 
                        'is_monetary'              => '0', 
                        'contribution_type_id'     => '0', 
                        'is_map'                   => '0', 
                        'is_active'                => '1' 
                        );
	
        $event = & crm_create_event($params);               
        $this->assertEqual($event['title'],'Annual Function');
        $this->assertEqual($event['summary'],'Regular function');
        $this->assertEqual($event['description'],'Award ceremony and cultural events');
        $this->assertEqual($event['event_type_id'],'3');
        $this->assertEqual($event['is_public'],'1');
        $this->assertEqual($event['start_date'],'20070219');
        #$this->assertEqual($event['end_date'],'');
        $this->assertEqual($event['is_online_registration'],'0');
        $this->assertEqual($event['registration_link_text'],'link');
        $this->assertEqual($event['max_participants'],'150');
        $this->assertEqual($event['event_full_text'],'efull');
        $this->assertEqual($event['is_monetary'],'0');
        $this->assertEqual($event['contribution_type_id'],'0');
        $this->assertEqual($event['is_map'],'0');
        $this->assertEqual($event['is_active'],'1');
        $this->_event2 = $event;
    
    }

    function testCreateEvent()
    {
        
        $params = array(
                        'title'                    => 'Annual Function',
                        'summary'                  => 'Regular function',
                        'description'              => 'Award ceremony and cultural events',
                        'event_type_id'            => '3',
                        'is_public'                => '1',
                        'start_date'               => '20070219', 
                        'end_date'                 => '20071019',
                        'is_online_registration'   => '0',
                        'registration_link_text'   => 'link',
                        'max_participants'         => '150',
                        'event_full_text'          => 'efull', 
                        'is_monetary'              => '0', 
                        'contribution_type_id'     => '0', 
                        'is_map'                   => '0', 
                        'is_active'                => '1' 
                        );
	
        $event = & crm_create_event($params);               
        $this->assertEqual($event['title'],'Annual Function');
        $this->assertEqual($event['summary'],'Regular function');
        $this->assertEqual($event['description'],'Award ceremony and cultural events');
        $this->assertEqual($event['event_type_id'],'3');
        $this->assertEqual($event['is_public'],'1');
        $this->assertEqual($event['start_date'],'20070219');
        $this->assertEqual($event['end_date'],'20071019');
        $this->assertEqual($event['is_online_registration'],'0');
        $this->assertEqual($event['registration_link_text'],'link');
        $this->assertEqual($event['max_participants'],'150');
        $this->assertEqual($event['event_full_text'],'efull');
        $this->assertEqual($event['is_monetary'],'0');
        $this->assertEqual($event['contribution_type_id'],'0');
        $this->assertEqual($event['is_map'],'0');
        $this->assertEqual($event['is_active'],'1');
        $this->_event = $event;
    }
    
    function testDeleteEvent()
    {
       
        $val = &crm_delete_event($this->_event['id']);
        $this->assertTrue($val);
        
        $val1 = &crm_delete_event($this->_event1['id']);
        $this->assertTrue($val1);
        
        $val2 = &crm_delete_event($this->_event2['id']);
        $this->assertTrue($val2);
        
    }
    
}
