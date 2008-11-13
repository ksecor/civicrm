<?php

require_once 'api/crm.php';

class TestOfCRM881Delete extends UnitTestCase
{   
    function setUp()
    {
    }
    
    function tearDown()
    {
    }
    
    function testCRM881Delete( )
    {
        $params    = array('contact_id' => '101');
        $contact   = crm_get_contact($params);

        crm_delete_location($contact, 91);

        $locations =& crm_get_locations($contact, null);
        print '<h2>all</h2><pre>';
        print_r($locations);
        print '</pre>';

    }

}

