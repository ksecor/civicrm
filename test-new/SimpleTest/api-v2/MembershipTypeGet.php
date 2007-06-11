<?php

require_once 'api/v2/Membership.php';

class TestOfMembershipTypeGetAPIV2 extends CiviUnitTestCase 
{
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }
   
    function testMembershipTypeGetEmpty()
    {
        $membershiptype = & civicrm_membership_types_get( $params );
        $this->assertEqual( $membershiptype['is_error'], 1 );
    }
        
    function testMembershipTypeGetWithoutId()
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
        
        $membershiptype = & civicrm_membership_types_get( $params );
        $this->assertEqual( $membershiptype['is_error'], 1 );
    }

    function testMembershipTypeGet()
    {
        $contactID = 1;
        $id = $this->membershipTypeCreate( $contactID );
        $params = array( 'id'=> $id );        
        $membershiptype = & civicrm_membership_types_get( $params );
                
        $this->assertEqual($membershiptype[$id]['name'],'General');
        $this->assertEqual($membershiptype[$id]['member_of_contact_id'],'1');
        $this->assertEqual($membershiptype[$id]['contribution_type_id'],'1');
        $this->assertEqual($membershiptype[$id]['duration_unit'],'year');
        $this->assertEqual($membershiptype[$id]['duration_interval'],'1');
        $this->assertEqual($membershiptype[$id]['period_type'],'rolling');
        $this->membershipTypeDelete( $membershiptype[$id]['id'] );
    }
}

?>