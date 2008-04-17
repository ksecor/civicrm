<?php

require_once 'api/v2/GroupContact.php';
require_once 'api/v2/Group.php';

/**
 * Class contains api test cases for "civicrm_group"
 *
 */

class TestOfGroupCreateAPIV2 extends CiviUnitTestCase 
{
    protected $_groupID;
    
    function tearDown( ) 
    {
        if ( $this->_groupID ) {
            $this->groupDelete( $this->_groupID );
        }
    }
    
    function testCreateGroupWithEmptyParams( )
    {
        $params = array( );
        $result = civicrm_group_add( $params );

        $this->assertEqual( $result['is_error'], 1 );
        $this->assertEqual( $result['error_message'], 'Required parameter missing');
    }    

    function testCreateGroupWithParamsNotArray( )
    {
        $params = 'test';
        $result = civicrm_group_add( $params );

        $this->assertEqual( $result['is_error'], 1 );
        $this->assertEqual( $result['error_message'], 'Required parameter missing' );
    }    

    function testCreateGroupParamsWithoutTitle( )
    {
        $params = array(
                        'domain_id'   => 1,
                        'description' => 'New Test Group Created',
                        'is_active'   => 1,
                        'visibility'  => 'Public User Pages and Listings',
                        );
                
        $result = civicrm_group_add( $params );
        
        $this->assertEqual( $result['is_error'], 1 );
        $this->assertEqual( $result['error_message'], 'Required parameter missing' );
    }
    
    function testGroupCreate( )
    {
        $params = array(
                        'name'        => 'Test Group 1',
                        'domain_id'   => 1,
                        'title'       => 'test_group_1',
                        'description' => 'New Test Group Created',
                        'is_active'   => 1,
                        'visibility'  => 'Public User Pages and Listings',
                        'group_type'  => '1,2'
                        );
       
        $result = civicrm_group_add( $params );
        
        $this->assertEqual( $result['is_error'], 0 );
        $this->assertDBState( 'CRM_Contact_DAO_Group', $result['result'], $params );
        $this->_groupID = $result['result'];
    }
    
    function testCreateGroupWithoutGroupName( )
    {
        $params = array(
                        'domain_id'   => 1,
                        'title'       => 'Test Group 1',
                        'description' => 'New Test Group Created',
                        'is_active'   => 1,
                        'visibility'  => 'Public User Pages and Listings',
                        );
        
        $result = civicrm_group_add( $params );
        $this->assertDBState( 'CRM_Contact_DAO_Group', $result['result'], $params );
        $this->_groupID = $result['result'];
    }
    
    function testUpdateGroupWithoutGroupName( )
    {
        $this->_groupID = $this->groupCreate();
        $params = array(
                        'domain_id'   => 1,
                        'title'       => 'Test Group 1',
                        'description' => 'New Test Group Updated',
                        'is_active'   => 1,
                        'visibility'  => 'Public User Pages and Listings',
                        'id'          => $this->_groupID
                        );
           
        $result = civicrm_group_add( $params );
        $this->assertDBState( 'CRM_Contact_DAO_Group', $result['result'], $params );
    }
    
    function testUpdateGroupWithoutNameTitle( )
    {
        $this->_groupID = $this->groupCreate();
        $params = array(
                        'domain_id'   => 1,
                        'description' => 'New Test description Group updated',
                        'is_active'   => 1,
                        'visibility'  => 'Public User Pages and Listings',
                        'id'          => $this->_groupID
                        );
           
        $result  = civicrm_group_add( $params );
        $this->assertEqual( $result['is_error'], 0 );
        $this->assertDBState( 'CRM_Contact_DAO_Group', $result['result'], $params );
    }
}
?>
