<?php

/**
 *  Include class definitions
 */
require_once 'tests/phpunit/CiviTest/CiviUnitTestCase.php';
require_once 'api/v2/CustomGroup.php';

/**
 *  Test APIv2 civicrm_create_custom_group
 *
 *  @package   CiviCRM
 */
class api_v2_CustomGroupTest extends CiviUnitTestCase
{
    
    function get_info( )
    {
        return array(
                     'name'        => 'Custom Group Create',
                     'description' => 'Test all Custom Group Create API methods.',
                     'group'       => 'CiviCRM API Tests',
                     );
    }
    
    function setUp() 
    {
        parent::setUp();
    }
    
    function tearDown() 
    {
    }
     
    function testCustomGroupCreateNoParam()
    {
        $params = array( );
        $customGroup =& civicrm_custom_group_create($params);
        $this->assertEquals($customGroup['is_error'], 1); 
        $this->assertEquals($customGroup['error_message'],'Params must include either \'class_name\' (string) or \'extends\' (array).');
    }
    
    function testCustomGroupCreateNoExtends()
    {
        $params = array( 'domain_id'        => 1,
                         'title'            => 'Test_Group_1',
                         'name'             => 'test_group_1',
                         'weight'           => 4,
                         'collapse_display' => 1,
                         'style'            => 'Tab',
                         'help_pre'         => 'This is Pre Help For Test Group 1',
                         'help_post'        => 'This is Post Help For Test Group 1',
                         'is_active'        => 1
                         );
        
        $customGroup =& civicrm_custom_group_create($params);
        $this->assertEquals($customGroup['error_message'],'Params must include either \'class_name\' (string) or \'extends\' (array).');
        $this->assertEquals($customGroup['is_error'],1);
    }
    
    function testCustomGroupCreate()
    {
        $params = array( 'title'            => 'Test_Group_1',
                         'name'             => 'test_group_1',
                         'extends'          => array('Individual'),
                         'weight'           => 4,
                         'collapse_display' => 1,
                         'style'            => 'Inline',
                         'help_pre'         => 'This is Pre Help For Test Group 1',
                         'help_post'        => 'This is Post Help For Test Group 1',
                         'is_active'        => 1
                         );
        
        $customGroup =& civicrm_custom_group_create($params);
        $this->assertEquals($customGroup['is_error'],0);
        $this->assertNotNull($customGroup['id']);
        $this->customGroupDelete($customGroup['id']);
    } 
    
    function testCustomGroupCreateNoTitle()
    {
        $params = array('extends'          => array('Contact'),
                        'weight'           => 5, 
                        'collapse_display' => 1,
                        'style'            => 'Tab',
                        'help_pre'         => 'This is Pre Help For Test Group 2',
                        'help_post'        => 'This is Post Help For Test Group 2'
                        );
        
        $customGroup =& civicrm_custom_group_create($params);
        $this->assertEquals($customGroup['error_message'],'Title parameter is required.');
        $this->assertEquals($customGroup['is_error'],1);
	} 
    
    function testCustomGroupCreateHouseholdNoWeight()
    { 
        $params = array('title'            => 'Test_Group_3',
                        'name'             => 'test_group_3',
                        'extends'          => array('Household'),
                        'collapse_display' => 1,
                        'style'            => 'Tab',
                        'help_pre'         => 'This is Pre Help For Test Group 3',
                        'help_post'        => 'This is Post Help For Test Group 3',
                        'is_active'        => 1
                        );
        
        $customGroup =& civicrm_custom_group_create($params);
        $this->assertEquals($customGroup['is_error'],0);
        $this->assertNotNull($customGroup['id']);
        $this->customGroupDelete($customGroup['id']);
    }
    
    function testCustomGroupCreateContributionDonation()
    {
        $params = array('title'            => 'Test_Group_6',
                        'name'             => 'test_group_6',
                        'extends'          => array( 'Contribution', 1 ),
                        'weight'           => 6,
                        'collapse_display' => 1,
                        'style'            => 'Inline',
                        'help_pre'         => 'This is Pre Help For Test Group 6',
                        'help_post'        => 'This is Post Help For Test Group 6',
                        'is_active'        => 1 
                        );
        
        $customGroup =& civicrm_custom_group_create($params); 
        $this->assertEquals($customGroup['is_error'], 0);
        $this->assertNotNull($customGroup['id']);
        $this->customGroupDelete($customGroup['id']);
    }
    
    function testCustomGroupCreateGroup()
    {
        $params = array('domain_id'        => 1,
                        'title'            => 'Test_Group_8',
                        'name'             => 'test_group_8',
                        'extends'          => array('Group'),
                        'weight'           => 7,
                        'collapse_display' => 1,
                        'is_active'        => 1,
                        'style'            => 'Inline',
                        'help_pre'         => 'This is Pre Help For Test Group 8',
                        'help_post'        => 'This is Post Help For Test Group 8'
                        );
        
        $customGroup =& civicrm_custom_group_create($params); 
        $this->assertEquals($customGroup['is_error'], 0);
        $this->assertNotNull($customGroup['id']);
        $this->customGroupDelete($customGroup['id']);
    }
    
    function testCustomGroupCreateActivityMeeting()
    {
        $params = array(
                        'title'            => 'Test_Group_10',
                        'name'             => 'test_group_10',
                        'extends'          => array('Activity', 1),
                        'weight'           => 8,
                        'collapse_display' => 1,
                        'style'            => 'Inline',
                        'help_pre'         => 'This is Pre Help For Test Group 10',
                        'help_post'        => 'This is Post Help For Test Group 10'
                        );
        
        $customGroup =& civicrm_custom_group_create($params); 
        $this->assertEquals($customGroup['is_error'], 0);
        $this->assertNotNull($customGroup['id']);
        $this->customGroupDelete($customGroup['id']);
    }


    function testCustomGroupDeleteWithoutGroupID( )
    {
        $params = array( );
        $customGroup =& civicrm_custom_group_delete($params);
        $this->assertEquals($customGroup['is_error'], 1);
        $this->assertEquals($customGroup['error_message'],'Invalid or no value for Custom group ID');
    }    
    
    function testCustomGroupDelete( )
    {
        $customGroup = $this->customGroupCreate('Individual', 'test_group'); 
        $params = array('id' => $customGroup['id']);                         
        $customGroup =& civicrm_custom_group_delete($params);  
        $this->assertEquals($customGroup['is_error'], 0);
    } 

    function testCustomFieldCreateNoParam()
    {
        $params = array();
        $customField =& civicrm_custom_field_create($params);
        $this->assertEquals($customField['is_error'], 1);
        $this->assertEquals( $customField['error_message'],'Missing Required field :custom_group_id' );
    }
    
    function testCustomFieldCreateWithoutGroupID( )
    {
        $fieldParams = array('name'           => 'test_textfield1',
                             'label'          => 'Name',
                             'html_type'      => 'Text',
                             'data_type'      => 'String',
                             'default_value'  => 'abc',
                             'weight'         => 4,
                             'is_required'    => 1,
                             'is_searchable'  => 0,
                             'is_active'      => 1
                             );
               
        $customField =& civicrm_custom_field_create($fieldParams);     
        $this->assertEquals($customField['is_error'], 1);
        $this->assertEquals( $customField['error_message'],'Missing Required field :custom_group_id' );
    }    
     
    function testCustomTextFieldCreate( )
    {
        $customGroup = $this->customGroupCreate('Individual','text_test_group');
        $params = array('custom_group_id' => $customGroup['id'],
                        'name'            => 'test_textfield2',
                        'label'           => 'Name1',
                        'html_type'       => 'Text',
                        'data_type'       => 'String',
                        'default_value'   => 'abc',
                        'weight'          => 4,
                        'is_required'     => 1,
                        'is_searchable'   => 0,
                        'is_active'       => 1
                        );
        
        $customField =& civicrm_custom_field_create($params);
        $this->assertEquals($customField['is_error'],0);
        $this->assertNotNull($customField['result']['customFieldId']);
        $this->customFieldDelete($customField['result']['customFieldId']); 
        $this->customGroupDelete($customGroup['id']); 
    } 

    function testCustomDateFieldCreate( )
    {
        $customGroup = $this->customGroupCreate('Individual','date_test_group');
        $params = array('custom_group_id' => $customGroup['id'],
                        'name'            => 'test_date',
                        'label'           => 'test_date',
                        'html_type'       => 'Select Date',
                        'data_type'       => 'Date',
                        'default_value'   => '20071212',
                        'weight'          => 4,
                        'is_required'     => 1,
                        'is_searchable'   => 0,
                        'is_active'       => 1
                        );
        $customField =& civicrm_custom_field_create($params); 
        $this->assertEquals($customField['is_error'],0);
        $this->assertNotNull($customField['result']['customFieldId']);
        $this->customFieldDelete($customField['result']['customFieldId']);
        $this->customGroupDelete($customGroup['id']); 
    } 
    
    function testCustomCountryFieldCreate( )
    {
        $customGroup = $this->customGroupCreate('Individual','Country_test_group');
        $params = array('custom_group_id' => $customGroup['id'],
                        'name'            => 'test_country',
                        'label'           => 'test_country',
                        'html_type'       => 'Select Country',
                        'data_type'       => 'Country',
                        'default_value'   => '1228',
                        'weight'          => 4,
                        'is_required'     => 1,
                        'is_searchable'   => 0,
                        'is_active'       => 1
                        );
               
        $customField =& civicrm_custom_field_create($params);  
        $this->assertEquals($customField['is_error'],0);
        $this->assertNotNull($customField['result']['customFieldId']);
        $this->customFieldDelete($customField['result']['customFieldId']);
        $this->customGroupDelete($customGroup['id']); 
    }
    
    function testCustomNoteFieldCreate( )
    {
        $customGroup = $this->customGroupCreate('Individual','Country2_test_group');
        $params = array('custom_group_id' => $customGroup['id'],
                        'name'            => 'test_note',
                        'label'           => 'test_note',
                        'html_type'       => 'TextArea',
                        'data_type'       => 'Memo',
                        'default_value'   => 'Hello',
                        'weight'          => 4,
                        'is_required'     => 1,
                        'is_searchable'   => 0,
                        'is_active'       => 1
                        );
        
        $customField =& civicrm_custom_field_create($params);  
        $this->assertEquals($customField['is_error'],0);
        $this->assertNotNull($customField['result']['customFieldId']);
        $this->customFieldDelete($customField['result']['customFieldId']);
        $this->customGroupDelete($customGroup['id']); 
    } 
    
    function testCustomFieldOptionValueCreate( )
    {
        $customGroup = $this->customGroupCreate('Contact', 'select_test_group');
        $params = array ('custom_group_id' => 1,
                         'label'           => 'Country',
                         'html_type'       => 'Select',
                         'data_type'       => 'String',
                         'weight'          => 4,
                         'is_required'     => 1,
                         'is_searchable'   => 0,
                         'is_active'       => 1,
                         'option_label'    => array( 'Label1','Label2'),
                         'option_value'    => array( 'val1', 'val2' ),
                         'option_weight'   => array( 1, 2),
                         'option_status'   => array( 1, 1),
                         );
        $this->fail( 'Needs fixing!' );      
//        $customField =& civicrm_custom_field_create($params);  
       
        $this->assertEquals($customField['is_error'],0);
        $this->assertNotNull($customField['result']['customFieldId']);
        $this->customFieldDelete($customField['result']['customFieldId']);
        $this->customGroupDelete($customGroup['id']); 
    } 
    
    function testCustomFieldSelectOptionValueCreate( )
    {
        $customGroup = $this->customGroupCreate('Contact', 'select_test_group');
        $params = array ('custom_group_id' => 1,
                         'label'           => 'PriceSelect',
                         'html_type'       => 'Select',
                         'data_type'       => 'Int',
                         'weight'          => 4,
                         'is_required'     => 1,
                         'is_searchable'   => 0,
                         'is_active'       => 1,
                         'option_label'    => array( 'Label1','Label2'),
                         'option_value'    => array( '10', '20' ),
                         'option_weight'   => array( 1, 2),
                         'option_status'   => array( 1, 1),
                         );
        $this->fail( 'Needs fixing!' );                         
//        $customField =& civicrm_custom_field_create($params);    

        $this->assertEquals($customField['is_error'],0);
        $this->assertNotNull($customField['result']['customFieldId']);
        $this->customFieldDelete($customField['result']['customFieldId']);
        $this->customGroupDelete($customGroup['id']); 
    }
    
    function testCustomFieldCheckBoxOptionValueCreate( )
    { 
        $customGroup = $this->customGroupCreate('Contact','CheckBox_test_group');
        $params = array ('custom_group_id' => $customGroup['id'],
                         'label'           => 'PriceChk',
                         'html_type'       => 'CheckBox',
                         'data_type'       => 'String',
                         'weight'          => 4,
                         'is_required'     => 1,
                         'is_searchable'   => 0,
                         'is_active'       => 1,
                         'option_label'    => array( 'Label1','Label2'),
                         'option_value'    => array( '10', '20' ),
                         'option_weight'   => array( 1, 2),
                         'option_status'   => array( 1, 1),
                         'default_checkbox_option' => array(1)
                         );
        
        $customField =& civicrm_custom_field_create($params); 

        $this->assertEquals($customField['is_error'],0);
        $this->assertNotNull($customField['result']['customFieldId']);
        $this->customFieldDelete($customField['result']['customFieldId']);
        $this->customGroupDelete($customGroup['id']); 
    }   
    
    function testCustomFieldRadioOptionValueCreate( )
    {
        $customGroup = $this->customGroupCreate('Contact', 'Radio_test_group');
        $params = array ('custom_group_id' => $customGroup['id'],
                         'label'           => 'PriceRadio',
                         'html_type'       => 'Radio',
                         'data_type'       => 'String',
                         'weight'          => 4,
                         'is_required'     => 1,
                         'is_searchable'   => 0,
                         'is_active'       => 1,
                         'option_label'    => array( 'radioLabel1','radioLabel2'),
                         'option_value'    => array( 10, 20 ),
                         'option_weight'   => array( 1, 2),
                         'option_status'   => array( 1, 1),
                         );
        
        $customField =& civicrm_custom_field_create($params); 

        $this->assertEquals($customField['is_error'],0);
        $this->assertNotNull($customField['result']['customFieldId']);
        $this->customFieldDelete($customField['result']['customFieldId']);
        $this->customGroupDelete($customGroup['id']); 
    } 
    
    function testCustomFieldMultiSelectOptionValueCreate( )
    {
        $customGroup = $this->customGroupCreate('Contact', 'MultiSelect_test_group');
        $params = array ('custom_group_id' => $customGroup['id'],
                         'label'           => 'PriceMufdlti',
                         'html_type'       => 'Multi-Select',
                         'data_type'       => 'String',
                         'weight'          => 4,
                         'is_required'     => 1,
                         'is_searchable'   => 0,
                         'is_active'       => 1,
                         'option_label'    => array( 'MultiLabel1','MultiLabel2'),
                         'option_value'    => array( 10, 20 ),
                         'option_weight'   => array( 1, 2),
                         'option_status'   => array( 1, 1),
                         );
              
        $customField =& civicrm_custom_field_create($params);    

        $this->assertEquals($customField['is_error'],0);
        $this->assertNotNull($customField['result']['customFieldId']);
        $this->customFieldDelete($customField['result']['customFieldId']);
        $this->customGroupDelete($customGroup['id']); 
    }     

    function testCustomFieldDeleteWithoutFieldID( )
    {
        $params = array( ); 
        $customField =& civicrm_custom_field_delete($params); 
        $this->assertEquals($customField['is_error'], 1);
        $this->assertEquals($customField['error_message'], 'Invalid or no value for Custom Field ID');
    }    
    
    function testCustomFieldDelete( )
    {
        $customGroup = $this->customGroupCreate('Individual','test_group');
        $customField = $this->customFieldCreate($customGroup['id'],'test_name'); 
        $this->assertNotNull($customField['result']['customFieldId']);
        $customField =& civicrm_custom_field_delete( $customField );
        $this->assertEquals($customField['is_error'], 0);
        $this->customGroupDelete($customGroup['id']);
    } 
    
    function testCustomFieldOptionValueDelete( )
    {
        $customGroup = $this->customGroupCreate('Contact','ABC' );  
        $customOptionValueFields = $this->customFieldOptionValueCreate($customGroup,'fieldABC' );
        $customField =& civicrm_custom_field_delete($customOptionValueFields);
        $this->assertEquals($customField['is_error'], 0);
        $this->customGroupDelete($customGroup['id']); 
    } 

    
}
