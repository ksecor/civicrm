<?php

require_once 'api/crm.php';

class TestOfGetMembershipStatuses extends UnitTestCase 
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
   

    function testGetWrongMembershipStatusWithoutId()
    {
        $params = array( 'name'=> 'LongLife' );        
        $membershipstatus = & crm_get_membership_statuses($params);
        $this->assertIsA($membershipstatus,'CRM_Core_Error');       
    }


    function testGetWrongMembershipStatusEmptyParam()
    {
        $params = array();        
        $membershipstatus = & crm_get_membership_statuses($params);
        $this->assertIsA($membershipstatus,'CRM_Core_Error');       
    }



    function testGetMembershipStatus()
    {
        $id =  $this->_membershipstatus['id'] ;
        $params = array( 'id'=> $id );        
        $membershipstatus = & crm_get_membership_statuses($params);    
        $this->assertEqual($membershipstatus[$id]['name'],'LongLife');
        $this->assertEqual($membershipstatus[$id]['start_event'], 'start_date');
        $this->assertEqual($membershipstatus[$id]['start_event_adjust_unit'], 'day');
        $this->assertEqual($membershipstatus[$id]['start_event_adjust_interval'], '12');
        $this->assertEqual($membershipstatus[$id]['end_event'],'end_date');
        $this->assertEqual($membershipstatus[$id]['end_event_adjust_unit'], 'day');
        $this->assertEqual($membershipstatus[$id]['end_event_adjust_interval'],'23' );
        $this->assertEqual($membershipstatus[$id]['is_current_member'], '1');
        $this->assertEqual($membershipstatus[$id]['is_active'], '1'  );                                                                         
    }
    
    
    function testDeleteMembershipStatus()
    {
        $val = &crm_delete_membership_status($this->_membershipstatus['id']);
        $this->assertNull($val);
    }

}