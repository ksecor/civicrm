<?php

require_once 'api/crm.php';

class TestOfDeleteTagAPI extends UnitTestCase 
{
    protected $_tag;
    //protected $_household;
    //protected $_organization;

    function setUp() 
    {
    }

    function tearDown() 
    {
    }

    /* Test cases for crm_create_location for Individual contact */ 

    function testDeleteTag()
    {
        $params = array('name'    => 'Manish1',
                        'description' => 'Zope',
                        'parent_id' => 'Null',
                        'domain_id' => '1'
                        );
        $tag =& crm_create_tag($params);
        //print_r($tag);
        crm_delete_tag($tag);
        //        print_r($tag);
        //crm_delete_tag($tag);  
    }
}
?>