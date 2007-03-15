<?php

require_once 'api/crm.php';

class TestOfGetMembershipTypes extends UnitTestCase 
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
                        'name'                      => '60+ Membership',
                        'description'               => 'people above 60 are given health instructions',
                        'member_of_contact_id'      => '1',
                        'contribution_type_id'      => '2',
                        'minimum_fee'               => '200',
                        'duration_unit'             => 'month',
                        'duration_interval'         => '10',
                        'period_type'               => 'fixed',
                        'fixed_period_start_day'    => '101',
                        'fixed_period_rollover_day' => '1231',
                        'relationship_type_id'      => '7',
                        'is_active'                 => '1',
                        'visibility'                => 'public'
                        );
	
        $this->_membershiptype = & crm_create_membership_type($params);             
    }

    function testGetWrongMembershipTypeEmptyParams()
    {
        $params = array();                        
        $membershiptype = & crm_get_membership_types($params);
        $this->assertIsA($membershiptype,'CRM_Core_Error');
    }
    

    function testGetWrongMembershipTypeWithoutId()
    {
        $params = array(
                        'name'                 => '60+ Membership',
                        'description'          => 'people above 60 are given health instructions',
                        'contribution_type_id' => '2',
                        'minimum_fee'          => '200',
                        'duration_unit'        => 'month',
                        'duration_interval'    => '10',
                        'visibility'           => 'public'
                        );
        
        $membershiptype = & crm_get_membership_types($params);
        $this->assertIsA($membershiptype,'CRM_Core_Error');
    }

      
    function testGetMembershipTypes()
    {
        $id = $this->_membershiptype['id'];
        $params = array(
                        'id'          => $id,
                        'description' => 'people above 60 are given health instructions',
                        );
        
        $membershiptype = & crm_get_membership_types($params);
        $this->assertEqual($membershiptype[$id]['name'],'60+ Membership'); 
        $this->assertEqual($membershiptype[$id]['description'],'people above 60 are given health instructions');
        $this->assertEqual($membershiptype[$id]['member_of_contact_id'],'1');
        $this->assertEqual($membershiptype[$id]['contribution_type_id'],'2');
        $this->assertEqual($membershiptype[$id]['minimum_fee'],'200');
        $this->assertEqual($membershiptype[$id]['duration_unit'],'month');
        $this->assertEqual($membershiptype[$id]['duration_interval'],'10');
        $this->assertEqual($membershiptype[$id]['period_type'],'fixed');
        $this->assertEqual($membershiptype[$id]['fixed_period_start_day'],'101');
        $this->assertEqual($membershiptype[$id]['fixed_period_rollover_day'],'1231');
        $this->assertEqual($membershiptype[$id]['relationship_type_id'],'7');
        $this->assertEqual($membershiptype[$id]['is_active'],'1');
        $this->assertEqual($membershiptype[$id]['visibility'],'public');                                      
    }    
    
    
    function testDeleteMembershipType()
    {
        $val = &crm_delete_membership_type($this->_membershiptype['id']);
        $this->assertNull($val);
    }

}
