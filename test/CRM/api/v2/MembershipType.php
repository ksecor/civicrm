<?php

require_once 'api/v2/Membership.php';

class TestOfMembershipTypeAPIV2 extends UnitTestCase 
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
        $membershiptype = & civicrm_membership_type_create($params);
        $this->assertEqual( $membershiptype['is_error'], 1 );
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
        
        $membershiptype = & civicrm_membership_type_create($params);
        $this->assertEqual( $membershiptype['is_error'], 1 );
  
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
        $membershiptype = & civicrm_membership_type_create($params);
        $this->assertEqual( $membershiptype['is_error'], 1 );
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
	
        $membershiptype = & civicrm_membership_type_create($params);        
        $this->_membershiptype1 = $membershiptype;
        $this->assertNotNull( $this->_membershiptype1['member_id'] );  

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
        
        $membershiptype = & civicrm_membership_type_create($params);               
        $this->assertNotNull( $membershiptype['member_id'] );   
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
	
        $this->_membershiptype = & civicrm_membership_type_create($params);
        $this->assertNotNull( $this->_membershiptype['member_id'] );   
    }
    

    function testUpdateWrongMembershipTypeEmptyParams()
    {
        $params = array();                        
        $membershiptype = & civicrm_membership_type_update($params);
        $this->assertEqual( $membershiptype['is_error'], 1 );
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
        
        $membershiptype = & civicrm_membership_type_update($params);
        $this->assertEqual( $membershiptype['is_error'], 1 );
    }


    function testUpdateMembershipType()
    {
        $params = array(
                        'id'                        => $this->_membershiptype['member_id'],
                        'name'                      => '30+ Membership',
                        'description'               => 'people above 30 are given health instructions',
                        'member_of_contact_id'      => '33',
                        'contribution_type_id'      => '1',
                        'minimum_fee'               => '200',
                        'duration_unit'             => 'year',
                        'duration_interval'         => '1',
                        'period_type'               => 'fixed',
                        'fixed_period_start_day'    => '101',
                        'fixed_period_rollover_day' => '1231',
                        'relationship_type_id'      => '7',
                        'visibility'                => 'public',
                        'is_active'                 => '1'
                        );
        
        $membershiptype = & civicrm_membership_type_update($params);
        $this->assertEqual($membershiptype['name'],'30+ Membership');
        $this->assertEqual($membershiptype['description'],'people above 30 are given health instructions');
        $this->assertEqual($membershiptype['member_of_contact_id'],'33');
        $this->assertEqual($membershiptype['contribution_type_id'],'1');
        $this->assertEqual($membershiptype['minimum_fee'],'200');
        $this->assertEqual($membershiptype['duration_unit'],'year');
        $this->assertEqual($membershiptype['duration_interval'],'1');
        $this->assertEqual($membershiptype['period_type'],'fixed');
        $this->assertEqual($membershiptype['fixed_period_start_day'],'101');
        $this->assertEqual($membershiptype['fixed_period_rollover_day'],'1231');
        $this->assertEqual($membershiptype['relationship_type_id'],'7');
        $this->assertEqual($membershiptype['is_active'],'1');
        $this->assertEqual($membershiptype['visibility'],'public');
    }
    function testDeleteBadMembershipTypeWrongId()
    {
        $id = -14588;
        $val = &civicrm_membership_type_delete($id);
    }

    function testDeleteBadMembershipTypeWithoutId()
    {
        $val = &civicrm_membership_type_delete($this->_membershiptype['name']);
    }

    

     function testDeleteBadMembershipTypeEmptyParam()
    {
        $param = array();
        $val = &civicrm_membership_type_delete($param);
    }

   
    function testDeleteMembershipType()
    {
        $val1 = &civicrm_membership_type_delete($this->_membershiptype1['member_id']);
        $val2 = &civicrm_membership_type_delete($this->_membershiptype2['member_id']);
        $val3 = &civicrm_membership_type_delete($this->_membershiptype['member_id']);

    }
}
