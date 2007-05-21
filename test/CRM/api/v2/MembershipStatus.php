<?php

require_once 'api/v2/Membership.php';

class TestOfMembershipTypeAPIV2 extends UnitTestCase 
{
    protected $_membershipstatus   = array();
    protected $_membershipstatus1   = array();
    protected $_membershipstatus2   = array();
        
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }

    function testCreateCreateWrongMembershipStatus()
    {
        $params = array();        
        $membershipstatus = & civicrm_membership_status_create($params);
        $this->assertEqual( $membershipstatus['is_error'], 1 );
    }
    
    function testCreateMembershipStatusWithoutName()
    {
        $params = array(
                        'start_event'                 => 'start_date',
                        'start_event_adjust_unit'     => 'day',
                        'start_event_adjust_interval' => '12',
                        'end_event'                   => 'month',
                        'end_event_adjust_unit'       => 'day',
                        'end_event_adjust_interval'   => '23',
                        'is_current_member'           => '1',
                        'is_active'                   => '1'
                        );
        
        $membershipstatus = & civicrm_membership_status_create($params);
        $this->assertEqual( $membershipstatus['is_error'], 1 );
    }
    
    
    function testCreateMembershipStatusWithoutStartEvent()
    {
        $params = array(
                        'name'                        => 'LongLife',                        
                        'start_event_adjust_interval' => '12',
                        'end_event'                   => 'month',
                        'end_event_adjust_unit'       => 'day',
                        'end_event_adjust_interval'   => '23',
                        'is_current_member'           => '1',
                        'is_active'                   => '1'
                        );
        
        $membershipstatus = & civicrm_membership_status_create($params);
        $this->assertNotNull( $membershipstatus['id'] ); 
        $this->_membershipstatus1 = $membershipstatus;
    }

    function testCreateMembershipStatus()
    {
        $params = array(
                        'name'                        => 'LongLife',
                        'start_event'                 => 'start_date',
                        'start_event_adjust_unit'     => 'day',
                        'start_event_adjust_interval' => '12',
                        'end_event'                   => 'month',
                        'end_event_adjust_unit'       => 'day',
                        'end_event_adjust_interval'   => '23',
                        'is_current_member'           => '1',
                        'is_active'                   => '1'
                        );
        
        $this->_membershipstatus =  & civicrm_membership_status_create($params);
        $this->assertNotNull( $this->_membershipstatus['id'] );  
    }    

    function testUpdateWrongMembershipStatusWithoutId()
    {
        $params = array( 'name'=> 'LongLife' );        
        $membershipstatus = & civicrm_membership_status_update( $params );
        $this->assertEqual( $membershipstatus['is_error'], 1 );
    }

    function testUpdateWrongMembershipStatusEmptyParam()
    {
        $params = array();        
        $membershipstatus = & civicrm_membership_status_update( $params );
        $this->assertEqual( $membershipstatus['is_error'], 1 );
    }
    
    function testUpdateMembershipStatus()
    {
        $params = array(
                        'id'                          => $this->_membershipstatus['id'],
                        'name'                        => 'ShortLife',
                        'start_event'                 => 'join_date',
                        'start_event_adjust_unit'     => 'month',
                        'start_event_adjust_interval' => '11',
                        'end_event'                   => 'end_date',
                        'end_event_adjust_unit'       => 'month',
                        'end_event_adjust_interval'   => '11',
                        'is_current_member'           => '1',
                        'is_active'                   => '1'
                        );
        $membershipstatus = & civicrm_membership_status_update( $params );

        $this->assertEqual($membershipstatus['name'],'ShortLife');
        $this->assertEqual($membershipstatus['start_event'], 'join_date');
        $this->assertEqual($membershipstatus['start_event_adjust_unit'], 'month');
        $this->assertEqual($membershipstatus['start_event_adjust_interval'], '11');
        $this->assertEqual($membershipstatus['end_event'],'end_date');
        $this->assertEqual($membershipstatus['end_event_adjust_unit'], 'month');
        $this->assertEqual($membershipstatus['end_event_adjust_interval'],'11' );
        $this->assertEqual($membershipstatus['is_current_member'], '1');
        $this->assertEqual($membershipstatus['is_active'], '1'  );
    }
    
    function testGetWrongMembershipStatusWithoutId()
    {
        $params = array( 'name'=> 'LongLife' );        
        $membershipstatus = & civicrm_membership_statuses_get( $params );
        $this->assertEqual( $membershipstatus['is_error'], 1 );
    }

    function testGetWrongMembershipStatusEmptyParam()
    {
        $params = array();        
        $membershipstatus = & civicrm_membership_statuses_get( $params );
        $this->assertEqual( $membershipstatus['is_error'], 1 );
    }


    function testGetMembershipStatus()
    {
        $id =  $this->_membershipstatus['id'] ;
        $params = array( 'id'=> $id );        
        $membershipstatus = & civicrm_membership_statuses_get( $params );
        $this->assertEqual($membershipstatus[$id]['name'],'ShortLife');
        $this->assertEqual($membershipstatus[$id]['start_event'], 'join_date');
        $this->assertEqual($membershipstatus[$id]['start_event_adjust_unit'], 'month');
        $this->assertEqual($membershipstatus[$id]['start_event_adjust_interval'], '11');
        $this->assertEqual($membershipstatus[$id]['end_event'],'end_date');
        $this->assertEqual($membershipstatus[$id]['end_event_adjust_unit'], 'month');
        $this->assertEqual($membershipstatus[$id]['end_event_adjust_interval'],'11' );
        $this->assertEqual($membershipstatus[$id]['is_current_member'], '1');
        $this->assertEqual($membershipstatus[$id]['is_active'], '1'  ); 
    }
     
    function testDeleteBadMembershipStatusWithoutId()
    {
        $val = &civicrm_membership_status_delete($this->_membershipstatus['name']);
        $this->assertEqual( $val['is_error'], 1);
    }
    
    function testDeleteBadMembershipStatusEmptyParam()
    {
        $param = array();
        $val = &civicrm_membership_status_delete($param);
        $this->assertEqual( $val['is_error'], 1);
    }
    
    function testDeleteMembershipStatus()
    {
        $val1 = &civicrm_membership_status_delete($this->_membershipstatus1['id']);
        $val2 = &civicrm_membership_status_delete($this->_membershipstatus['id']);
        $this->assertNull( $val1);
        $this->assertNull( $val2);
    }
}
  