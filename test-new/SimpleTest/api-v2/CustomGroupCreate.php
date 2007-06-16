<?php

require_once 'api/v2/CustomGroup.php';

class TestOfCustomGroupCreateAPIV2 extends CiviUnitTestCase 
{
    
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }
    
    function testCustomGroupCreateNoParam()
    {
        $params = array( );
        $customGroup =& civicrm_custom_group_create($params); 
        $this->assertEqual($customGroup['is_error'],1);
        $this->assertEqual($customGroup['error_message'],'params is not an array');
    }
   
    function testCustomGroupCreateNoClassName()
    {
        $params = array( 'domain_id'        => 1,
                         'title'            => 'Test Group 1 For Creating Custom Group',
                         'name'             => 'test_group_1',
                         'weight'           => 4,
                         'collapse_display' => 1,
                         'style'            => 'Tab',
                         'help_pre'         => 'This is Pre Help For Test Group 1',
                         'help_post'        => 'This is Post Help For Test Group 1',
                         'is_active'        => 1
                         );
        $customGroup =& civicrm_custom_group_create($params);
        $this->assertEqual($customGroup['error_message'],'class_name is not set');
        $this->assertEqual($customGroup['is_error'],1);
    }
   
    function testCustomGroupCreate()
    {
        $params = array( 'domain_id'        => 1,
                         'title'            => 'Test Group 1 For Creating Custom Group',
                         'name'             => 'test_group_1',
                         'class_name'       => 'Individual',
                         'weight'           => 4,
                         'collapse_display' => 1,
                         'style'            => 'Tab',
                         'help_pre'         => 'This is Pre Help For Test Group 1',
                         'help_post'        => 'This is Post Help For Test Group 1',
                         'is_active'        => 1
                         );
        
        $customGroup =& civicrm_custom_group_create($params);  
        $this->assertEqual($customGroup['is_error'],0);
        $this->assertNotNull($customGroup['custom_group_id']);
        $this->customGroupDelete($customGroup['custom_group_id']);
    } 
     
    function testCustomGroupCreateNoTitle()
    {
        $params = array('domain_id'        => 1,
                        'class_name'       => 'Contact',
                        'weight'           => 5, 
                        'collapse_display' => 1,
                        'style'            => 'Tab',
                        'help_pre'         => 'This is Pre Help For Test Group 2',
                        'help_post'        => 'This is Post Help For Test Group 2'
                        );
       
        $customGroup =& civicrm_custom_group_create($params); 
        $this->assertEqual($customGroup['is_error'],0);
        $this->assertNotNull($customGroup['custom_group_id']);
        $this->customGroupDelete($customGroup['custom_group_id']);
    }
    
    function testCustomGroupCreateHouseholdNoWeight()
    { 
        $params = array('domain_id'        => 1,
                        'title'            => 'Test Group 3 For Creating Custom Group',
                        'name'             => 'test_group_3',
                        'class_name'       =>  'Household',
                        'collapse_display' => 1,
                        'style'            => 'Tab',
                        'help_pre'         => 'This is Pre Help For Test Group 3',
                        'help_post'        => 'This is Post Help For Test Group 3'
                        );
        
        $customGroup =& civicrm_custom_group_create($params);  
        $this->assertEqual($customGroup['is_error'],0);
        $this->assertNotNull($customGroup['custom_group_id']);
        $this->customGroupDelete($customGroup['custom_group_id']);
    }
    
    function testCustomGroupCreateContribution()
    {
        $params = array('domain_id'        => 1,
                        'title'            => 'Test Group 6 For Creating Custom Group',
                        'name'             => 'test_group_6',
                        'class_name'       => 'Contribution',
                        'weight'           => 6,
                        'collapse_display' => 1,
                        'style'            => 'Inline',
                        'help_pre'         => 'This is Pre Help For Test Group 6',
                        'help_post'        => 'This is Post Help For Test Group 6'
                        );

        $customGroup =& civicrm_custom_group_create($params); 
        $this->assertEqual($customGroup['is_error'], 0);
        $this->assertNotNull($customGroup['custom_group_id']);
        $this->customGroupDelete($customGroup['custom_group_id']);
    }
    
    function testCustomGroupCreateGroup()
    {
        $params = array('domain_id'        => 1,
                        'title'            => 'Test Group 8 For Creating Custom Group',
                        'name'             => 'test_group_8',
                        'class_name'       => 'Group',
                        'weight'           => 7,
                        'collapse_display' => 1,
                        'style'            => 'Inline',
                        'help_pre'         => 'This is Pre Help For Test Group 8',
                        'help_post'        => 'This is Post Help For Test Group 8'
                        );
        
        $customGroup =& civicrm_custom_group_create($params); 
        $this->assertEqual($customGroup['is_error'], 0);
        $this->assertNotNull($customGroup['custom_group_id']);
        $this->customGroupDelete($customGroup['custom_group_id']);
    }
    
    function testCustomGroupCreateActivity()
    {
        $params = array('domain_id'        => 1,
                        'title'            => 'Test Group 10 For Creating Custom Group',
                        'name'             => 'test_group_10',
                        'class_name'       => 'Activity',
                        'weight'           => 8,
                        'collapse_display' => 1,
                        'style'            => 'Inline',
                        'help_pre'         => 'This is Pre Help For Test Group 10',
                        'help_post'        => 'This is Post Help For Test Group 10'
                        );
      
        $customGroup =& civicrm_custom_group_create($params); 
        $this->assertEqual($customGroup['is_error'], 0);
        $this->assertNotNull($customGroup['custom_group_id']);
        $this->customGroupDelete($customGroup['custom_group_id']);
    }
    
    function testCustomGroupCreatePhonecall()
    {
        $params = array('domain_id'        => 1,
                        'title'            => 'Test Group 11 For Creating Custom Group',
                        'name'             => 'test_group_11',
                        'class_name'       => 'Phonecall',
                        'weight'           => 9,
                        'collapse_display' => 1,
                        'style'            => 'Inline',
                        'help_pre'         => 'This is Pre Help For Test Group 11',
                        'help_post'        => 'This is Post Help For Test Group 11'
                        );
        
        $customGroup =& civicrm_custom_group_create($params);  
        $this->assertEqual($customGroup['is_error'], 0); 
        $this->assertNotNull($customGroup['custom_group_id']);
        $this->customGroupDelete($customGroup['custom_group_id']);
    }
    
    function testCustomGroupCreateMeeting()
    {
        $params = array('domain_id'        => 1,
                        'title'            => 'Test Group 13 For Creating Custom Group',
                        'name'             => 'test_group_13',
                        'class_name'       => 'Meeting',
                        'weight'           => 9,
                        'collapse_display' => 1,
                        'style'            => 'Inline',
                        'help_pre'         => 'This is Pre Help For Test Group 13',
                        'help_post'        => 'This is Post Help For Test Group 13'
                        );

        $customGroup =& civicrm_custom_group_create($params);  
        $this->assertEqual($customGroup['is_error'], 0); 
        $this->assertNotNull($customGroup['custom_group_id']);
        $this->customGroupDelete($customGroup['custom_group_id']);
    }
    
}
?>
