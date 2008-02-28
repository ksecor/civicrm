<?php

require_once 'api/crm.php';

class TestOfGetParticipants extends UnitTestCase 
{
    protected $_participant1;
    protected $_participant2;
            
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }

    function testCreateParticipant1()
    {
        $params = array(
                        'event_id'      => 1,
                        'status_id'     => 2,
                        'role_id'       => 1,
                        'register_date' => date( 'YmdHis' ),
                        'source'        => 'Wimbeldon',
                        'event_level'   => 'Payment'
                        );
       
        $this->_participant1 = & crm_create_participant($params,35);
        $this->assertEqual($this->_participant1['contact_id'], 35);
        $this->assertEqual($this->_participant1['event_id'],1);
        $this->assertEqual($this->_participant1['status_id'],2);
        $this->assertEqual($this->_participant1['role_id'],1);
        $this->assertEqual($this->_participant1['source'],'Wimbeldon');
        $this->assertEqual($this->_participant1['event_level'],'Payment');
    }     
    
    function testCreateParticipant2()
    {
        $params = array(
                        'event_id'      => 2,
                        'status_id'     => 3,
                        'role_id'       => 3,
                        'register_date' => '2006-01-21',
                        'source'        => 'US Open',
                        'event_level'   => 'Donation'                        
                        );
       
        $this->_participant2 = & crm_create_participant($params,35);
        $this->assertEqual($this->_participant2['event_id'],2);
        $this->assertEqual($this->_participant2['status_id'],3);
        $this->assertEqual($this->_participant2['role_id'],3);
        $this->assertEqual($this->_participant2['source'],'US Open');
        $this->assertEqual($this->_participant2['event_level'],'Donation');
    }


    function testGetParticipantsByEventId()
    {
        $params = array('event_id' => $this->_participant2['event_id']);
        $participant = & crm_get_participants($params);
        foreach ( $participant as $id => $value ) {
            $this->assertEqual($value['event_id'],$this->_participant2['event_id']);               
        }
    }

    function testGetParticipantsByContactId()
    {
        $params = array('contact_id' => 35);
        $participant = & crm_get_participants($params);
        $this->assertEqual($participant[$this->_participant1['id']]['contact_id'], 35);
        $this->assertEqual($participant[$this->_participant1['id']]['event_id'],1);
        $this->assertEqual($participant[$this->_participant1['id']]['status_id'],2);
        $this->assertEqual($participant[$this->_participant1['id']]['role_id'],1);
        $this->assertEqual($participant[$this->_participant1['id']]['source'],'Wimbeldon');
        $this->assertEqual($participant[$this->_participant1['id']]['event_level'],'Payment');  

        $this->assertEqual($participant[$this->_participant2['id']]['contact_id'], 35);
        $this->assertEqual($participant[$this->_participant2['id']]['event_id'],2);
        $this->assertEqual($participant[$this->_participant2['id']]['status_id'],3);
        $this->assertEqual($participant[$this->_participant2['id']]['role_id'],3);
        $this->assertEqual($participant[$this->_participant2['id']]['source'],'US Open');
        $this->assertEqual($participant[$this->_participant2['id']]['event_level'],'Donation');        
    }
    
    function testGetParticipantsByContactIdStatusId()
    {
        $params = array(
                        'contact_id' => 35,
                        'status_id'  => 2
                        );
        $participant = & crm_get_participants($params);
        $this->assertEqual($participant[$this->_participant1['id']]['contact_id'], 35);
        $this->assertEqual($participant[$this->_participant1['id']]['event_id'],1);
        $this->assertEqual($participant[$this->_participant1['id']]['status_id'],2);
        $this->assertEqual($participant[$this->_participant1['id']]['role_id'],1);
        $this->assertEqual($participant[$this->_participant1['id']]['source'],'Wimbeldon');
        $this->assertEqual($participant[$this->_participant1['id']]['event_level'],'Payment');  
    }

    function testDeleteParticipant()
    {
        $delete1 = & crm_delete_participant($this->_participant1['id']);
        $this->assertNull($delete1);

        $delete2 = & crm_delete_participant($this->_participant2['id']);
        $this->assertNull($delete2);        
    }
}
?>
