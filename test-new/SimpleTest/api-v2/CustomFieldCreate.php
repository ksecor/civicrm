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
        $this->assertNotNull($customField['id']);
        $this->customFieldDelete($customField['id']); 
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
        
        $optionValue = array ('label' => 'Label1',
                              'value' => 'value1',
                              'name'  => 'Name1',
                              'weight'=> 1,
                              'is_active'=>1
                              );
        
        $params = array('fieldParams' => $fieldParams,
                        'optionGroup' => $optionGroup,
                        'optionValue' => $optionValue,
                        'customGroup' => $customGroup,
                        );
        
        $customField =& civicrm_custom_field_create($params);          
        $this->assertEqual($customField['is_error'],0);
        $this->assertNotNull($customField['id']);
        $this->customFieldDelete($customField['id']); 
        $this->customGroupDelete($customGroup['id']); 
    }     
}
?>
 
