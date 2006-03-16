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
        $individual = array('email' => 'richorris@y.net',
                            'location_type' => 'Home',
                            'street_address' => '123 Main Street',
                            'city' => 'San Francisco',
                            'state_province' => 'California',
                            'country'     => 'United States',
                            'postal_code' => '94111',
                            'phone' => '(555) 555-5555'
                            );
        $createContact = crm_create_contact($individual);
        $this->assertIsA($createContact, 'CRM_Contact_BAO_Contact');
        
        $params = array( 'email' => 'richorris@y.net' );
        if ($contact = crm_get_contact($params)) {
            $info = array('first_name' => 'Rich',
                          'last_name' => 'Orris',
                          'street_address' => '123 Main Street',
                          'city' => 'Portland',
                          'state_province' => 'OR',
                          'country'     => 'US',
                          'postal_code' => '94222',
                          'phone' => '(555) 666-6666');
            $contact = crm_update_contact($contact, $info);
            CRM_Core_Error::debug( 'Updated Contact', $contact );
        }
    }
}
?>