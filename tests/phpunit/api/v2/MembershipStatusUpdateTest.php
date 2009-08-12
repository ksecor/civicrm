<?php

require_once 'api/v2/Membership.php';
require_once 'CiviTest/CiviUnitTestCase.php';

class api_v2_MembershipStatusUpdateTest extends CiviUnitTestCase {

    function get_info( )
    {
        return array(
                     'name'        => 'MembershipStatus Update',
                     'description' => 'Test all MembershipStatus Update API methods.',
                     'group'       => 'CiviCRM API Tests',
                     );
    }

    function setUp( ) 
    {
        parent::setUp();
    }

    function testMembershipStatusUpdateEmpty( ) 
    {
        $params = array( );
        $result = civicrm_membership_status_update( $params );
        $this->assertEquals( $result['is_error'], 1 );
    }

    function testMembershipStatusUpdateMissingRequired( ) 
    {
        $params = array( 'title' => 'Does not make sense' );
        $result = civicrm_membership_status_update( $params );
        $this->assertEquals( $result['is_error'], 1 );
    }
    
    function testMembershipStatusUpdate( ) 
    {
        $membershipStatusID = $this->membershipStatusCreate( );
        $params = array( 'id'   => $membershipStatusID,
                         'name' => 'new member',
                         );
        $result = civicrm_membership_status_update( $params );
        $this->assertEquals( $result['is_error'], 0 );
        $this->membershipStatusDelete( $membershipStatusID );
    }
    
    function tearDown( ) 
    {
    }
    
}

