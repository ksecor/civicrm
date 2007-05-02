<?php

require_once 'api/crm.php';

class TestOfCreateCustomGroupAPI extends UnitTestCase 
{
    
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }

    function testCreateNoParam()
    {
        $params = array();
        $class_name = 'Contact';
        $customGroup =& crm_create_custom_group($class_name, $params);
        $this->assertIsA($customGroup, 'CRM_Core_Error');
    }
    
    function testCreateCustomGroup()
    {
        $params = array('domain_id' => 1,
                        'title' => 'Test Group 1 For Creating Custom Group',
                        'name'  => 'test_group_1',
                        'weight' => 4,
                        'collapse_display' => 1,
                        'style' => 'Tab',
                        'help_pre' => 'This is Pre Help For Test Group 1',
                        'help_post' => 'This is Post Help For Test Group 1'
                        );
        $class_name = 'Individual';
        $customGroup =& crm_create_custom_group($class_name, $params);
        $this->assertIsA($customGroup, 'CRM_Core_BAO_CustomGroup');
        $this->customGroup1 = $customGroup;
    } 
    
    function testCustomGroupNoTitle()
    {
        $params = array('domain_id' => 1,
                        'weight' => 5, 
                        'collapse_display' => 1,
                        'style' => 'Tab',
                        'help_pre' => 'This is Pre Help For Test Group 2',
                        'help_post' => 'This is Post Help For Test Group 2'
                        );
        $class_name = 'Contact';
        $customGroup =& crm_create_custom_group($class_name, $params);
        $this->assertIsA($customGroup, 'CRM_Core_BAO_CustomGroup');
        $this->customGroup2 = $customGroup;
    }
    
    function testCustomGroupErrorNoWeight()
    {
        $params = array('domain_id' => 1,
                        'title' => 'Test Group 3 For Creating Custom Group',
                        'name'  => 'test_group_3',
                        'collapse_display' => 1,
                        'style' => 'Tab',
                        'help_pre' => 'This is Pre Help For Test Group 3',
                        'help_post' => 'This is Post Help For Test Group 3'
                        );
        $class_name = 'Household';
        $customGroup =& crm_create_custom_group($class_name, $params);
        $this->assertIsA($customGroup, 'CRM_Core_Error');
    }
    
    function testCustomGroupErrorNoDomain()
    {
        $params = array('title' => 'Test Group 4 For Creating Custom Group',
                        'name'  => 'test_group_4',
                        'collapse_display' => 1,
                        'weight' => 5,
                        'style' => 'Tab',
                        'help_pre' => 'This is Pre Help For Test Group 4',
                        'help_post' => 'This is Post Help For Test Group 4'
                        );
        $class_name = 'Organization';
        $customGroup =& crm_create_custom_group($class_name, $params);
        $this->assertIsA($customGroup, 'CRM_Core_Error');
    }
    
    function testCreateCustomGroupForContributionError()
    {
        $params = array('domain_id' => 1,
                        'title' => 'Test Group 5 For Creating Custom Group',
                        'name'  => 'test_group_5',
                        'weight' => 6,
                        'collapse_display' => 1,
                        'style' => 'Tab',
                        'help_pre' => 'This is Pre Help For Test Group 5',
                        'help_post' => 'This is Post Help For Test Group 5'
                        );
        $class_name = 'Contribution';
        $customGroup =& crm_create_custom_group($class_name, $params);
        $this->assertIsA($customGroup, 'CRM_Core_Error');
    }
    
    function testCreateCustomGroupForContribution()
    {
        $params = array('domain_id' => 1,
                        'title' => 'Test Group 6 For Creating Custom Group',
                        'name'  => 'test_group_6',
                        'weight' => 6,
                        'collapse_display' => 1,
                        'style' => 'Inline',
                        'help_pre' => 'This is Pre Help For Test Group 6',
                        'help_post' => 'This is Post Help For Test Group 6'
                        );
        $class_name = 'Contribution';
        $customGroup =& crm_create_custom_group($class_name, $params);
        $this->assertIsA($customGroup, 'CRM_Core_BAO_CustomGroup');
        $this->customGroup3 = $customGroup;
    }
    
    function testCreateCustomGroupForGroupError()
    {
        $params = array('domain_id' => 1,
                        'title' => 'Test Group 7 For Creating Custom Group',
                        'name'  => 'test_group_7',
                        'weight' => 7,
                        'collapse_display' => 1,
                        'style' => 'Tab',
                        'help_pre' => 'This is Pre Help For Test Group 7',
                        'help_post' => 'This is Post Help For Test Group 7'
                        );
        $class_name = 'Group';
        $customGroup =& crm_create_custom_group($class_name, $params);
        $this->assertIsA($customGroup, 'CRM_Core_Error');
    }
    
    function testCreateCustomGroupForGroup()
    {
        $params = array('domain_id' => 1,
                        'title' => 'Test Group 8 For Creating Custom Group',
                        'name'  => 'test_group_8',
                        'weight' => 7,
                        'collapse_display' => 1,
                        'style' => 'Inline',
                        'help_pre' => 'This is Pre Help For Test Group 8',
                        'help_post' => 'This is Post Help For Test Group 8'
                        );
        $class_name = 'Group';
        $customGroup =& crm_create_custom_group($class_name, $params);
        $this->assertIsA($customGroup, 'CRM_Core_BAO_CustomGroup');
        $this->customGroup4 = $customGroup;
    }
    
    function testCreateCustomGroupForActivityError()
    {
        $params = array('domain_id' => 1,
                        'title' => 'Test Group 9 For Creating Custom Group',
                        'name'  => 'test_group_9',
                        'weight' => 8,
                        'collapse_display' => 1,
                        'style' => 'Tab',
                        'help_pre' => 'This is Pre Help For Test Group 9',
                        'help_post' => 'This is Post Help For Test Group 9'
                        );
        $class_name = 'Activity';
        $customGroup =& crm_create_custom_group($class_name, $params);
        $this->assertIsA($customGroup, 'CRM_Core_Error');
    }
    
    function testCreateCustomGroupForActivity()
    {
        $params = array('domain_id' => 1,
                        'title' => 'Test Group 10 For Creating Custom Group',
                        'name'  => 'test_group_10',
                        'weight' => 8,
                        'collapse_display' => 1,
                        'style' => 'Inline',
                        'help_pre' => 'This is Pre Help For Test Group 10',
                        'help_post' => 'This is Post Help For Test Group 10'
                        );
        $class_name = 'Activity';
        $customGroup =& crm_create_custom_group($class_name, $params);
        $this->assertIsA($customGroup, 'CRM_Core_BAO_CustomGroup');
        $this->customGroup5 = $customGroup;
    }
    
    function testCreateCustomGroupForPhonecallError()
    {
        $params = array('domain_id' => 1,
                        'title' => 'Test Group 11 For Creating Custom Group',
                        'name'  => 'test_group_11',
                        'weight' => 9,
                        'collapse_display' => 1,
                        'style' => 'Tab',
                        'help_pre' => 'This is Pre Help For Test Group 11',
                        'help_post' => 'This is Post Help For Test Group 11'
                        );
        $class_name = 'Phonecall';
        $customGroup =& crm_create_custom_group($class_name, $params);
        $this->assertIsA($customGroup, 'CRM_Core_Error');
    }
    
    function testCreateCustomGroupForPhonecall()
    {
        $params = array('domain_id' => 1,
                        'title' => 'Test Group 12 For Creating Custom Group',
                        'name'  => 'test_group_12',
                        'weight' => 9,
                        'collapse_display' => 1,
                        'style' => 'Inline',
                        'help_pre' => 'This is Pre Help For Test Group 12',
                        'help_post' => 'This is Post Help For Test Group 12'
                        );
        $class_name = 'Phonecall';
        $customGroup =& crm_create_custom_group($class_name, $params);
        $this->assertIsA($customGroup, 'CRM_Core_BAO_CustomGroup');
        $this->customGroup6 = $customGroup;
    }
    
    function testCreateCustomGroupForMeetingError()
    {
        $params = array('domain_id' => 1,
                        'title' => 'Test Group 13 For Creating Custom Group',
                        'name'  => 'test_group_13',
                        'weight' => 9,
                        'collapse_display' => 1,
                        'style' => 'Tab',
                        'help_pre' => 'This is Pre Help For Test Group 13',
                        'help_post' => 'This is Post Help For Test Group 13'
                        );
        $class_name = 'Meeting';
        $customGroup =& crm_create_custom_group($class_name, $params);
        $this->assertIsA($customGroup, 'CRM_Core_Error');
    }
    
    function testCreateCustomGroupForMeeting()
    {
        $params = array('domain_id' => 1,
                        'title' => 'Test Group 14 For Creating Custom Group',
                        'name'  => 'test_group_14',
                        'weight' => 9,
                        'collapse_display' => 1,
                        'style' => 'Inline',
                        'help_pre' => 'This is Pre Help For Test Group 14',
                        'help_post' => 'This is Post Help For Test Group 14'
                        );
        $class_name = 'Meeting';
        $customGroup =& crm_create_custom_group($class_name, $params);
        $this->assertIsA($customGroup, 'CRM_Core_BAO_CustomGroup');
        $this->customGroup7 = $customGroup;
    }
    
    function testDeleteCustomGroup()
    {
        $val =&   crm_delete_custom_group($this->customGroup1->id);
        $this->assertNull($val);
        $val =&   crm_delete_custom_group($this->customGroup2->id);
        $this->assertNull($val);
        $val =&   crm_delete_custom_group($this->customGroup3->id);
        $this->assertNull($val);
        $val =&   crm_delete_custom_group($this->customGroup4->id);
        $this->assertNull($val);
        $val =&   crm_delete_custom_group($this->customGroup5->id);
        $this->assertNull($val);
        $val =&   crm_delete_custom_group($this->customGroup6->id);
        $this->assertNull($val);
        $val =&   crm_delete_custom_group($this->customGroup7->id);
        $this->assertNull($val);
    }
}
?>
