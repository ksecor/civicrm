<?php

require_once 'api/v2/Membership.php';
require_once 'CiviTest/CiviUnitTestCase.php';

class api_v2_MembershipTypeUpdateTest extends CiviUnitTestCase 
{
    protected $_contactID;
    protected $_contributionTypeID;

    function get_info( )
    {
        return array(
                     'name'        => 'MembershipType Update',
                     'description' => 'Test all Membership Type Update API methods.',
                     'group'       => 'CiviCRM API Tests',
                     );
    }

    function setUp() 
    {
        parent::setUp();

        $this->_contactID           = $this->organizationCreate( ) ;
        $this->_contributionTypeID  = $this->contributionTypeCreate( );
        
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
    
    function tearDown() 
    {
        $this->contactDelete( $this->_contactID ) ;
        $this->contributionTypeDelete($this->_contributionTypeID);
    }
}

