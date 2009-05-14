<?php

require_once 'CiviTestCase.php';
require_once 'Contact.php';
require_once 'CRM/Member/BAO/Membership.php';
require_once 'ContributionPage.php';
require_once 'Membership.php';
require_once 'CRM/Member/BAO/MembershipType.php';
class BAO_Member_Membership extends CiviTestCase 
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
    }

    function testCreate( )
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
                        'status_id'          => 2                       
                        );
        $ids = array();
        CRM_Member_BAO_Membership::create( $params, $ids );

        $membershipId = $this->assertDBNotNull( 'CRM_Member_BAO_Membership', $contactId, 'id', 
                                                'contact_id', 'Database check for created membership.' );
        
        // Now call create() to modify an existing Membership
        
        $params = array( );
        $params = array(
                        'contact_id'         => $contactId,  
                        'membership_type_id' => '2',
                        'join_date'          => '2006-01-21',
                        'start_date'         => '2006-01-21',
                        'end_date'           => '2006-12-21',
                        'source'             => 'Payment',
                        'is_override'        => 1,
                        'status_id'          => 2    
                        );
        $ids = array(
                     'membership' => $membershipId
                     );
        CRM_Member_BAO_Membership::create( $params, $ids );
        
        $membershipTypeId = $this->assertDBNotNull( 'CRM_Member_BAO_Membership', $contactId,
                                                    'membership_type_id', 'contact_id',
                                                    'Database check on updated membership record.' );
        $this->assertEqual( $membershipTypeId, 2, 'Verify membership type id is 2.');
        
        Contact::delete( $contactId );

    }

    function testGetValues( )
    {
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
                        'status_id'          => 2                       
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
                        'status_id'          => 2                       
                        );
        $ids = array();
        CRM_Member_BAO_Membership::create( $params, $ids );

        $membershipId2 = $this->assertDBNotNull( 'CRM_Member_BAO_Membership', 'source123', 'id', 
                                                'source', 'Database check for created membership.' );

        $membership = array('contact_id' => $contactId);
        $membershipValues = array(); 
        CRM_Member_BAO_Membership::getValues($membership, $membershipValues, true);
        
        $this->assertEqual( $membershipValues[$membershipId1]['membership_id'], $membershipId1, 'Verify membership record 1 is fetched.');
        
        $this->assertEqual( $membershipValues[$membershipId2]['membership_id'], $membershipId2, 'Verify membership record 2 is fetched.');
        Contact::delete( $contactId );
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
                        'status_id'          => 2                       
                        );
        $ids = array();
        CRM_Member_BAO_Membership::create( $params, $ids );

        $membershipId = $this->assertDBNotNull( 'CRM_Member_BAO_Membership', $contactId, 'id', 
                                                'contact_id', 'Database check for created membership.' );
        $params = array( 'id' => $membershipId ); 
        CRM_Member_BAO_Membership::retrieve( $params, $values );
        $this->assertEqual( $values['id'], $membershipId, 'Verify membership record is retrieved.');
        Contact::delete( $contactId );
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
                        'status_id'          => 2                       
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
                        'status_id'          => 4                       
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
        
        $this->assertEqual( $activeMembers[$membershipId1]['id'], $membership[$membershipId1]['id'], 'Verify active membership record is retrieved.');
        
        $this->assertEqual( $inActiveMembers[$membershipId2]['id'], $membership[$membershipId2]['id'], 'Verify inactive membership record is retrieved.');
        
        Contact::delete( $contactId );
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
                        'status_id'          => 2                       
                        );
        $ids = array();
        CRM_Member_BAO_Membership::create( $params, $ids );
        
        $membershipId = $this->assertDBNotNull( 'CRM_Member_BAO_Membership', $contactId, 'id', 
                                                'contact_id', 'Database check for created membership.' );
        CRM_Member_BAO_Membership::deleteMembership( $membershipId );
        
        $membershipId = $this->assertDBNull( 'CRM_Member_BAO_Membership', $contactId, 'id', 
                                             'contact_id', 'Database check for deleted membership.' );
        Contact::delete( $contactId );
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
                        'status_id'          => 2                       
                        );
        $ids = array();
        CRM_Member_BAO_Membership::create( $params, $ids );
        
        $membershipId = $this->assertDBNotNull( 'CRM_Member_BAO_Membership', $contactId, 'id', 
                                                'contact_id', 'Database check for created membership.' );
        $membership = CRM_Member_BAO_Membership::getContactMembership($contactId, 1, 0 );
        
        $this->assertEqual( $membership['id'], $membershipId, 'Verify membership record is retrieved.' );
        Contact::delete( $contactId );
    }


    /*
     * Function to build Membership  Block in Contribution Pages
     *
     */
    function testbuildMembershipBlock ( )
    {
        //create membership type 
        $membershipType     = Membership::createMembershipType( );
        //create contribution page
        $contributionPageID = ContributionPage::create( );
        //create membership blok and add to contribution page
        $membershipBlock    = Membership::createMembershipBlock( $membershipType->id , $contributionPageID );
        
        $getMembershipBlock = CRM_Member_BAO_Membership::getMembershipBlock( $contributionPageID );
        require_once 'CRM/Contribute/Form/Contribution/Main.php';
        $main =new  CRM_Contribute_Form_Contribution_Main();
        $main->_id = 1;
        $main->_mode = 'test';
        $main->_membershipBlock = array(
                                        'id'                  => $membershipBlock->id,      //block id
                                        'entity_table'        => 'civicrm_contribution_page',
                                        'entity_id'           => $contributionPageID, //page id
                                        'membership_types'    => $membershipType->id,
                                        'display_min_fee'     => 1,
                                        'is_separate_payment' => 0,
                                        'new_title'           => 'Membership Levels and Fees',
                                        'new_text'            => 'Membership Levels and Fees',
                                        'renewal_title'       => 'Renew or Upgrade Your Membership',
                                        'renewal_text'        => 'Renew or Upgrade Your Membership',
                                        'is_required'         => 1,
                                        'is_active'           => 1
                                        );
      $buildMemberBlock = CRM_Member_BAO_Membership::buildMembershipBlock( $main, $contributionPageID, true , null,null,true,null );
     
      //delete membership type
      CRM_Member_BAO_MembershipType::del( $membershipType->id );
      //delete membership block
      Membership::deleteMembershipBlock( $membershipBlock->id);
      //delete contribution page
      ContributionPage::delete( $contributionPageID );
      //delete contact id
      Contact::delete( $membershipType->orgnizationID );
     
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
                        'status_id'          => 2                       
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
                        'status_id'          => 2                       
                        );
        $ids = array();
        CRM_Member_BAO_Membership::create( $params, $ids );

        $membershipId = $this->assertDBNotNull( 'CRM_Member_BAO_Membership', $contactId, 'id', 
                                                'contact_id', 'Database check for created membership.' );
        $yearStart        = date('Y').'0101';
        $currentDate      = date('Ymd');
        CRM_Member_BAO_Membership::getMembershipStarts( $membershipType->id, $yearStart,$currentDate );
        
        Contact::delete( $contactId ); 
        
        CRM_Member_BAO_MembershipType::del( $membershipType->id );
       
        Contact::delete( $membershipType->orgnizationID );
       
      
    }

    /*
     * Function to get a count of membership for a specified membership type,
     * optionally for a specified date.
     *
     */

    function testgetMembershipCount( ) 
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
                        'status_id'          => 2                       
                        );
        $ids = array();
        CRM_Member_BAO_Membership::create( $params, $ids );
        
        $membershipId = $this->assertDBNotNull( 'CRM_Member_BAO_Membership', $contactId, 'id', 
                                                'contact_id', 'Database check for created membership.' );
        $currentDate  = date('Ymd');
        $test         = 0;
        CRM_Member_BAO_Membership::getMembershipCount( $membershipType->id, $currentDate,$test );
        
        Contact::delete( $contactId ); 
        
        CRM_Member_BAO_MembershipType::del( $membershipType->id );
        
        Contact::delete( $membershipType->orgnizationID );
              
    }


    /*
     * Function check the status of the membership before adding membership for a contact
     *
     */

    function teststatusAvilability( ) 
    {

        $contactId  = Contact::createIndividual( );

        CRM_Member_BAO_Membership::statusAvilability( $contactId );

        Contact::delete( $contactId );
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
                        'status_id'          => 2                       
                        );
        $ids = array();
        
        CRM_Member_BAO_Membership::create( $params, $ids );

        $membershipId = $this->assertDBNotNull( 'CRM_Member_BAO_Membership', $contactId, 'id', 
                                                'contact_id', 'Database check for created membership.' );

        CRM_Member_BAO_Membership::sortName( $membershipId );

        $this->assertDBCompareValue( 'CRM_Contact_DAO_Contact', $contactId , 'sort_name', 'id','Doe, John',
                                     'Database check for sort name record.' );

        Contact::delete( $contactId );
        
        CRM_Member_BAO_MembershipType::del( $membershipType->id );
        
        Contact::delete( $membershipType->orgnizationID );
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
                        'status_id'          => 2                       
                        );
        $ids = array();
        
        CRM_Member_BAO_Membership::create( $params, $ids );

        $membershipId = $this->assertDBNotNull( 'CRM_Member_BAO_Membership', $contactId, 'id', 
                                                'contact_id', 'Database check for created membership.' );
        
        CRM_Member_BAO_Membership::deleteRelatedMemberships( $membershipId );
        
        Contact::delete( $contactId );
        
        CRM_Member_BAO_MembershipType::del( $membershipType->id );
        
        Contact::delete( $membershipType->orgnizationID );
    }
}
?>