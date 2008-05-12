<?php

require_once 'api/crm.php';

class TestOfCreateCustomValueAPI extends UnitTestCase 
{
    protected $_individual    = array();
    protected $_houseHold     = array();
    protected $_organization  = array();
    protected $_organization02  = array();
    protected $_customFieldI  = array();
    protected $_customFieldH  = array();
    protected $_customFieldO  = array();
    protected $_customFieldC1 = array();
    protected $_customFieldC2 = array();
    
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }
    
    /**************************************
     * Create Contacts for the Tests
     *************************************/
    
    function testCreateIndividual() 
    {
        $params = array(
                        'first_name'    => 'abc1',
                        'last_name'     => 'xyz1',
                        'email'         => 'man1@yahoo.com',
                        'location_type' => 'Work'
                        );
        $contact =& crm_create_contact($params, 'Individual');
        $this->assertIsA($contact, 'CRM_Contact_BAO_Contact');
        $this->assertEqual($contact->contact_type, 'Individual');
        $this->_individual = $contact;
    }
    
    function testCreateHousehold() 
    {
        $params = array('household_name' => 'The abc Household');
        $contact =& crm_create_contact($params, 'Household');
        $this->assertIsA($contact, 'CRM_Contact_BAO_Contact');
        $this->assertEqual($contact->contact_type, 'Household');
        $this->_houseHold = $contact;
    }
    
    function testCreateOrganization() 
    {
        $params = array('organization_name' => 'The abc Organization');
        $contact =& crm_create_contact($params, 'Organization');
        $this->assertIsA($contact, 'CRM_Contact_BAO_Contact');
        $this->assertEqual($contact->contact_type, 'Organization');
        $this->_organization = $contact;
    }
    
    function testCreateOrganization02() 
    {
        $params = array('organization_name' => 'The abc02 Organization');
        $contact =& crm_create_contact($params, 'Organization');
        $this->assertIsA($contact, 'CRM_Contact_BAO_Contact');
        $this->assertEqual($contact->contact_type, 'Organization');
        $this->_organization02 = $contact;
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

        $paramsOption2 = array('value' => 'hello 2',
                               'label' => 'Option 2 Field 2 Group 1',
                               'is_active' => 1,
                               'weight' => 3
                               );
        $option2 =& crm_create_option_value($paramsOption2, $customField2); 
        $this->assertIsA($option2, 'CRM_Core_BAO_CustomOption');

        $paramsOption3 = array('value' => 'hello 3',
                               'label' => 'Option 3 Field 2 Group 1',
                               'is_active' => 1,
                               'weight' => 6
                               );
        $option3 =& crm_create_option_value($paramsOption3, $customField2); 
        $this->assertIsA($option3, 'CRM_Core_BAO_CustomOption');

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
        $this->_customGroupC = $customGroup;

    }
    
    function testCreateCustomGroupFieldOptionI()
    {
        $paramsG = array('domain_id' => 1,
                         'title' => 'New Group 2 For Creating Custom Field',
                         'name'  => 'new_group_2',
                         'weight' => 4,
                         'style' => 'Inline',
                         'collapse_display' => 1,
                         'is_active' => 1,
                         'help_pre' => 'This is Pre Help For New Group 2.',
                         'help_post' => 'This is for extending INDIVIDUAL type of Class.'
                         );
        $class_name = 'Individual';
        $customGroup =& crm_create_custom_group($class_name, $paramsG);
        $this->assertIsA($customGroup, 'CRM_Core_BAO_CustomGroup');
        $this->_customGroupI = $customGroup;
        
        $paramsF = array('label' => 'Test Field 1 for Group 2',
                         'name'  => 'test_field_1_group_2',
                         'weight' => 3,
                         'data_type' => 'Int',
                         'html_type' => 'Radio',
                         'is_searchable' => 1, 
                         'is_active' => 1,
                         'options_per_line' => 4,
                         'help_pre' => 'Pre Help For Test Field 1 for Group 2',
                         'help_post'=> 'Post Help For Test Field 1 for Group 2'
                         );
        $customField = & crm_create_custom_field($customGroup, $paramsF);
        $this->assertIsA($customField, 'CRM_Core_BAO_CustomField');
        $this->_customFieldI = $customField;
        
        $paramsOption1 = array('value' => 1,
                               'label' => 'Option 1 Field 1 Group 2',
                               'weight' => 1,
                               'is_active' => 1
                               );
        $option1 =& crm_create_option_value($paramsOption1, $customField); 
        $this->assertIsA($option1, 'CRM_Core_BAO_CustomOption');
        
        $paramsOption2 = array('value' => 2,
                               'label' => 'Option 2 Field 1 Group 2',
                               'weight' => 4,
                               'is_active' => 1
                               );
        $option2 =& crm_create_option_value($paramsOption2, $customField); 
        $this->assertIsA($option2, 'CRM_Core_BAO_CustomOption');

        $paramsOption3 = array('value' => 3,
                               'label' => 'Option 3 Field 1 Group 2',
                               'weight' => 6,
                               'is_active' => 1
                               );
        $option3 =& crm_create_option_value($paramsOption3, $customField); 
        $this->assertIsA($option3, 'CRM_Core_BAO_CustomOption');

        $paramsOption4 = array('value' => 4,
                               'label' => 'Option 4 Field 1 Group 2',
                               'weight' => 2,
                               'is_active' => 1
                               );
        $option4 =& crm_create_option_value($paramsOption4, $customField); 
        $this->assertIsA($option4, 'CRM_Core_BAO_CustomOption');

        $paramsOption5 = array('value' => 5,
                               'label' => 'Option 5 Field 1 Group 2',
                               'weight' => 5,
                               'is_active' => 1
                               );
        $option5 =& crm_create_option_value($paramsOption5, $customField); 
        $this->assertIsA($option5, 'CRM_Core_BAO_CustomOption');
        
        $paramsOption6 = array('value' => 6,
                               'label' => 'Option 6 Field 1 Group 2',
                               'weight' => 1,
                               'is_active' => 1
                               );
        $option6 =& crm_create_option_value($paramsOption6, $customField); 
        $this->assertIsA($option6, 'CRM_Core_BAO_CustomOption');
    }
    
    function testCreateCustomGroupFieldOptionH()
    {
        $params = array('domain_id' => 1,
                        'title' => 'New Group 3 For Creating Custom Field',
                        'name'  => 'new_group_3',
                        'weight' => 5,
                        'style' => 'Inline',
                        'collapse_display' => 0,
                        'help_pre' => 'This is Pre Help For New Group 3.',
                        'help_post' => 'This is for extending HOUSEHOLD type of Class.',
                        'is_active' => 1
                        );
        $class_name = 'Household';
        $customGroup =& crm_create_custom_group($class_name, $params);
        $this->assertIsA($customGroup, 'CRM_Core_BAO_CustomGroup');
        $this->_customGroupH = $customGroup;
        
        $paramsF = array('label' => 'Test Field 1 for Group 3',
                         'name'  => 'test_field_1_group_3',
                         'weight' => 2,
                         'data_type' => 'Memo',
                         'html_type' => 'Text Area',
                         'is_searchable' => '1', 
                         'is_active' => 1,
                         'help_pre' => 'Pre Help For Test Field 1 for Group 3',
                         'help_post'=> 'Post Help For Test Field 1 for Group 3'
                         );
        $customField = & crm_create_custom_field($customGroup, $paramsF);
        $this->assertIsA($customField, 'CRM_Core_BAO_CustomField');
        $this->_customFieldH = $customField;
    }
    
    function testCreateCustomGroupFieldOptionO()
    {
        $params = array('domain_id' => 1,
                        'title' => 'New Group 4 For Creating Custom Field',
                        'name'  => 'new_group_4',
                        'weight' => 6,
                        'style' => 'Inline',
                        'collapse_display' => 0,
                        'help_pre' => 'This is Pre Help For New Group 4.',
                        'help_post' => 'This is for extending ORGANIZATION type of Class.',
                        'is_active' => 1
                        );
        $class_name = 'Organization';
        $customGroup =& crm_create_custom_group($class_name, $params);
        $this->assertIsA($customGroup, 'CRM_Core_BAO_CustomGroup');
        $this->_customGroupO = $customGroup;
        
        $paramsF = array('label' => 'Test Field 1 for Group 4',
                         'name'  => 'test_field_1_group_4',
                         'weight' => 2,
                         'data_type' => 'Memo',
                         'html_type' => 'Text Area',
                         'is_searchable' => '1', 
                         'is_active' => 1,
                         'help_pre' => 'Pre Help For Test Field 1 for Group 4',
                         'help_post'=> 'Post Help For Test Field 1 for Group 4'
                         );
        $customField = & crm_create_custom_field($customGroup, $paramsF);
        $this->assertIsA($customField, 'CRM_Core_BAO_CustomField');
        $this->_customFieldO = $customField;
    }
    
    function testCreateCustomGroupFieldOptionG()
    {
        $params = array('domain_id' => 1,
                        'title' => 'New Group 5 For Creating Custom Field',
                        'name'  => 'new_group_5',
                        'weight' => 3,
                        'style' => 'Inline',
                        'collapse_display' => 0,
                        'is_active' => 1,
                        'help_pre' => 'This is Pre Help For New Group 1.',
                        'help_post' => 'This is for extending GROUP type of Class.'
                        );
        $class_name = 'Group';
        $customGroup =& crm_create_custom_group($class_name, $params);
        $this->assertIsA($customGroup, 'CRM_Core_BAO_CustomGroup');
        
        $paramsF1 = array('label' => 'Test Field 1 for Group 5',
                          'name'  => 'test_field_1_group_5',
                          'data_type' => 'String',
                          'html_type' => 'Text',
                          'is_searchable' => '1', 
                          'is_active' => 1,
                          'help_pre' => 'Pre Help For Test Field 1 for Group 5',
                          'help_post'=> 'Post Help For Test Field 1 for Group 5',
                          'weight' => 3
                          );
        $customField1 = & crm_create_custom_field($customGroup, $paramsF1);
        $this->assertIsA($customField1, 'CRM_Core_BAO_CustomField');
        $this->_customFieldG1 = $customField1;
        
        $paramsF2 = array('label' => 'Test Field 2 for Group 5',
                         'name'  => 'test_field_2_group_5',
                         'weight' => 3,
                         'data_type' => 'Int',
                         'html_type' => 'Radio',
                         'is_searchable' => 1, 
                         'is_active' => 1,
                         'options_per_line' => 2,
                         'help_pre' => 'Pre Help For Test Field 2 for Group 5',
                         'help_post'=> 'Post Help For Test Field 2 for Group 5'
                         );
        $customField2 = & crm_create_custom_field($customGroup, $paramsF2);
        $this->assertIsA($customField2, 'CRM_Core_BAO_CustomField');
        $this->_customFieldG2 = $customField2;
        
        $paramsOption1 = array('value' => 1,
                               'label' => 'Option 1 Field 2 Group 5',
                               'weight' => 1,
                               'is_active' => 1
                               );
        $option1 =& crm_create_option_value($paramsOption1, $customField2); 
        $this->assertIsA($option1, 'CRM_Core_BAO_CustomOption');
        
        $paramsOption2 = array('value' => 2,
                               'label' => 'Option 2 Field 2 Group 5',
                               'weight' => 4,
                               'is_active' => 1
                               );
        $option2 =& crm_create_option_value($paramsOption2, $customField2); 
        $this->assertIsA($option2, 'CRM_Core_BAO_CustomOption');
        $this->_customGroupG = $customGroup;
    }
    
    function testCreateCustomGroupFieldOptionPC()
    {
        $params = array('domain_id' => 1,
                        'title' => 'New Group 6 For Creating Custom Field',
                        'name'  => 'new_group_6',
                        'weight' => 3,
                        'style' => 'Inline',
                        'collapse_display' => 0,
                        'is_active' => 1,
                        'help_pre' => 'This is Pre Help For New Group 6.',
                        'help_post' => 'This is for extending PHONE CALL type of Class.'
                        );
        $class_name = 'Phonecall';
        $customGroup =& crm_create_custom_group($class_name, $params);
        $this->assertIsA($customGroup, 'CRM_Core_BAO_CustomGroup');
        
        $paramsF1 = array('label' => 'Test Field 1 for Group 6',
                          'name'  => 'test_field_1_group_6',
                          'data_type' => 'String',
                          'html_type' => 'Text',
                          'is_searchable' => '1', 
                          'is_active' => 1,
                          'help_pre' => 'Pre Help For Test Field 1 for Group 6',
                          'help_post'=> 'Post Help For Test Field 1 for Group 6',
                          'weight' => 3
                          );
        $customField1 = & crm_create_custom_field($customGroup, $paramsF1);
        $this->assertIsA($customField1, 'CRM_Core_BAO_CustomField');
        $this->_customFieldPC1 = $customField1;
        
        $paramsF2 = array('label' => 'Test Field 2 for Group 6',
                         'name'  => 'test_field_2_group_6',
                         'weight' => 3,
                         'data_type' => 'Int',
                         'html_type' => 'Radio',
                         'is_searchable' => 1, 
                         'is_active' => 1,
                         'options_per_line' => 2,
                         'help_pre' => 'Pre Help For Test Field 2 for Group 6',
                         'help_post'=> 'Post Help For Test Field 2 for Group 6'
                         );
        $customField2 = & crm_create_custom_field($customGroup, $paramsF2);
        $this->assertIsA($customField2, 'CRM_Core_BAO_CustomField');
        $this->_customFieldPC2 = $customField2;
        
        $paramsOption1 = array('value' => 1,
                               'label' => 'Option 1 Field 2 Group 6',
                               'weight' => 1,
                               'is_active' => 1
                               );
        $option1 =& crm_create_option_value($paramsOption1, $customField2); 
        $this->assertIsA($option1, 'CRM_Core_BAO_CustomOption');
        
        $paramsOption2 = array('value' => 2,
                               'label' => 'Option 2 Field 2 Group 6',
                               'weight' => 4,
                               'is_active' => 1
                               );
        $option2 =& crm_create_option_value($paramsOption2, $customField2); 
        $this->assertIsA($option2, 'CRM_Core_BAO_CustomOption');
        $this->_customGroupPC = $customGroup;
    }
    
    function testCreateCustomGroupFieldOptionM()
    {
        $params = array('domain_id' => 1,
                        'title' => 'New Group 7 For Creating Custom Field',
                        'name'  => 'new_group_7',
                        'weight' => 3,
                        'style' => 'Inline',
                        'collapse_display' => 0,
                        'is_active' => 1,
                        'help_pre' => 'This is Pre Help For New Group 7.',
                        'help_post' => 'This is for extending MEETING type of Class.'
                        );
        $class_name = 'Meeting';
        $customGroup =& crm_create_custom_group($class_name, $params);
        $this->assertIsA($customGroup, 'CRM_Core_BAO_CustomGroup');
        
        $paramsF1 = array('label' => 'Test Field 1 for Group 7',
                          'name'  => 'test_field_1_group_7',
                          'data_type' => 'String',
                          'html_type' => 'Text',
                          'is_searchable' => '1', 
                          'is_active' => 1,
                          'help_pre' => 'Pre Help For Test Field 1 for Group 7',
                          'help_post'=> 'Post Help For Test Field 1 for Group 7',
                          'weight' => 3
                          );
        $customField1 = & crm_create_custom_field($customGroup, $paramsF1);
        $this->assertIsA($customField1, 'CRM_Core_BAO_CustomField');
        $this->_customFieldM1 = $customField1;
        
        $paramsF2 = array('label' => 'Test Field 2 for Group 7',
                         'name'  => 'test_field_2_group_7',
                         'weight' => 3,
                         'data_type' => 'Int',
                         'html_type' => 'Radio',
                         'is_searchable' => 1, 
                         'is_active' => 1,
                         'options_per_line' => 2,
                         'help_pre' => 'Pre Help For Test Field 2 for Group 7',
                         'help_post'=> 'Post Help For Test Field 2 for Group 7'
                         );
        $customField2 = & crm_create_custom_field($customGroup, $paramsF2);
        $this->assertIsA($customField2, 'CRM_Core_BAO_CustomField');
        $this->_customFieldM2 = $customField2;
        
        $paramsOption1 = array('value' => 1,
                               'label' => 'Option 1 Field 2 Group 7',
                               'weight' => 1,
                               'is_active' => 1
                               );
        $option1 =& crm_create_option_value($paramsOption1, $customField2); 
        $this->assertIsA($option1, 'CRM_Core_BAO_CustomOption');
        
        $paramsOption2 = array('value' => 2,
                               'label' => 'Option 2 Field 2 Group 7',
                               'weight' => 4,
                               'is_active' => 1
                               );
        $option2 =& crm_create_option_value($paramsOption2, $customField2); 
        $this->assertIsA($option2, 'CRM_Core_BAO_CustomOption');
        $this->_customGroupM = $customGroup;
    }
    
    /**************************************
     * Custom Value for the Tests.
     *************************************/
    
    function testCustomValueI()
    {
        $value = array ('value' => '3');
        $customValue = crm_create_custom_value('civicrm_contact', $this->_individual->id, $this->_customFieldI, $value);
        $this->assertIsA($customValue, 'CRM_Core_BAO_CustomValue');
    }
      
    function testCustomValueC()
    {
        $value1 = array('value' => 'This is Test String');
        $customValue1 = crm_create_custom_value('civicrm_contact', $this->_houseHold->id, $this->_customFieldC1, $value1);
        $this->assertIsA($customValue1, 'CRM_Core_BAO_CustomValue');
        
        $value2 = array('value' => 'hello 5, hello 2, hello 3');
        $customValue2 = crm_create_custom_value('civicrm_contact', $this->_organization->id, $this->_customFieldC2, $value2, ',');
        $this->assertIsA($customValue2, 'CRM_Core_BAO_CustomValue');
    }
    
    function testCustomValueH()
    {
        $value = array('value' => 'This is demo Memo for Household');
        $customValue = crm_create_custom_value('civicrm_contact', $this->_houseHold->id, $this->_customFieldH, $value);
        $this->assertIsA($customValue, 'CRM_Core_BAO_CustomValue');
    }
    
    function testCustomValueO()
    {
        $value = array('value' => 'This is demo Memo for Organization');
        $customValue = crm_create_custom_value('civicrm_contact', $this->_organization->id, $this->_customFieldO, $value);
        $this->assertIsA($customValue, 'CRM_Core_BAO_CustomValue');
    }
    
    function testCustomValueO1()
    {
        $value = array('value' => 'This is demo Memo for Organization01');
        $customValue = crm_create_custom_value('civicrm_contact', $this->_organization02->id, $this->_customFieldO, $value);
        $this->assertIsA($customValue, 'CRM_Core_BAO_CustomValue');
    }
    /*
    function testWrongCustomValueI()
    {
        $value = array('value' => '12');
        $customValue = crm_create_custom_value('civicrm_contact', $this->_individual->id, $this->_customFieldI, $value, ',');
        $this->assertIsA($customValue, 'CRM_Core_BAO_CustomValue');
    }
    */
 

    function testDeleteIndividual() 
    {
       
        $val =& crm_delete_contact(& $this->_individual);
        $this->assertNull($val);
    }
    
    function testDeleteHousehold() 
    {
        $val =& crm_delete_contact(& $this->_houseHold);
        $this->assertNull($val);
    }
    
    function testDeleteOrganization() 
    {
        $val =& crm_delete_contact(& $this->_organization);
        $this->assertNull($val);
        
        $val =& crm_delete_contact(& $this->_organization02);
        $this->assertNull($val);
    }
    
    function testDeleteCustomGroup()
    {
       
        $val =&  crm_delete_custom_field($this->_customFieldC1->id);
        $this->assertNull($val);
        $val =&  crm_delete_custom_field($this->_customFieldC2->id);
        $this->assertNull($val);
        $val =&  crm_delete_custom_field($this->_customFieldI->id);
        $this->assertNull($val);
        $val =&  crm_delete_custom_field($this->_customFieldH->id);
        $this->assertNull($val);
        $val =&  crm_delete_custom_field($this->_customFieldO->id);
        $this->assertNull($val);
        $val =&  crm_delete_custom_field($this->_customFieldG1->id);
        $this->assertNull($val);
        $val =&  crm_delete_custom_field($this->_customFieldG2->id);
        $this->assertNull($val);
        $val =&  crm_delete_custom_field($this->_customFieldPC1->id);
        $this->assertNull($val);
        $val =&  crm_delete_custom_field($this->_customFieldPC2->id);
        $this->assertNull($val);
        $val =&  crm_delete_custom_field($this->_customFieldM1->id);
        $this->assertNull($val);
        $val =&  crm_delete_custom_field($this->_customFieldM2->id);
        
        
        $val =&  crm_delete_custom_group($this->_customGroupC->id);
        $this->assertNull($val);
        $val =&  crm_delete_custom_group($this->_customGroupI->id);
        $this->assertNull($val);
        $val =&  crm_delete_custom_group($this->_customGroupH->id);
        $this->assertNull($val);
        $val =&  crm_delete_custom_group($this->_customGroupO->id);
        $this->assertNull($val);
        $val =&  crm_delete_custom_group($this->_customGroupG->id);
        $this->assertNull($val);
        $val =&  crm_delete_custom_group($this->_customGroupPC->id);
        $this->assertNull($val);
        $val =&  crm_delete_custom_group($this->_customGroupM->id);
        $this->assertNull($val);
    }
    
}

