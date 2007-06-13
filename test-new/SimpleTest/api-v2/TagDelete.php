<?php

require_once 'api/v2/Tag.php';

class TestOfTagDeleteAPIV2 extends CiviUnitTestCase 
{
    function setUp() 
    {
    }

    function testTagDeleteEmtyTag()
    {
        $tag = array();
        $tagDelete =& civicrm_tag_delete($tag);
        $this->assertEqual($tagDelete['is_error'],1 );
    }
    
    function testDeleteTagError()
    {
        $tag = "noID";
        
        $tagDelete =& civicrm_tag_delete($tag);
        print_r($tagDelete);
        $this->assertEqual($tagDelete['is_error'],1 );
        
    }
    
    function testTagDelete()
    {
        $tagID = $this->tagCreate( ); 
        $tagDelete =& civicrm_tag_delete($tagID); 
        $this->assertEqual($tagDelete['is_error'],0 );
        $this->assertNull($tagDelete);
    }
    
    function tearDown() 
    {
    }
}

?>
