<?php

require_once 'api/v2/GroupContact.php';
require_once 'CiviTest/CiviUnitTestCase.php';

class api_v2_GroupContactTest extends CiviUnitTestCase 
{
   
    protected $_contactId;
    protected $_contactId1;

    function get_info( )
    {
        return array(
                     'name'        => 'Group Contact Create',
                     'description' => 'Test all Group Contact Create API methods.',
                     'group'       => 'CiviCRM API Tests',
                     );
    }
    
    function setUp() 
    {
        parent::setUp();
        $this->_contactId = $this->individualCreate();

        $this->_groupId1  = $this->groupCreate( );
        $params = array( 'contact_id.1' => $this->_contactId,
                         'group_id'     => $this->_groupId1 );
        
        civicrm_group_contact_add( $params );
        
        $group = array(
                       'name'        => 'Test Group 2',
                       'domain_id'   => 1,
                       'title'       => 'New Test Group2 Created',
                       'description' => 'New Test Group2 Created',
                       'is_active'   => 1,
                       'visibility'  => 'User and User Admin Only',
                       );
        $this->_groupId2  = $this->groupCreate( $group );
        $params = array( 'contact_id.1' => $this->_contactId,
                         'group_id'     =>  $this->_groupId2  );
        
        civicrm_group_contact_add( $params );
        
        $this->_group = array($this->_groupId1  => array( 'title'      => 'New Test Group Created',
                                                          'visibility' => 'Public Pages',
                                                          'in_method'  => 'API'),
                              $this->_groupId2  => array( 'title'      => 'New Test Group2 Created',
                                                          'visibility' => 'User and User Admin Only',
                                                          'in_method'  => 'API' ));

    }
    
    function tearDown() 
    {
        $this->contactDelete($this->_contactId);
        if (  $this->_contactId1 ){
            $this->contactDelete($this->_contactId1);
        }
    }

    function testGetGroupContactsWithEmptyParams( ) 
    {
        $params = array( );
        $groups = civicrm_group_contact_get( $params );
        
        $this->assertEquals( $groups['is_error'], 1 );
        $this->assertEquals( $groups['error_message'], 'contact_id is a required field' );
    }
    
   function testGetGroupContacts( ) 
   {
       $params = array( 'contact_id' => $this->_contactId );
       $groups = civicrm_group_contact_get( $params );
                 
       foreach( $groups as $v  ){ 
           $this->assertEquals( $v['title'], $this->_group[$v['group_id']]['title'] );
           $this->assertEquals( $v['visibility'], $this->_group[$v['group_id']]['visibility'] );
           $this->assertEquals( $v['in_method'], $this->_group[$v['group_id']]['in_method'] );
       }
   }
   

    
    function testCreateGroupContactsWithEmptyParams( ) 
    {
        $params = array( );
        $groups = civicrm_group_contact_add( $params );
        
        $this->assertEquals( $groups['is_error'], 1 );
        $this->assertEquals( $groups['error_message'], 'contact_id is a required field' );
    }

    function testCreateGroupContactsWithoutGroupIdParams( ) 
    {
        $params = array(
                        'contact_id.1' => $this->_contactId,
                        );
        
        $groups = civicrm_group_contact_add( $params );
        
        $this->assertEquals( $groups['is_error'], 1 );
        $this->assertEquals( $groups['error_message'], 'group_id is a required field' );
    }
    
    
    function testCreateGroupContacts( ) 
    {
        $cont = array( 'first_name'       => 'Amiteshwar',
                       'middle_name'      => 'L.',
                       'last_name'        => 'Prasad',
                       'prefix_id'        => 3,
                       'suffix_id'        => 3,
                       'email'            => 'amiteshwar.prasad@civicrm.org',
                       'contact_type'     => 'Individual');
        
        $this->_contactId1 = $this->individualCreate( $cont );
        $params = array(
                        'contact_id.1' => $this->_contactId,
                        'contact_id.2' => $this->_contactId1,
                        'group_id'     => 1 );
        
        $groups = civicrm_group_contact_add( $params );
        
        $this->assertEquals( $groups['is_error'], 0 );
        $this->assertEquals( $groups['not_added'], 1 );
        $this->assertEquals( $groups['added'], 1 );
        $this->assertEquals( $groups['total_count'], 2 );
        
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
        
        
       $groups = civicrm_group_contact_remove( $params );
             
        $this->assertEquals( $groups['is_error'], 0 );
        $this->assertEquals( $groups['removed'], 1 );
        $this->assertEquals( $groups['total_count'], 1 );

    }



  
}


