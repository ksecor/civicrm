<?php

require_once 'api/v2/Membership.php';

class TestOfMembershipTypeUpdateAPIV2 extends CiviUnitTestCase 
{
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }

    function testMembershipTypeUpdateEmptyParams()
    {
        $params = array();                        
        $membershiptype = & civicrm_membership_type_update($params);
        $this->assertEqual( $membershiptype['is_error'], 1 );
    } 

    function testMembershipTypeUpdateWithoutId()
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
    function testMembershipTypeUpdate()
    {
        $contactID = 1;
        $id = $this->membershipTypeCreate( $contactID );
        $params = array(
                        'id'                        => $id,
                        'name'                      => 'Updated General',
                        'member_of_contact_id'      => '2',
                        'contribution_type_id'      => '1',
                        'duration_unit'             => 'month',
                        'duration_interval'         => '10',
                        'period_type'               => 'fixed',
                        );
        
        $membershiptype = & civicrm_membership_type_update($params);
        $this->assertEqual($membershiptype['name'],'Updated General');
        $this->assertEqual($membershiptype['member_of_contact_id'],'2');
        $this->assertEqual($membershiptype['contribution_type_id'],'1');
        $this->assertEqual($membershiptype['duration_unit'],'month');
        $this->assertEqual($membershiptype['duration_interval'],'10');
        $this->assertEqual($membershiptype['period_type'],'fixed');
        $this->membershipTypeDelete( $membershiptype['id']);
    }
}

?>