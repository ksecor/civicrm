<?php

require_once 'api/crm.php';

class TestOfGetRelationshipAPI extends UnitTestCase 
{
    private $rel ="";
    private $contact1 ,$contact2,$contact3,$contact4;
    function setUp() 
    {
    }

    function tearDown() 
    {
    }
    
    function testCreateContacts() 
    {
        $params = array('first_name'    => 'abc4',
                        'last_name'     => 'xyz4',
                        'email'         => 'man4@yahoo.com',
                        );
        $this->contact1 =& crm_create_contact($params, 'Individual');
      
        $params = array('first_name'    => 'abc5',
                        'last_name'     => 'xyz5',
                        'email'         => 'man5@yahoo.com',
                        );
        $this->contact2 =& crm_create_contact($params, 'Individual');
      
        $params = array('household_name' => 'The abc Household');
        $this->contact3 =& crm_create_contact($params, 'Household');
     
        $params = array('organization_name' => 'The abc Organization');
        $this->contact4 =& crm_create_contact($params, 'Organization');
      
    }

    function testCreateRelationship() 
    {
        require_once 'CRM/Contact/DAO/Contact.php';
        $params = array('start_date' => array('d'=>'10','M'=>'1','Y'=>'2005'),'end_date' => array('d'=>'10','M'=>'1','Y'=>'2006'));
        $relationShip = 'Child of';
        $this->rel = crm_create_relationship($this->contact1,$this->contact2, $relationShip, $params);
        $this->assertIsA($this->rel, 'CRM_Contact_DAO_Relationship');
       
    }

    function testHouseholdRelationship() 
    {
        require_once 'CRM/Contact/DAO/Contact.php';
        $params = array('start_date' => array('d'=>'10','M'=>'1','Y'=>'2005'),'end_date' => array('d'=>'10','M'=>'1','Y'=>'2006'));
        $relationShip = 'Household Member of';
        $this->rel = crm_create_relationship($this->contact1,$this->contact3, $relationShip, $params);
        $this->assertIsA($this->rel, 'CRM_Contact_DAO_Relationship');
       
    }
    function testOrganizationRelationship() 
    {
        require_once 'CRM/Contact/DAO/Contact.php';
        $params = array('start_date' => array('d'=>'10','M'=>'1','Y'=>'2005'),'end_date' => array('d'=>'10','M'=>'1','Y'=>'2006'));
        $relationShip = 'Employee of';
        $this->rel = crm_create_relationship($this->contact1,$this->contact4, $relationShip, $params);
        $this->assertIsA($this->rel, 'CRM_Contact_DAO_Relationship');
       
    }

    function testCreateMultipleRelationship() 
    {
        require_once 'CRM/Contact/DAO/Contact.php';
        $params = array('start_date' => array('d'=>'10','M'=>'1','Y'=>'2005'),'end_date' => array('d'=>'10','M'=>'1','Y'=>'2006'));
        $relationShip = 'Child of';
        $this->rel = crm_create_relationship($this->contact1,$this->contact2, $relationShip, $params);
        $relationShip = 'Sibling of';
        $this->rel = crm_create_relationship($this->contact1,$this->contact2, $relationShip, $params);
        $this->assertIsA($this->rel, 'CRM_Contact_DAO_Relationship');
      
       
    }


    function testGetRelationship()
    {
        require_once 'CRM/Contact/DAO/Contact.php';
        $rel = crm_get_relationships($this->contact1,$this->contact2);
       
        $relationShip = array('Child of');
        $rel = crm_get_relationships($this->contact1,$this->contact2,$relationShip);
        foreach($rel as $key=>$value) { 
            $this->assertEqual($value['relation'], 'Child of');
        }
      
        $relationShip = array('Sibling of');
        $rel = crm_get_relationships($this->contact1,$this->contact2,$relationShip);
        foreach($rel as $key=>$value) { 
            $this->assertEqual($value['relation'], 'Sibling of');
        }
        $relationShip = array('Child of','Sibling of');
        $rel = crm_get_relationships($this->contact1,$this->contact2,$relationShip);
        
        $relationShip = array('Household Member of');
        $rel = crm_get_relationships($this->contact1,$this->contact3,$relationShip);
        foreach($rel as $key=>$value) { 
            $this->assertEqual($value['relation'], 'Household Member of');
        }
        $relationShip = array('Employee of');
        $rel = crm_get_relationships($this->contact1,$this->contact4,$relationShip);
        foreach($rel as $key=>$value) { 
            $this->assertEqual($value['relation'], 'Employee of');
        }

    }

    function testNoRelationship()
    {
        $relationShip = array('Spouse of');
        $rel = crm_get_relationships($this->contact1,$this->contact2,$relationShip);
        $rel = crm_get_relationships($this->contact2,$this->contact3);
        $this->assertEqual($rel, array());
    }

    function testGetRelationshipWithError()
    {
        require_once 'CRM/Contact/DAO/Contact.php';
        
        $rel = crm_get_relationships($contact1);
       
        
        $this->assertIsA($rel,'CRM_Core_Error');
        
    }

    function testDeleteContact()
    {
        crm_delete_contact($this->contact1,$this->contact1->id);
        crm_delete_contact($this->contact2,$this->contact2->id);
        crm_delete_contact($this->contact3,$this->contact3->id);
        crm_delete_contact($this->contact4,$this->contact4->id);
    }


}

