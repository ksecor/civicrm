<?php

require_once 'api/crm.php';

class TestOfUpdateCustomFieldAPI extends UnitTestCase 
{
    
    function setUp() 
    {
    }
    
    function tearDown() 
    {
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
        $this->customGroup =& crm_create_custom_group($class_name, $paramsG);
        $this->assertIsA($this->customGroup, 'CRM_Core_BAO_CustomGroup');
        
        $paramsF = array('label' => 'Test Field 1 for Group 2',
                         'name'  => 'test_field_1',
                         'weight' => 3,
                         'data_type' => 'string',
                         'html_type' => 'text',
                         'is_searchable' => '1', 
                         'help_pre' => 'Pre Help For Tes Field 1 for Group 2',
                         'help_post'=> 'Post Help For Tes Field 1 for Group 2'
                         );
        $this->customField = & crm_create_custom_field($this->customGroup, $paramsF);
        $this->assertIsA($this->customField, 'CRM_Core_BAO_CustomField');
    }
    
    function testUpdateCustomFieldIErrorForCustomField()
    {
        $paramsF = array(
                         'help_pre' => 'Pre Help For Test Field 1 for Group 2..Edit',
                         'help_post'=> 'Post Help For Test Field 1 for Group 2..Edit'
                         );
        $customField = & crm_update_custom_field($paramsF, $otherCustomField);
        $this->assertIsA($customField, 'CRM_Core_Error');
    }
    
    function testUpdateCustomFieldIErrorForParams()
    {
        $paramsF;
        $customField = & crm_update_custom_field($paramsF, $this->customField);
        $this->assertIsA($customField, 'CRM_Core_Error');
    }
    
    function testUpdateCustomFieldIErrorForHTMLType()
    {
        $paramsF = array(
                         'label' => 'Test Field 1 for Group 2',
                         'name'  => 'test_field_1',
                         'weight' => 3,
                         'data_type' => 'string',
                         'html_type' => 'text',
                         'is_searchable' => '1',
                         'help_pre' => 'Pre Help For Test Field 1 for Group 2..Edit',
                         'help_post'=> 'Post Help For Test Field 1 for Group 2..Edit'
                         );
        $customField = & crm_update_custom_field($paramsF, $this->customField);
        $this->assertIsA($customField, 'CRM_Core_Error');
    }
    
    function testUpdateCustomFieldI()
    {
        $paramsF = array(
                         'weight' => 4,
                         'is_searchable' => 0,
                         'help_pre' => 'Pre Help For Test Field 1 for Group 2..Edit',
                         'help_post'=> 'Post Help For Test Field 1 for Group 2..Edit'
                         );
        $customField = & crm_update_custom_field($paramsF, $this->customField);
        $this->assertIsA($customField, 'CRM_Core_BAO_CustomField');
        $this->assertEqual($customField->label, 'Test Field 1 for Group 2');
    }

    function testDeleteCustomGroup()
    {
        crm_delete_custom_field($this->customField->id);
        crm_delete_custom_group($this->customGroup->id);
    }

}

