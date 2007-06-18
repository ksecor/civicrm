<?php

require_once 'api/v2/CustomGroup.php';

class TestOfCustomGroupDeleteAPIV2 extends CiviUnitTestCase 
{
    
    function setUp() 
    {
    }
    
    function tearDown() 
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
        $title = 'test_group';
        $className= 'Individual';
        $customGroupID = $this->customGroupCreate($className , $title); 
        $params = array('id' => $customGroupID); 
        $customGroup =& civicrm_custom_group_delete($params);
        $this->assertEqual($customGruup['is_error'], 0);
    } 
}
?>
 