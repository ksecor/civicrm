<?php

require_once 'api/crm.php';

class TestOfCreateUFGroupAPI extends UnitTestCase 
{
    
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }
    
    function testCreateUFGroupError()
    {
        $params = array();
        $UFGroup = crm_create_uf_group($params);
        $this->assertIsA($UFGroup, 'CRM_Core_Error');
    }
    
    function testCreateUFGroup()
    {
        $params = array('title' => 'New Profile Group G01');
        $UFGroup = crm_create_uf_group($params);
        $this->assertIsA($UFGroup, 'CRM_Core_BAO_UFGroup');
    }
    
    function testCreateUFGroupAll()
    {
        $params = array(
                        'title'     => 'New Profile Group G02',
                        'help_pre'  => 'Help For Profile Group G02',
                        'is_active' => 1
                        );
        $UFGroup = crm_create_uf_group($params);
        $this->assertIsA($UFGroup, 'CRM_Core_BAO_UFGroup');
    }
    
    function testDeleteUFField()
    {
        $UFField = crm_delete_uf_field($this->_UFField);
        $this->assertNull($UFField);
    }
    
    function testDeleteUFGroup()
    {
        $UFGroup = crm_delete_uf_group($this->_UFGroup);
        $this->assertNull($UFGroup);
    }
}
?>