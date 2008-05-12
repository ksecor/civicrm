<?php

require_once 'api/v2/Group.php';

/**
 * Class contains api test cases for "civicrm_group"
 *
 */

class TestOfGroupCreateAPIV2 extends CiviUnitTestCase 
{
    
    function setUp( ) 
    {
    }
    
    function testCreateGroupWithEmptyParams( )
    {
        $params = array( );
        $result = civicrm_group_add( $params );

        $this->assertEqual( $result['is_error'], 1 );
        $this->assertEqual( $result['error_message'], 'No input parameters present');
    }    

    function testCreateGroupWithParamsNotArray( )
    {
        $params = 'test';
        $result = civicrm_group_add( $params );

        $this->assertEqual( $result['is_error'], 1 );
        $this->assertNotEqual( $result['error_message'], 'Missing require fields ( title )' );
        $this->assertEqual( $result['error_message'], 'Params is not an array' );
    }    

    function testCreateGroupParamsWithoutTitle( )
    {
        $params = array(
                        'domain_id'   => 1,
                        'title'       => 'New Test Group Created',
                        'description' => 'New Test Group Created',
                        'is_active'   => 1,
                        'visibility'  => 'Public User Pages and Listings',
                        );
                
        $result = civicrm_group_add( $params );

        $this->assertEqual( $result['is_error'], 1 );
        $this->assertEqual( $result['error_message'], 'Missing required fields ( name )' );
    }
    
    function testGroupCreate( )
    {
        $params = array(
                        'name'        => 'Test Group 1',
                        'domain_id'   => 1,
                        'title'       => 'New Test Group Created',
                        'description' => 'New Test Group Created',
                        'is_active'   => 1,
                        'visibility'  => 'Public User Pages and Listings',
                        );
        $result = civicrm_group_add( $params );

        $this->assertDBState( 'CRM_Contact_DAO_Group', $result['id'], $params );

        $this->groupDelete( $result );
    }
    /**
     * Group with custom data 
     * ( will do this, once custom * v2 api are ready 
         with all changed schema for custom data  )
     */
    function testGroupCreateWithCustomData( )
    {         
        
    }
    
    function tearDown( ) 
    {
        
    }
}


