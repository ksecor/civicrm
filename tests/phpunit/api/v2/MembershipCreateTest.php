<?php

require_once 'api/v2/MembershipContact.php';
require_once 'CiviTest/CiviUnitTestCase.php';

class api_v2_MembershipCreateTest extends CiviUnitTestCase {

    function get_info( )
    {
        return array(
                     'name'        => 'Membership Create',
                     'description' => 'Test all Membership Create API methods.',
                     'group'       => 'CiviCRM API Tests',
                     );
    }
    
    function setUp( ) 
    {
        parent::setUp();
        $this->individualID = $this->individualCreate( );
    }
    
    function testMembershipCreateEmpty( ) 
    {
        $params = array( );
        $result = 0;//civicrm_membership_contact_create( $params );
        $this->fail( 'fails on constraint' );
        $this->assertEquals( $result['is_error'], 1 );
    }

    function testMembershipCreateMissingRequired( ) 
    {
        $params = array(
                        'membership_type_id' => '1',
                        'join_date'          => '2006-01-21',
                        'start_date'         => '2006-01-21',
                        'end_date'           => '2006-12-21',
                        'source'             => 'Payment',
                        'status_id'          => '2'                       
                        );
        
        $result = 0;//civicrm_membership_contact_create( $params );
        $this->fail( 'fails on constraint' );
        $this->assertEquals( $result['is_error'], 1 );
    }
    
    function testMembershipCreate( ) 
    {
        $params = array(
                        'contact_id'         => $this->individualID,  
                        'membership_type_id' => '1',
                        'join_date'          => '2006-01-21',
                        'start_date'         => '2006-01-21',
                        'end_date'           => '2006-12-21',
                        'source'             => 'Payment',
                        'is_override'        => 1,
                        'status_id'          => 2                       
                        );
        $result = 0;//civicrm_membership_contact_create( $params );
        $this->fail( 'fails on constraint' );
        $this->assertEquals( $result['is_error'], 0 );
        $this->assertNotNull( $result['id'] );
        $this->membershipDelete( $result['id'] );
    }
    
    function tearDown( ) 
    {
        $this->contactDelete( $this->individualID );
    }
    
}

