<?php

require_once 'api/v2/Membership.php';

class TestOfMembershipDelete extends CiviUnitTestCase {
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
        $this->_contactID           = $this->individualCreate( ) ;
        $this->_contributionTypeID  = $this->contributionTypeCreate();
        $this->_membershipTypeID    = $this->membershipTypeCreate( $this->_contactID,$this->_contributionTypeID );
        $this->_membershipStatusID  = $this->membershipStatusCreate( 'test status' );
    }

    function testMembershipDeleteEmpty( ) 
    {
        $params = array( );
        $result = civicrm_membership_delete( $params );
        $this->assertEqual( $result['is_error'], 1 );
    }

    function testMembershipDeleteMissingRequired( ) 
    {
        $result = civicrm_membership_delete( $emptyMembershipID );
        $this->assertEqual( $result['is_error'], 1 );
    }

    function testMembershipDelete( ) 
    {
        $params = array( 'contact_id'         => $this->_contactID, 
                         'membership_type_id' => $this->_membershipTypeID, 
                         'status_id'          => $this->_membershipStatusID );
        $membershipID = $this->contactMembershipCreate( $params );
        
        $result = civicrm_membership_delete( $membershipID );
        $this->assertEqual( $result['is_error'], 0 );
    }

    function tearDown( ) 
    {
        $this->membershipStatusDelete( $this->_membershipStatusID ); 
        $this->membershipTypeDelete  ( $this->_membershipTypeID   );
        $this->contactDelete         ( $this->_contactID          ) ;
        $this->contributionTypeDelete( $this->_contributionTypeID );
    }

}

