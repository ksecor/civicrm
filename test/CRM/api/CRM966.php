<?php

require_once 'api/crm.php';

class TestOfCRM966 extends UnitTestCase
{   
    function setUp()
    {
    }
    
    function tearDown()
    {
    }
    
    function testCRM966( )
    {
        $contactGroups = crm_contact_get_groups(30, 'Removed');

        CRM_Core_Error::debug('Groups',$contactGroups);
    }
}

?>