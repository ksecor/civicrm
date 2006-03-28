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
    
    function testCRM825()
    {
        // Get the Contacts to a group.
        $group = new CRM_Contact_DAO_Group();
        $group->id = 1;
        $sort = array("contact_id" => "ASC");
        $returnProperties =array('method','display_name','do_not_email', 'email');
        //$returnProperties = null;
        $contacts = crm_get_group_contacts(&$group, $returnProperties ,'Added',$sort, $offset = 0, $row_count = 25 );
        
        foreach($contacts as $contact) {
            $this->assertIsA($contact,'CRM_Contact_DAO_Contact');
            echo 'Contact ID: ' . $contact->contact_id . ', Display Name: ' . $contact->display_name . ', Do Not Email: ' . $contact->do_not_email . ', Email: ' . $contact->email . '<br />';
        }
        
        // Create one group
        $groupParams = array('name' => 'Trial Group',
                             'title' => 'Trial Group',
                             'is_active' => 1,
                             );
        $groupCreated = crm_create_group($groupParams);
        $this->assertIsA($groupCreated, 'CRM_Contact_DAO_Group');
        
        $groupContacts = crm_add_group_contacts($groupCreated, $contacts);
        $this->assertNull($groupContacts);
        
        // now add the previous contacts to the group created
        //$returnProperties = null;
        $sort = array("contact_id" => "DESC");
        $newContacts = crm_get_group_contacts($groupCreated, null ,'Added',$sort, $offset = 0, $row_count = 25 );
        foreach($newContacts as $contact) {
            $this->assertIsA($contact,'CRM_Contact_DAO_Contact');
            echo 'Contact ID: ' . $contact->contact_id . ', Display Name: ' . $contact->display_name . ', Do Not Email: ' . $contact->do_not_email . ', Email: ' . $contact->email . '<br />';
        }
    }
}
?>