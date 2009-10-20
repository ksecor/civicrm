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

require_once 'api/v2/MembershipContact.php';
require_once 'api/v2/Membership.php';
require_once 'api/v2/MembershipType.php';
require_once 'api/v2/MembershipStatus.php';
require_once 'CiviTest/CiviUnitTestCase.php';

/**
 * Test class for MembershipContact API - civicrm_membership_contact_*
 *
 *  @package   CiviCRM
 */
class api_v2_MembershipContactTest extends CiviUnitTestCase {

    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp( ) 
    {
        parent::setUp();

        $this->_contactID           = $this->individualCreate( ) ;
        $this->_membershipTypeID    = $this->membershipTypeCreate( $this->_contactID  );
        $this->_membershipStatusID  = $this->membershipStatusCreate( 'test status' );                

        $params = array(
                        'contact_id'         => $this->_contactID,  
                        'membership_type_id' => $this->_membershipTypeID,
                        'join_date'          => '2009-01-21',
                        'start_date'         => '2009-01-21',
                        'end_date'           => '2009-12-21',
                        'source'             => 'Payment',
                        'is_override'        => 1,
                        'status_id'          => $this->_membershipStatusID
                        );
        
        $this->_membershipID = $this->contactMembershipCreate( $params );
        
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown( ) 
    {
    }

///////////////// civicrm_contact_memberships_get methods

    /**
     * Test civicrm_contact_memberships_get with empty params.
     * Error expected.
     */
    function testGetWithEmptyParams()
    {
        $params = array();
        $result = & civicrm_contact_memberships_get( $params );
        $this->assertEquals( $result['is_error'], 1,
                             "In line " . __LINE__ );
    }

    /**
     * Test civicrm_contact_memberships_get with params with wrong type.
     * Gets treated as contact_id, memberships expected.
     */
    function testGetWithWrongParamsType()
    {
        $params = 'a string';
        $result = & civicrm_contact_memberships_get( $params );
        $this->assertEquals( $result['is_error'], 1,
                             "In line " . __LINE__ );
    }


    /**
     * Test civicrm_contact_memberships_get with params not array.
     * Gets treated as contact_id, memberships expected.
     */
    function testGetWithParamsContactId()
    {
        $membership =& civicrm_contact_memberships_get( $this->_contactID );

        $result = $membership[$this->_contactID][$this->_membershipID];

        $this->assertEquals($result['contact_id'],         $this->_contactID, "In line " . __LINE__);
        $this->assertEquals($result['membership_type_id'], $this->_membershipTypeID, "In line " . __LINE__);
        $this->assertEquals($result['status_id'],          $this->_membershipStatusID, "In line " . __LINE__);
        $this->assertEquals($result['join_date'],          '2009-01-21', "In line " . __LINE__);
        $this->assertEquals($result['start_date'],         '2009-01-21', "In line " . __LINE__);
        $this->assertEquals($result['end_date'],           '2009-12-21', "In line " . __LINE__);
        $this->assertEquals($result['source'],             'Payment', "In line " . __LINE__);
        $this->assertEquals($result['is_override'],         1, "In line " . __LINE__);        
    }
        
    /**
     * Test civicrm_contact_memberships_get with proper params.
     * Memberships expected.
     */
    function testGet()
    {
        $params = array ( 'contact_id' => $this->_contactID );

        $membership =& civicrm_contact_memberships_get( $params );

        $result = $membership[$this->_contactID][$this->_membershipID];

        $this->assertEquals($result['contact_id'],         $this->_contactID, "In line " . __LINE__);
        $this->assertEquals($result['membership_type_id'], $this->_membershipTypeID, "In line " . __LINE__);
        $this->assertEquals($result['status_id'],          $this->_membershipStatusID, "In line " . __LINE__);
        $this->assertEquals($result['join_date'],          '2009-01-21', "In line " . __LINE__);
        $this->assertEquals($result['start_date'],         '2009-01-21', "In line " . __LINE__);
        $this->assertEquals($result['end_date'],           '2009-12-21', "In line " . __LINE__);
        $this->assertEquals($result['source'],             'Payment', "In line " . __LINE__);
        $this->assertEquals($result['is_override'],         1, "In line " . __LINE__);        
    }


///////////////// civicrm_membership_contact_create methods

    /**
     * Test civicrm_contact_memberships_create with empty params.
     * Error expected.
     */    
    function testCreateWithEmptyParams() 
    {
        $params = array();
        $result = civicrm_membership_contact_create( $params );
        $this->assertEquals( $result['is_error'], 1 );
    }

    /**
     * Test civicrm_contact_memberships_create with params with wrong type.
     * Error expected.
     */
    function testCreateWithParamsString()
    {
        $params = 'a string';
        $result = & civicrm_contact_membership_create( $params );
        $this->assertEquals( $result['is_error'], 1,
                             "In line " . __LINE__ );
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
        $this->assertEquals( $result['is_error'], 1 );
    }
    
    function testMembershipCreate( ) 
    {
        $params = array(
                        'contact_id'         => $this->_contactID,  
                        'membership_type_id' => $this->_membershipTypeID,
                        'join_date'          => '2006-01-21',
                        'start_date'         => '2006-01-21',
                        'end_date'           => '2006-12-21',
                        'source'             => 'Payment',
                        'is_override'        => 1,
                        'status_id'          => $this->_membershipStatusID                       
                        );

        $result = civicrm_contact_membership_create( $params );
        $this->assertEquals( $result['is_error'], 0 );
        $this->assertNotNull( $result['id'] );
    }

///////////////// civicrm_membership_delete methods

    /**
     * Test civicrm_contact_memberships_delete with params with wrong type.
     * Error expected.
     */
    function testDeleteWithParamsString()
    {
        $params = 'a string';
        $result = & civicrm_contact_membership_create( $params );
        $this->assertEquals( $result['is_error'], 1,
                             "In line " . __LINE__ );
    }
    
    function testMembershipDeleteEmpty( ) 
    {
        $params = array( );
        $result = civicrm_membership_delete( $params );
        $this->assertEquals( $result['is_error'], 1 );
    }

    function testMembershipDelete( ) 
    {
        $result = civicrm_membership_delete( $this->_membershipID );
        $this->assertEquals( $result['is_error'], 0 );
    }
    
}

