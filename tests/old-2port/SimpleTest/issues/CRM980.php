<?php

require_once 'api/crm.php';

class TestOfCRM980 extends UnitTestCase
{   
    function setUp()
    {
    }
    
    function tearDown()
    {
    }
    
    function testCRM980( )
    {        
        $params = array ('id'   => 2,
                         'name' => 'Company');
        $tag = crm_get_tag($params);
        
        $this->assertIsA($tag , 'CRM_Core_BAO_Tag');
        $this->assertEqual($tag->id, $params['id']);
        $this->assertEqual($tag->name, $params['name']);
    }
}

