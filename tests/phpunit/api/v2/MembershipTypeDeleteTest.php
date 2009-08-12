<?php

require_once 'api/v2/Membership.php';
require_once 'CiviTest/CiviUnitTestCase.php';

class api_v2_MembershipTypeDeleteTest extends CiviUnitTestCase {

    function get_info( )
    {
        return array(
                     'name'        => 'MembershipType Delete',
                     'description' => 'Test all Membership Type Delete API methods.',
                     'group'       => 'CiviCRM API Tests',
                     );
    }   
    
    function setUp( ) 
    {
        parent::setUp();
    }

    function testMembershipTypeDeleteEmpty ( ) {
        $params = array( );
        $return = civicrm_membership_type_delete( $params );
        $this->assertEquals( $return['is_error'], 1 );
    }

    function testMembershipTypeDeleteNotExists ( ) {
        $params = array( 'id' => 'doesNotExist' );
        $return = civicrm_membership_type_delete( $params );
        $this->assertEquals( $return['is_error'], 1 );
    }

    function testMembershipTypeDelete( ) {
        $orgID = $this->organizationCreate( );
        $membershipTypeID = $this->membershipTypeCreate( $orgID );
        $params['id'] = $membershipTypeID;
        $result = civicrm_membership_type_delete( $params );
        $this->assertEquals( $result['is_error'], 0 );
        $this->contactDelete( $orgID );
    }

    function tearDown( ) {
    }

}

