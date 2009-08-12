<?php

require_once 'api/v2/Membership.php';

class TestOfMembershipStatusCreate extends CiviUnitTestCase {
    
    function get_info( )
    {
        return array(
                     'name'        => 'MembershipStatus Create',
                     'description' => 'Test all MembershipStatus Create API methods.',
                     'group'       => 'CiviCRM API Tests',
                     );
    }
   
    function setup( ) {
    }

    function testMembershipStatusCreateEmpty( ) {
        $params = array( );
        $result = civicrm_membership_status_create( $params );
        $this->assertEqual( $result['is_error'], 1 );
    }

    function testMembershipStatusCreateMissingRequired( ) {
        $params = array( 'title' => 'Does not make sense' );
        $result = civicrm_membership_status_create( $params );
        $this->assertEqual( $result['is_error'], 1 );
    }

    function testMembershipStatusCreate( ) {
        $params = array( 'name' => 'test membership status' );
        $result = civicrm_membership_status_create( $params );
        $this->assertEqual( $result['is_error'], 0 );
        $this->assertNotNull( $result['id'] );
        $this->membershipStatusDelete( $result['id'] );
    }

    function tearDown( ) {
    }

}

