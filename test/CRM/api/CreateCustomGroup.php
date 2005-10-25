<?php

require_once 'api/crm.php';

class TestOfCreateCustomGroupAPI extends UnitTestCase 
{
    
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }

    function testCreateNoParam()
    {
        $params = array();
        $class_name = 'Contact';
        $customGroup =& crm_create_custom_group($class_name, $params);
        $this->assertIsA($customGroup, 'CRM_Core_Error');
    }
    
    function testCreateCustomGroup()
    {
        $params = array('domain_id' => 1,
                        'title' => 'Test Group 1 For Creating Custom Group',
                        'name'  => 'test_group_1',
                        'weight' => 4,
                        'collapse_display' => 1,
                        'help_pre' => 'This is Pre Help For Test Group 1',
                        'help_post' => 'This is Post Help For Test Group 1'
                        );
        $class_name = 'Individual';
        $customGroup =& crm_create_custom_group($class_name, $params);
        $this->assertIsA($customGroup, 'CRM_Core_BAO_CustomGroup');
    } 
    
    function testCustomGroupNoTitle()
    {
        $params = array('domain_id' => 1,
                        'weight' => 5, 
                        'collapse_display' => 1,
                        'help_pre' => 'This is Pre Help For Test Group 2',
                        'help_post' => 'This is Post Help For Test Group 2'
                        );
        $class_name = 'Contact';
        $customGroup =& crm_create_custom_group($class_name, $params);
        $this->assertIsA($customGroup, 'CRM_Core_BAO_CustomGroup');
    }
    
    function testCustomGroupNoWeight()
    {
        $params = array('domain_id' => 1,
                        'title' => 'Test Group 3 For Creating Custom Group',
                        'name'  => 'test_group_3',
                        'collapse_display' => 1,
                        'help_pre' => 'This is Pre Help For Test Group 3',
                        'help_post' => 'This is Post Help For Test Group 3'
                        );
        $class_name = 'Household';
        $customGroup =& crm_create_custom_group($class_name, $params);
        $this->assertIsA($customGroup, 'CRM_Core_Error');
    }
    
    function testCustomGroupNoDomain()
    {
        $params = array('title' => 'Test Group 4 For Creating Custom Group',
                        'name'  => 'test_group_4',
                        'collapse_display' => 1,
                        'weight' => 5,
                        'help_pre' => 'This is Pre Help For Test Group 4',
                        'help_post' => 'This is Post Help For Test Group 4'
                        );
        $class_name = 'Organization';
        $customGroup =& crm_create_custom_group($class_name, $params);
        $this->assertIsA($customGroup, 'CRM_Core_Error');
    }
}