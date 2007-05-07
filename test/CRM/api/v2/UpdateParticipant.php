<?php

require_once 'api/crm.php';

class TestOfUpdateParticipantAPIV2 extends UnitTestCase 
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
        $params['contact_id'] = 35; 
       
        $this->_participant = & civicrm_participant_create($params);
        $this->assertEqual( $participant['is_error'], 0 );
        $this->assertNotNull( $this->_participant['participant_id'] );

    }

    function testUpdateEmptyParticipant()
    {
        $params = array();        
        $participant = & civicrm_participant_update($params);
        $this->assertEqual( $participant['is_error'], 1 );
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
        $participant = & civicrm_participant_update($params);
        $this->assertEqual( $participant['is_error'], 1 );
    }
   

    function testUpdateParticipant()
    {
        $params = array(
                        'id'            => $this->_participant['participant_id'],
                        'event_id'      => 2,
                        'status_id'     => 3,
                        'role_id'       => 3,
                        'register_date' => '2006-01-21',
                        'source'        => 'US Open',
                        'event_level'   => 'Donation'                        
                        );
       
        $this->_participant = & civicrm_participant_update($params);
        $this->assertEqual($this->_participant['event_id'],2);
        $this->assertEqual($this->_participant['status_id'],3);
        $this->assertEqual($this->_participant['role_id'],3);
        $this->assertEqual($this->_participant['register_date'],20060121);
        $this->assertEqual($this->_participant['source'],'US Open');
        $this->assertEqual($this->_participant['event_level'],'Donation');
    }


    function testDeleteParticipant()
    {
        $delete = & civicrm_participant_delete($this->_participant['participant_id']);
        $this->assertNull($delete);
    }
}
?>
