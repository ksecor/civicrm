<?php

require_once 'api/crm.php';

class TestOfGetCustomOptionValue extends UnitTestCase 
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
    
    function testCreateCustomGroupFieldOptionG()
    {
        $params = array('domain_id' => 1,
                        'title'            => 'Phone Information',
                        'name'             => 'new_group_2',
                        'weight'           => 3,
                        'style'            => 'Inline',
                        'collapse_display' => 0,
                        'is_active'        => 1,
                        'help_pre'         => 'Phonecall',
                        'help_post'        => 'Phone Call Information of a Contact.'
                        );
        $class_name = 'Phonecall';
        $customGroup =& crm_create_custom_group($class_name, $params);
        $this->assertIsA($customGroup, 'CRM_Core_BAO_CustomGroup');
        
        $paramsF1 = array('label'         => 'Name of Contact',
                          'name'          => 'test_field_1_group_2',
                          'data_type'     => 'String',
                          'html_type'     => 'Text',
                          'is_searchable' => '1', 
                          'is_active'     => 1,
                          'help_pre'      => 'Name',
                          'help_post'     => 'Please enter contact name.',
                          'weight'        => 3
                          );
        $this->customField1 = & crm_create_custom_field($customGroup, $paramsF1);
        $this->assertIsA($this->customField1, 'CRM_Core_BAO_CustomField');
        
        $paramsF2 = array('label'            => 'Prefferd time to call',
                          'name'             => 'test_field_2_group_2',
                          'data_type'        => 'String',
                          'html_type'        => 'CheckBox',
                          'is_searchable'    => '1', 
                          'is_active'        => 1,
                          'help_pre'         => 'Preferance',
                          'help_post'        => 'Select the preffered time of call',
                          'weight'           => 4,
                          'options_per_line' => 3
                          );
        $this->customField2 = & crm_create_custom_field($customGroup, $paramsF2);
        $this->assertIsA($this->customField2, 'CRM_Core_BAO_CustomField');
        
        $paramsOption1 = array('value' => 'WD : 8 AM to 11 AM',
                               'label' => 'Morning during Weekdays',
                               'is_active' => 1,
                               'weight' => 1
                               );
        $option1 =& crm_create_option_value($paramsOption1, $this->customField2); 
        $this->assertIsA($option1, 'CRM_Core_BAO_CustomOption');
        
        $paramsOption2 = array('value' => 'WD : 1 PM to 4 PM',
                               'label' => 'Afternoon during Weekdays',
                               'is_active' => 1,
                               'weight' => 2
                               );
        $option2 =& crm_create_option_value($paramsOption2, $this->customField2); 
        $this->assertIsA($option2, 'CRM_Core_BAO_CustomOption');
        
        $paramsOption3 = array('value' => 'WD : 6 PM to 9 PM',
                               'label' => 'Evening during Weekdays',
                               'is_active' => 1,
                               'weight' => 3
                               );
        $option3 =& crm_create_option_value($paramsOption3, $this->customField2); 
        $this->assertIsA($option3, 'CRM_Core_BAO_CustomOption');
        
        $paramsOption4 = array('value' => 'WE : 8 AM to 11 AM',
                               'label' => 'Morning Weekend',
                               'is_active' => 1,
                               'weight' => 4
                               );
        $option4 =& crm_create_option_value($paramsOption4, $this->customField2); 
        $this->assertIsA($option4, 'CRM_Core_BAO_CustomOption');
        
        $paramsOption5 = array('value' => 'WE : 1 PM to 4 PM',
                               'label' => 'Afternoon Weekend',
                               'is_active' => 1,
                               'weight' => 5
                               );
        $option5 =& crm_create_option_value($paramsOption5, $this->customField2); 
        $this->assertIsA($option5, 'CRM_Core_BAO_CustomOption');
        
        $paramsOption6 = array('value' => 'WE : 6 PM to 9 PM',
                               'label' => 'Evening Weekend',
                               'is_active' => 1,
                               'weight' => 6
                               );
        $option6 =& crm_create_option_value($paramsOption6, $this->customField2); 
        $this->assertIsA($option6, 'CRM_Core_BAO_CustomOption');
        $this->customGroup = $customGroup;
        
    }
    
    function testGetOptionValuePC()
    {
        // Get the options for the Custom Field.
        $options =& crm_get_option_values($this->customField2);
        CRM_Core_Error::debug('Options', $options);
    }

    function testDeleteCustomGroup()
    {
        crm_delete_custom_field($this->customField1->id);
        crm_delete_custom_field($this->customField2->id);
        crm_delete_custom_group($this->customGroup->id);
    }
    
}
?>
