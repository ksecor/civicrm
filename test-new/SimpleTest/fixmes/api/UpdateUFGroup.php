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
                        'title'     => 'New Profile Group G04',
                        'help_pre'  => 'Help For Profile Group G04',
                        'is_active' => 0
                        );
        $this->_UFGroup = crm_create_uf_group($params);
        $this->assertIsA($this->_UFGroup, 'CRM_Core_DAO_UFGroup');
    }
    
    function testUpdateUFGroup()
    {
        $params = array(
                        'help_pre'  => 'Help For Profile Group G04 .. Updated',
                        'is_active' => 1
                        );
        $UFGroup = crm_update_uf_group($params, $this->_UFGroup);
        $this->assertIsA($UFGroup, 'CRM_Core_DAO_UFGroup');
        $this->assertNotEqual($UFGroup->help_pre, 'Help For Profile Group G04');
    }
    
    function testDeleteUFGroup()
    {
         $UFGroup = crm_delete_uf_group($this->_UFGroup);
         $this->assertEqual($UFGroup,true);
    }
}
?>
