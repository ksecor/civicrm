<?php

require_once 'api/crm.php';

class TestOfCRM521 extends UnitTestCase 
{
    
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }
    
    function testCreateCustomField()
    {
        // add custom group
        $params = array('domain_id'        => 1,
                        'title'            => 'New Group 1',
                        'weight'           => 3,
                        'collapse_display' => 0,
                        'help_pre'         => 'This is Pre Help For New Group 1.',
                        'help_post'        => 'This is for extending Organization Type of Class.',
                        'is_active'        => 1,
                        'style'            => 'Inline'
                        );
        
        $class_name = 'Organization';
        $customGroup =& crm_create_custom_group($class_name, $params);
        $this->assertIsA($customGroup, 'CRM_Core_BAO_CustomGroup');
        
        //create custom fields
        $paramsF1 = array('label'     => 'Field 1',
                          'weight'    => 3,
                          'html_type' => 'text',
                          'data_type' => 'String',
                          'is_active' => 1 
                         );
        $customField1 = & crm_create_custom_field($customGroup, $paramsF1);
        $this->assertIsA($customGroup, 'CRM_Core_BAO_CustomGroup');

        $paramsF2 = array('label'      => 'Field 2',
                          'weight'     => 4,
                          'html_type'  => 'text',
                          'data_type'  => 'String',
                          'is_active'  => 1 
                         );
        $customField2 = & crm_create_custom_field($customGroup, $paramsF2);
        $this->assertIsA($customGroup, 'CRM_Core_BAO_CustomGroup');

        //create organization
        $params = array('organization_name' => 'Test Organization');
        $contact =& crm_create_contact($params, 'Organization');
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->contact_type, 'Organization');
        
        //adding custom values for the organization
        $customField1Value = array('value' => 'this is the value for field 1');
        $customValue1 = crm_create_custom_value('civicrm_contact', $contact->id, $customField1, $customField1Value);
        $this->assertIsA($customValue1, 'CRM_Core_BAO_CustomValue');

        $customField2Value = array('value' => 'this is the value for field 2');
        $customValue2 = crm_create_custom_value('civicrm_contact', $contact->id, $customField2, $customField2Value);
        $this->assertIsA($customValue2, 'CRM_Core_BAO_CustomValue');
    }
}
?>