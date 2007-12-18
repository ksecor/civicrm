<?php

require_once 'api/v2/CustomGroup.php';

class TestOfCustomFieldCreateAPIV2 extends CiviUnitTestCase 
{
    
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }
    
    function testCustomFieldCreateNoParam()
    {
        $params = array();
        $customField =& civicrm_custom_field_create($params); 
        $this->assertEqual($customField['is_error'], 1);
        $this->assertEqual( $customField['error_message'],'Missing Required field :custom_group_id' );
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
        $params = array('fieldParams' => $fieldParams );
        
        $customField =& civicrm_custom_field_create($params);     
        $this->assertEqual($customField['is_error'], 1);
        $this->assertEqual( $customField['error_message'],'Missing Required field :custom_group_id' );
    }    
     
    function testCustomTextFieldCreate( )
    {
        $customGroup = $this->customGroupCreate('Individual','text_test_group');
        $fieldParams = array('custom_group_id' => $customGroup['id'],
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
        $params = array('fieldParams' => $fieldParams);
        
        $customField =& civicrm_custom_field_create($params); 
        $this->assertEqual($customField['is_error'],0);
        $this->assertNotNull($customField['result']['customFieldId']);
        $this->customFieldDelete($customField['result']['customFieldId']); 
        $this->customGroupDelete($customGroup['id']); 
    } 

    function testCustomDateFieldCreate( )
    {
        $customGroup = $this->customGroupCreate('Individual','date_test_group');
        $fieldParams = array('custom_group_id' => $customGroup['id'],
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
        $params = array('fieldParams' => $fieldParams);
        
        $customField =& civicrm_custom_field_create($params);  
        $this->assertEqual($customField['is_error'],0);
        $this->assertNotNull($customField['result']['customFieldId']);
        $this->customFieldDelete($customField['result']['customFieldId']);
        $this->customGroupDelete($customGroup['id']); 
    } 
    
    function testCustomCountryFieldCreate( )
    {
        $customGroup = $this->customGroupCreate('Individual','Country_test_group');
        $fieldParams = array('custom_group_id' => $customGroup['id'],
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
        $params = array('fieldParams' => $fieldParams);
        
        $customField =& civicrm_custom_field_create($params);  
        $this->assertEqual($customField['is_error'],0);
        $this->assertNotNull($customField['result']['customFieldId']);
        $this->customFieldDelete($customField['result']['customFieldId']);
        $this->customGroupDelete($customGroup['id']); 
    }
    
    function testCustomNoteFieldCreate( )
    {
        $customGroup = $this->customGroupCreate('Individual','Country_test_group');
        $fieldParams = array('custom_group_id' => $customGroup['id'],
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
        $params = array('fieldParams' => $fieldParams);
        
        $customField =& civicrm_custom_field_create($params);  
        $this->assertEqual($customField['is_error'],0);
        $this->assertNotNull($customField['result']['customFieldId']);
        $this->customFieldDelete($customField['result']['customFieldId']);
        $this->customGroupDelete($customGroup['id']); 
    } 
    
    function testCustomFieldOptionValueCreate( )
    {
        $customGroup = $this->customGroupCreate('Contact', 'select_test_group');
        $fieldParams = array ('custom_group_id' => $customGroup['id'],
                              'label'           => 'Country',
                              'html_type'       => 'Select',
                              'data_type'       => 'String',
                              'weight'          => 4,
                              'is_required'     => 1,
                              'is_searchable'   => 0,
                              'is_active'       => 1
                              );
        
        $optionGroup = array('domain_id' => 1,
                             'name'      => 'option_group1',
                             'label'     => 'option_group_label1',
                             'is_active' => 1
                             );
        
        $optionValue[] = array ('label'     => 'Label1',
                                'value'     => 'value1',
                                'name'      => 'Name1',
                                'weight'    => 1,
                                'is_active' => 1
                                );
        
        $params = array('fieldParams' => $fieldParams,
                        'optionGroup' => $optionGroup,
                        'optionValue' => $optionValue,
                        'customGroup' => $customGroup,
                        );
        
        $customField =& civicrm_custom_field_create($params);          
        $this->assertEqual($customField['is_error'],0);
        $this->assertNotNull($customField['result']['customFieldId']);
        $this->customFieldDelete($customField['result']['customFieldId']);
        $this->customGroupDelete($customGroup['id']); 
    } 
    
    function testCustomFieldSelectOptionValueCreate( )
    {
        $customGroup = $this->customGroupCreate('Contact', 'select_test_group');
        $fieldParams = array ('custom_group_id' => $customGroup['id'],
                              'label'           => 'Price',
                              'html_type'       => 'Select',
                              'data_type'       => 'Int',
                              'weight'          => 4,
                              'is_required'     => 1,
                              'is_searchable'   => 0,
                              'is_active'       => 1
                              );
        
        $optionGroup = array('domain_id' => 1 );
        
        $optionValue[] = array ('label'     => 'Label1',
                                'value'     => '10',
                                'weight'    => 1,
                                'is_active' => 1
                                );
        
        $optionValue[] = array ('label'     => 'Label2',
                                'value'     => '20',
                                'weight'    => 2,
                                'is_active' => 1
                                );
        
        $params = array('fieldParams' => $fieldParams,
                        'optionGroup' => $optionGroup,
                        'optionValue' => $optionValue,
                        'customGroup' => $customGroup,
                        );
        
        $customField =& civicrm_custom_field_create($params);          
        $this->assertEqual($customField['is_error'],0);
        $this->assertNotNull($customField['result']['customFieldId']);
        $this->customFieldDelete($customField['result']['customFieldId']);
        $this->customGroupDelete($customGroup['id']); 
    }
    
    function testCustomFieldCheckBoxOptionValueCreate( )
    {
        $customGroup = $this->customGroupCreate('Contact', 'CheckBox_test_group');
        $fieldParams = array ('custom_group_id' => $customGroup['id'],
                              'label'           => 'Price',
                              'html_type'       => 'CheckBox',
                              'data_type'       => 'String',
                              'weight'          => 4,
                              'is_required'     => 1,
                              'is_searchable'   => 0,
                              'is_active'       => 1
                              );
        
        $optionGroup = array('domain_id' => 1 );
        
        $optionValue[] = array ('label'     => 'Price First',
                                'value'     => '10',
                                'weight'    => 1,
                                'is_active' => 1
                                );
        
        $optionValue[] = array ('label'     => 'Price Second',
                                'value'     => '20',
                                'weight'    => 2,
                                'is_active' => 1
                                );
        
        $params = array('fieldParams' => $fieldParams,
                        'optionGroup' => $optionGroup,
                        'optionValue' => $optionValue,
                        'customGroup' => $customGroup,
                        );
        
        $customField =& civicrm_custom_field_create($params);          
        $this->assertEqual($customField['is_error'],0);
        $this->assertNotNull($customField['result']['customFieldId']);
        $this->customFieldDelete($customField['result']['customFieldId']);
        $this->customGroupDelete($customGroup['id']); 
    }   
    
    function testCustomFieldRadioOptionValueCreate( )
    {
        $customGroup = $this->customGroupCreate('Contact', 'Radio_test_group');
        $fieldParams = array ('custom_group_id' => $customGroup['id'],
                              'label'           => 'Price',
                              'html_type'       => 'Radio',
                              'data_type'       => 'Float',
                              'weight'          => 4,
                              'is_required'     => 1,
                              'is_searchable'   => 0,
                              'is_active'       => 1
                              );
        
        $optionGroup = array('domain_id' => 1 );
        
        $optionValue[] = array ('label'     => 'Price First',
                                'value'     => '10',
                                'weight'    => 1,
                                'is_active' => 1
                                );
        
        $optionValue[] = array ('label'     => 'Price Second',
                                'value'     => '20',
                                'weight'    => 2,
                                'is_active' => 1
                                );
        
        $params = array('fieldParams' => $fieldParams,
                        'optionGroup' => $optionGroup,
                        'optionValue' => $optionValue,
                        'customGroup' => $customGroup,
                        );
        
        $customField =& civicrm_custom_field_create($params);          
        $this->assertEqual($customField['is_error'],0);
        $this->assertNotNull($customField['result']['customFieldId']);
        $this->customFieldDelete($customField['result']['customFieldId']);
        $this->customGroupDelete($customGroup['id']); 
    } 
    
    function testCustomFieldMultiSelectOptionValueCreate( )
    {
        $customGroup = $this->customGroupCreate('Contact', 'MultiSelect_test_group');
        $fieldParams = array ('custom_group_id' => $customGroup['id'],
                              'label'           => 'Price',
                              'html_type'       => 'Multi-Select',
                              'data_type'       => 'String',
                              'weight'          => 4,
                              'is_required'     => 1,
                              'is_searchable'   => 0,
                              'is_active'       => 1
                              );
        
        $optionGroup = array('domain_id' => 1 );
        
        $optionValue[] = array ('label'     => 'Price First',
                                'value'     => '10',
                                'weight'    => 1,
                                'is_active' => 1
                                );
        
        $optionValue[] = array ('label'     => 'Price Second',
                                'value'     => '20',
                                'weight'    => 2,
                                'is_active' => 1
                                );
        
        $params = array('fieldParams' => $fieldParams,
                        'optionGroup' => $optionGroup,
                        'optionValue' => $optionValue,
                        'customGroup' => $customGroup,
                        );
        
        $customField =& civicrm_custom_field_create($params);          
        $this->assertEqual($customField['is_error'],0);
        $this->assertNotNull($customField['result']['customFieldId']);
        $this->customFieldDelete($customField['result']['customFieldId']);
        $this->customGroupDelete($customGroup['id']); 
    }     
    
}
?>
 
