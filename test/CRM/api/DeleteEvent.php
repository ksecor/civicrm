<?php

require_once 'api/crm.php';

class TestOfDeleteEvent extends UnitTestCase 
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
                        'summary'                  => 'Regular function',
                        'description'              => 'Award ceremony and cultural events',
                        'event_type_id'            => '3',
                        'is_public'                => '1',
                        'start_date'               => '20070219',
                        'is_online_registration'   => '0',
                        'is_monetary'              => '0', 
                        'contribution_type_id'     => '0', 
                        'is_map'                   => '0', 
                        'is_active'                => '1' 
                        );
	
        $this->_event = & crm_create_event($params);
        $this->assertEqual($this->_event['title'],'Annual Function');
    }

    function testDeleteBadEventWrongId()
    {
        $id = -14588;
        $val = &crm_delete_event($id);
        $this->assertFalse($val);
        
    }

    function testDeleteBadEventWithoutId()
    {
        $val = &crm_delete_event($this->_event['title']);
        $this->assertFalse($val);
       
    }

    function testDeleteBadEventEmptyParam()
    {
        $param = array();
        $val = &crm_delete_event($param);
        $this->assertIsA($val,'CRM_Core_Error'); 
    }

    function testDeleteEvent()
    {
        $val = &crm_delete_event($this->_event['id']);
        $this->assertTrue($val);
    }
}
