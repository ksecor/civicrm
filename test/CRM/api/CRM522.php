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
        $params1 = array('first_name'    => 'fname',
                         'last_name'     => 'lname',
                         'location_type' => 'Work',
                         'phone'         => '11111',
                         'phone_type'    => 'Phone'
                         );
        
        $contact =& crm_create_contact($params1, 'Individual');
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->contact_type, 'Individual');
        
        $params2 = array('contact_id'    => $contact->id, 
                         'first_name'    => 'fname_updated',
                         'last_name'     => 'lname_updated',
                         'location_type' => 'Work',
                         'phone'         => '2222',
                         'phone_type'    => 'Fax'
                         );
        
        $contact = crm_update_contact($contact, $params2);
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        
    }
}
?>