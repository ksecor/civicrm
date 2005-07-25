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


    function testGetGroupContactsWithFilter()
    {
       
        $group = new CRM_Contact_DAO_Group();
        $group->id = 1;
        $sort = array("date" => "DESC");
        $returnProperties =array('method','date');
        $contacts = crm_get_group_contacts(&$group, $returnProperties ,"In",$sort, $offset = 0, $row_count = 25 );
        
        foreach($contacts as $contact) {
            $this->assertIsA($contact,'CRM_Contact_DAO_Contact');
        }
        

        
        
    }
    
     function testGetGroupContactsWithStatus()
    {
        $group = new CRM_Contact_DAO_Group();
        $group->id = 1;
        $returnProperties =array('method','date');
        $contacts = crm_get_group_contacts(&$group, $returnProperties , $status = 'Out', $sort = null, $offset = 0, $row_count = 25 );
        
        foreach($contacts as $contact) {
            $this->assertIsA($contact,'CRM_Contact_DAO_Contact');
        }
        

        
        
    }
}
?>