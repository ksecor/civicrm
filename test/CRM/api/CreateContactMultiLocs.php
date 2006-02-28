<?php

require_once 'api/crm.php';

class TestOfCreateContactAPI extends UnitTestCase 
{
    protected $_individual   = array();
    
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }    
    
    function testCreateIndividualwithLocationType() 
    {
        $params = array('first_name'    => 'Dan',
                        'last_name'     => 'Conberg',
                        'email'         => 'dan.conberg@myco.com',
                        'phone'         => '650 222-2200',
                        'phone_type'    => 'Phone',
                        'im'            => 'dan.conberg1', 
                        'im_provider'   => 'Yahoo',
                        'street_address'=> '12 Main Street',
                        'city'          => 'San Mateo',
                        'postal_code'   => '94001',
                        'state_province'=> 'CA',
                        'country'       => 'US',
                        'location_type' => 'Work'
                        );
        $contact =& crm_create_contact($params, 'Individual');
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->contact_type, 'Individual');
        $this->_individual[$contact->id] = $contact;
        echo('<h3>Contact with One Location</h3>');
        print_r($contact);

        // Now add a second location.
        $homePhone  = & new CRM_Core_DAO_Phone();
        $homePhone->phone       = '415 324-1021';
        $homePhone->phone_type  = 'Mobile';
        $phone = array($homePhone);

        $homeEmail  =& new CRM_Core_DAO_Email();
        $homeEmail->email = 'dan.conberg@gmail.com';
        $email = array($homeEmail);
        
        $params = array (
                     'location_type' => 'Home',
                     'email'         => $email,
                     'phone'         => $phone,
                     'street_address'=> '330 Upper Terrace',
                     'postal_code'   => '94117',
                     'city'          => 'San Francisco',
                     'state_province_id'=> 1004,
                     'country_id'       => 1228
                     );
        $location =& crm_create_location($contact, $params);
    
        echo('<h3>Contact After Adding Second Location</h3>');
        print_r($contact);
    }
    
// ? Alternate method: Can we pass an array of location objects into crm_create_contact ?
}

?>