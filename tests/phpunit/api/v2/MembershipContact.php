<?php

require_once 'api/v2/MembershipContact.php';
require_once 'api/v2/Membership.php';
require_once 'api/v2/MembershipType.php';
require_once 'api/v2/MembershipStatus.php';
require_once 'CiviTest/CiviUnitTestCase.php';

class api_v2_MembershipTest extends CiviUnitTestCase {

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
        
        $this->_contactID           = $this->individualCreate( ) ;
        $this->_contributionTypeID  = $this->contributionTypeCreate();
        $this->_membershipTypeID    = $this->membershipTypeCreate( $this->_contactID,$this->_contributionTypeID );
        $this->_membershipStatusID  = $this->membershipStatusCreate( 'test status' );                
    }

    function tearDown( ) 
    {
        $this->contactDelete( $this->individualID );

        $this->membershipStatusDelete( $this->_membershipStatusID ); 
        $this->membershipTypeDelete( $this->_membershipTypeID );
        $this->contactDelete( $this->_contactID ) ;
        $this->contributionTypeDelete( $this->_contributionTypeID );
    }

    function testMembershipTypeGetWithoutId()
    {
        $membership = & civicrm_contact_memberships_get( $emptyContactID );
        $this->assertEquals( $membership['is_error'], 1 );
    }
        
    function testMembershipGet()
    {
        $params = array( 'contact_id'         => $this->_contactID, 
                         'membership_type_id' => $this->_membershipTypeID, 
                         'status_id'          => $this->_membershipStatusID, 
                         'is_override'        => 1
                         );
        $id = $this->contactMembershipCreate( $params );
        
        $membership =& civicrm_contact_memberships_get( $this->_contactID );
        
        $this->assertEquals($membership[$this->_contactID][$id]['contact_id'],         $this->_contactID);
        $this->assertEquals($membership[$this->_contactID][$id]['membership_type_id'], $this->_membershipTypeID);
        $this->assertEquals($membership[$this->_contactID][$id]['status_id'],          $this->_membershipStatusID);
        $this->assertEquals($membership[$this->_contactID][$id]['join_date'],          '2007-01-21');
        $this->assertEquals($membership[$this->_contactID][$id]['start_date'],         '2007-01-21');
        $this->assertEquals($membership[$this->_contactID][$id]['end_date'],           '2007-12-21');
        $this->assertEquals($membership[$this->_contactID][$id]['source'],             'Payment' );
        
        $this->membershipDelete( $membership[$this->_contactID][$id]['id'] );
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
    
    function testMembershipDeleteEmpty( ) 
    {
        $params = array( );
        $result = civicrm_membership_delete( $params );
        $this->assertEquals( $result['is_error'], 1 );
    }

    function testMembershipDeleteMissingRequired( ) 
    {
        $result = civicrm_membership_delete( $emptyMembershipID );
        $this->assertEquals( $result['is_error'], 1 );
    }

    function testMembershipDelete( ) 
    {
        $params = array( 'contact_id'         => $this->_contactID, 
                         'membership_type_id' => $this->_membershipTypeID, 
                         'status_id'          => $this->_membershipStatusID );
        $membershipID = $this->contactMembershipCreate( $params );
        
        $result = civicrm_membership_delete( $membershipID );
        $this->assertEquals( $result['is_error'], 0 );
    }
    
}

