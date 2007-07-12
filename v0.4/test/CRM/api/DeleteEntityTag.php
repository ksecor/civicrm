<?php

require_once 'api/crm.php';

class TestOfDeleteEntityTagAPI extends UnitTestCase 
{
    protected $_tag = array();
    protected $_individual = array();
    protected $_tagEntity = array();
    
    
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }
    
    // Create Tag.
    function testCreateTag01()
    {
        $params = array(
                        'name'        => 'New Tag 01',
                        'description' => 'This is description for New Tag 01',
                        'domain_id'   => 1
                        );
        $tag =& crm_create_tag($params);
        $this->assertIsA($tag, 'CRM_Core_DAO_Tag');
        $this->_tag[1] = $tag;
    }
    
    function testCreateTag02()
    {
        $params = array(
                        'name'        => 'New Tag 02',
                        'description' => 'This is description for New Tag 02',
                        'domain_id'   => 1
                        );
        $tag =& crm_create_tag($params);
        $this->assertIsA($tag, 'CRM_Core_DAO_Tag');
        $this->_tag[2] = $tag;
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
    
    // Assign Tag to the Entities.
    function testCreateEntityTag()
    {
        for ($i=0; $i<3; $i++) {
            $entity1 = $this->_individual[$i];
            $tag1    = $this->_tag[1];
            $tagEntity1 =& crm_create_entity_tag($tag1, $entity1);
            $this->_tagEntity[$tagEntity1->tag_id] = $tagEntity1;
            $this->assertIsA($tagEntity1, 'CRM_Core_BAO_EntityTag');
        }
        
        for ($i=0; $i<5; $i++) {
            $entity2 = $this->_individual[$i];
            $tag2    = $this->_tag[2];
            $tagEntity2 =& crm_create_entity_tag($tag2, $entity2);
            $this->_tagEntity[$tagEntity2->tag_id] = $tagEntity2;            
            $this->assertIsA($tagEntity2, 'CRM_Core_BAO_EntityTag');
        }
    }
    
    // Tags by Entity
    function testTagsByEntityNotNull()
    {
        $entity = $this->_individual[2];
        $tag =& crm_tags_by_entity($entity);
        foreach ($tag as $tagID) {
            $this->assertNotNull($tagID);
        }
    }
    
    // Delete Entity Tag
    function testDeleteEntityTagError()
    {
        $tagEntityObj = array();
        $result =& crm_delete_entity_tag($tagEntityObj);
        $this->assertIsA($result, 'CRM_Core_Error');
    }
    
    function testDeleteEntityTag()
    {
        foreach ($this->_tagEntity as $tagEntityObj) {
            //print_r($tagEntityObj);
            $result =& crm_delete_entity_tag($tagEntityObj);
            //print_r($result);
            $this->assertNull($result);
        }
    }
    
    // Delete data created for the test cases.
    function testDeleteTag()
    {
        $tag1 = $this->_tag[1];
        $tagDelete1 =& crm_delete_tag($tag1);
        $this->assertNull($tagDelete1);
        
        $tag2 = $this->_tag[2];
        $tagDelete2 =& crm_delete_tag($tag2);
        $this->assertNull($tagDelete2);
    }
    
    function testDeleteIndividual()
    {
        for ($i=0; $i<count($this->_individual); $i++) {
            $contact = $this->_individual[$i];
            $val =& crm_delete_contact($contact,102);
            $this->assertNull($val);
        }
    }
    
}
?>
