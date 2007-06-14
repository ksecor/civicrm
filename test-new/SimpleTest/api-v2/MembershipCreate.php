<?php

require_once 'api/v2/Membership.php';

class TestOfMembershipCreateAPIV2 extends CiviUnitTestCase {
    
    function setup( ) 
    {
        $this->individualID = $this->individualCreate( );
    }
    
    function testMembershipCreateEmpty( ) 
    {
        $params = array( );
        $result = civicrm_contact_membership_create( $params );
        $this->assertEqual( $result['is_error'], 1 );
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
        
        $result = civicrm_contact_membership_create( $params );
        $this->assertEqual( $result['is_error'], 1 );
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
                        'status_id'          => '2'                       
                        );
        $result = civicrm_contact_membership_create( $params );
        $this->assertEqual( $result['is_error'], 0 );
        $this->assertNotNull( $result['id'] );
        $this->membershipDelete( $result['id'] );
    }
    
    function tearDown( ) 
    {
        $this->contactDelete( $this->individualID );
    }
    
}

?>