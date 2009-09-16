<?php

require_once 'api/v2/Tag.php';
require_once 'CiviTest/CiviUnitTestCase.php';

class api_v2_TagTest extends CiviUnitTestCase 
{
    
    function setUp() 
    {
    }
    
    function testTagCreateEmptyParams()
    {
        $params = array( );
        $result =& civicrm_tag_create($params); 
        $this->assertEquals( $result['is_error'], 1,"In line " . __LINE__ );
        $this->assertEquals( $result['error_message'],'No input parameters present' );
    }    
    
    function testTagCreate()
    {
        $params = array(
                        'name'        => 'New Tag3',
                        'description' => 'This is description for New Tag 02'
                        );
        
        $tag =& civicrm_tag_create($params); 
        $this->assertEquals($tag['is_error'], 0);
        $this->assertNotNull($tag['tag_id']);
        $this->tagDelete($tag['tag_id']);
    }


    function testTagDeleteEmtyTag( )
    {
        $tag = array( );
        $tagDelete =& civicrm_tag_delete( $tag );
        $this->assertEquals( $tagDelete['is_error'], 1 );
        $this->assertEquals( $tagDelete['error_message'],'Could not find tag_id in input parameters' );
    }
    
    function testTagDeleteError( )
    {
        $tag = "noID";
        
        $tagDelete =& civicrm_tag_delete($tag); 
        $this->assertEquals( $tagDelete['is_error'], 1 ); 
        $this->assertEquals( $tagDelete['error_message'],'Could not find tag_id in input parameters' );            
    }
    
    function testTagDelete( )
    {
        $tagID = $this->tagCreate(null); 
        $params = array('tag_id'=> $tagID);
        $tagDelete =& civicrm_tag_delete($params ); 
        $this->assertEquals( $tagDelete['is_error'], 0 );
    }
    
    function tearDown() 
    {  
    }
}

