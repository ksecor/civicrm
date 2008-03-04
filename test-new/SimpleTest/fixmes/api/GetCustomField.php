<?php

require_once 'api/crm.php';

class TestOfGetCustomField extends UnitTestCase 
{
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }
    
    /***********************************************************
     * Creating Custom Group, Custom Field, Options for testing.
     **********************************************************/
    
    function testCreateCustomGroupFieldOptionC()
    {
        $params = array('domain_id' => 1,
                        'title' => 'New Group 1 For Creating Custom Field',
                        'name'  => 'new_group_1',
                        'weight' => 3,
                        'style' => 'Inline',
                        'collapse_display' => 0,
                        'is_active' => 1,
                        'help_pre' => 'This is Pre Help For New Group 1.',
                        'help_post' => 'This is for extending CONTACT type of Class.'
                        );
        $class_name = 'Contact';
        $customGroup =& crm_create_custom_group($class_name, $params);
        $this->assertIsA($customGroup, 'CRM_Core_BAO_CustomGroup');
        
        $paramsF1 = array('label' => 'Test Field 1 for Group 1',
                          'name'  => 'test_field_1_group_1',
                          'data_type' => 'String',
                          'html_type' => 'Text',
                          'is_searchable' => '1', 
                          'is_active' => 1,
                          'help_pre' => 'Pre Help For Test Field 1 for Group 2',
                          'help_post'=> 'Post Help For Test Field 1 for Group 2',
                          'weight' => 3
                          );
        $this->customField1 = & crm_create_custom_field($customGroup, $paramsF1);
        $this->assertIsA($this->customField1, 'CRM_Core_BAO_CustomField');
        
        $paramsF2 = array('label' => 'Test Field 2 for Group 1',
                          'name'  => 'test_field_2_group_1',
                          'data_type' => 'String',
                          'html_type' => 'CheckBox',
                          'is_searchable' => '1', 
                          'is_active' => 1,
                          'help_pre' => 'Pre Help For Test Field 2 for Group 1',
                          'help_post'=> 'Post Help For Test Field 2 for Group 1',
                          'weight' => 4,
                          'options_per_line' => 2
                          );
        $this->customField2 = & crm_create_custom_field($customGroup, $paramsF2);
        $this->assertIsA($this->customField2, 'CRM_Core_BAO_CustomField');
        
        $paramsOption1 = array('value' => 'hello 1',
                               'label' => 'Option 1 Field 2 Group 1',
                               'is_active' => 1,
                               'weight' => 1
                               );
        $option1 =& crm_create_option_value($paramsOption1, $this->customField2); 
        $this->assertIsA($option1, 'CRM_Core_BAO_CustomOption');
        
        $paramsOption2 = array('value' => 'hello 2',
                               'label' => 'Option 2 Field 2 Group 1',
                               'is_active' => 1,
                               'weight' => 3
                               );
        $option2 =& crm_create_option_value($paramsOption2, $this->customField2); 
        $this->assertIsA($option2, 'CRM_Core_BAO_CustomOption');
        
        $paramsOption3 = array('value' => 'hello 3',
                               'label' => 'Option 3 Field 2 Group 1',
                               'is_active' => 1,
                               'weight' => 6
                               );
        $option3 =& crm_create_option_value($paramsOption3, $this->customField2); 
        $this->assertIsA($option3, 'CRM_Core_BAO_CustomOption');
        
        $paramsOption4 = array('value' => 'hello 4',
                               'label' => 'Option 4 Field 2 Group 1',
                               'is_active' => 1,
                               'weight' => 2
                               );
        $option4 =& crm_create_option_value($paramsOption4, $this->customField2); 
        $this->assertIsA($option4, 'CRM_Core_BAO_CustomOption');
        
        $paramsOption5 = array('value' => 'hello 5',
                               'label' => 'Option 5 Field 2 Group 1',
                               'is_active' => 1,
                               'weight' => 4
                               );
        $option5 =& crm_create_option_value($paramsOption5, $this->customField2); 
        $this->assertIsA($option5, 'CRM_Core_BAO_CustomOption');
        
        $paramsOption6 = array('value' => 'hello 6',
                               'label' => 'Option 6 Field 2 Group 1',
                               'is_active' => 1,
                               'weight' => 1
                               );
        $option6 =& crm_create_option_value($paramsOption6, $this->customField2); 
        $this->assertIsA($option6, 'CRM_Core_BAO_CustomOption');
        $this->customGroup1 = $customGroup;
    }
    
    function testCreateCustomGroupFieldOptionG()
    {
        $params = array('domain_id' => 1,
                        'title' => 'New Group 2 For Creating Custom Field',
                        'name'  => 'new_group_2',
                        'weight' => 3,
                        'style' => 'Inline',
                        'collapse_display' => 0,
                        'is_active' => 1,
                        'help_pre' => 'This is Pre Help For New Group 2.',
                        'help_post' => 'This is for extending GROUP type of Class.'
                        );
        $class_name = 'Group';
        $customGroup =& crm_create_custom_group($class_name, $params);
        $this->assertIsA($customGroup, 'CRM_Core_BAO_CustomGroup');
        
        $paramsF1 = array('label' => 'Test Field 1 for Group 2',
                          'name'  => 'test_field_1_group_2',
                          'data_type' => 'String',
                          'html_type' => 'Text',
                          'is_searchable' => '1', 
                          'is_active' => 1,
                          'help_pre' => 'Pre Help For Test Field 1 for Group 2',
                          'help_post'=> 'Post Help For Test Field 1 for Group 2',
                          'weight' => 3
                          );
        $this->customField3 = & crm_create_custom_field($customGroup, $paramsF1);
        $this->assertIsA($this->customField1, 'CRM_Core_BAO_CustomField');
        
        $paramsF2 = array('label' => 'Test Field 2 for Group 2',
                          'name'  => 'test_field_2_group_2',
                          'data_type' => 'String',
                          'html_type' => 'CheckBox',
                          'is_searchable' => '1', 
                          'is_active' => 1,
                          'help_pre' => 'Pre Help For Test Field 2 for Group 2',
                          'help_post'=> 'Post Help For Test Field 2 for Group 2',
                          'weight' => 4,
                          'options_per_line' => 4
                          );
        $this->customField4 = & crm_create_custom_field($customGroup, $paramsF2);
        $this->assertIsA($this->customField4, 'CRM_Core_BAO_CustomField');
        
        $paramsOption1 = array('value' => 'hello 1',
                               'label' => 'Option 1 Field 2 Group 2',
                               'is_active' => 1,
                               'weight' => 1
                               );
        $option1 =& crm_create_option_value($paramsOption1, $this->customField4); 
        $this->assertIsA($option1, 'CRM_Core_BAO_CustomOption');
        
        $paramsOption2 = array('value' => 'hello 2',
                               'label' => 'Option 2 Field 2 Group 2',
                               'is_active' => 1,
                               'weight' => 3
                               );
        $option2 =& crm_create_option_value($paramsOption2, $this->customField4); 
        $this->assertIsA($option2, 'CRM_Core_BAO_CustomOption');
        
        $paramsOption3 = array('value' => 'hello 3',
                               'label' => 'Option 3 Field 2 Group 2',
                               'is_active' => 1,
                               'weight' => 6
                               );
        $option3 =& crm_create_option_value($paramsOption3, $this->customField4); 
        $this->assertIsA($option3, 'CRM_Core_BAO_CustomOption');
        
        $paramsOption4 = array('value' => 'hello 4',
                               'label' => 'Option 4 Field 2 Group 2',
                               'is_active' => 1,
                               'weight' => 2
                               );
        $option4 =& crm_create_option_value($paramsOption4, $this->customField4); 
        $this->assertIsA($option4, 'CRM_Core_BAO_CustomOption');
        
        $paramsOption5 = array('value' => 'hello 5',
                               'label' => 'Option 5 Field 2 Group 2',
                               'is_active' => 1,
                               'weight' => 4
                               );
        $option5 =& crm_create_option_value($paramsOption5, $this->customField4); 
        $this->assertIsA($option5, 'CRM_Core_BAO_CustomOption');
        
        $paramsOption6 = array('value' => 'hello 6',
                               'label' => 'Option 6 Field 2 Group 2',
                               'is_active' => 1,
                               'weight' => 1
                               );
        $option6 =& crm_create_option_value($paramsOption6, $this->customField4); 
        $this->assertIsA($option6, 'CRM_Core_BAO_CustomOption');
        $this->customGroup2 = $customGroup;
    }
    
    /*****************************************************
     * test cases for crm_get_custom_field
     ****************************************************/
    function testGetCustomFieldByName()
    {
        $params = array('name' => 'test_field_1_group_1');
        $customField =& crm_get_custom_field($params);
        $this->assertIsA($customField, 'CRM_Core_DAO_CustomField');
        $this->assertEqual('Test Field 1 for Group 1', $customField->label);
    }
    
    function testGetCustomFieldByLabel()
    {
        $params = array('label' => 'Test Field 2 for Group 2');
        $customField =& crm_get_custom_field($params);
        $this->assertIsA($customField, 'CRM_Core_DAO_CustomField');
        $this->assertEqual('test_field_2_group_2', $customField->name);
    }

    function testDeleteCustomGroup()
    {
        crm_delete_custom_field($this->customField1->id);
        crm_delete_custom_field($this->customField2->id);
        crm_delete_custom_field($this->customField3->id);
        crm_delete_custom_field($this->customField4->id);
        crm_delete_custom_group($this->customGroup1->id);
        crm_delete_custom_group($this->customGroup2->id);
    }
}


