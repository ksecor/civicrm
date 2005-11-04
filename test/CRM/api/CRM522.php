<?php

require_once 'api/crm.php';

class TestOfCRM522 extends UnitTestCase 
{
    
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }
    
    function testCreateContactIndividual()
    {
        $contact = crm_get_contact( array( 'id' => 111 ) );

        $params2 = array('contact_id'    => 111, 
                         'first_name'    => 'fname_updated',
                         'last_name'     => 'lname_updated',
                         'location_type' => 'Work',
                         'phone'         => '2222',
                         'phone_type'    => 'Phone'
                         );
        
        $contact = crm_update_contact($contact, $params2);
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        
    }
}
?>