<?php

require_once 'api/crm.php';

class TestOfCreateCustomFieldAPI extends UnitTestCase 
{
    
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }
    
    function testCreateCustomFieldC()
    {
        $params = array('domain_id' => 1,
                        'title' => 'New Group 1 For Creating Custom Field',
                        'name'  => 'new_group_1',
                        'weight' => 3,
                        'collapse_display' => 0,
                        'help_pre' => 'This is Pre Help For New Group 1.',
                        'help_post' => 'This is for extending CONTACT type of Class.'
                        );
        $class_name = 'Contact';
        $customGroup =& crm_create_custom_group($class_name, $params);
        $this->assertIsA($customGroup, 'CRM_Core_BAO_CustomGroup');
        
        $paramsF = array('label' => 'Test Field 1 for Group 2',
                         'weight' => 3,
                         );
        $customField = & crm_create_custom_field($customGroup, $paramsF);
        $this->assertIsA($customGroup, 'CRM_Core_BAO_CustomGroup');
        
    }
    
    function testCreateCustomFieldI()
    {
        $paramsG = array('domain_id' => 1,
                        'title' => 'New Group 2 For Creating Custom Field',
                        'name'  => 'new_group_2',
                        'weight' => 4,
                        'collapse_display' => 1,
                        'help_pre' => 'This is Pre Help For New Group 2.',
                        'help_post' => 'This is for extending INDIVIDUAL type of Class.'
                        );
        $class_name = 'Individual';
        $customGroup =& crm_create_custom_group($class_name, $paramsG);
        $this->assertIsA($customGroup, 'CRM_Core_BAO_CustomGroup');
     
        $paramsF = array('label' => 'Test Field 1 for Group 2',
                         'name'  => 'test_field_1',
                         'weight' => 3,
                         'data_type' => 'string',
                         'html_type' => 'text',
                         'is_searchable' => '1', 
                         'help_pre' => 'Pre Help For Tes Field 1 for Group 2',
                         'help_post'=> 'Post Help For Tes Field 1 for Group 2'
                         );
        $customField = & crm_create_custom_field($customGroup, $paramsF);
        $this->assertIsA($customGroup, 'CRM_Core_BAO_CustomGroup');
        
    }
    
    function testCreateCustomFieldH()
    {
        $params = array('domain_id' => 1,
                        'title' => 'New Group 3 For Creating Custom Field',
                        'name'  => 'new_group_3',
                        'weight' => 5,
                        'collapse_display' => 0,
                        'help_pre' => 'This is Pre Help For New Group 3.',
                        'help_post' => 'This is for extending HOUSEHOLD type of Class.',
                        'is_active' => 1
                        );
        $class_name = 'Household';
        $customGroup =& crm_create_custom_group($class_name, $params);
        $this->assertIsA($customGroup, 'CRM_Core_BAO_CustomGroup');
        
        $paramsF = array('label' => 'Test Field 1 for Group 3',
                         'name'  => 'test_field_1',
                         'weight' => 2,
                         'data_type' => 'Int',
                         'html_type' => 'CheckBox',
                         'is_searchable' => '1', 
                         'help_pre' => 'Pre Help For Tes Field 1 for Group 3',
                         'help_post'=> 'Post Help For Tes Field 1 for Group 3'
                         );
        $customField = & crm_create_custom_field($customGroup, $paramsF);
        $this->assertIsA($customField, 'CRM_Core_BAO_CustomField');
    }
    
    function testCreateCustomFieldONoWeight()
    {
        $params = array('domain_id' => 1,
                        'title' => 'New Group 4 For Creating Custom Field',
                        'name'  => 'new_group_4',
                        'weight' => 6,
                        'collapse_display' => 1,
                        'help_pre' => 'This is Pre Help For New Group 4.',
                        'help_post' => 'This is for extending ORGANIZATION type of Class.'
                        );
        $class_name = 'Organization';
        $customGroup =& crm_create_custom_group($class_name, $params);
        $this->assertIsA($customGroup, 'CRM_Core_BAO_CustomGroup');
        
        $paramsF = array('label' => 'Test Field 1 for Group 4',
                         'name'  => 'test_field_1',
                         'data_type' => 'Int',
                         'html_type' => 'CheckBox',
                         'is_searchable' => '1', 
                         'help_pre' => 'Pre Help For Tes Field 1 for Group 4',
                         'help_post'=> 'Post Help For Tes Field 1 for Group 4'
                         );
        $customField = & crm_create_custom_field($customGroup, $paramsF);
        $this->assertIsA($customField, 'CRM_Core_Error');
    }
    
    function testCreateCustomFieldEmptyParam()
    {
        $params = array('domain_id' => 1,
                        'title' => 'New Group 5 For Creating Custom Field',
                        'name'  => 'new_group_5',
                        'weight' => 3,
                        'collapse_display' => 0,
                        'help_pre' => 'This is Pre Help For New Group 5.',
                        'help_post' => 'This is for extending CONTACT type of Class.'
                        );
        $class_name = 'Contact';
        $customGroup =& crm_create_custom_group($class_name, $params);
        $this->assertIsA($customGroup, 'CRM_Core_BAO_CustomGroup');
        
        $paramsF = array();
        $customField = & crm_create_custom_field($customGroup, $paramsF);
        $this->assertIsA($customField, 'CRM_Core_Error');
    }
    
}
?>