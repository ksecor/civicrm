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
        $this->_UFGroup[$UFGroup->id] = $UFGroup;
        $this->assertIsA($UFGroup, 'CRM_Core_DAO_UFGroup');
    }
    
    function testCreateUFGroup02()
    {
        $params = array(
                        'title'     => 'New Profile Group G02',
                        'help_pre'  => 'Help For Profile Group G02',
                        'is_active' => 1
                        );
        $UFGroup = crm_create_uf_group($params);
        $this->_UFGroup[$UFGroup->id] = $UFGroup;
        $this->assertIsA($UFGroup, 'CRM_Core_DAO_UFGroup');
    }
    
    function testCreateUFGroup03()
    {
        $params = array(
                        'title'     => 'New Profile Group G03',
                        'help_pre'  => 'Help For Profile Group G03',
                        'help_post' => 'This is Profile Group G03'
                        );
        $UFGroup = crm_create_uf_group($params);
        $this->_UFGroup[$UFGroup->id] = $UFGroup;
        $this->assertIsA($UFGroup, 'CRM_Core_DAO_UFGroup');
    }
    
    function testDeleteUFGroup()
    {
        foreach ($this->_UFGroup as $id => $group) {
            $UFGroup = crm_delete_uf_group($group);
            $this->assertEqual($UFGroup,true);
        }
    }
}
?>