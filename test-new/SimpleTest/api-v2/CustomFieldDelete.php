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
        $this->assertNotNull($customField['error_message']);
        $this->assertEqual($customGroup['result'], 0);
    }    
    
    function testCustomFieldDelete( )
    {
        $customGroupID = $this->customGroupCreate('Individual','test_group');
        $customFieldID = $this->customFieldCreate($customGroupID,'test_name');
        $params = array('id' => $customFieldID);
        $customField =& civicrm_custom_field_delete($params); 
        $this->assertEqual($customField['is_error'], 0);
        $this->assertEqual($customField['result'], 1);
        $this->customGroupDelete($customGroupID);
    } 
    
}
?>
 