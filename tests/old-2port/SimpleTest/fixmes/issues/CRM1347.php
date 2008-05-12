<?php

require_once 'api/crm.php';
require_once 'api/Mailer.php';

class TestOfCRM1184 extends UnitTestCase 
{
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }
    
    function testUnsubscribe( ) 
    {
        crm_mailer_event_unsubscribe( 1, 1, 'd1844add45d26c1ef9ac1e1eb9d5387d619ba6cd' );
    }
    
}


