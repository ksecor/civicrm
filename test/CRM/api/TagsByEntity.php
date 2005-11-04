<?php

require_once 'api/crm.php';

class TestOfTagsByEntityAPI extends UnitTestCase 
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

    function testTagsByEntity()
    {

       $params  = array('contact_id' => 79 );
       $entity  = crm_get_contact($params);
       $tag=crm_tags_by_entity($entity);
       print_r($tag);

    }
}
?>