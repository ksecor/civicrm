<?php

require_once 'api/v2/Tag.php';

class TestOfTagCreateAPIV2 extends CiviUnitTestCase 
{
    
    function setUp() 
    {
    }

    function testTagCreateWithoutDomainID()
    {
        $params = array(
                        'name'        => 'New Tag1',
                        'description' => 'This is description for New Tag 01'
                        );

        $result =& civicrm_tag_create($params);
        $this->assertEqual( $result['is_error'],1 );
    }
    
    function testTagCreateEmptyParams()
    {
        $params = array( );
        $result =& civicrm_tag_create($params);
        $this->assertEqual( $result['is_error'],1 );
    }
    
    function testTagCreateWithDomainID()
    {
        $params = array(
                        'name'      => 'NewTag002',
                        'domain_id' => '1'
                        );
        
        $tag =& civicrm_tag_create($params);
        $this->assertEqual($tag['is_error'], 0);
        civicrm_tag_delete($tag['tag_id']);
    }
    
    function testTagCreate()
    {
        $params = array(
                        'name'        => 'New Tag3',
                        'description' => 'This is description for New Tag 02',
                        'domain_id'   => '1'
                        );
        
        $tag =& civicrm_tag_create($params);
        $this->assertEqual($tag['is_error'], 0);
        civicrm_tag_delete($tag['tag_id']);
     }
    
    function tearDown() 
    {  
    }
}
?>
