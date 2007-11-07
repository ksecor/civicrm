<?php

require_once 'api/v2/CustomGroup.php';

class TestOfCustomFieldDeleteAPIV2 extends CiviUnitTestCase 
{
    
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }
   
    function testCustomFieldDeleteWithoutFieldID( )
    {
        $params = array( ); 
        $customField =& civicrm_custom_field_delete($params); 
        $this->assertEqual($customField['is_error'], 1);
        $this->assertEqual($customField['error_message'], 'Invalid or no value for Custom Field ID');
        $this->assertEqual($customGroup['result'], 0);
    }    
    
    function testCustomFieldDelete( )
    {
        $customGroup = $this->customGroupCreate('Individual','test_group'); 
        $customFields = $this->customFieldCreate($customGroup['id'],'test_name'); 
        $params = array('id' => $customFields['id']);
        $customField =& civicrm_custom_field_delete($params); 
        $this->assertEqual($customField['is_error'], 0);
        $this->customGroupDelete($customGroup['id']);
    } 
    
    function testCustomFieldOptionValueDelete( )
    {
        $customGroup = $this->customGroupCreate('Contact','ABC' );  
        $customOptionValueFields = $this->customFieldOptionValueCreate($customGroup,'fieldABC' );
        $customField =& civicrm_custom_field_delete($customOptionValueFields);
        $this->assertEqual($customField['is_error'], 0);
        $this->customGroupDelete($customGroup['id']); 
    } 
    
}
?>

