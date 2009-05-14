<?php

require_once 'api/v2/Membership.php';

class TestOfMembershipTypeDelete extends CiviUnitTestCase {

    function get_info( )
    {
        return array(
                     'name'        => 'MembershipType Delete',
                     'description' => 'Test all Membership Type Delete API methods.',
                     'group'       => 'CiviCRM API Tests',
                     );
    }   
    
    function setup( ) 
    {
    }

    function testMembershipTypeDeleteEmpty ( ) {
        $params = array( );
        $return = civicrm_membership_type_delete( $params );
        $this->assertEqual( $return['is_error'], 1 );
    }

    function testMembershipTypeDeleteNotExists ( ) {
        $params = array( 'id' => 'doesNotExist' );
        $return = civicrm_membership_type_delete( $params );
        $this->assertEqual( $return['is_error'], 1 );
    }

    function testMembershipTypeDelete( ) {
        $orgID = $this->organizationCreate( );
        $membershipTypeID = $this->membershipTypeCreate( $orgID );
        $params['id'] = $membershipTypeID;
        $result = civicrm_membership_type_delete( $params );
        $this->assertEqual( $result['is_error'], 0 );
        $this->contactDelete( $orgID );
    }

    function tearDown( ) {
    }

}

