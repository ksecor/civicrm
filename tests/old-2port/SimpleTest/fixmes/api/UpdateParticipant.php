<?php

require_once 'api/crm.php';

class TestOfUpdateParticipant extends UnitTestCase 
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
                        'register_date' => 20070219,
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

    function testUpdateEmptyParticipant()
    {
        $params = array();        
        $participant = & crm_update_participant($params);
        $this->assertIsA($participant,'CRM_Core_Error');
    }
    
    function testCreateErrorParticipantWithoutId()
    {
        $params = array(
                        'event_id'      => 2,
                        'status_id'     => 3,
                        'role_id'       => 3,
                        'register_date' => date( 'YmdHis' ),
                        'source'        => 'Wimbeldon',
                        'event_level'   => 'Payment'
                        );        
        $participant = & crm_update_participant($params);
        $this->assertIsA($participant,'CRM_Core_Error');
    }
   

    function testUpdateParticipant()
    {
        $params = array(
                        'id'            => $this->_participant['id'],
                        'event_id'      => 2,
                        'status_id'     => 3,
                        'role_id'       => 3,
                        'register_date' => '2006-01-21',
                        'source'        => 'US Open',
                        'event_level'   => 'Donation'                        
                        );
       
        $this->_participant = & crm_update_participant($params);
        $this->assertEqual($this->_participant['event_id'],2);
        $this->assertEqual($this->_participant['status_id'],3);
        $this->assertEqual($this->_participant['role_id'],3);
        $this->assertEqual($this->_participant['register_date'],20060121);
        $this->assertEqual($this->_participant['source'],'US Open');
        $this->assertEqual($this->_participant['event_level'],'Donation');
    }


    function testDeleteParticipant()
    {
        $delete = & crm_delete_participant($this->_participant['id']);
        $this->assertNull($delete);
    }
}

