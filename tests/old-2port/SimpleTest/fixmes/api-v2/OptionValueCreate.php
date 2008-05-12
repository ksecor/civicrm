<?php

require_once 'api/v2/CustomGroup.php';

class TestOfOptionValueCreateAPIV2 extends CiviUnitTestCase 
{
    
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }

    function testOptionValueCreateNoParam()
    {
        $params = array();
        $optionValue =& civicrm_option_value_create($params); 
        $this->assertEqual($optionValue['is_error'], 1);
        $this->assertEqual($optionValue['error_message'],'Missing required Field : Custom Field ID');
    }
     
    function testOptionValueCreateWithoutFieldID( )
    { 
        
        $params = array('name'            => 'test_option_value',
                        'label'           => 'Name',
                        'default_value'   => 'abc',
                        'weight'          => 4,
                        'is_optgroup'     => 0,
                        'is_reserved'     => 1,
                        'is_active'       => 1
                        );

        $optionValue =& civicrm_option_value_create($params); 
        $this->assertEqual($optionValue['is_error'], 1);
        $this->assertEqual($optionValue['error_message'],'Missing required Field : Custom Field ID');
    }
    
    function testOptionValueCreate( )
    {
        $customGroupID = $this->customGroupCreate('Contact','ABC' ); 
        $customFieldID = $this->customFieldCreate($customGroupID,'GroupABC' );
        $params = array('custom_field_id' => $customFieldID,
                        'name'            => 'test_option_value',
                        'label'           => 'Name',
                        'default_value'   => 'abc',
                        'weight'          => 4,
                        'is_optgroup'     => 0,
                        'is_reserved'     => 1,
                        'is_active'       => 1
                        );
        
        $optionValue =& civicrm_option_value_create($params); 
        $this->assertEqual($optionValue['is_error'], 0);
        $this->assertNotNull($optionValue['custom_option_id']);
        $this->customFieldDelete($customFieldID);
        $this->customGroupDelete($customGroupID);
    }    
}

 