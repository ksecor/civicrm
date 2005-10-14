<?php

require_once 'api/crm.php';

class TestOfCreateRelationshipAPI extends UnitTestCase 
{
    function setUp() 
    {
    }

    function tearDown() 
    {
    }

    function testCreateRelationship() 
    {
        $params = array('start_date' => 20051013, 'end_date' => 20051014);
        
        $rel = crm_create_relationship('', '', '1_a_b', $params);
    }
}

?>