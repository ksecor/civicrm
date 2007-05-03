<?php

require_once 'api/crm.php';

class TestOfCRM785 extends UnitTestCase
{   
    function setUp()
    {
    }
    
    function tearDown()
    {
    }
    
    function testCRM836( )
    {
        $html = crm_uf_get_profile_html_by_id( 0, 1 );
        echo $html;
    }
    
}
?>
