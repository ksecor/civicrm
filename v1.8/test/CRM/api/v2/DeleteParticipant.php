<?php

require_once 'api/crm.php';

class TestOfDeleteParticipantAPIV2 extends UnitTestCase 
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
        $params['contact_id'] = 35;
        $this->_participant = & civicrm_participant_create($params);
        $this->assertEqual( $this->_participant['is_error'], 0 );
        $this->assertNotNull( $this->_participant['participant_id'] );

    }     
    
    function testDeleteEmptyParticipant()
    {
        $params = array();        
        $delete = & civicrm_participant_delete($params);
        $this->assertEqual( $delete['is_error'], 1 );
    }
    
    function testCreateErrorParticipantWrongId()
    {
        $id = -165;      
        $delete = & civicrm_participant_delete($params);
        $this->assertEqual( $delete['is_error'], 1 );
    }

    function testCreateErrorParticipantWithoutId()
    {
        $delete = & civicrm_participant_delete($this->_participant['event_id']);
        $this->assertEqual( $delete['is_error'], 1 );
    }

    function testDeleteParticipant()
    {
        $delete = & civicrm_participant_delete($this->_participant['participant_id']);
        $this->assertNull($delete);
    }
}
?>
