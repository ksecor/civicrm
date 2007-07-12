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
        foreach ($contacts as $key => $value) { 
            $this->assertIsA($contacts[$key],'CRM_Contact_DAO_Contact');
        }
    }
    
    function testGetGroupContactsWithFilter()
    {
        $group = new CRM_Contact_DAO_Group();
        $group->id = 2;
        $returnProperties = null;
        $contacts = crm_get_group_contacts(&$group, $returnProperties ,'Added',$sort, $offset = 0, $row_count = 25 );
        foreach ($contacts as $key => $value) { 
            $this->assertIsA($contacts[$key],'CRM_Contact_DAO_Contact');
        }
    }

    function testGetGroupContactsWithStatus()
    {
        $group = new CRM_Contact_DAO_Group();
        $group->id = 3;
        $contacts = crm_get_group_contacts(&$group, $returnProperties , $status = 'Removed', $sort = null, $offset = 0, $row_count = 25 );
        foreach ($contacts as $key => $value) { 
            $this->assertIsA($contacts[$key],'CRM_Contact_DAO_Contact');
        }
    }
}
?>
