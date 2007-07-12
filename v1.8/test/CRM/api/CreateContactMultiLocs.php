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
        $params = array('first_name'    => 'Bob',
                        'last_name'     => 'Conberg',
                        'email'         => 'Bob.conberg@myco.com',
                        'phone'         => '650 222-2200',
                        'phone_type'    => 'Phone',
                        'im'            => 'bob.conberg1', 
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

        // Now add a second location. Email, phone and im data must be passed as an array
        // of one or more objects.
        $homePhone  = & new CRM_Core_DAO_Phone();
        $homePhone->phone       = '415 324-1021';
        $homePhone->phone_type  = 'Mobile';
        $phone = array($homePhone);

        $homeEmail  =& new CRM_Core_DAO_Email();
        $homeEmail->email = 'dan.conberg@gmail.com';
        $email = array($homeEmail);
        
        $params = array (
                     'location_type' => 'Home',
                     'is_primary'    => true,
                     'email'         => $email,
                     'phone'         => $phone,
                     'street_address'=> '330 Upper Terrace',
                     'postal_code'   => '94117',
                     'city'          => 'San Francisco',
                     'state_province'=> 'CA',
                     // Can also pass state_province_id name-value pair or use full state-province name.
                     // 'state_province_id'=> 1004,
                     //'state_province'=> 'California',
                     'country'       => 'US'
                     // Can also pass country_id name-value pair or use full country name.
                     //'country_id'       => 1228
                     //'country'       => 'United States'
                         );
        $location =& crm_create_location($contact, $params);
        echo('<h3>Contact After Adding Second Location</h3>');
        print_r($location);
    }
    
}

?>
