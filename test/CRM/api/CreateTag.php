<?php

require_once 'api/crm.php';

class TestOfCreateTagAPI extends UnitTestCase 
{
     protected $_tag;

    function setUp() 
    {
    }

    function tearDown() 
    {
    }

    /* Test cases for crm_create_tag for creating a new tag */ 
    
    function testCreateTag()
    {
        $params = array('name'    => 'New Tag',
                        'description' => 'newTag',
                        'parent_id' => 'NULL',
                        'domain_id' => '1'
                        );
        $tag =& crm_create_tag($params);
        $this->_tag = $tag;

        $this->assertIsA($tag, 'CRM_Core_DAO_Tag');
        $this->assertEqual($tag->name, 'New Tag');
        $this->assertEqual($tag->description, 'newTag');
        $this->assertEqual($tag->parent_id, 'NULL');
        $this->assertEqual($tag->domain_id, '1');

    }

    /*
    function testCreateTagWithDomainID()
    {
        $params = array(
                        'domain_id' => '1'
                        );

        $tag =& crm_create_tag($params);
        
        $this->assertIsA($tag, 'NULL');
        $this->assertEqual($tag->name, '');
        $this->assertEqual($tag->description, '');
        $this->assertEqual($tag->parent_id, '');
        $this->assertEqual($tag->domain_id, '');
        //print_r($tag->id);

    }
    */
    /* Test cases for crm_create_tag for checking the error */ 

    function testCreateTagErrorWithoutDomainID()
    {
        $params = array('name' => 'New Tag',
                        'description' => 'newTag',
                        'parent_id' => 'NULL',
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


    /* Test cases for crm_delete_tag for deleting the tag */ 
        
    function testDeleteTag()
    {
        $rTag =& crm_delete_tag($this->_tag);
        $this->assertNULL($rTag);
    }
}
?>