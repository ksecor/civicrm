<?php

require_once 'api/crm.php';

class TestOfCRM2191 extends UnitTestCase 
{
    protected $_houseHold     = array();
    protected $_customFieldC1 = array();
    
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }
    
    /**************************************
     * Create Contacts for the Tests
     *************************************/

    function testCreateHousehold() 
    {
        $params = array('household_name' => 'The abc Household');
        $contact =& crm_create_contact($params, 'Household');
        $this->assertIsA($contact, 'CRM_Contact_BAO_Contact');
        $this->assertEqual($contact->contact_type, 'Household');
        $this->_houseHold = $contact;
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
    }

    /**************************************
     * Custom Value for the Tests.
     *************************************/
    
    function testCustomValueC()
    {
        $value1       = array('value' => 'This is Test String');
        $customValue1 = 
            crm_create_custom_value('civicrm_contact', $this->_houseHold->id, $this->_customFieldC1, $value1);

        $this->assertIsA($customValue1, 'CRM_Core_BAO_CustomValue');
        
        $value2       = array('value' => 'This is Overwritten String');
        $customValue2 = 
            crm_create_custom_value('civicrm_contact', $this->_houseHold->id, $this->_customFieldC1, $value2);
        
        $this->assertIsA($customValue2, 'CRM_Core_BAO_CustomValue');
    }
}
?>