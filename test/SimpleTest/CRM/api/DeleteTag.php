<?php

require_once 'api/crm.php';

class TestOfDeleteTagAPI extends UnitTestCase 
{
    protected $_tag;
    //protected $_household;
    //protected $_organization;

    function setUp() 
    {
    }

    function tearDown() 
    {
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
        $this->_tag = $tag;
    }
    
    function testDeleteTagErrorEmtyTag()
    {
        $tag = array();
        $tagDelete =& crm_delete_tag($tag);
        $this->assertIsA($tagDelete, 'CRM_Core_Error');
    }
    
    function testDeleteTag()
    {
        $tag = $this->_tag;
        $tagDelete =& crm_delete_tag($tag);
        $this->assertNull($tagDelete);
    }
    
    /*function testDeleteTagError()
    {
        $tag = $this->_tag;
        $tagDelete =& crm_delete_tag($tag);
        $this->assertIsA($tagDelete, 'CRM_Core_Error');
    }*/
}

?>
