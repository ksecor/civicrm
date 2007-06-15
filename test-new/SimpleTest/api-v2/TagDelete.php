<?php

require_once 'api/v2/Tag.php';

class TestOfTagDeleteAPIV2 extends CiviUnitTestCase 
{
    function setUp( )
    {
    }

    function testTagDeleteEmtyTag( )
    {
        $tag = array( );
        $tagDelete =& civicrm_tag_delete( $tag );
        $this->assertEqual( $tagDelete['is_error'], 1 );
        $this->assertNotNull($tagDelete['error_message']);
    }
    
    function testTagDeleteError( )
    {
        $tag = "noID";
        
        $tagDelete =& civicrm_tag_delete($tag);
        $this->assertEqual( $tagDelete['is_error'], 1 ); 
        $this->assertNotNull($tagDelete['error_message']);
        
    }
    
    function testTagDelete( )
    {
        $tagID = $this->tagCreate(null); 
        $params = array('tag_id'=> $tagID);
        $tagDelete =& civicrm_tag_delete($params ); 
        $this->assertEqual( $tagDelete['is_error'], 0 );
    }
    
    function tearDown() 
    {
    }
}

?>
