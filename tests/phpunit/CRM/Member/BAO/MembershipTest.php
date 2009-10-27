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
require_once 'CiviTest/Contact.php';
require_once 'CiviTest/Custom.php';
require_once 'CRM/Member/BAO/Membership.php';
require_once 'CiviTest/ContributionPage.php';
require_once 'CiviTest/Membership.php';
require_once 'api/v2/MembershipType.php';
require_once 'api/v2/MembershipStatus.php';
require_once 'CRM/Member/BAO/MembershipType.php';

class CRM_Member_BAO_MembershipTest extends CiviUnitTestCase
{
    function get_info( ) 
    {
        return array(
                     'name'        => 'Membership BAOs',
                     'description' => 'Test all Member_BAO_Membership methods.',
                     'group'       => 'CiviCRM BAO Tests',
                     );
    }
    
    function setUp( ) 
    {
        parent::setUp();
        
        $this->_contactID           = $this->organizationCreate( ) ;
        $this->_membershipTypeID    = $this->membershipTypeCreate( $this->_contactID );
        $this->_membershipStatusID  = $this->membershipStatusCreate( 'test status' );
    }

    function testCreate( )
    {

        $contactId = Contact::createIndividual( );
        
        $params = array(
                        'contact_id'         => $contactId,  
                        'membership_type_id' => $this->_membershipTypeID,
                        'join_date'          => '2006-01-21',
                        'start_date'         => '2006-01-21',
                        'end_date'           => '2006-12-21',
                        'source'             => 'Payment',
                        'is_override'        => 1,
                        'status_id'          => $this->_membershipStatusID
                        );
        $ids = array();
        CRM_Member_BAO_Membership::create( $params, $ids );

        $membershipId = $this->assertDBNotNull( 'CRM_Member_BAO_Membership', $contactId, 'id', 
                                                'contact_id', 'Database check for created membership.' );
        
        // Now call create() to modify an existing Membership
        
        $params = array( );
        $params = array(
                        'contact_id'         => $contactId,  
                        'membership_type_id' => $this->_membershipTypeID,
                        'join_date'          => '2006-01-21',
                        'start_date'         => '2006-01-21',
                        'end_date'           => '2006-12-21',
                        'source'             => 'Payment',
                        'is_override'        => 1,
                        'status_id'          => $this->_membershipStatusID
                        );
        $ids = array(
                     'membership' => $membershipId
                     );
        CRM_Member_BAO_Membership::create( $params, $ids );
        
        $membershipTypeId = $this->assertDBNotNull( 'CRM_Member_BAO_Membership', $contactId,
                                                    'membership_type_id', 'contact_id',
                                                    'Database check on updated membership record.' );
        $this->assertEquals( $membershipTypeId, 2, 'Verify membership type id is 2.');
        
        Contact::delete( $contactId );

    }

    function testGetValues( )
    {
//        $this->markTestSkipped( 'causes mysterious exit, needs fixing!' );    
        //  Calculate membership dates based on the current date
        $now           = time( );
        $year_from_now = $now + ( 365 * 24 * 60 * 60 );
        $last_month    = $now - ( 30 * 24 * 60 * 60 );
        $year_from_last_month = $last_month + ( 365 * 24 * 60 * 60 );
        
        $contactId = Contact::createIndividual( );
        
        $params = array(
                        'contact_id'         => $contactId,  
                        'membership_type_id' => '1',
                        'join_date'          => date( 'Y-m-d' ),
                        'start_date'         => date( 'Y-m-d' ),
                        'end_date'           => date( 'Y-m-d', $year_from_now ),
                        'source'             => 'Payment',
                        'is_override'        => 1,
                        'status_id'          => $this->_membershipStatusID
                        );
        $ids = array();
        CRM_Member_BAO_Membership::create( $params, $ids );

        $membershipId1 = $this->assertDBNotNull( 'CRM_Member_BAO_Membership', $contactId, 'id', 
                                                'contact_id', 'Database check for created membership.' );

        $params = array(
                        'contact_id'         => $contactId,  
                        'membership_type_id' => '2',
                        'join_date'          => date( 'Y-m-d', $last_month ),
                        'start_date'         => date( 'Y-m-d', $last_month ),
                        'end_date'           => date( 'Y-m-d', $year_from_last_month ),
                        'source'             => 'Source123',
                        'is_override'        => 0,
                        'status_id'          => $this->_membershipStatusID
                        );
        $ids = array();
        CRM_Member_BAO_Membership::create( $params, $ids );

        $membershipId2 = $this->assertDBNotNull( 'CRM_Member_BAO_Membership', 'source123', 'id', 
                                                'source', 'Database check for created membership.' );

        $membership = array('contact_id' => $contactId);
        $membershipValues = array(); 
        CRM_Member_BAO_Membership::getValues($membership, $membershipValues, true);
        
        $this->assertEquals( $membershipValues[$membershipId1]['membership_id'], $membershipId1, 'Verify membership record 1 is fetched.');
        
        $this->assertEquals( $membershipValues[$membershipId2]['membership_id'], $membershipId2, 'Verify membership record 2 is fetched.');
    }

    function testRetrieve ()
    {
        $contactId = Contact::createIndividual( );
        
        $params = array(
                        'contact_id'         => $contactId,  
                        'membership_type_id' => '1',
                        'join_date'          => '2006-01-21',
                        'start_date'         => '2006-01-21',
                        'end_date'           => '2006-12-21',
                        'source'             => 'Payment',
                        'is_override'        => 1,
                        'status_id'          => $this->_membershipStatusID
                        );
        $ids = array();
        CRM_Member_BAO_Membership::create( $params, $ids );

        $membershipId = $this->assertDBNotNull( 'CRM_Member_BAO_Membership', $contactId, 'id', 
                                                'contact_id', 'Database check for created membership.' );
        $params = array( 'id' => $membershipId ); 
        CRM_Member_BAO_Membership::retrieve( $params, $values );
        $this->assertEquals( $values['id'], $membershipId, 'Verify membership record is retrieved.');
    }

    function testActiveMembers ()
    {
        $contactId = Contact::createIndividual( );
        
        $params = array(
                        'contact_id'         => $contactId,  
                        'membership_type_id' => '1',
                        'join_date'          => '2006-01-21',
                        'start_date'         => '2006-01-21',
                        'end_date'           => '2006-12-21',
                        'source'             => 'Payment',
                        'is_override'        => 1,
                        'status_id'          => $this->_membershipStatusID
                        );
        $ids = array();
        CRM_Member_BAO_Membership::create( $params, $ids );

        $membershipId1 = $this->assertDBNotNull( 'CRM_Member_BAO_Membership', $contactId, 'id', 
                                                'contact_id', 'Database check for created membership.' );
        $params = array( 'id' => $membershipId1 ); 
        CRM_Member_BAO_Membership::retrieve( $params, $values1 );
        $membership = array($membershipId1 => $values1);
        
        $params = array(
                        'contact_id'         => $contactId,  
                        'membership_type_id' => '1',
                        'join_date'          => '2006-01-21',
                        'start_date'         => '2006-01-21',
                        'end_date'           => '2006-12-21',
                        'source'             => 'PaySource',
                        'is_override'        => 1,
                        'status_id'          => $this->_membershipStatusID
                        );
        $ids = array();
        CRM_Member_BAO_Membership::create( $params, $ids );
        
        $membershipId2 = $this->assertDBNotNull( 'CRM_Member_BAO_Membership', 'PaySource', 'id', 
                                                 'source', 'Database check for created membership.' );
        
        $params = array( 'id' => $membershipId2 ); 
        CRM_Member_BAO_Membership::retrieve( $params, $values2 );
        $membership[$membershipId2] = $values2;
        
        $activeMembers = CRM_Member_BAO_Membership::activeMembers( $membership );
        $inActiveMembers = CRM_Member_BAO_Membership::activeMembers( $membership, 'inactive');
        
        $this->assertEquals( $activeMembers[$membershipId1]['id'], $membership[$membershipId1]['id'], 'Verify active membership record is retrieved.');
        
        $this->assertEquals( $inActiveMembers[$membershipId2]['id'], $membership[$membershipId2]['id'], 'Verify inactive membership record is retrieved.');
    }
    
    function testDeleteMembership ()
    {
        $contactId = Contact::createIndividual( );
        
        $params = array(
                        'contact_id'         => $contactId,  
                        'membership_type_id' => '1',
                        'join_date'          => '2006-01-21',
                        'start_date'         => '2006-01-21',
                        'end_date'           => '2006-12-21',
                        'source'             => 'Payment',
                        'is_override'        => 1,
                        'status_id'          => $this->_membershipStatusID
                        );
        $ids = array();
        CRM_Member_BAO_Membership::create( $params, $ids );
        
        $membershipId = $this->assertDBNotNull( 'CRM_Member_BAO_Membership', $contactId, 'id', 
                                                'contact_id', 'Database check for created membership.' );
        CRM_Member_BAO_Membership::deleteMembership( $membershipId );
        
        $membershipId = $this->assertDBNull( 'CRM_Member_BAO_Membership', $contactId, 'id', 
                                             'contact_id', 'Database check for deleted membership.' );
    }
    
    function testGetContactMembership ()
    {
        $contactId = Contact::createIndividual( );
        
        $params = array(
                        'contact_id'         => $contactId,  
                        'membership_type_id' => '1',
                        'join_date'          => '2006-01-21',
                        'start_date'         => '2006-01-21',
                        'end_date'           => '2006-12-21',
                        'source'             => 'Payment',
                        'is_override'        => 1,
                        'status_id'          => $this->_membershipStatusID
                        );
        $ids = array();
        CRM_Member_BAO_Membership::create( $params, $ids );
        
        $membershipId = $this->assertDBNotNull( 'CRM_Member_BAO_Membership', $contactId, 'id', 
                                                'contact_id', 'Database check for created membership.' );
        $membership = CRM_Member_BAO_Membership::getContactMembership($contactId, 1, 0 );
        
        $this->assertEquals( $membership['id'], $membershipId, 'Verify membership record is retrieved.' );
    }


    /*
     * Function to get the contribution 
     * page id from the membership record
     */

    function testgetContributionPageId( )  
    {
        $contactId = Contact::createIndividual( );
               
        $params = array(
                        'contact_id'         => $contactId,  
                        'membership_type_id' => '1',
                        'join_date'          => '2008-01-21',
                        'start_date'         => '2008-01-21',
                        'end_date'           => '2008-12-21',
                        'source'             => 'Payment',
                        'is_override'        => 1,
                        'status_id'          => $this->_membershipStatusID
                        );
        $ids = array();
        CRM_Member_BAO_Membership::create( $params, $ids );
        
        
        $membershipId = $this->assertDBNotNull( 'CRM_Member_BAO_Membership', $contactId, 'id', 
                                                'contact_id', 'Database check for created membership.' );
        $membership[$membershipId]['renewPageId'] = CRM_Member_BAO_Membership::getContributionPageId( $membershipId );
               
        Contact::delete( $contactId );
    }
    /*
     * Function to get membership joins/renewals 
     * for a specified membership
     * type.
     *
     */

    function testgetMembershipStarts( ) 
    {
        $membershipType     = Membership::createMembershipType( );
     
        $contactId = Contact::createIndividual( );
     
               
        $params = array(
                        'contact_id'         => $contactId,  
                        'membership_type_id' => $membershipType->id,
                        'join_date'          => '2008-01-21',
                        'start_date'         => '2008-01-21',
                        'end_date'           => '2008-12-21',
                        'source'             => 'Payment',
                        'is_override'        => 1,
                        'status_id'          => $this->_membershipStatusID
                        );
        $ids = array();
        CRM_Member_BAO_Membership::create( $params, $ids );

        $membershipId = $this->assertDBNotNull( 'CRM_Member_BAO_Membership', $contactId, 'id', 
                                                'contact_id', 'Database check for created membership.' );
        $yearStart        = date('Y').'0101';
        $currentDate      = date('Ymd');
        CRM_Member_BAO_Membership::getMembershipStarts( $membershipType->id, $yearStart,$currentDate );
        
    }

    /*
     * Function to get a count of membership for a specified membership type,
     * optionally for a specified date.
     *
     */

    function testGetMembershipCount( ) 
    {
        $membershipType = Membership::createMembershipType( );
     
        $contactId      = Contact::createIndividual( );
     
               
        $params = array(
                        'contact_id'         => $contactId,  
                        'membership_type_id' => $membershipType->id,
                        'join_date'          => '2008-01-21',
                        'start_date'         => '2008-01-21',
                        'end_date'           => '2008-12-21',
                        'source'             => 'Payment',
                        'is_override'        => 1,
                        'status_id'          => $this->_membershipStatusID
                        );
        $ids = array();
        CRM_Member_BAO_Membership::create( $params, $ids );
        
        $membershipId = $this->assertDBNotNull( 'CRM_Member_BAO_Membership', $contactId, 'id', 
                                                'contact_id', 'Database check for created membership.' );
        $currentDate  = date('Ymd');
        $test         = 0;
        CRM_Member_BAO_Membership::getMembershipCount( $membershipType->id, $currentDate,$test );        
    }


    /*
     * Function check the status of the membership before adding membership for a contact
     *
     */

    function teststatusAvilability( ) 
    {

        $contactId  = Contact::createIndividual( );

        CRM_Member_BAO_Membership::statusAvilability( $contactId );
    }

    /*
     * Function take sort name of contact during 
     * batch update member via profile
     *
     */

    function testsortName( ) 
    {
        $membershipType = Membership::createMembershipType( );
        
        $contactId      = Contact::createIndividual( );
        
        $params = array(
                        'contact_id'         => $contactId,  
                        'membership_type_id' => $membershipType->id,
                        'join_date'          => '2008-01-21',
                        'start_date'         => '2008-01-21',
                        'end_date'           => '2008-12-21',
                        'source'             => 'Payment',
                        'is_override'        => 1,
                        'status_id'          => $this->_membershipStatusID
                        );
        $ids = array();
        
        CRM_Member_BAO_Membership::create( $params, $ids );

        $membershipId = $this->assertDBNotNull( 'CRM_Member_BAO_Membership', $contactId, 'id', 
                                                'contact_id', 'Database check for created membership.' );

        CRM_Member_BAO_Membership::sortName( $membershipId );

        $this->assertDBCompareValue( 'CRM_Contact_DAO_Contact', $contactId , 'sort_name', 'id','Doe, John',
                                     'Database check for sort name record.' );

    }

    /*
     * Function to delete related memberships
     *
     */

    function testdeleteRelatedMemberships( ) 
    {
        $contactId = Contact::createIndividual( );
        $membershipType = Membership::createMembershipType( );
        
        $params = array(
                        'contact_id'         => $contactId,  
                        'membership_type_id' => $membershipType->id,
                        'join_date'          => '2008-01-21',
                        'start_date'         => '2008-01-21',
                        'end_date'           => '2008-12-21',
                        'source'             => 'Payment',
                        'is_override'        => 1,
                        'status_id'          => $this->_membershipStatusID
                        );
        $ids = array();
        
        CRM_Member_BAO_Membership::create( $params, $ids );

        $membershipId = $this->assertDBNotNull( 'CRM_Member_BAO_Membership', $contactId, 'id', 
                                                'contact_id', 'Database check for created membership.' );
        
        CRM_Member_BAO_Membership::deleteRelatedMemberships( $membershipId );
        
    }
}
?>