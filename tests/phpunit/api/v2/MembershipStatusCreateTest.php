<?php

require_once 'api/v2/Membership.php';
require_once 'CiviTest/CiviUnitTestCase.php';

class api_v2_MembershipStatusCreateTest extends CiviUnitTestCase {
    
    function get_info( )
    {
        return array(
                     'name'        => 'MembershipStatus Create',
                     'description' => 'Test all MembershipStatus Create API methods.',
                     'group'       => 'CiviCRM API Tests',
                     );
    }
   
    function setUp( ) {
        parent::setUp();
    }

    function testMembershipStatusCreateEmpty( ) {
        $params = array( );
        $result = civicrm_membership_status_create( $params );
        $this->assertEquals( $result['is_error'], 1 );
    }

    function testMembershipStatusCreateMissingRequired( ) {
        $params = array( 'title' => 'Does not make sense' );
        $result = civicrm_membership_status_create( $params );
        $this->assertEquals( $result['is_error'], 1 );
    }

    function testMembershipStatusCreate( ) {
        $params = array( 'name' => 'test membership status' );
        $result = civicrm_membership_status_create( $params );
        $this->assertEquals( $result['is_error'], 0 );
        $this->assertNotNull( $result['id'] );
        $this->membershipStatusDelete( $result['id'] );
    }

    function tearDown( ) {
    }

}

