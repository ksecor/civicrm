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

require_once 'CiviTest/CiviUnitTestCase.php';
require_once 'api/v2/Membership.php';
require_once 'CiviTest/CiviUnitTestCase.php';

class api_v2_MembershipTest extends CiviUnitTestCase
{

    public function setUp()
    {
        //  Connect to the database
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
     *  Test civicrm_membership_delete()
     */
    function testMembershipDelete()
    {
        $result = civicrm_membership_delete($this->_membershipID);

        $this->assertEquals( $result['is_error'], 0,
                             "In line " . __LINE__ );      
   
        $this->assertEquals( $result['result'], 1,
                             "In line " . __LINE__ );   
    }

    /**
     * check civicrm_membership_delete() with empty parameter
     */
    function testMembershipDeleteEmpty( )
    {
        $membershipId = null;
        $result = civicrm_membership_delete($membershipId);
        $this->assertEquals( $result['is_error'], 1,
                             "In line " . __LINE__ );
    }

    /**
     *  Test civicrm_membership_delete() with invalid Membership Id
     */
    function testMembershipDeleteWithInvalidMembershipId( )
    {
        $membershipId = 'membership';
        $result = civicrm_membership_delete($membershipId);
        $this->assertEquals( $result['is_error'], 1,
                             "In line " . __LINE__ );

        $membershipId = 2.4;
        $result = civicrm_membership_delete($membershipId);
        $this->assertEquals( $result['is_error'], 1,
                             "In line " . __LINE__ );

        $membershipId = array('id' => $this->_membershipID);
        $result = civicrm_membership_delete($membershipId);
        $this->assertEquals( $result['is_error'], 1,
                             "In line " . __LINE__ );
    }

    /**
     *  All other methods calls MembershipType and MembershipContact
     *  api.
     */
    
} 