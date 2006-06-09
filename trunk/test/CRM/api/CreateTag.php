<?php

require_once 'api/crm.php';

class TestOfCreateTagAPI extends UnitTestCase 
{
    protected $_tag = array();
    
    protected $_t = 0;
    
    function setUp() 
    {
    }

    function tearDown() 
    {
    }
    
    function testCreateTagErrorWithoutDomainID()
    {
        $params = array(
                        'name'        => 'New Tag 01',
                        'description' => 'This is description for New Tag 01'
                        );
        $tagError =& crm_create_tag($params);
        
        $this->assertIsA($tagError, 'CRM_Core_Error');
    }

    function testCreateTagErrorEmptyParams()
    {
        $params = array( );
        $tagError =& crm_create_tag($params);
        
        $this->assertIsA($tagError, 'CRM_Core_Error');
    }
    
    function testCreateTagWithDomainID()
    {
        $params = array(
                        'name'      => 'New Tag 02',
                        'domain_id' => '1'
                        );
        $tag =& crm_create_tag($params);
        
        $this->assertIsA($tag, 'CRM_Core_DAO_Tag');
        $this->assertEqual($tag->name, 'New Tag 02');
        $this->_tag[$this->_t++] = $tag;
    }
    
    function testCreateTag()
    {
        $params = array(
                        'name'        => 'New Tag 03',
                        'description' => 'This is description for New Tag 02',
                        'domain_id'   => '1'
                        );
        $tag =& crm_create_tag($params);
        
        $this->assertIsA($tag,'CRM_Core_DAO_Tag');
        $this->assertEqual($tag->name, 'New Tag 03');
        $this->assertEqual($tag->description, 'This is description for New Tag 02');
        $this->assertEqual($tag->parent_id, NULL);
        $this->assertEqual($tag->domain_id, '1');
        $this->_tag[$this->_t++] = $tag;
    }
    
    function testDeleteTag()
    {
        for ($i=0; $i<count($this->_tag); $i++) {
            $tag = $this->_tag[$i];
            $tagDelete =& crm_delete_tag($tag);
            $this->assertNULL($tagDelete);
        }
    }
}
?>