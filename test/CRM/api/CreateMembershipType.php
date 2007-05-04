<?php

require_once 'api/crm.php';

class TestOfCreateMembershipType extends UnitTestCase 
{
    protected $_membershiptype   = array();
    protected $_membershiptype1   = array();
    protected $_membershiptype2   = array();
        
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }

    function testCreateWrongMembershipType()
    {
        $params = array();        
        $membershiptype = & crm_create_membership_type($params);
        $this->assertIsA($membershiptype,'CRM_Core_Error');
    }
    
    

    function testCreateWrongMembershipTypeWithoutmemberofcontactid()
    {
        $params = array(
                        'name'                 => '60+ Membership',
                        'description'          => 'people above 60 are given health instructions',
                        'contribution_type_id' => '2',
                        'minimum_fee'          => '200',
                        'duration_unit'        => 'month',
                        'duration_interval'    => '10',
                        'period_type'          => 'rolling',
                        'visibility'           => 'public'
                        );
        
        $membershiptype = & crm_create_membership_type($params);
        $this->assertIsA($membershiptype,'CRM_Core_Error');
    }

    
    function testCreateWrongMembershipTypeWithoutContributionTypeId()
    {
        
        $params = array(
                        'name'                 => '70+ Membership',
                        'description'          => 'people above 70 are given health instructions',
                        'member_of_contact_id' => '1',
                        'minimum_fee'          => '200',
                        'duration_unit'        => 'month',
                        'duration_interval'    => '10',
                        'period_type'          => 'rolling',
                        'visibility'           => 'public'
                        );
        $membershiptype = & crm_create_membership_type($params);
        $this->assertIsA($membershiptype,'CRM_Core_Error');
    }

    function testCreateMembershipTypeWithoutDurationUnit()
    {
        
        $params = array(
                        'name'                 => '80+ Membership',
                        'description'          => 'people above 80 are given health instructions',
                        'member_of_contact_id' => '1',
                        'contribution_type_id' => '2',
                        'minimum_fee'          => '200',
                        'duration_unit'        => 'month',
                        'duration_interval'    => '10',                 
                        'visibility'           => 'public'
                        );
	
        $membershiptype = & crm_create_membership_type($params);        
        $this->assertEqual($membershiptype['name'],'80+ Membership');
        $this->assertEqual($membershiptype['description'],'people above 80 are given health instructions');
        $this->assertEqual($membershiptype['member_of_contact_id'],'1');
        $this->assertEqual($membershiptype['contribution_type_id'],'2');
        $this->assertEqual($membershiptype['minimum_fee'],'200');        
        $this->assertEqual($membershiptype['duration_interval'],'10');        
        $this->assertEqual($membershiptype['visibility'],'public');
        $this->_membershiptype1 = $membershiptype;
    }

    

    function testCreateMembershipTypeWithoutName()
    {
        $params = array(
                        'description'          => 'people above 50 are given health instructions',
                        'member_of_contact_id' => '1',
                        'contribution_type_id' => '2',
                        'minimum_fee'          => '200',
                        'duration_unit'        => 'month',
                        'duration_interval'    => '10',
                        'period_type'          => 'rolling',
                        'visibility'           => 'public'
                        );
        
        $membershiptype = & crm_create_membership_type($params);               
        $this->assertEqual($membershiptype['description'],'people above 50 are given health instructions');
        $this->assertEqual($membershiptype['member_of_contact_id'],'1');
        $this->assertEqual($membershiptype['contribution_type_id'],'2');
        $this->assertEqual($membershiptype['minimum_fee'],'200');
        $this->assertEqual($membershiptype['duration_unit'],'month');
        $this->assertEqual($membershiptype['duration_interval'],'10');
        $this->assertEqual($membershiptype['period_type'],'rolling');
        $this->assertEqual($membershiptype['visibility'],'public');
        $this->_membershiptype2 = $membershiptype;
    }


    function testCreateMembershipType()
    {
        $params = array(
                        'name'                 => '40+ Membership',
                        'description'          => 'people above 40 are given health instructions',
                        'member_of_contact_id' => '1',
                        'contribution_type_id' => '2',
                        'minimum_fee'          => '200',
                        'duration_unit'        => 'month',
                        'duration_interval'    => '10',
                        'period_type'          => 'rolling',
                        'visibility'           => 'public'
                        );
	
        $this->_membershiptype = & crm_create_membership_type($params);
        $this->assertEqual($this->_membershiptype['name'],'40+ Membership');
        $this->assertEqual($this->_membershiptype['description'],'people above 40 are given health instructions');
        $this->assertEqual($this->_membershiptype['member_of_contact_id'],'1');
        $this->assertEqual($this->_membershiptype['contribution_type_id'],'2');
        $this->assertEqual($this->_membershiptype['minimum_fee'],'200');
        $this->assertEqual($this->_membershiptype['duration_unit'],'month');
        $this->assertEqual($this->_membershiptype['duration_interval'],'10');
        $this->assertEqual($this->_membershiptype['period_type'],'rolling');
        $this->assertEqual($this->_membershiptype['visibility'],'public');        
    }

    
    function testDeleteMembershipType()
    {
        $val = &crm_delete_membership_type($this->_membershiptype['id']);
        $this->assertNull($val);

        $val1 = &crm_delete_membership_type($this->_membershiptype1['id']);
        $this->assertNull($val1);

        $val2 = &crm_delete_membership_type($this->_membershiptype2['id']);
        $this->assertNull($val2);
    }

}
