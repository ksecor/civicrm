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
        
        $params = array('name'            => 'test_textfield',
                        'label'           => 'Name',
                        'html_type'       => 'Text',
                        'data_type'       => 'String',
                        'default_value'   => 'abc',
                        'weight'          => 4,
                        'is_required'     => 1,
                        'is_searchable'   => 0,
                        'is_active'       => 1
                        );
        $customField =& civicrm_custom_field_create($params);  
        $this->assertEqual($customField['is_error'], 1);
        $this->assertEqual( $customField['error_message'],'Missing Required field :custom_group_id' );
    }    
    
    function testCustomTextFieldCreate( )
    {
        $customGroupID = $this->customGroupCreate('Individual','test_group');
        $params = array('custom_group_id' => $customGroupID,
                        'name'            => 'test_textfield',
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
        $this->assertEqual($customField['is_error'],0);
        $this->assertNotNull($customField['custom_field_id']);
        $this->customFieldDelete($customField['custom_field_id']);
        $this->customGroupDelete($customGroupID);
    } 

    function testCustomSelectFieldCreate( )
    {
        $customGroupID = $this->customGroupCreate('Individual', 'test_group');
        $params = array('custom_group_id' => $customGroupID,
                        'name'            => 'test_selectfield',
                        'label'           => 'Country',
                        'html_type'       => 'Select',
                        'data_type'       => 'String',
                        'weight'          => 4,
                        'is_required'     => 1,
                        'is_searchable'   => 0,
                        'is_active'       => 1
                        );
          
        $customField =& civicrm_custom_field_create($params); 
        $this->assertEqual($customField['is_error'],0);
        $this->assertNotNull($customField['custom_field_id']);
        $this->customFieldDelete($customField['custom_field_id']);
        $this->customGroupDelete($customGroupID);
    } 
   
}
?>
 