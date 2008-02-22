<?php
require_once 'api/v2/Group.php';

class TestOfGroupDeleteAPIV2 extends CiviUnitTestCase 
{
    function testGroupDeleteWithEmptyParams( )
    {
        $params      = null;        
        $deleteGroup =& civicrm_group_delete( $params ); 
        
        $this->assertEqual( $deleteGroup['is_error'], 1 );
        $this->assertEqual( $deleteGroup['error_message'], 'Required parameter missing');
    }
    
    function testGroupDeleteWithWrongID( )
    {
        $params      = array( 'id' => 0 );        
        $deleteGroup =& civicrm_group_delete( $params ); 
        
        $this->assertEqual( $deleteGroup['is_error'], 1 );
        $this->assertEqual( $deleteGroup['error_message'], 'Required parameter missing');
    }
    
    function testGroupDelete( )
    {
        $groupID     = $this->groupCreate();
        $params      = array( 'id' => $groupID );
        $deleteGroup =& civicrm_group_delete( $params );   
       
        $this->assertEqual( $deleteGroup['is_error'], 0 );
        $this->assertEqual( $deleteGroup['result'], 1 );
    }
    
}
?>
