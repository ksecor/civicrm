<?php

require_once 'api/crm.php';

class TestOfCRM1184 extends UnitTestCase 
{
    private $rel ="";
    private $contact1 ,$contact2, $contact3, $contact4, $contact5;
    
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
        
        $params = array('household_name' => 'The abc01 Household');
        $this->contact3 =& crm_create_contact($params, 'Household');
        
        $params = array('organization_name' => 'The abc Organization');
        $this->contact4 =& crm_create_contact($params, 'Organization');
        
        $params = array('household_name' => 'The abc02 Household');
        $this->contact5 =& crm_create_contact($params, 'Household');
        
    }
    
    function testCreateRelationship01() 
    {
        require_once 'CRM/Contact/DAO/Contact.php';
        $params = array('start_date' => array('d'=>'10','M'=>'1','Y'=>'2005'),'end_date' => array('d'=>'10','M'=>'1','Y'=>'2006'));
        $relationShip = 'Sibling of';
        $this->rel = crm_create_relationship($this->contact1,$this->contact2, $relationShip, $params);
        $this->assertIsA($this->rel, 'CRM_Contact_DAO_Relationship');
        //print_r($this->rel);
       
    }

    function testCreateRelationship02() 
    {
        require_once 'CRM/Contact/DAO/Contact.php';
        $params = array('start_date' => array('d'=>'10','M'=>'1','Y'=>'2005'),'end_date' => array('d'=>'10','M'=>'1','Y'=>'2006'));
        $relationShip = 'Household Member of';
        $this->rel = crm_create_relationship($this->contact1,$this->contact3, $relationShip, $params);
        $this->assertIsA($this->rel, 'CRM_Contact_DAO_Relationship');
        //print_r($this->rel);
       
    }
    
    function testCreateRelationship03() 
    {
        require_once 'CRM/Contact/DAO/Contact.php';
        $params = array('start_date' => array('d'=>'10','M'=>'1','Y'=>'2005'),'end_date' => array('d'=>'10','M'=>'1','Y'=>'2006'));
        $relationShip = 'Household Member of';
        $this->rel = crm_create_relationship($this->contact1,$this->contact5, $relationShip, $params);
        $this->assertIsA($this->rel, 'CRM_Contact_DAO_Relationship');
        //print_r($this->rel);
       
    }
    
    function testCreateRelationship04() 
    {
        require_once 'CRM/Contact/DAO/Contact.php';
        $params = array('start_date' => array('d'=>'10','M'=>'1','Y'=>'2005'),'end_date' => array('d'=>'10','M'=>'1','Y'=>'2006'));
        $relationShip = 'Employee of';
        $this->rel = crm_create_relationship($this->contact1,$this->contact4, $relationShip, $params);
        $this->assertIsA($this->rel, 'CRM_Contact_DAO_Relationship');
        //print_r($this->rel);
       
    }
        
    function testGetRelationshipAll()
    {
        require_once 'CRM/Contact/DAO/Contact.php';
        $rel = crm_get_relationships($this->contact1);
        CRM_Core_Error::debug('Relationship All', $rel);
    }
    
    function testGetRelationshipRelationshipType()
    {
        require_once 'CRM/Contact/DAO/Contact.php';
        $rel = crm_get_relationships($this->contact1, null, array('Household Member of'));
        CRM_Core_Error::debug('Relationship for Rel Type', $rel);
    }
    
    function testGetRelationshipRelationshipTypeContactB()
    {
        require_once 'CRM/Contact/DAO/Contact.php';
        $rel = crm_get_relationships($this->contact1, $this->contact5, array('Household Member of'));
        CRM_Core_Error::debug('Relationship - Rel Type - COntact B', $rel);
    }
    
    
}

?>