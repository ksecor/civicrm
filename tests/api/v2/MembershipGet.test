<?php

require_once 'api/v2/Membership.php';

class TestOfMembershipGetAPIV2 extends CiviUnitTestCase 
{
    protected $_contactID;
    protected $_contributionTypeID;
    protected $_membershipTypeID;
    protected $_membershipStatusID;

    function get_info( )
    {
        return array(
                     'name'        => 'Membership Get',
                     'description' => 'Test all Membership Get API methods.',
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
    
    function testMembershipTypeGetWithoutId()
    {
        $membership = & civicrm_contact_memberships_get( $emptyContactID );
        $this->assertEqual( $membership['is_error'], 1 );
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
        
        $this->assertEqual($membership[$this->_contactID][$id]['contact_id'],         $this->_contactID);
        $this->assertEqual($membership[$this->_contactID][$id]['membership_type_id'], $this->_membershipTypeID);
        $this->assertEqual($membership[$this->_contactID][$id]['status_id'],          $this->_membershipStatusID);
        $this->assertEqual($membership[$this->_contactID][$id]['join_date'],          '2007-01-21');
        $this->assertEqual($membership[$this->_contactID][$id]['start_date'],         '2007-01-21');
        $this->assertEqual($membership[$this->_contactID][$id]['end_date'],           '2007-12-21');
        $this->assertEqual($membership[$this->_contactID][$id]['source'],             'Payment' );
        
        $this->membershipDelete( $membership[$this->_contactID][$id]['id'] );
    }

    function tearDown() 
    {
        $this->membershipStatusDelete( $this->_membershipStatusID ); 
        $this->membershipTypeDelete( $this->_membershipTypeID );
        $this->contactDelete( $this->_contactID ) ;
        $this->contributionTypeDelete( $this->_contributionTypeID );
    }
}

