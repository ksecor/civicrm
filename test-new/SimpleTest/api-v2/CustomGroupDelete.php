<?php

require_once 'api/v2/CustomGroup.php';

class TestOfCustomGroupDeleteAPIV2 extends CiviUnitTestCase 
{
    function setUp( ) 
    {
    }
    
    function tearDown( ) 
    {
    }
   
    function testCustomGroupDeleteWithoutGroupID( )
    {
        $customGroup =& civicrm_custom_group_delete($params);
        $this->assertEqual($customGroup['is_error'], 1);
        $this->assertEqual($customGroup['error_message'],'Params is not an array');
    }    
    
    function testCustomGroupDelete( )
    {
        $customGroup = $this->customGroupCreate('Individual', 'test_group'); 
        $params = array('id' => $customGroup['id']);                         
        $customGroup =& civicrm_custom_group_delete($params);  
        $this->assertEqual($customGruup['is_error'], 0);
    } 
}
?>
 
