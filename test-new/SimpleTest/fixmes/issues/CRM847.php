<?php

require_once 'api/crm.php';

class TestOfCRM847 extends UnitTestCase
{   
    function setUp()
    {
    }
    
    function tearDown()
    {
    }
    
    function testCRM847( )
    {
        $fields = crm_uf_get_profile_fields( 1 );
        CRM_Core_Error::debug( 'f', $fields );
    }

}

