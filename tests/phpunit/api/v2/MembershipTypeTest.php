<?php

require_once 'api/v2/Membership.php';
require_once 'CiviTest/CiviUnitTestCase.php';

class api_v2_MembershipTypeTest extends CiviUnitTestCase 
{
    protected $_contactID;
    protected $_contributionTypeID;

    function get_info( )
    {
        return array(
                     'name'        => 'MembershipType Create',
                     'description' => 'Test all Membership Type Create API methods.',
                     'group'       => 'CiviCRM API Tests',
                     );
    } 
    
    function setUp() 
    {
        parent::setUp();

        $this->_contactID           = $this->organizationCreate( ) ;
        $this->_contributionTypeID  = $this->contributionTypeCreate( );
              
    }

    function tearDown() 
    {
        $this->contactDelete( $this->_contactID ) ;
        $this->contributionTypeDelete($this->_contributionTypeID);
    }

    function testMembershipTypeGetEmpty()
    {
        $membershiptype = & civicrm_membership_types_get( $params );
        $this->assertEquals( $membershiptype['is_error'], 1 );
    }
        
    function testMembershipTypeGetWithoutId()
    {
        $params = array(
                        'name'                 => '60+ Membership',
                        'description'          => 'people above 60 are given health instructions',
                        'contribution_type_id' => $this->_contributionTypeID ,
                        'minimum_fee'          => '200',
                        'duration_unit'        => 'month',
                        'duration_interval'    => '10',
                        'visibility'           => 'public'
                        );
        
        $membershiptype = & civicrm_membership_types_get( $params );
        $this->assertEquals( $membershiptype['is_error'], 1 );
    }

    function testMembershipTypeGet()
    {
       
        $id = $this->membershipTypeCreate( $this->_contactID,$this->_contributionTypeID );
        $params = array( 'id'=> $id );        
        $membershiptype = & civicrm_membership_types_get( $params );
                       
        $this->assertEquals($membershiptype[$id]['name'],'General');
        $this->assertEquals($membershiptype[$id]['member_of_contact_id'],$this->_contactID);
        $this->assertEquals($membershiptype[$id]['contribution_type_id'],$this->_contributionTypeID);
        $this->assertEquals($membershiptype[$id]['duration_unit'],'year');
        $this->assertEquals($membershiptype[$id]['duration_interval'],'1');
        $this->assertEquals($membershiptype[$id]['period_type'],'rolling');
        $this->membershipTypeDelete( $membershiptype[$id]['id'] );
    }
    
    function testMembershipTypeCreateEmpty()
    {
        $params = array();        
        $membershiptype = & civicrm_membership_type_create($params);
        $this->assertEquals( $membershiptype['is_error'], 1 );
    }
          
    function testMembershipTypeCreateWithoutMemberOfContactId()
    {
        $params = array(
                        'name'                 => '60+ Membership',
                        'description'          => 'people above 60 are given health instructions',                        'contribution_type_id' => $this->_contributionTypeID,
                        'minimum_fee'          => '200',
                        'duration_unit'        => 'month',
                        'duration_interval'    => '10',
                        'period_type'          => 'rolling',
                        'visibility'           => 'public'
                        );
        
        $membershiptype = & civicrm_membership_type_create($params);
        $this->assertEquals( $membershiptype['is_error'], 1 );
  
    }
    
    function testMembershipTypeCreateWithoutContributionTypeId()
    {
      $params = array(
                        'name'                 => '70+ Membership',
                        'description'          => 'people above 70 are given health instructions',                        'member_of_contact_id' => $this->_contactID,
                        'minimum_fee'          => '200',
                        'duration_unit'        => 'month',
                        'duration_interval'    => '10',
                        'period_type'          => 'rolling',
                        'visibility'           => 'public'
                        );
        $membershiptype = & civicrm_membership_type_create($params);
        $this->assertEquals( $membershiptype['is_error'], 1 );
    }   
         
    function testMembershipTypeCreateWithoutDurationUnit()
    {
        
        $params = array(
                        'name'                 => '80+ Membership',
                        'description'          => 'people above 80 are given health instructions',                        'member_of_contact_id' => $this->_contactID,
                        'contribution_type_id' => $this->_contributionTypeID,
                        'minimum_fee'          => '200',
                        'duration_unit'        => 'month',
                        'duration_interval'    => '10',                 
                        'visibility'           => 'public'
                        );
        
        $membershiptype = & civicrm_membership_type_create($params);
        $this->assertEquals( $membershiptype['is_error'], 0 );
        $this->assertNotNull( $membershiptype['id'] );   
        $this->membershipTypeDelete( $membershiptype['id'] );
        
    }
       
    function testMembershipTypeCreateWithoutName()
    {
        $params = array(
                        'name'                 => '50+ Membership',
                        'description'          => 'people above 50 are given health instructions',
                        'member_of_contact_id' => $this->_contactID,
                        'contribution_type_id' => $this->_contributionTypeID,
                        'minimum_fee'          => '200',
                        'duration_unit'        => 'month',
                        'duration_interval'    => '10',
                        'period_type'          => 'rolling',
                        'visibility'           => 'public'
                        );
        
        $membershiptype = & civicrm_membership_type_create($params);  
        $this->assertEquals( $membershiptype['is_error'], 0 );
        if ( ! $membershiptype['is_error'] ) {
            $this->assertNotNull( $membershiptype['id'] );   
            $this->membershipTypeDelete( $membershiptype['id'] );
        }
    }
    
    function testMembershipTypeCreate()
    {
        $params = array(
                        'name'                 => '40+ Membership',
                        'description'          => 'people above 40 are given health instructions', 
                        'member_of_contact_id' => $this->_contactID,
                        'contribution_type_id' => $this->_contributionTypeID,
                        'minimum_fee'          => '200',
                        'duration_unit'        => 'month',
                        'duration_interval'    => '10',
                        'period_type'          => 'rolling',
                        'visibility'           => 'public'
                        );
	
        $membershiptype = & civicrm_membership_type_create($params);  
        $this->assertEquals( $membershiptype['is_error'], 0 );
        if ( ! $membershiptype['is_error'] ) {
            $this->assertNotNull( $membershiptype['id'] );   
            $this->membershipTypeDelete( $membershiptype['id'] );
        }
    }

    function testMembershipTypeUpdateEmptyParams()
    {
        $params = array();                        
        $membershiptype = & civicrm_membership_type_update($params);
        $this->assertEquals( $membershiptype['is_error'], 1 );
    } 

    function testMembershipTypeUpdateWithoutId()
    {
        $params = array(
                        'name'                 => '60+ Membership',
                        'description'          => 'people above 60 are given health instructions',                        'member_of_contact_id' => $this->_contactID,
                        'contribution_type_id' => $this->_contributionTypeID,
                        'minimum_fee'          => '1200',
                        'duration_unit'        => 'month',
                        'duration_interval'    => '10',
                        'period_type'          => 'rolling',
                        'visibility'           => 'public'
                        );
        
        $membershiptype = & civicrm_membership_type_update($params);
        $this->assertEquals( $membershiptype['is_error'], 1 );
    }

    function BROKEN_testMembershipTypeUpdate()
    {
        $id = $this->membershipTypeCreate( $this->_contactID,$this->_contributionTypeID );
        $params = array(
                        'id'                        => $id,
                        'name'                      => 'Updated General',
                        'member_of_contact_id'      => '2',
                        'contribution_type_id'      => '1',
                        'duration_unit'             => 'month',
                        'duration_interval'         => '10',
                        'period_type'               => 'fixed',
                        );
        
        $membershiptype = & civicrm_membership_type_update($params);
        $this->assertEquals($membershiptype['name'],'Updated General');
        $this->assertEquals($membershiptype['member_of_contact_id'],'2');
        $this->assertEquals($membershiptype['contribution_type_id'],'1');
        $this->assertEquals($membershiptype['duration_unit'],'month');
        $this->assertEquals($membershiptype['duration_interval'],'10');
        $this->assertEquals($membershiptype['period_type'],'fixed');
        $this->membershipTypeDelete( $membershiptype['id']);
    }

    function testMembershipTypeDeleteEmpty ( ) {
        $params = array( );
        $return = civicrm_membership_type_delete( $params );
        $this->assertEquals( $return['is_error'], 1 );
    }

    function testMembershipTypeDeleteNotExists ( ) {
        $params = array( 'id' => 'doesNotExist' );
        $return = civicrm_membership_type_delete( $params );
        $this->assertEquals( $return['is_error'], 1 );
    }

    function testMembershipTypeDelete( ) {
        $orgID = $this->organizationCreate( );
        $membershipTypeID = $this->membershipTypeCreate( $orgID );
        $params['id'] = $membershipTypeID;
        $result = civicrm_membership_type_delete( $params );
        $this->assertEquals( $result['is_error'], 0 );
        $this->contactDelete( $orgID );
    }
    

}
 
?> 