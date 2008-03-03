<?php

require_once 'api/crm.php';

class TestOfGetEntitiesByTagAPI extends UnitTestCase 
{
    protected $_tag;
    protected $_individual;
    protected $_household;
    protected $_organization;
    
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }
    
    // Create Tag.
    function testCreateTag()
    {
        $params = array(
                        'name'        => 'New Tag',
                        'description' => 'This is description for New Tag',
                        'domain_id'   => 1
                        );
        $tag =& crm_create_tag($params);
        $this->assertIsA($tag, 'CRM_Core_DAO_Tag');
        $this->_tag = $tag;
    }
    
    // Create Entities.
    function testCreateIndividual() 
    {
        for ($i=0; $i<5; $i++) {
            $params = array(
                            'first_name' => 'Manish 0' . $i,
                            'last_name'  => 'Zope 0' . $i
                            );
            $contact =& crm_create_contact($params, 'Individual');
            $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
            $this->assertEqual($contact->contact_type, 'Individual');
            $this->_individual[$i] = $contact;
        }
    }
    
    function testCreateHousehold() 
    {
        for ($i=0; $i<3; $i++) {
            $params = array('household_name' => 'Zope House 0' . $i);
            $contact =& crm_create_contact($params, 'Household');
            $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
            $this->assertEqual($contact->contact_type, 'Household');
            $this->_household[$i] = $contact;
        }
    }
    
    function testCreateOrganization() 
    {
        $params = array('organization_name' => 'Zope Industries 01');
        $contact =& crm_create_contact($params, 'Organization');
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->contact_type, 'Organization');
        $this->_organization = $contact;
    }
    
    // Assign Tag to the Entities.
    function testCreateEntityTag()
    {
        
        $tag = $this->_tag;
        
        foreach ($this->_individual as $idI => $objI) {
            $entityI = $this->_individual[$idI];
            $tagEntityI =& crm_create_entity_tag($tag, $entityI);
            
            $this->assertIsA($tagEntityI, 'CRM_Core_BAO_EntityTag');
        }
        
        foreach ($this->_household as $idH => $objH) {
            $entityH = $this->_household[$idH];
            $tagEntityH =& crm_create_entity_tag($tag, $entityH);
            
            $this->assertIsA($tagEntityH, 'CRM_Core_BAO_EntityTag');
        }
        
        $entityO = $this->_organization;
        $tagEntityO =& crm_create_entity_tag($tag, $entityO);
            
        $this->assertIsA($tagEntityO, 'CRM_Core_BAO_EntityTag');
    }
    
    // Get Entities by Tag.
    function testGetEntitiesByTagErrorNoTag()
    {
        $tag = array();
        $entity =& crm_get_entities_by_tag($tag);
        $this->assertIsA($entity, 'CRM_Core_Error');
    }
    
    function testGetEntitiesByTag()
    {
        $tag = $this->_tag;
        $entity =& crm_get_entities_by_tag($tag);
        foreach ($entity as $id => $obj) {
            $this->assertIsA($obj, 'CRM_Contact_BAO_Contact');
        }
    }
    
    function testGetIndividualEntitiesByTag()
    {
        $tag = $this->_tag;
        $entity =& crm_get_entities_by_tag($tag, 'Individual');
        foreach ($entity as $id => $obj) {
            $this->assertIsA($obj, 'CRM_Contact_BAO_Contact');
        }
    }
    
    function testGetHouseholdEntitiesByTag()
    {
        $tag = $this->_tag;
        $entity =& crm_get_entities_by_tag($tag, 'Household');
        foreach ($entity as $id => $obj) {
             $this->assertIsA($obj, 'CRM_Contact_BAO_Contact');
        }
    }
    
    function testGetOrganizationEntitiesByTag()
    {
        $tag = $this->_tag;
        $entity =& crm_get_entities_by_tag($tag, 'Organization');
        foreach ($entity as $id => $obj) {
             $this->assertIsA($obj, 'CRM_Contact_BAO_Contact');
        }
    }
    
    // Delete data created for the test cases.
    function testDeleteTag()
    {
        $tag = $this->_tag;
        $tagDelete =& crm_delete_tag($tag);
        $this->assertNull($tagDelete);
    }
    
    function testDeleteIndividual()
    {
        for ($i=0; $i<count($this->_individual); $i++) {
            $contact = $this->_individual[$i];
            $val =& crm_delete_contact($contact,102);
            $this->assertNull($val);
        }
    }
    
    function testDeleteHousehold()
    {
        for ($i=0; $i<count($this->_household); $i++) {
            $contact = $this->_household[$i];
            $val =& crm_delete_contact($contact,102);
            $this->assertNull($val);
        }
    }    
    
    function testDeleteOrganization()
    {
        $contact = $this->_organization;
        $val =& crm_delete_contact($contact,102);
        $this->assertNull($val);
    }
}
?>
