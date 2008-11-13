<?php
require_once 'api/Membership.php';
class TestOfDeleteMembershipType extends UnitTestCase 
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
        $this->assertEqual($this->_membershiptype['name'],'60+ Membership');
    }

    function testDeleteBadMembershipTypeWrongId()
    {
        $id = -14588;
        $val = &crm_delete_membership_type($id);
        $this->assertIsA($val,'CRM_Core_Error');
        //CRM_Core_Error::debug('WrongIdval',$val);
    }

    function testDeleteBadMembershipTypeWithoutId()
    {
        $val = &crm_delete_membership_type($this->_membershiptype['name']);
        $this->assertIsA($val,'CRM_Core_Error');
        //CRM_Core_Error::debug('WithoutIdval',$val);
    }

    

     function testDeleteBadMembershipTypeEmptyParam()
    {
        $param = array();
        $val = &crm_delete_membership_type($param);
        $this->assertIsA($val,'CRM_Core_Error');        
    }

   
    function testDeleteMembershipType()
    {
        $val = &crm_delete_membership_type($this->_membershiptype['id']);
        $this->assertNull($val);
    }

}
