<?php

require_once 'api/v2/Membership.php';
require_once 'CiviTest/CiviUnitTestCase.php';

class api_v2_MembershipStatusTest extends CiviUnitTestCase {
    
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
        parent::setUp();

        $this->_contactID           = $this->individualCreate( ) ;
        $this->_contributionTypeID  = $this->contributionTypeCreate();
        
        $this->_membershipTypeID    = $this->membershipTypeCreate( $this->_contactID,$this->_contributionTypeID );
        $this->_membershipStatusID  = $this->membershipStatusCreate( 'test status' );
    }

    function tearDown( ) 
    {
        $this->membershipStatusDelete( $this->_membershipStatusID ); 
        $this->membershipTypeDelete  ( $this->_membershipTypeID   );
        
        $this->contactDelete         ( $this->_contactID          ) ;
        
        $this->contributionTypeDelete( $this->_contributionTypeID );
    }

/// create section
    function testCreateWithEmptyParams( ) {
        $params = array( );
        $result = civicrm_membership_status_create( $params );
        $this->assertEquals( $result['is_error'], 1,"In line " . __LINE__ );
    }

    function testCreateWithWrongParamsType()
    {
        $params = 'a string';
        $result = civicrm_membership_status_create($params);
        $this->assertEquals( $result['is_error'], 1,"In line " . __LINE__ );
    }

    function testCreateWithMissingRequired( ) {
        $params = array( 'title' => 'Does not make sense' );
        $result = civicrm_membership_status_create( $params );
        $this->assertEquals( $result['is_error'], 1,"In line " . __LINE__ );
    }

    function testCreate( ) {
        $params = array( 'name' => 'test membership status' );
        $result = civicrm_membership_status_create( $params );
        $this->assertEquals( $result['is_error'], 0 );
        $this->assertNotNull( $result['id'] );
        $this->membershipStatusDelete( $result['id'] );
    }

///// update section
    function testUpdateWithEmptyParams( )
    {
        $params = array( );
        $result = civicrm_membership_status_update( $params );
        $this->assertEquals( $result['is_error'], 1,"In line " . __LINE__ );
    }

    function testUpdateWithMissingRequired( )
    {
        $params = array( 'title' => 'Does not make sense' );
        $result = civicrm_membership_status_update( $params );
        $this->assertEquals( $result['is_error'], 1,"In line " . __LINE__ );
    }
    
    function testUpdate( ) 
    {
        $membershipStatusID = $this->membershipStatusCreate( );
        $params = array( 'id'   => $membershipStatusID,
                         'name' => 'new member',
                         );
        $result = civicrm_membership_status_update( $params );
        $this->assertEquals( $result['is_error'], 0 );
        $this->membershipStatusDelete( $membershipStatusID );
    }

/// calculate membership section
    function testCalculateStatusWithEmptyParams( )
    {
        $calcParams = array( );
        
        $result = civicrm_membership_status_calc( $calcParams );
        $this->assertEquals( $result['is_error'], 1,"In line " . __LINE__ );
    }
    
    function testCalculateStatusWithNoMembershipID( )
    {
        $calcParams = array( 'title' => 'Does not make sense' );
        
        $result = civicrm_membership_status_calc( $calcParams );
        $this->assertEquals( $result['is_error'], 1,"In line " . __LINE__ );
    }
    
    function testCalculateStatus( )
    {
        $this->markTestSkipped( 'Mysterious exit happens when executing this test... :-(' );        
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
        
        $this->assertEquals( $result['is_error'], 0 );
        $this->assertEquals( $membershipStatusID,$result['id'] );
        $this->assertNotNull( $result['id'] );
        
        $this->membershipDelete( $membershipID );
    }

//// delete section
    function testDeleteEmptyParams( ) {
        $params = array( );
        $result = civicrm_membership_status_delete( $params );
        $this->assertEquals( $result['is_error'], 1,"In line " . __LINE__ );
    }

    function testDeleteWrongParamsType()
    {
        $params = 'incorrect value';
        $result = civicrm_membership_status_delete( $params );
        $this->assertEquals( $result['is_error'], 1, "In line " . __LINE__ );
    }

    function testDeleteWithMissingRequired( ) {
        $params = array( 'title' => 'Does not make sense' );
        $result = civicrm_membership_status_delete( $params );
        $this->assertEquals( $result['is_error'], 1,"In line " . __LINE__ );
    }

    function testDelete( ) {
        $membershipID = $this->membershipStatusCreate( );
        $params = array( 'id' => $membershipID );
        $result = civicrm_membership_status_delete( $params );
        $this->assertEquals( $result['is_error'], 0 );
    }        
}

