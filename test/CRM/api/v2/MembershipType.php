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
}
