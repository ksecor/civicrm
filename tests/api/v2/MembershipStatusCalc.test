<?php

require_once 'api/v2/Membership.php';

class TestOfMembershipStatusCalcAPIV2 extends CiviUnitTestCase {
    
    protected $_contactID;
    protected $_contributionTypeID;
    protected $_membershipTypeID;
    protected $_membershipStatusID;

    function get_info( )
    {
        return array(
                     'name'        => 'MembershipStatus Calc',
                     'description' => 'Test all MembershipStatus Calc API methods.',
                     'group'       => 'CiviCRM API Tests',
                     );
    }

    function setup( ) 
    {
        $this->_contactID           = $this->individualCreate( ) ;
        $this->_contributionTypeID  = $this->contributionTypeCreate();
        
        $this->_membershipTypeID    = $this->membershipTypeCreate( $this->_contactID,$this->_contributionTypeID );
        $this->_membershipStatusID  = $this->membershipStatusCreate( 'test status' );
    }
    
    function testMembershipStatusCalcEmpty( ) 
    {
        $calcParams = array( );
        
        $result = civicrm_membership_status_calc( $calcParams );
        $this->assertEqual( $result['is_error'], 1 );
    }
    
    function testMembershipStatusCalcNoMembershipid( ) 
    {
        $calcParams = array( 'title' => 'Does not make sense' );
        
        $result = civicrm_membership_status_calc( $calcParams );
        $this->assertEqual( $result['is_error'], 1 );
    }
    
    function testMembershipStatusCalc( ) 
    {
        $params = array( 
                        'contact_id'         => $this->_contactID, 
                        'membership_type_id' => $this->_membershipTypeID,
                        'join_date'   => '2007-06-14',
                        'start_date'  => '2007-06-14',
                        'end_date'    => '2008-06-13'
                        );
        $membershipID = $this->contactMembershipCreate( $params );
        
        $membershipStatusID = CRM_Core_DAO::getFieldValue('CRM_Member_DAO_Membership',$membershipID,'status_id');
        
        $calcParams = array( 'membership_id' => $membershipID );
        $result = civicrm_membership_status_calc( $calcParams );
        
        $this->assertEqual( $result['is_error'], 0 );
        $this->assertEqual( $membershipStatusID,$result['id'] );
        $this->assertNotNull( $result['id'] );
        
        $this->membershipDelete( $membershipID );
    }
    
    function tearDown( ) 
    {
        $this->membershipStatusDelete( $this->_membershipStatusID ); 
        $this->membershipTypeDelete  ( $this->_membershipTypeID   );
        
        $this->contactDelete         ( $this->_contactID          ) ;
        
        $this->contributionTypeDelete( $this->_contributionTypeID );
    }
    
}

