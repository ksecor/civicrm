<?php

require_once 'api/crm.php';

class TestOfDeleteParticipant extends UnitTestCase 
{
    protected $_participant;
            
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }

    function testCreateParticipant()
    {
        $params = array(
                        'event_id'      => 1,
                        'status_id'     => 2,
                        'role_id'       => 1,
                        'register_date' => date( 'YmdHis' ),
                        'source'        => 'Wimbeldon',
                        'event_level'   => 'Payment'
                        );
       
        $this->_participant = & crm_create_participant($params,35);
        $this->assertEqual($this->_participant['contact_id'], 35);
        $this->assertEqual($this->_participant['event_id'],1);
        $this->assertEqual($this->_participant['status_id'],2);
        $this->assertEqual($this->_participant['role_id'],1);
        $this->assertEqual($this->_participant['source'],'Wimbeldon');
        $this->assertEqual($this->_participant['event_level'],'Payment');
    }     
    
    function testDeleteEmptyParticipant()
    {
        $params = array();        
        $delete = & crm_delete_participant($params);
        $this->assertIsA($delete,'CRM_Core_Error');
    }
    
    function testCreateErrorParticipantWrongId()
    {
        $id = -165;      
        $delete = & crm_delete_participant($params);
        $this->assertIsA($delete,'CRM_Core_Error');
    }

    function testCreateErrorParticipantWithoutId()
    {
        $delete = & crm_delete_participant($this->_participant['event_id']);
        $this->assertIsA($delete,'CRM_Core_Error');
    }

    function testDeleteParticipant()
    {
        $delete = & crm_delete_participant($this->_participant['id']);
        $this->assertNull($delete);
    }
}

