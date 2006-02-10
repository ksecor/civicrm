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
                        'style' => 'Tab',
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
                        'style' => 'Tab',
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
                        'style' => 'Tab',
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
                        'style' => 'Tab',
                        'help_pre' => 'This is Pre Help For Test Group 4',
                        'help_post' => 'This is Post Help For Test Group 4'
                        );
        $class_name = 'Organization';
        $customGroup =& crm_create_custom_group($class_name, $params);
        $this->assertIsA($customGroup, 'CRM_Core_Error');
    }
    
    function testCreateCustomGroupForContribution()
    {
        $params = array('domain_id' => 1,
                        'title' => 'Test Group 5 For Creating Custom Group',
                        'name'  => 'test_group_5',
                        'weight' => 6,
                        'collapse_display' => 1,
                        'style' => 'Tab',
                        'help_pre' => 'This is Pre Help For Test Group 5',
                        'help_post' => 'This is Post Help For Test Group 5'
                        );
        $class_name = 'Contribution';
        $customGroup =& crm_create_custom_group($class_name, $params);
        $this->assertIsA($customGroup, 'CRM_Core_BAO_CustomGroup');
    }
    
    function testCreateCustomGroupForGroup()
    {
        $params = array('domain_id' => 1,
                        'title' => 'Test Group 6 For Creating Custom Group',
                        'name'  => 'test_group_6',
                        'weight' => 7,
                        'collapse_display' => 1,
                        'style' => 'Tab',
                        'help_pre' => 'This is Pre Help For Test Group 6',
                        'help_post' => 'This is Post Help For Test Group 6'
                        );
        $class_name = 'Group';
        $customGroup =& crm_create_custom_group($class_name, $params);
        $this->assertIsA($customGroup, 'CRM_Core_BAO_CustomGroup');
    }
    
    function testCreateCustomGroupForActivity()
    {
        $params = array('domain_id' => 1,
                        'title' => 'Test Group 7 For Creating Custom Group',
                        'name'  => 'test_group_7',
                        'weight' => 8,
                        'collapse_display' => 1,
                        'style' => 'Tab',
                        'help_pre' => 'This is Pre Help For Test Group 7',
                        'help_post' => 'This is Post Help For Test Group 7'
                        );
        $class_name = 'Activities';
        $customGroup =& crm_create_custom_group($class_name, $params);
        $this->assertIsA($customGroup, 'CRM_Core_BAO_CustomGroup');
    }
    
    function testCreateCustomGroupForPhonecall()
    {
        $params = array('domain_id' => 1,
                        'title' => 'Test Group 8 For Creating Custom Group',
                        'name'  => 'test_group_8',
                        'weight' => 9,
                        'collapse_display' => 1,
                        'style' => 'Tab',
                        'help_pre' => 'This is Pre Help For Test Group 8',
                        'help_post' => 'This is Post Help For Test Group 8'
                        );
        $class_name = 'Phonecall';
        $customGroup =& crm_create_custom_group($class_name, $params);
        $this->assertIsA($customGroup, 'CRM_Core_BAO_CustomGroup');
    }
    
    function testCreateCustomGroupForMeeting()
    {
        $params = array('domain_id' => 1,
                        'title' => 'Test Group 9 For Creating Custom Group',
                        'name'  => 'test_group_9',
                        'weight' => 9,
                        'collapse_display' => 1,
                        'style' => 'Tab',
                        'help_pre' => 'This is Pre Help For Test Group 9',
                        'help_post' => 'This is Post Help For Test Group 9'
                        );
        $class_name = 'Meeting';
        $customGroup =& crm_create_custom_group($class_name, $params);
        $this->assertIsA($customGroup, 'CRM_Core_BAO_CustomGroup');
    }
}