<?php

require_once 'api/crm.php';

class TestOfCRM1495 extends UnitTestCase 
{
    function setUp( ) 
    {
    }

    function tearDown( ) 
    {
    }


    function testMailerSubscribe( )
    {
        require_once 'api/Mailer.php';
        $se =& crm_mailer_event_subscribe( 'c1497@example.com', 1, 1 );
        CRM_Core_Error::debug( 's', $se );
    }
}


