<?php

require_once 'api/crm.php';

class TestOfDeleteMembershipStatus extends UnitTestCase 
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
                        'end_event'                   => 'month',
                        'end_event_adjust_unit'       => 'day',
                        'end_event_adjust_interval'   => '23',
                        'is_current_member'           => '1',
                        'is_active'                   => '1'
                        );
        
        $this->_membershipstatus = & crm_create_membership_status($params);
    }      
   

    function testDeleteWrongMembershipStatus()
    {
        $params = array();        
        $membershipstatus = & crm_delete_membership_status($params);
        $this->assertIsA($membershipstatus,'CRM_Core_Error');       
    }

    /*function testDeleteBadMembershipStatusWrongId()
    {
        $params = array('id' => -124);        
        $membershipstatus = & crm_delete_membership_status($params);
        $this->assertIsA($membershipstatus,'CRM_Core_Error');       
    }*/

        
    function testDeleteMembershipStatus()
    {
        $val = &crm_delete_membership_status($this->_membershipstatus['id']);
        $this->assertNull($val);
    }

}
