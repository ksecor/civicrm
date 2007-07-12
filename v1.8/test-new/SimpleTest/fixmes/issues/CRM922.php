<?php

require_once 'api/crm.php';

class TestOfCRM922 extends UnitTestCase
{   
    function setUp()
    {
    }
    
    function tearDown()
    {
    }
    
    function testCRM922( )
    {
        $params    = array('contact_id' => '101');
        $contact   = crm_get_contact($params);

        print '<pre>';
        print_r($contact);
        print '</pre>';
    }

}
?>
