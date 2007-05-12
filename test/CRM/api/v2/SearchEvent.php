<?php

require_once 'api/v2/Event.php';
require_once 'api/v2/Participant.php';

class TestOfSearchEventAPIV2 extends UnitTestCase 
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
	
        $this->_event = & civicrm_event_create($params);
    }
    function testCreateParticipant()
    {
        $params = array(
                        'event_id'      => $this->_event['event_id'],
                        'status_id'     => 2,
                        'role_id'       => 1,
                        'register_date' => '2005-05-07',
                        'source'        => 'Wimbeldon',
                        'event_level'   => 'Payment',
                        'contact_id'    => 35,
                        );
       
        $this->_participant = & civicrm_participant_create($params);
    }
    function testGetEvent()
    {
        $id = $this->_event['event_id'];
        $params = array(
                        'event_id'          => $id,
                        'description' => 'Award ceremony and cultural events',
                        );
        
        $event = & civicrm_event_get($params);
        $this->assertEqual( $event['event_id'], $id );
        $this->assertEqual( $event['event_title'], 'Annual Function' );

    }
    
    function testDeleteParticipant()
    {
        $delete = & civicrm_participant_delete($this->_participant['participant_id']);
        $this->assertNull($delete);
    }
    
    function testDeleteEvent()
    {
        $val = &civicrm_event_delete($this->_event['event_id']);
        $this->assertEqual( $val['is_error'], 0 );    
    }

}
