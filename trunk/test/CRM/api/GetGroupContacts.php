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
        $group->id = 2;
        $contacts = crm_get_group_contacts(&$group);
        foreach($contacts as $contact) {
            $this->assertIsA($contact,'CRM_Contact_DAO_Contact');
        }
    }
    
    function testGetGroupContactsWithFilter()
    {
        $group = new CRM_Contact_DAO_Group();
        $group->id = 1;
        $sort = array("date" => "DESC");
        //$returnProperties =array('method','display_name','do_not_email');
        $returnProperties =null;
        $contacts = crm_get_group_contacts(&$group, $returnProperties ,'Added',$sort, $offset = 0, $row_count = 25 );
        
        foreach($contacts as $contact) {
            $this->assertIsA($contact,'CRM_Contact_DAO_Contact');
            echo 'Contact ID: ' . $contact->civicrm_contact_id . ', Display Name: ' . $contact->display_name . ', Do Not Email: ' . $contact->do_not_email . '<br />';
        }
    }
    
    function testGetGroupContactsWithStatus()
    {
        $group = new CRM_Contact_DAO_Group();
        $group->id = 3;
        $returnProperties =array('method','date');
        $contacts = crm_get_group_contacts(&$group, $returnProperties , $status = 'Removed', $sort = null, $offset = 0, $row_count = 25 );
        foreach($contacts as $contact) {
            $this->assertIsA($contact,'CRM_Contact_DAO_Contact');
        }
    }
}
?>
