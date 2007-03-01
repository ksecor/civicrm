<?php

require_once 'api/v2/GroupContact.php';
require_once 'api/v2/Contact.php';

class TestOfEntityTag extends UnitTestCase 
{
    function setUp() 
    {
        // make sure this is just _41 and _generated
    }
    
    function tearDown() 
    {
    }
  
    function testAddToGroup( ) {
        $params = array(
                        'contact_id.1' => 1,
                        'contact_id.2' => 2,
                        'contact_id.3' => 3,
                        'group_id'     => 1 );

        $this->listGroupMembers( );
        civicrm_group_contact_add( $params );
        $this->listGroupMembers( );
    }

    function listGroupMembers( ) {
        $params = array( 'group' => array( 1 => 1 ),
                         'return.contact_id' => 1,
                         'return.display_name' => 1 );
        $contacts = civicrm_contact_search( $params );
        CRM_Core_Error::debug( 'c', $contacts );
    }

    function testGetGroupMembers( ) {
        $params = array( 'contact_id' => 102 );
        $groups = civicrm_group_contact_get( $params );
        CRM_Core_Error::debug( 'g', $groups );
    }

    function testRemoveFromGroup( ) {
        $params = array(
                        'contact_id.1' => 1,
                        'contact_id.2' => 2,
                        'contact_id.3' => 3,
                        'group_id'     => 1 );
        civicrm_group_contact_remove( $params );
        $this->listGroupMembers( );
    }

}

?>