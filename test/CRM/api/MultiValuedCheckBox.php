<?php

require_once 'api/crm.php';

class MultiValuedCheckBox extends UnitTestCase 
{
    protected $_individual    = array();
    protected $_customFieldC1 = array();
    protected $_customFieldC2 = array();

    
    
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }
        
    function testCreateIndividual() 
    {
        $params = array('first_name'    => 'abc1',
                        'last_name'     => 'xyz1',
                        'email'         => 'man1@yahoo.com',
                        'location_type' => 'Work'
                        );
        $contact =& crm_create_contact($params, 'Individual');
        $this->assertIsA($contact, 'CRM_Contact_BAO_Contact');
        $this->assertEqual($contact->contact_type, 'Individual');
        $this->_individual = $contact;
    }
    
    function testCreateCheckBoxes() 
    {

        $params = array('domain_id' => 1,
                        'title' => 'Custom Group',
                        'name'  => 'new_group',
                        'weight' => 3,
                        'style' => 'Inline',
                        'collapse_display' => 0,
                        'is_active' => 1,
                        'help_pre' => 'This is Pre Help For New Group .',
                        'help_post' => 'This is for extending CONTACT type of Class.'
                        );
        $class_name = 'Contact';
        $customGroup =& crm_create_custom_group($class_name, $params);
        $this->assertIsA($customGroup, 'CRM_Core_BAO_CustomGroup');
        $paramsF2 = array('label' => 'Test Field',
                          'name'  => 'test_field',
                          'data_type' => 'String',
                          'html_type' => 'CheckBox',
                          'is_searchable' => '1', 
                          'is_active' => 1,
                          'help_pre' => 'Pre Help For Test Field ',
                          'help_post'=> 'Post Help For Test Field ',
                          'weight' => 4,
                          'options_per_line' => 4
                          );
        $customField2 = & crm_create_custom_field($customGroup, $paramsF2);
        $this->assertIsA($customField2, 'CRM_Core_BAO_CustomField');
        $this->_customFieldC2 = $customField2;
        
        $paramsOption1 = array('value' => 'hello 1',
                               'label' => 'ABC',
                               'is_active' => 1,
                               'weight' => 1
                               );
        $option1 =& crm_create_option_value($paramsOption1, $customField2); 
        $this->assertIsA($option1, 'CRM_Core_BAO_CustomOption');

        $paramsOption2 = array('value' => 'hello 2',
                               'label' => 'XYZ',
                               'is_active' => 1,
                               'weight' => 3
                               );
        $option2 =& crm_create_option_value($paramsOption2, $customField2); 
        $this->assertIsA($option2, 'CRM_Core_BAO_CustomOption');

        $paramsOption3 = array('value' => 'hello 3',
                               'label' => 'MNO',
                               'is_active' => 1,
                               'weight' => 6
                               );
      
        $option3 =& crm_create_option_value($paramsOption3, $customField2); 
        $this->assertIsA($option3, 'CRM_Core_BAO_CustomOption');
    
    }
    
    function testAddCheckBoxeValues() 
    {
        $value2 = array('value' => 'hello 3,hello 1');
        $customValue2 = crm_create_custom_value('civicrm_contact', $this->_individual->id, $this->_customFieldC2, $value2, ',');
        $this->assertIsA($customValue2, 'CRM_Core_BAO_CustomValue');
    }
    
}
