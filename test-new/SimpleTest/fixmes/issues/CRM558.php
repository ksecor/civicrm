<?php

require_once 'api/crm.php';

class TestOfCRM560 extends UnitTestCase 
{
    
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }
    
    function testGetUFCreateHTML( )
    {
        $html = crm_uf_get_create_html( );
        echo $html;
    }
}

?>
