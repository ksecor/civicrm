<?php

require_once 'api/crm.php';

class TestOfCRM881 extends UnitTestCase
{   
    function setUp()
    {
        $cParams = array('contact_id' => '101');
        $contact = crm_get_contact($cParams);
#       crm_delete_location($contact, 'Home');
#       crm_delete_location($contact, 'Work');

#       $lParams = array('location_type' => 'Home', 'street_address' => 'Odyńca 1 m. 2');
#       crm_create_location($contact, $lParams);
#       $lParams = array('location_type' => 'Home', 'street_address' => 'Niepodległości 3 m. 4');
#       crm_create_location($contact, $lParams);
#       $lParams = array('location_type' => 'Work', 'street_address' => 'Kukułki 5');
#       crm_create_location($contact, $lParams);
#       $lParams = array('location_type' => 'Work', 'street_address' => 'Nowowiejska 6');
#       crm_create_location($contact, $lParams);
        
    }
    
    function tearDown()
    {
    }
    
    function testCRM881( )
    {
        $params    = array('contact_id' => '101');
        $contact   = crm_get_contact($params);
        $locTypes  = array('Home');
        $locations =& crm_get_locations($contact, $locTypes);

        print '<pre>';
        print_r($locations);
        print '</pre>';
    }

}
?>
