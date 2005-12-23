<?php

require_once 'api/crm.php';

class TestOfUpdateUFGroupAPI extends UnitTestCase 
{
    protected $_UFGroup;
    
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }
    
    function testCreateUFGroup()
    {
        $params = array(
                        'title'     => 'New Profile Group G03',
                        'help_pre'  => 'Help For Profile Group G03',
                        'is_active' => 1
                        );
        $UFGroup = crm_create_uf_group($params);
        $this->assertIsA($UFGroup, 'CRM_Core_DAO_UFGroup');
        $this->_UFGroup = $UFGroup;
    }
    
    function testUpdateUFGroupError()
    {
        $params = array();
        $UFGroup = crm_update_uf_group($params, $this->_UFGroup->id);
        $this->assertIsA($UFGroup,'CRM_Core_DAO_UFGroup');
    }
    
    function testUpdateUFGroup()
    {
        $params = array('help_pre' => 'Help For Profile Group G03 .. Updated');
        $UFGroup = crm_update_uf_group($params, $this->_UFGroup->id);
        $this->assertIsA($UFGroup, 'CRM_Core_DAO_UFGroup');
    }
    
    
    function testDeleteUFGroup()
    {
         $UFGroup = crm_delete_uf_group($this->_UFGroup);
         $this->assertEqual($UFGroup,true);
    }
}
?>