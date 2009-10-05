<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.0                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/


require_once 'api/v2/Membership.php';
require_once 'api/v2/MembershipStatus.php';
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

    function setUp( ) 
    {
        parent::setUp();

        $this->_contactID           = $this->individualCreate( ) ;
        
        $this->_membershipTypeID    = $this->membershipTypeCreate( $this->_contactID  );
        $this->_membershipStatusID  = $this->membershipStatusCreate( 'test status' );
    }

    function tearDown( ) 
    {
    }

///////////////// civicrm_membership_status_create methods

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

///////////////// civicrm_membership_status_update methods

    function testUpdateWrongParamsType( ) {
        $params = 1;
        $result = civicrm_membership_status_update( $params );
        $this->assertEquals( $result['is_error'], 1,"In line " . __LINE__ );
    }

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

///////////////// civicrm_membership_status_calc methods

    function testCalcWrongParamsType()
    {
        $params = 'incorrect value';
        $result = civicrm_membership_status_calc( $params );
        $this->assertEquals( $result['is_error'], 1, "In line " . __LINE__ );
    }

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
        $join_date = new DateTime();
        $start_date = new DateTime();
        $end_date = new DateTime();
        $join_date->modify("-5 months");
        $start_date->modify("-5 months");
        $end_date->modify("+7 months");

        $params = array( 'contact_id'         => $this->_contactID, 
                         'membership_type_id' => $this->_membershipTypeID,
                         'membership_status_id' => $this->_membershipStatusID,
                         'join_date'   => $join_date->format('Y-m-d'),
                         'start_date'  => $start_date->format('Y-m-d'),
                         'end_date'    => $end_date->format('Y-m-d') );
                         
        $membershipID = $this->contactMembershipCreate( $params );
        $membershipStatusID = CRM_Core_DAO::getFieldValue('CRM_Member_DAO_Membership',$membershipID,'status_id');
        $calcParams = array( 'membership_id' => $membershipID );
        $result = civicrm_membership_status_calc( $calcParams );
        $this->assertEquals( $result['is_error'], 0 );
        $this->assertEquals( $membershipStatusID,$result['id'] );
        $this->assertNotNull( $result['id'] );
    }

///////////////// civicrm_membership_status_delete methods

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

