<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

require_once 'api/v2/Tag.php';
require_once 'CiviTest/CiviUnitTestCase.php';

class api_v2_TagTest extends CiviUnitTestCase 
{
    
    function setUp() 
    {
        parent::setUp();
    }

    function tearDown() 
    {  
    }

///////////////// civicrm_tag_get methods

    /**
     * Test civicrm_tag_get with wrong params type.
     */
    public function testGetWrongParamsType()
    {
        $params ='is_string';
        $result =& civicrm_tag_get($params);
        $this->assertEquals( $result['error_message'],'Params is not an array.' );       
    }

    /**
     * Test civicrm_tag_get with empty params.
     */
    public function testGetEmptyParams()
    {
        $params = array( );
        $result =& civicrm_tag_get($params);
        $this->assertEquals( $result['error_message'],'Required parameters missing.' );
    }

    /* Test civicrm_tag_get with wrong params.
     */
    public function testGetWrongParams()
    {
        $params = array(
                        'name'        => 'Wrong Tag Name',
                       );
        $result =& civicrm_tag_get($params);
        $this->assertEquals( $result['error_message'],'Exact match not found.' );
    }
    
    /**
     * Test civicrm_tag_get - success expected.
     */
    public function testGet()
    {   
        $params = array( 'name'        => 'New Tag',
                         'description' => 'This is description for New Tag 02' );
        $tag =& civicrm_tag_create($params);
        $params = array( 'name'        => 'New Tag',
                         );
        $GetTag = & civicrm_tag_get($params);
        $this->assertEquals($tag['is_error'], 0);
        $this->assertEquals($GetTag['id'],$tag['tag_id'],' Checking Tag Id '); 
    }  
    

///////////////// civicrm_tag_create methods
    
    function testCreateWrongParamsType()
    {
        $params = 'a string';
        $result =& civicrm_tag_create($params);
        $this->assertEquals( $result['is_error'], 1,"In line " . __LINE__ );
        $this->assertEquals( $result['error_message'],'Input parameters is not an array' );
    }

    function testCreateEmptyParams()
    {
        $params = array( );
        $result =& civicrm_tag_create($params); 
        $this->assertEquals( $result['is_error'], 1,"In line " . __LINE__ );
        $this->assertEquals( $result['error_message'],'No input parameters present' );
    }  
    
    function testCreatePasstagInParams()
    {
        $params = array( 'tag'         => 10,
                         'name'        => 'New Tag23',
                         'description' => 'This is description for New Tag 02' );
        $tag =& civicrm_tag_create($params);
        $this->assertEquals( $tag['tag_id'], 10, 'Checking tag ID' );
       
    }  

    function testCreate()
    {
        $params = array( 'name'        => 'New Tag3',
                         'description' => 'This is description for New Tag 02' );
        
        $tag =& civicrm_tag_create($params); 
        $this->assertEquals($tag['is_error'], 0);
        $this->assertNotNull($tag['tag_id']);
    }

///////////////// civicrm_tag_delete methods

    function testDeleteWrongParams()
    {
        $tag = array( 'tag_id' => 'incorrect value');
        $tagDelete =& civicrm_tag_delete( $tag );
        $this->assertEquals( $tagDelete['is_error'], 1 );
        $this->assertEquals( $tagDelete['error_message'],'Could not delete tag' );
    }
     
    function testDeleteWrongParamsType()
    {
        $tag = 'is string';
        $tagDelete =& civicrm_tag_delete( $tag );
        $this->assertEquals( $tagDelete['is_error'], 1 );
        $this->assertEquals( $tagDelete['error_message'],'Input parameters is not an array' );
    }

    function testDeleteEmptyParams()
    {
        $tag = array( );
        $tagDelete =& civicrm_tag_delete( $tag );
        $this->assertEquals( $tagDelete['is_error'], 1 );
        $this->assertEquals( $tagDelete['error_message'],'Could not find tag_id in input parameters' );
    }

    function testDeleteWithoutTagId()
    {
        $tag = array( 'some_other_key' => 1 );
        
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
    

}

