<?php

require_once 'api/crm.php';

class TestOfGetUFMatchID extends UnitTestCase 
{

    
    function setUp() 
    {
    }

    function tearDown() 
    {
    }

    function testGETUFId()
    {
        $contact_id = 300;
        echo crm_uf_get_uf_id( $contact_id ) . "\n";
    }


    function testGETContactId()
    {
        $uf_id = 20;
        echo crm_uf_get_match_id( $uf_id ) . "\n";
    }


}


