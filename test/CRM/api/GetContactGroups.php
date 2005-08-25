<?php

require_once 'api/crm.php';

class TestOfGetContactGroups extends UnitTestCase 
{

    
    function setUp() 
    {
    }

    function tearDown() 
    {
    }

    function testGetContactGroups()
    {
        
        $contact = new CRM_Contact_DAO_Contact();
        $contact->id = 42;
        $groups = crm_contact_groups( $contact, 'Added' );
        foreach($groups as $group) {
            print_r( $group );
        }
    }

}

?>
