<?php

require_once 'api/v2/GroupContact.php';

class TestOfGroupContactGetAPIV2 extends CiviUnitTestCase 
{
    private $_group = array( 1 => array( 'title'      => 'Administrators',
                                         'visibility' => 'User and User Admin Only',
                                         'in_method'  => 'API'),
                             2 => array( 'title'      => 'Newsletter Subscribers',
                                         'visibility' => 'Public User Pages and Listings',
                                         'in_method'  => 'API' ));
    function setUp() 
    {
        $this->_contactId = $this->individualCreate();
        $params = array( 'contact_id.1' => $this->_contactId,
                         'group_id'     => 1 );
        
        civicrm_group_contact_add( $params );
        
        $params = array( 'contact_id.1' => $this->_contactId,
                         'group_id'     => 2 );
        
        $groupContact = civicrm_group_contact_add( $params );
      
    }
    
    function tearDown() 
    {
        $this->contactGroupDelete( $this->_contactId );
        $this->contactDelete($this->_contactId);
    }
    
    function testGetGroupContactsWithEmptyParams( ) 
    {
        $params = array( );
        $groups = civicrm_group_contact_get( $params );
        
        $this->assertEqual( $groups['is_error'], 1 );
        $this->assertEqual( $groups['error_message'], 'contact_id is a required field' );
    }
    
   function testGetGroupContacts( ) 
   {
       $params = array( 'contact_id' => $this->_contactId );
       $groups = civicrm_group_contact_get( $params );
             
       foreach( $groups as $v  ){ 
           $this->assertEqual($v['title'], $this->_group[$v['group_id']]['title'] );
           $this->assertEqual($v['visibility'], $this->_group[$v['group_id']]['visibility'] );
           $this->assertEqual($v['in_method'], $this->_group[$v['group_id']]['in_method'] );
       }
   }
   
   
}

?>
