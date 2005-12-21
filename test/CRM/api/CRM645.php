<?php

require_once 'api/crm.php';

class TestOfCreateCustomValueAPI extends UnitTestCase 
{
    protected $_customFieldC1;
    protected $_customFieldC2;
    protected $_option1;
    protected $_option2;
    protected $_option3;

    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }
    
    /**************************************
     * Create Custom Group and Custom Fields for the Tests
     *************************************/
    
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
        $customField1 = & crm_create_custom_field($customGroup, $paramsF1);
        $this->assertIsA($customField1, 'CRM_Core_BAO_CustomField');
        $this->_customFieldC1 = $customField1;
        
        $paramsF2 = array('label' => 'Test Field 2 for Group 1',
                          'name'  => 'test_field_2_group_1',
                          'data_type' => 'String',
                          'html_type' => 'CheckBox',
                          'is_searchable' => '1', 
                          'is_active' => 1,
                          'help_pre' => 'Pre Help For Test Field 2 for Group 2',
                          'help_post'=> 'Post Help For Test Field 2 for Group 2',
                          'weight' => 4,
                          'options_per_line' => 4
                          );
        $customField2 = & crm_create_custom_field($customGroup, $paramsF2);
        $this->assertIsA($customField2, 'CRM_Core_BAO_CustomField');
        $this->_customFieldC2 = $customField2;
        
        $paramsOption1 = array('value' => 'hello 1',
                               'label' => 'Option 1 Field 2 Group 1',
                               'is_active' => 1,
                               'weight' => 1
                               );
        $option1 =& crm_create_option_value($paramsOption1, $customField2); 
        $this->assertIsA($option1, 'CRM_Core_BAO_CustomOption');
        $this->_option1 = $option1;
        
        $paramsOption2 = array('value' => 'hello 2',
                               'label' => 'Option 2 Field 2 Group 1',
                               'is_active' => 1,
                               'weight' => 3
                               );
        $option2 =& crm_create_option_value($paramsOption2, $customField2); 
        $this->assertIsA($option2, 'CRM_Core_BAO_CustomOption');
        $this->_option2 = $option2;
        
        $paramsOption3 = array('value' => 'hello 3',
                               'label' => 'Option 3 Field 2 Group 1',
                               'is_active' => 1,
                               'weight' => 6
                               );
        $option3 =& crm_create_option_value($paramsOption3, $customField2); 
        $this->assertIsA($option3, 'CRM_Core_BAO_CustomOption');
        $this->_option3 = $option3;
        
        $paramsOption4 = array('value' => 'hello 4',
                               'label' => 'Option 4 Field 2 Group 1',
                               'is_active' => 1,
                               'weight' => 2
                               );
        $option4 =& crm_create_option_value($paramsOption4, $customField2); 
        $this->assertIsA($option4, 'CRM_Core_BAO_CustomOption');
        
        $paramsOption5 = array('value' => 'hello 5',
                               'label' => 'Option 5 Field 2 Group 1',
                               'is_active' => 1,
                               'weight' => 4
                               );
        $option5 =& crm_create_option_value($paramsOption5, $customField2); 
        $this->assertIsA($option5, 'CRM_Core_BAO_CustomOption');
        
        $paramsOption6 = array('value' => 'hello 6',
                               'label' => 'Option 6 Field 2 Group 1',
                               'is_active' => 1,
                               'weight' => 1
                               );
        $option6 =& crm_create_option_value($paramsOption6, $customField2); 
        $this->assertIsA($option6, 'CRM_Core_BAO_CustomOption');
    }
    
    /********************************************************************
     * test case for crm_get_custom_field
     *******************************************************************/

    function testGetCustomFieldByName()
    {
        $params = array(
                        'name' => 'test_field_1_group_1',
                        );
        $customField =& crm_get_custom_field($params);
        $this->assertEqual('Test Field 1 for Group 1', $customField->label);
    }
    
    function testGetCustomFieldById()
    {
        $params = array(
                        'id' => $this->_customFieldC2->id,
                        );
        $customField =& crm_get_custom_field($params);
        $this->assertEqual('Test Field 2 for Group 1', $customField->label);
    }
    
    /********************************************************************
     * test case for crm_update_option_value
     *******************************************************************/
    
    function testUpdateOptionValue()
    {
        $optionId = $this->_option2->id;
        $params   = array('value' => 'Hello2..Edit'); 
        $option   =& crm_update_option_value($params, $optionId);
        $this->assertIsA($option, 'CRM_Core_BAO_CustomOption');
        $this->assertEqual('Hello2..Edit', $option->value);
    }
    
    /********************************************************************
     * test case for crm_delete_option_value
     *******************************************************************/
    
    function testDeleteOptionValue()
    {
        $optionId = $this->_option3->id;
        $option   =& crm_delete_option_value($optionId);
        $this->assertNull($option);
    }
}
?>