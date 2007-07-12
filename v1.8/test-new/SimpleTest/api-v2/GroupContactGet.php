<?php

require_once 'api/v2/GroupContact.php';

class TestOfGroupContactGetAPIV2 extends CiviUnitTestCase 
{
    function setUp() 
    {
        $this->_contactId = $this->individualCreate();
        $this->contactGroupCreate( $this->_contactId );
    }
    
    function tearDown() 
    {
        $this->contactGroupDelete( $this->_contactId );
        $this->contactDelete($this->_contactId);
    }
    
    function testGetEmptyGroupMembers( ) {
        $params = array( );
        $groups = civicrm_group_contact_get( $params );
        $this->assertEqual( $groups['is_error'], 1 );
    }

   function testGetGroupMembers( ) {
        $params = array( 'contact_id' => $this->_contactId );
        $groups = civicrm_group_contact_get( $params );
   }


  
}

?>
