<?php

require_once 'api/crm.php';

class TestOfGetTagAPI extends UnitTestCase 
{
    protected $_tag;
    

    function setUp() 
    {
    }

    function tearDown() 
    {
    }
    
    function testCreateTag()
    {
        $params = array(
                        'domain_id'   => 1,
                        'name'        => 'Blood Donor',
                        'description' => 'Willing to donate blood'
                        );
        $tag =& crm_create_tag($params);
        $this->assertIsA($tag, 'CRM_Core_DAO_Tag');
        $this->_tag = $tag;
    }
    
    function testGetEmptyTag()
    {
        $param = array();
        $tag =&crm_get_tag($param);
        $this->assertIsA($tag, 'CRM_Core_Error');
    }    
    
    function testGetTagByName()
    {
        $param = array('name' => 'Blood Donor');
        $tag =&crm_get_tag($param);
        $this->assertIsA($tag, 'CRM_Core_DAO_Tag');
        $this->assertEqual($tag->domain_id, $this->_tag->domain_id);
        $this->assertEqual($tag->name, $this->_tag->name);
        $this->assertEqual($tag->description, $this->_tag->description);
    }
    
    function testGetTagById()
    {
        $param = array('id' => $this->_tag->id);
        $tag =&crm_get_tag($param);
        $this->assertIsA($tag, 'CRM_Core_DAO_Tag');
        $this->assertEqual($tag->domain_id, $this->_tag->domain_id);
        $this->assertEqual($tag->name, $this->_tag->name);
        $this->assertEqual($tag->description, $this->_tag->description);
    } 
   
    function testGetTagByIdAndName()
    {
        $param = array(
                       'id'   => $this->_tag->id,
                       'name' => 'Blood Donor'
                       );
        $tag =&crm_get_tag($param);
        $this->assertIsA($tag, 'CRM_Core_DAO_Tag');
        $this->assertEqual($tag->domain_id, $this->_tag->domain_id);
        $this->assertEqual($tag->name, $this->_tag->name);
        $this->assertEqual($tag->description, $this->_tag->description);
    } 

    function testGetBadTagByDescription()
    {
        $param = array('description' => 'For profit organization.');
        $tag =&crm_get_tag($param);
        $this->assertIsA($tag, 'CRM_Core_Error');
    }    
    
    function testGetBadTagByDomainId()
    {
        $param = array('domain_id' => 1);
        $tag =&crm_get_tag($param);
        $this->assertIsA($tag, 'CRM_Core_Error');
    }    
    
    function testDeleteTag()
    {
        $param = $this->_tag;
        $tag =& crm_delete_tag($param);
        $this->assertNull($tag);
    }
}
?>
