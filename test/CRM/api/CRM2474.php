<?php

require_once 'api/crm.php';

class TestOfGetGroupContacts extends UnitTestCase 
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
        $group->id = 1;
        $contacts = crm_get_group_contacts(&$group);
        foreach($contacts as $contact) {
            $this->assertIsA($contact,'CRM_Contact_DAO_Contact');
        }
    }

}

?>
