<?php

require_once 'api/v2/EntityTag.php';
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
  
    function testAddToTag( ) {
        $params = array(
                        'contact_id.1' => 1,
                        'contact_id.2' => 2,
                        'contact_id.3' => 3,
                        'tag_id'     => 1 );

        $this->listTagMembers( );
        civicrm_entity_tag_add( $params );
        $this->listTagMembers( );
    }

    function listTagMembers( ) {
        $params = array( 'tag' => array( 1 => 1 ),
                         'return.contact_id' => 1,
                         'return.display_name' => 1 );
        $contacts = civicrm_contact_search( $params );
        CRM_Core_Error::debug( 'c', $contacts );
    }

    function testRemoveFromTag( ) {
        $params = array(
                        'contact_id.1' => 1,
                        'contact_id.2' => 2,
                        'contact_id.3' => 3,
                        'tag_id'       => 1 );
        civicrm_entity_tag_remove( $params );
        $this->listTagMembers( );
    }

}


