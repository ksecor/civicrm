<?php

require_once 'api/crm.php';

class TestOfCRM922 extends UnitTestCase
{   
    function setUp()
    {
    }
    
    function tearDown()
    {
    }
    
    function testCRM922( )
    {
        $params    =  array( 'organization_name' => 'The abc Organization1',
                             'email' => 'foo@bar1.org' );
        $contact   =& crm_create_contact( $params, 'Organization' );
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->contact_type, 'Organization');
        
        require_once 'CRM/Core/DAO/Tag.php';
        $tag =& new CRM_Core_DAO_Tag( );
        $tag->id = 2;
        $tag->find( true );
        $tagEntity1 =& crm_create_entity_tag($tag, $contact);
        CRM_Core_Error::debug( 'o', $tagEntity1 );
    }

}

