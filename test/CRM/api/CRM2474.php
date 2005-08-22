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
        $group->id = 4;
        $contacts = crm_get_group_contacts(&$group);
        echo count( $contacts ) . "\n";
        foreach($contacts as $contact) {
            echo $contact->sort_name . ', ' . $contact->contact_id . "\n";
            $this->assertIsA($contact,'CRM_Contact_DAO_Contact');
        }
    }

}

?>
