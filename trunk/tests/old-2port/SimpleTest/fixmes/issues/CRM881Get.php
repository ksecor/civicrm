<?php

require_once 'api/crm.php';

class TestOfCRM881Get extends UnitTestCase
{   
    function setUp()
    {
        $cParams = array('contact_id' => '101');
        $contact = crm_get_contact($cParams);
#       crm_delete_location($contact, 'Home');
#       crm_delete_location($contact, 'Work');

#       $lParams = array('location_type' => 'Home', 'street_address' => 'Odynca 1 m. 2');
#       crm_create_location($contact, $lParams);
#       $lParams = array('location_type' => 'Home', 'street_address' => 'Niepodleglosci 3 m. 4');
#       crm_create_location($contact, $lParams);
#       $lParams = array('location_type' => 'Work', 'street_address' => 'Kukulki 5');
#       crm_create_location($contact, $lParams);
#       $lParams = array('location_type' => 'Work', 'street_address' => 'Nowowiejska 6');
#       crm_create_location($contact, $lParams);
#       $lParams = array('location_type' => 'Work', 'street_address' => 'Mazowiecka 7');
#       crm_create_location($contact, $lParams);
#       $lParams = array('location_type' => 'Home', 'street_address' => 'Kielecka 8');
#       crm_create_location($contact, $lParams);
        
    }
    
    function tearDown()
    {
    }
    
    function testCRM881Get( )
    {
        $params    = array('contact_id' => '101');
        $contact   = crm_get_contact($params);

        $locations =& crm_get_locations($contact, null);
        print '<h2>all</h2><pre>';
        print_r($locations);
        print '</pre>';

        $locations =& crm_get_locations($contact, array('Home'));
        print '<h2>Home</h2><pre>';
        print_r($locations);
        print '</pre>';

        $locations =& crm_get_locations($contact, array('Work'));
        print '<h2>Work</h2><pre>';
        print_r($locations);
        print '</pre>';

    }

}

