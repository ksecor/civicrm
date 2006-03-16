<?php

require_once 'api/crm.php';

class TestOfUpdateCustomGroupAPI extends UnitTestCase 
{
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }
    
    function testCreateCustomGroup()
    {
        $params = array('domain_id' => 1,
                        'title' => 'Test Group 1 For Creating Custom Group',
                        'name'  => 'test_group_1',
                        'weight' => 4,
                        'collapse_display' => 1,
                        'style' => 'Inline',
                        'help_pre' => 'This is Pre Help For Test Group 1',
                        'help_post' => 'This is Post Help For Test Group 1'
                        );
        $class_name = 'Activity';
        $this->customGroup =& crm_create_custom_group($class_name, $params);
        $this->assertIsA($this->customGroup, 'CRM_Core_BAO_CustomGroup');
    }
    
    function testUpdateCustomGroupErrorForCustomGroup()
    {
        $params = array('help_pre' => 'This is Pre Help For Test Group 1..Edit',
                        'help_post' => 'This is Post Help For Test Group 1..Edit'
                        );
        $updatedCustomGroup =& crm_update_custom_group($params, $otherCustomGroup);
        $this->assertIsA($updatedCustomGroup, 'CRM_Core_Error');
    }
    
    function testUpdateCustomGroupErrorForExtends()
    {
        $params = array(
                        'extends' => 'Household',
                        'help_pre' => 'This is Pre Help For Test Group 1..Edit',
                        'help_post' => 'This is Post Help For Test Group 1..Edit'
                        );
        $updatedCustomGroup =& crm_update_custom_group($params, $this->customGroup);
        $this->assertIsA($updatedCustomGroup, 'CRM_Core_Error');
    }
    
    function testUpdateCustomGroupErrorForParams()
    {
        $params;
        $updatedCustomGroup =& crm_update_custom_group($params, $this->customGroup);
        $this->assertIsA($updatedCustomGroup, 'CRM_Core_Error');
    }
    
    function testUpdateCustomGroup()
    {
        $params = array(
                        'help_pre' => 'This is Pre Help For Test Group 1..Edit',
                        'help_post' => 'This is Post Help For Test Group 1..Edit'
                        );
        $updatedCustomGroup =& crm_update_custom_group($params, $this->customGroup);
        $this->assertIsA($updatedCustomGroup, 'CRM_Core_BAO_CustomGroup');
    }
    
    function testUpdateCustomGroupWithDomainID()
    {
        $params = array(
                        'domain_id' => 1,
                        'weight' => 4,
                        'collapse_display' => 0,
                        'help_pre' => 'This is Pre Help For Test Group 2..Edit',
                        'help_post' => 'This is Post Help For Test Group 2..Edit'
                        );
        $updatedCustomGroup =& crm_update_custom_group($params, $this->customGroup);
        $this->assertIsA($updatedCustomGroup, 'CRM_Core_BAO_CustomGroup');
    }
}
?>