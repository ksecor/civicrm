<?php

require_once 'api/crm.php';

class TestOfCreateEntityTagAPI extends UnitTestCase 
{
    protected $_tag             = array();
    protected $_tagIndividual;
    protected $_tagHousehold;
    protected $_tagOrganization;
    protected $_tagGroup;
    protected $_individual      = array();
    protected $_household       = array();
    protected $_organization    = array();

    protected $_t = 0;
    
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }
    
    // Creating Various Tags
    function testCreateTagIndividul()
    {
        $params = array(
                        'name'        => 'New Tag Individual',
                        'description' => 'This is description for New Tag Individual',
                        'domain_id'   => 1
                        );
        $tag =& crm_create_tag($params);
        $this->assertIsA($tag, 'CRM_Core_DAO_Tag');
        $this->_tag[$tag->id] = $tag;
        $this->_tagIndividual = $tag;
    }
    
    function testCreateTagHousehold()
    {
        $params = array(
                        'name'        => 'New Tag Household',
                        'description' => 'This is description for New Tag Household',
                        'domain_id'   => 1
                        );
        $tag =& crm_create_tag($params);
        $this->assertIsA($tag, 'CRM_Core_DAO_Tag');
        $this->_tag[$tag->id] = $tag;
        $this->_tagHousehold = $tag;
    }
    
    function testCreateTagOrganization()
    {
        $params = array(
                        'name'        => 'New Tag Organization',
                        'description' => 'This is description for New Tag Organization',
                        'domain_id'   => 1
                        );
        $tag =& crm_create_tag($params);
        $this->assertIsA($tag, 'CRM_Core_DAO_Tag');
        $this->_tag[$tag->id] = $tag;
        $this->_tagOrganization = $tag;
    }
    
    function testCreateTagGroup()
    {
        $params = array(
                        'name'        => 'New Tag Group',
                        'description' => 'This is description for New Tag Group',
                        'domain_id'   => 1
                        );
        $tag =& crm_create_tag($params);
        $this->assertIsA($tag, 'CRM_Core_DAO_Tag');
        $this->_tag[$tag->id] = $tag;
        $this->_tagGroup = $tag;
    }
    
    // Creating Neccessary Contacts or Entities.
    function testCreateIndividual() 
    {
        $params = array(
                        'first_name' => 'Manish 01',
                        'last_name'  => 'Zope 01'
                        );
        $contact =& crm_create_contact($params, 'Individual');
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->contact_type, 'Individual');
        $this->_individual = $contact;
    }
    
    function testCreateHousehold() 
    {
        $params = array('household_name' => 'Zope House 01');
            $contact =& crm_create_contact($params, 'Household');
            $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
            $this->assertEqual($contact->contact_type, 'Household');
            $this->_household = $contact;
    }
    
    function testCreateOrganization() 
    {
        $params = array('organization_name' => 'Zope Industries 01');
        $contact =& crm_create_contact($params, 'Organization');
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->contact_type, 'Organization');
        $this->_organization = $contact;
    }
    
    // Test Cases for create_entity_tag.
    function testCreateEntityTagErrorNoEntity()
    {
        $tag = $this->_tagGroup;
        $entity = array();
        $tagEntity =& crm_create_entity_tag($tag, $entity);
        
        $this->assertIsA($tagEntity, 'CRM_Core_Error');
    }
    
    function testCreateEntityTagErrorNoTag()
    {
        $tag = array();
        $entity = $this->_household;
        $tagEntity =& crm_create_entity_tag($tag, $entity);
        
        $this->assertIsA($tagEntity, 'CRM_Core_Error');
    }
    
    function testCreateEntityTagIndividual()
    {
        $tag = $this->_tagIndividual;
        $entity = $this->_individual;
        $tagEntity =& crm_create_entity_tag($tag, $entity);
        
        $this->assertIsA($tagEntity, 'CRM_Core_BAO_EntityTag');
        $this->assertEqual($tagEntity->tag_id, $tag->id);
        $this->assertEqual($tagEntity->contact_id, $entity->id);
    }
    
    function testCreateEntityTagHousehold()
    {
        $tag = $this->_tagHousehold;
        $entity = $this->_household;
        $tagEntity =& crm_create_entity_tag($tag, $entity);
        
        $this->assertIsA($tagEntity, 'CRM_Core_BAO_EntityTag');
        $this->assertEqual($tagEntity->tag_id, $tag->id);
        $this->assertEqual($tagEntity->contact_id, $entity->id);
    }
    
    function testCreateEntityTagOrganization()
    {
        $tag = $this->_tagOrganization;
        $entity = $this->_organization;
        $tagEntity =& crm_create_entity_tag($tag, $entity);
        
        $this->assertIsA($tagEntity, 'CRM_Core_BAO_EntityTag');
        $this->assertEqual($tagEntity->tag_id, $tag->id);
        $this->assertEqual($tagEntity->contact_id, $entity->id);
    }
    
    // Deleting the tags and contacts created for the test case. 
    function testDeleteTag()
    {
        //for ($i=0; $i<count($this->_tag); $i++) {
        foreach ($this->_tag as $id => $tagObj) {
            //$tag = $this->_tag[$id];
            $tagDelete =& crm_delete_tag($tagObj);
            $this->assertNull($tagDelete);
        }
    }
    
    function testDeleteIndividual()
    {
        $contact = $this->_individual;
        $val =& crm_delete_contact($contact,102);
        $this->assertNull($val);
    }
    
    function testDeleteHousehold()
    {
        $contact = $this->_household;
        $val =& crm_delete_contact($contact,102);
        $this->assertNull($val);
    }    
    
    function testDeleteOrganization()
    {
        $contact = $this->_organization;
        $val =& crm_delete_contact($contact,102);
        $this->assertNull($val);
    }
}
?>
