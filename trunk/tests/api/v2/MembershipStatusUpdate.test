<?php

require_once 'api/v2/Membership.php';

class TestOfMembershipStatusUpdateAPIV2 extends CiviUnitTestCase {
    
    function setup( ) 
    {
    }

    function testMembershipStatusUpdateEmpty( ) 
    {
        $params = array( );
        $result = civicrm_membership_status_update( $params );
        $this->assertEqual( $result['is_error'], 1 );
    }

    function testMembershipStatusUpdateMissingRequired( ) 
    {
        $params = array( 'title' => 'Does not make sense' );
        $result = civicrm_membership_status_update( $params );
        $this->assertEqual( $result['is_error'], 1 );
    }
    
    function testMembershipStatusUpdate( ) 
    {
        $membershipStatusID = $this->membershipStatusCreate( );
        $params = array( 'id'   => $membershipStatusID,
                         'name' => 'new member',
                         );
        $result = civicrm_membership_status_update( $params );
        $this->assertEqual( $result['is_error'], 0 );
        $this->membershipStatusDelete( $membershipStatusID );
    }
    
    function tearDown( ) 
    {
    }
    
}

