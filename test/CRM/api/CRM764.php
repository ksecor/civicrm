<?php

require_once 'api/crm.php';

class TestOfCRM755 extends UnitTestCase 
{
    function setUp() 
    {
    }

    function tearDown() 
    {
    }
    
    function testCRM764() 
    {
        $params = array( 'email' => 'lobo@google.com' );
        if ($contact = crm_get_contact($params)) {
            $info = array('first_name' => 'Rich',
                          'last_name' => 'Orris',
                          'street_address' => '123 Main Street',
                          'city' => 'San Francisco',
                          'state_province' => 'CA',
                          'country'     => 'US',
                          'postal_code' => '94111',
                          'phone' => '(555) 555-5555');
            $contact = crm_update_contact($contact, $info);
            CRM_Core_Error::debug( 'c', $contact );
        }
    }    
}

?>