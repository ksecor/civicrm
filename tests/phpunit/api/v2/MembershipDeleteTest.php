<?php

require_once 'api/v2/Membership.php';
require_once 'CiviTest/CiviUnitTestCase.php';

class api_v2_MembershipDeleteTest extends CiviUnitTestCase {
    protected $_contactID;
    protected $_contributionTypeID;
    protected $_membershipTypeID;
    protected $_membershipStatusID;

    function get_info( )
    {
        return array(
                     'name'        => 'Membership Delete',
                     'description' => 'Test all Membership Delete API methods.',
                     'group'       => 'CiviCRM API Tests',
                     );
    }

    function setUp() 
    {
        parent::setUp();
        $this->_contactID           = $this->individualCreate( ) ;
        $this->_contributionTypeID  = $this->contributionTypeCreate();
        $this->_membershipTypeID    = $this->membershipTypeCreate( $this->_contactID,$this->_contributionTypeID );
        $this->_membershipStatusID  = $this->membershipStatusCreate( 'test status' );
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

    function tearDown( ) 
    {
        $this->membershipStatusDelete( $this->_membershipStatusID ); 
        $this->membershipTypeDelete  ( $this->_membershipTypeID   );
        $this->contactDelete         ( $this->_contactID          ) ;
        $this->contributionTypeDelete( $this->_contributionTypeID );
    }

}

