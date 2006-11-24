<?php

require_once 'api/crm.php';

class TestOfUpdateMembershipStatus extends UnitTestCase 
{
    protected $_membershipstatus   = array();
       
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }
    

    function testCreateMembershipStatus()
    {
        $params = array(
                        'name'                        => 'LongLife',
                        'start_event'                 => 'start_date',
                        'start_event_adjust_unit'     => 'day',
                        'start_event_adjust_interval' => '12',
                        'end_event'                   => 'end_date',
                        'end_event_adjust_unit'       => 'day',
                        'end_event_adjust_interval'   => '23',
                        'is_current_member'           => '1',
                        'is_active'                   => '1'
                        );        
        $this->_membershipstatus = & crm_create_membership_status($params);
    }      
   

    function testUpdateWrongMembershipStatusWithoutId()
    {
        $params = array( 'name'=> 'LongLife' );        
        $membershipstatus = & crm_update_membership_status($params);
        $this->assertIsA($membershipstatus,'CRM_Core_Error');       
    }


    function testUpdateWrongMembershipStatusEmptyParam()
    {
        $params = array();        
        $membershipstatus = & crm_update_membership_status($params);
        $this->assertIsA($membershipstatus,'CRM_Core_Error');       
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
        $membershipstatus = & crm_update_membership_status($params);      
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
    
    
    function testDeleteMembershipStatus()
    {
        $val = &crm_delete_membership_status($this->_membershipstatus['id']);
        $this->assertNull($val);
    }

}