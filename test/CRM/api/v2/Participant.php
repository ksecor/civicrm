<?php

require_once 'api/crm.php';

class TestOfCreateParticipantAPIV2 extends UnitTestCase 
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
        $params['contact_id'] = 35;
        $this->_participant = & civicrm_participant_create($params);

        $this->assertEqual( $this->_participant['is_error'], 1 );
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
        $participant = & civicrm_participant_create($params);
        $this->assertEqual( $participant['is_error'], 1 );
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
        $participant = & civicrm_participant_create($params);
        $this->assertEqual( $participant['is_error'], 1 );
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
                        'contact_id'    => 35,
                        );
       
        $this->_participant = & civicrm_participant_create($params);
        $this->assertEqual( $this->_participant['is_error'], 0 );
        $this->assertNotNull( $this->_participant['participant_id'] );
     }     

    function testGetParticipantsByEventId()
    {
        $params = array('event_id' => $this->_participant2['event_id']);
        $participant = & civicrm_participant_get($params);
        foreach ( $participant as $id => $value ) {
            $this->assertEqual($value['event_id'],$this->_participant2['event_id']);               
        }
    }

    function testDeleteParticipant()
    {
        $delete = & civicrm_participant_delete($this->_participant['participant_id']);
        $this->assertNull($delete);
    }
 }
?>
