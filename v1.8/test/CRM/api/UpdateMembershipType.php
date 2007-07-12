<?php

require_once 'api/crm.php';

class TestOfUpdateMembershipType extends UnitTestCase 
{
    protected $_membershiptype   = array();
       
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }

    function testCreateMembershipType()
    {
        $params = array(
                        'name'                 => '60+ Membership',
                        'description'          => 'people above 60 are given health instructions',
                        'member_of_contact_id' => '1',
                        'contribution_type_id' => '2',
                        'minimum_fee'          => '200',
                        'duration_unit'        => 'month',
                        'duration_interval'    => '10',
                        'period_type'          => 'rolling',
                        'visibility'           => 'public'
                        );
	
        $this->_membershiptype = & crm_create_membership_type($params);
    }   
     

    function testUpdateWrongMembershipTypeEmptyParams()
    {
        $params = array();                        
        $membershiptype = & crm_update_membership_type($params);
        $this->assertIsA($membershiptype,'CRM_Core_Error');
    }


    function testUpdateWrongMembershipTypeWithoutId()
    {
        $params = array(
                        'name'                 => '60+ Membership',
                        'description'          => 'people above 60 are given health instructions',
                        'member_of_contact_id' => '33',
                        'contribution_type_id' => '1',
                        'minimum_fee'          => '1200',
                        'duration_unit'        => 'month',
                        'duration_interval'    => '10',
                        'period_type'          => 'rolling',
                        'visibility'           => 'public'
                        );
        
        $membershiptype = & crm_update_membership_type($params);
        $this->assertIsA($membershiptype,'CRM_Core_Error');
    }


    function testUpdateMembershipType()
    {
        $params = array(
                        'id'                        => $this->_membershiptype['id'],
                        'name'                      => '30+ Membership',
                        'description'               => 'people above 30 are given health instructions',
                        'member_of_contact_id'      => '33',
                        'contribution_type_id'      => '1',
                        'minimum_fee'               => '1200',
                        'duration_unit'             => 'month',
                        'duration_interval'         => '10',
                        'period_type'               => 'fixed',
                        'fixed_period_start_day'    => '101',
                        'fixed_period_rollover_day' => '1231',
                        'relationship_type_id'      => '7',
                        'visibility'                => 'public',
                        'is_active'                 => '1'
                        );
        
        $membershiptype = & crm_update_membership_type($params);
        $this->assertEqual($membershiptype['name'],'30+ Membership');
        $this->assertEqual($membershiptype['description'],'people above 30 are given health instructions');
        $this->assertEqual($membershiptype['member_of_contact_id'],'33');
        $this->assertEqual($membershiptype['contribution_type_id'],'1');
        $this->assertEqual($membershiptype['minimum_fee'],'1200');
        $this->assertEqual($membershiptype['duration_unit'],'month');
        $this->assertEqual($membershiptype['duration_interval'],'10');
        $this->assertEqual($membershiptype['period_type'],'fixed');
        $this->assertEqual($membershiptype['fixed_period_start_day'],'101');
        $this->assertEqual($membershiptype['fixed_period_rollover_day'],'1231');
        $this->assertEqual($membershiptype['relationship_type_id'],'7');
        $this->assertEqual($membershiptype['is_active'],'1');
        $this->assertEqual($membershiptype['visibility'],'public');
    }

    function testDeleteMembershipType()
    {
        $val = &crm_delete_membership_type($this->_membershiptype['id']);
        $this->assertNull($val);
    }

}
