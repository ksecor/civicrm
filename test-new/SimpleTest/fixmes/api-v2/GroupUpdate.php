<?php

require_once 'api/v2/Group.php';

/**
 * Class contains api test cases for "civicrm_group"
 *
 */
class TestOfGroupUpdateAPIV2 extends CiviUnitTestCase 
{
    protected $_groupID;

    function setUp() 
    {
        $this->_groupID   = $this->groupCreate( );
    }
    
    /**
     * check with empty array
     */
    function testGroupUpdateEmpty( )
    {
        $params = array( );
        $result =& civicrm_group_update( $params );
   
        $this->assertEqual( $result['is_error'], 1 );
        $this->assertEqual( $result['error_message'], 'No parameters present' );
    
    }

    /**
     * check if required fields are not passed
     */
    function testGroupUpdateWithoutRequired( )
    {
        $params = array(
                        'name'        => 'Test Group 1',
                        'domain_id'   => 1,
                        'title'       => 'Test Group for update group',
                        'description' => 'Test Group for update group',
                        'is_active'   => 0,
                        'visibility'  => 'Public User Pages and Listings',
                        );

        $result =& civicrm_group_update( $params );
        $this->assertEqual( $result['is_error'], 1 );
        $this->assertEqual( $result['error_message'], 'No parameters present' );
    }

    /**
     * check with incorrect required fields
     */
    function testGroupUpdateWithIncorrectData( )
    {
        $params = array(
                        'id'          => 'crack it',
                        'name'        => 'Test Group 1',
                        'domain_id'   => 1,
                        'title'       => 'Test Group for update group',
                        'description' => 'Test Group for update group',
                        'is_active'   => 0,
                        'visibility'  => 'Public User Pages and Listings',

                        
                        );

        $result =& civicrm_group_update($params);
        $this->assertEqual( $result['is_error'], 1 );
        $this->assertEqual( $result['error_message'], 'Invalid or no value for Group ID' );
    }

    function testGroupUpdate( )
    {
        $params = array(
                        'id'          => $this->_groupID,
                        'name'        => 'Test Group 1',
                        'domain_id'   => 1,
                        'title'       => 'Test Group for update group',
                        'is_active'   => 0,
                        'visibility'  => 'Newsletter Subscribers',
                        );

        $result =& civicrm_group_update( $params );

        $this->assertEqual( $result['is_error'], 0 );
        $this->assertEqual( $result['id'], $this->_groupID );
        $this->assertEqual( $result['domain_id'], 1 );
        $this->assertEqual( $result['title'],'Test Group for update group' );
        $this->assertEqual( $result['is_active'], 0 );
        $this->assertEqual( $result['visibility'],'Newsletter Subscribers' );
    }

    /**
     * Group with custom data 
     * ( will do this, once custom * v2 api are ready 
         with all changed schema for custom data  )
     */
    function testGroupUpdateWithCustomData( )
    {         
        
    }

    function tearDown() 
    {
        $this->groupDelete( $this->_groupID );
    }
}
 
?> 