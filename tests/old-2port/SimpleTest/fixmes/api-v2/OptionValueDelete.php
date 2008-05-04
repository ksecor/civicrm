<?php

require_once 'api/v2/CustomGroup.php';

class TestOfOptionValueDeleteAPIV2 extends CiviUnitTestCase 
{
    
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }
    
    function testOptionValueDeleteWithoutFieldID( )
    {
        $params = array( ); 
        $optionValue =& civicrm_option_value_delete($params); 
        $this->assertEqual($optionValue['is_error'], 1); 
        $this->assertEqual($optionValue['error_message'], 'Invalid or no value for Custom option ID');
    }    
    
    function testOptionValueDelete( )
    {
        $customGroupID = $this->customGroupCreate('Contact','ABC' );  
        $customFieldID = $this->customFieldCreate($customGroupID,'fieldABC' );
        $optionValueID = $this->optionValueCreate($customFieldID,'test_option' );
        $params = array('id' => $optionValueID);
        $optionValue =& civicrm_option_value_delete($params); 
        $this->assertEqual($optionValue['is_error'], 0);
        $this->assertEqual($optionValue['result'], 1);
        $this->customFieldDelete($customFieldID);
        $this->customGroupDelete($customGroupID);
    } 
    
}

 