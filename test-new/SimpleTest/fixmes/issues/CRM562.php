<?php

require_once 'api/crm.php';

class TestOfCRM562 extends UnitTestCase 
{

    
    function setUp() 
    {
    }

    function tearDown() 
    {
    }

    function testGetGroupContacts()
    {
        
        $group = new CRM_Contact_DAO_Group();
        $group->id = 2;
        $contacts = crm_get_group_contacts(&$group, null, null);
        CRM_Core_Error::debug( 'c', count( $contacts ) );
        foreach($contacts as $contact) {
            $this->assertIsA($contact,'CRM_Contact_DAO_Contact');
        }
    }
}


