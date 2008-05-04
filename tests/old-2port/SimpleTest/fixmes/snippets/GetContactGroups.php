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
        $contact->id = 23;
        $groups = crm_contact_groups( $contact );
        foreach($groups as $group) {
           CRM_Core_Error::debug('Group',$group ); 
        } 
    }


    function testGetContactGroups1()
    {
        
        $contact = new CRM_Contact_DAO_Contact();
        $contact->id = 23;
        $groups = crm_contact_groups( $contact, 'Added' );
        foreach($groups as $group) {
            CRM_Core_Error::debug('Group Added',$group );
        }
    }


    function testGetContactGroups2()
    {
        
        $contact = new CRM_Contact_DAO_Contact();
        $contact->id = 62;
        $groups = crm_contact_groups( $contact, 'Removed' );
        foreach($groups as $group) {
            CRM_Core_Error::debug('Group Removed',$group );
        }
    }
}


