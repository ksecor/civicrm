<?php

require_once 'api/crm.php';

class TestOfDeleteEventAPIV2 extends UnitTestCase 
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
	
        $this->_event = & civicrm_event_create($params);
        $this->assertEqual( $this->_event['is_error'], 0 );
    }

    function testDeleteBadEventWrongId()
    {
        $id = -14588;
        $val = &civicrm_event_delete($id);
        $this->assertEqual( $val['is_error'], 1 );
        
    }

    function testDeleteBadEventWithoutId()
    {
        $val = &civicrm_event_delete($this->_event['title']);
        $this->assertEqual( $val['is_error'], 1 );
       
    }

    function testDeleteBadEventEmptyParam()
    {
        $param = array();
        $val = &civicrm_event_delete($param);
        $this->assertEqual( $val['is_error'], 1 );
    }

    function testDeleteEvent()
    {
        $val = &civicrm_event_delete($this->_event['event_id']);
        $this->assertNull($val);
    }
}
