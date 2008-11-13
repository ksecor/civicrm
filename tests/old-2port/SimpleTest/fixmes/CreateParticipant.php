<?php

require_once 'api/crm.php';

class TestOfCreateParticipant extends UnitTestCase 
{
    protected $_participant;
            
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }

    function testCreateEmptyParticipant()
    {
        $params = array();        
        $participant = & crm_create_participant($params,35);
        $this->assertIsA($participant,'CRM_Core_Error');
    }
    
    function testCreateErrorParticipantWithoutEventId()
    {
        $params = array(
                        'status_id'     => 2,
                        'role_id'       => 1,
                        'register_date' => date( 'YmdHis' ),
                        'source'        => 'Wimbeldon',
                        'event_level'   => 'Payment'
                        );        
        $participant = & crm_create_participant($params,NULL);
        $this->assertIsA($participant,'CRM_Core_Error');
    }


    function testCreateErrorParticipantWithoutContactId()
    {
        $params = array(
                        'event_id'      => 1,
                        'status_id'     => 2,
                        'role_id'       => 1,
                        'register_date' => date( 'YmdHis' ),
                        'source'        => 'Wimbeldon',
                        'event_level'   => 'Payment'
                        );        
        $participant = & crm_create_participant($params,NULL);
        $this->assertIsA($participant,'CRM_Core_Error');
    }
    
    function testCreateParticipant()
    {
        $params = array(
                        'event_id'      => 1,
                        'status_id'     => 2,
                        'role_id'       => 1,
                        'register_date' => '2005-05-07',
                        'source'        => 'Wimbeldon',
                        'event_level'   => 'Payment',
                        );
       
        $this->_participant = & crm_create_participant($params,35);
        $this->assertEqual($this->_participant['contact_id'], 35);
        $this->assertEqual($this->_participant['event_id'],1);
        $this->assertEqual($this->_participant['status_id'],2);
        $this->assertEqual($this->_participant['role_id'],1);
        $this->assertEqual($this->_participant['source'],'Wimbeldon');
        $this->assertEqual($this->_participant['register_date'],20050507);
        $this->assertEqual($this->_participant['event_level'],'Payment');
    }     

    function testDeleteParticipant()
    {
        $delete = & crm_delete_participant($this->_participant['id']);
        $this->assertNull($delete);
    }
}

