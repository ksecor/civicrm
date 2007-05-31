<?php

require_once 'api/v2/Event.php';


class TestOfCreateEventAPIV2 extends UnitTestCase 
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

        $event = & civicrm_event_create($params);
        $this->assertEqual( $event['is_error'], 1 );
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
        
        $event = & civicrm_event_create($params);
        $this->assertEqual( $event['is_error'], 1 );
    
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

        $event = & civicrm_event_create($params);
        $this->assertEqual( $event['is_error'], 1 );
    
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

        $event = & civicrm_event_create($params);
        $this->assertEqual( $event['is_error'], 1 );
        
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
        
        $event = & civicrm_event_create($params);  
	$this->assertNotNull( $event['event_id'] );    
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
	
        $event = & civicrm_event_create($params);
        $this->assertNotNull( $event['event_id'] );               

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
	
        $event = & civicrm_event_create($params);  
        $this->assertNotNull( $event['event_id'] );                

        $this->_event = $event;
    }
    function testUpdateEvent()
    {
      $params = array(
		      'id'                       => $this->_event['event_id'],
		      'title'                    => 'Dinner',
		      'summary'                  => 'Regular function', 
		      'description'              => 'Award ceremony and cultural events',
		      'event_type_id'            => '1',
		      'is_public'                => '0',
		      'start_date'               => '20080219',
		      'is_online_registration'   => '1',
		      'max_participants'         => '100',
		      'is_monetary'              => '0', 
		      'contribution_type_id'     => '0', 
		      'is_map'                   => '0', 
		      'is_active'                => '1' 
		      );
      
      $event = & civicrm_event_create($params);
    }
    
    function testDeleteEvent()
    {
       
        $val = &civicrm_event_delete($this->_event['event_id']);
        $this->assertEqual( $val['is_error'], 0 );    
        $val1 = &civicrm_event_delete($this->_event1['event_id']);
        $this->assertEqual( $val['is_error'], 0 );    
        
    }
    
}
