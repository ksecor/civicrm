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
        foreach($contacts as $contact) {
            echo $contact->contact_id . "\n";
            $this->assertIsA($contact,'CRM_Contact_DAO_Contact');
        }
    }


    function testGetGroupContactsWithFilter()
    {
        return;
       
        $group = new CRM_Contact_DAO_Group();
        $group->id = 3;
        $sort = array("date" => "DESC");
//        $returnProperties =array('method','display_name','do_not_email');
        $returnProperties =array();
        $contacts = crm_get_group_contacts(&$group, $returnProperties ,'Added',$sort, $offset = 0, $row_count = 25 );
        
        foreach($contacts as $contact) {
            $this->assertIsA($contact,'CRM_Contact_DAO_Contact');
            echo $contact->civicrm_contact_id . ',' . $contact->display_name . ',' . $contact->do_not_email . '<br />';
        }
        

        
        
    }
    
     function testGetGroupContactsWithStatus()
    {
        return;
        $group = new CRM_Contact_DAO_Group();
        $group->id = 1;
        $returnProperties =array('method','date');
        $contacts = crm_get_group_contacts(&$group, $returnProperties , $status = 'Removed', $sort = null, $offset = 0, $row_count = 25 );
        
        foreach($contacts as $contact) {
            $this->assertIsA($contact,'CRM_Contact_DAO_Contact');
        }
        

        
        
    }
}
?>
