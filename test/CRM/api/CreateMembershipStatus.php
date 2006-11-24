<?php

require_once 'api/crm.php';

class TestOfCreateMembershipStatus extends UnitTestCase 
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
    
    function testCreateWrongMembershipStatus()
    {
        $params = array();        
        $membershipstatus = & crm_create_membership_status($params);
        $this->assertIsA($membershipstatus,'CRM_Core_Error');
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
        
        $membershipstatus = & crm_create_membership_status($params);        
        $this->assertEqual($membershipstatus['start_event'],'start_date');
        $this->assertEqual($membershipstatus['start_event_adjust_unit'],'day');
        $this->assertEqual($membershipstatus['start_event_adjust_interval'],'12');
        $this->assertEqual($membershipstatus['end_event'],'month');        
        $this->assertEqual($membershipstatus['end_event_adjust_unit'],'day');
        $this->assertEqual($membershipstatus['end_event_adjust_interval'],'23');
        $this->assertEqual($membershipstatus['is_current_member'],'1');        
        $this->assertEqual($membershipstatus['is_active'],'1');
        $this->_membershipstatus1 = $membershipstatus;
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
        
        $membershipstatus = & crm_create_membership_status($params);
        $this->assertEqual($membershipstatus['name'],'LongLife');      
        $this->assertEqual($membershipstatus['start_event_adjust_interval'],'12');
        $this->assertEqual($membershipstatus['end_event'],'month');        
        $this->assertEqual($membershipstatus['end_event_adjust_unit'],'day');
        $this->assertEqual($membershipstatus['end_event_adjust_interval'],'23');
        $this->assertEqual($membershipstatus['is_current_member'],'1');        
        $this->assertEqual($membershipstatus['is_active'],'1');        
        $this->_membershipstatus2 = $membershipstatus;
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
        
        $this->_membershipstatus = & crm_create_membership_status($params);
        $this->assertEqual($this->_membershipstatus['name'],'LongLife');
        $this->assertEqual($this->_membershipstatus['start_event'],'start_date');
        $this->assertEqual($this->_membershipstatus['start_event_adjust_unit'],'day');
        $this->assertEqual($this->_membershipstatus['start_event_adjust_interval'],'12');
        $this->assertEqual($this->_membershipstatus['end_event'],'month');        
        $this->assertEqual($this->_membershipstatus['end_event_adjust_unit'],'day');
        $this->assertEqual($this->_membershipstatus['end_event_adjust_interval'],'23');
        $this->assertEqual($this->_membershipstatus['is_current_member'],'1');        
        $this->assertEqual($this->_membershipstatus['is_active'],'1');
    }    

    
    function testDeleteMembershipStatus()
    {
        $val = &crm_delete_membership_status($this->_membershipstatus['id']);
        $this->assertNull($val);

        $val1 = &crm_delete_membership_status($this->_membershipstatus1['id']);
        $this->assertNull($val1);

        $val2 = &crm_delete_membership_status($this->_membershipstatus2['id']);
        $this->assertNull($val2);
    }

}