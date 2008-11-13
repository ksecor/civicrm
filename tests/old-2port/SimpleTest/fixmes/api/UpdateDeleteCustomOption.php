<?php

require_once 'api/crm.php';

class TestOfUpdateDeleteCustomOption extends UnitTestCase 
{
    var $optionError;
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
        $class_name = 'Individual';
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
        $this->option1Group1 = $option1;
        
        $paramsOption2 = array('value' => 'hello 2',
                               'label' => 'Option 2 Field 2 Group 1',
                               'is_active' => 1,
                               'weight' => 2
                               );
        $option2 =& crm_create_option_value($paramsOption2, $this->customField2); 
        $this->assertIsA($option2, 'CRM_Core_BAO_CustomOption');
        $this->option2Group1 = $option2;
        
        $paramsOption3 = array('value' => 'hello 3',
                               'label' => 'Option 3 Field 2 Group 1',
                               'is_active' => 1,
                               'weight' => 3
                               );
        $option3 =& crm_create_option_value($paramsOption3, $this->customField2); 
        $this->assertIsA($option3, 'CRM_Core_BAO_CustomOption');
        $this->option3Group1 = $option3;
                
        $paramsOption4 = array('value' => 'hello 4',
                               'label' => 'Option 4 Field 2 Group 1',
                               'is_active' => 1,
                               'weight' => 4
                               );
        $option4 =& crm_create_option_value($paramsOption4, $this->customField2); 
        $this->assertIsA($option4, 'CRM_Core_BAO_CustomOption');
        $this->option4Group1 = $option4;
        
        $paramsOption5 = array('value' => 'hello 5',
                               'label' => 'Option 5 Field 2 Group 1',
                               'is_active' => 1,
                               'weight' => 5
                               );
        $option5 =& crm_create_option_value($paramsOption5, $this->customField2); 
        $this->assertIsA($option5, 'CRM_Core_BAO_CustomOption');
        $this->option5Group1 = $option5;
        
        $paramsOption6 = array('value' => 'hello 6',
                               'label' => 'Option 6 Field 2 Group 1',
                               'is_active' => 1,
                               'weight' => 6
                               );
        $option6 =& crm_create_option_value($paramsOption6, $this->customField2); 
        $this->assertIsA($option6, 'CRM_Core_BAO_CustomOption');
        $this->option6Group1 = $option6;
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
        $class_name = 'Phonecall';
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
        $this->assertIsA($this->customField3, 'CRM_Core_BAO_CustomField');
        
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
        $this->option1Group2 = $option1;
        
        $paramsOption2 = array('value' => 'hello 2',
                               'label' => 'Option 2 Field 2 Group 2',
                               'is_active' => 1,
                               'weight' => 2
                               );
        $option2 =& crm_create_option_value($paramsOption2, $this->customField4); 
        $this->assertIsA($option2, 'CRM_Core_BAO_CustomOption');
        $this->option2Group2 = $option2;
        
        $paramsOption3 = array('value' => 'hello 3',
                               'label' => 'Option 3 Field 2 Group 2',
                               'is_active' => 1,
                               'weight' => 3
                               );
        $option3 =& crm_create_option_value($paramsOption3, $this->customField4); 
        $this->assertIsA($option3, 'CRM_Core_BAO_CustomOption');
        $this->option3Group2 = $option3;
        
        $paramsOption4 = array('value' => 'hello 4',
                               'label' => 'Option 4 Field 2 Group 2',
                               'is_active' => 1,
                               'weight' => 4
                               );
        $option4 =& crm_create_option_value($paramsOption4, $this->customField4); 
        $this->assertIsA($option4, 'CRM_Core_BAO_CustomOption');
        $this->option4Group2 = $option4;
        
        $paramsOption5 = array('value' => 'hello 5',
                               'label' => 'Option 5 Field 2 Group 2',
                               'is_active' => 1,
                               'weight' => 5
                               );
        $option5 =& crm_create_option_value($paramsOption5, $this->customField4); 
        $this->assertIsA($option5, 'CRM_Core_BAO_CustomOption');
        $this->option5Group2 = $option5;
        
        $paramsOption6 = array('value' => 'hello 6',
                               'label' => 'Option 6 Field 2 Group 2',
                               'is_active' => 1,
                               'weight' => 6
                               );
        $option6 =& crm_create_option_value($paramsOption6, $this->customField4); 
        $this->assertIsA($option6, 'CRM_Core_BAO_CustomOption');
        $this->option6Group2 = $option6;
        $this->customGroup2 = $customGroup;
    }
    
    /*****************************************************
     * test cases for crm_update_option_value
     ****************************************************/
    function testUpdateOptionValueError()
    {
        $params   = array('value' => 'Hello6..Edit'); 
        $option   =& crm_update_option_value($params, $this->optionError);
        $this->assertIsA($option, 'CRM_Core_Error');
    }
    function testUpdateOptionValueI()
    {
        $params   = array('value' => 'Hello3..Edit'); 
        $option   =& crm_update_option_value($params, $this->option3Group1);
        $this->assertIsA($option, 'CRM_Core_BAO_CustomOption');
        $this->assertEqual('Hello3..Edit', $option->value);
    }
    
    function testUpdateOptionValuePC()
    {
        $params   = array('value' => 'Hello5..Edit'); 
        $option   =& crm_update_option_value($params, $this->option5Group2);
        $this->assertIsA($option, 'CRM_Core_BAO_CustomOption');
        $this->assertEqual('Hello5..Edit', $option->value);
    }
    
    /********************************************************************
     * test case for crm_delete_option_value
     *******************************************************************/
    
    function testDeleteOptionValueI()
    {
        $option =& crm_delete_option_value($this->option4Group1);
        $this->assertNull($option);
        
        // Get the options for the Custom Field and confirm if the option is deleted.
        $options =& crm_get_option_values($this->customField2);
        $this->assertNull($options[$this->option4Group1->id]);
    }
    
    function testDeleteOptionValuePC()
    {
        $option =& crm_delete_option_value($this->option6Group2);
        $this->assertNull($option);
        
        // Get the options for the Custom Field and confirm if the option is deleted.
        $options =& crm_get_option_values($this->customField4);
        $this->assertNull($options[$this->option6Group2->id]);
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

