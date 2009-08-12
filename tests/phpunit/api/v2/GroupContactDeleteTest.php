<?php

require_once 'api/v2/GroupContact.php';
require_once 'CiviTest/CiviUnitTestCase.php';

class api_v2_GroupContactDeleteTest extends CiviUnitTestCase 
{
    function get_info( )
    {
        return array(
                     'name'        => 'Group Contact Delete',
                     'description' => 'Test all Group Contact Delete API methods.',
                     'group'       => 'CiviCRM API Tests',
                     );
    }   

    function setUp() 
    {
        parent::setUp();
        $this->_contactId = $this->individualCreate();
//        $this->contactGroupCreate( $this->_contactId );
    }
    
    function tearDown() 
    { 
//        $this->contactGroupDelete( $this->_contactId );
        $this->contactDelete($this->_contactId);
             
    }
    
    function testDeleteGroupContactsWithEmptyParams( ) 
    {
        $params = array( );
        $groups = civicrm_group_contact_remove( $params );
       
        $this->assertEquals( $groups['is_error'], 1 );
        $this->assertEquals( $groups['error_message'], 'contact_id is a required field' );
    }

    function testDeleteGroupContactsWithoutGroupIdParams( ) 
    {
        $params = array( );
        $params = array(
                        'contact_id.1' => $this->_contactId,
                        );
        
        $groups = civicrm_group_contact_remove( $params );
              
        $this->assertEquals( $groups['is_error'], 1 );
        $this->assertEquals( $groups['error_message'], 'group_id is a required field' );
    }
    
    
    function testDeleteGroupContacts( ) 
    {
        $params = array(
                        'contact_id.1' => $this->_contactId,
                        'group_id'     => 1 );
        
        
//        $groups = civicrm_group_contact_remove( $params );
        $this->fail( 'civicrm_group_contact_remove throws fatal error' );
             
        $this->assertEquals( $groups['is_error'], 0 );
        $this->assertEquals( $groups['removed'], 1 );
        $this->assertEquals( $groups['total_count'], 1 );

    }

  
}


