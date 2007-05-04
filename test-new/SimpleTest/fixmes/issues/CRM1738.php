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
        $params = array('first_name'    => 'Dave',
                        'last_name'     => 'Tomberg',
                        'email'         => 'dave.tomberg@myco.com',
                        'location_type' => 'Billing',
                        );
        $contact =& crm_create_contact($params, 'Individual');
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->contact_type, 'Individual');
        $this->_individual[$contact->id] = $contact;

        // Now add a second location.
        $phone  = array( array( 'phone'      => '415 324-1021',
                                'phone_type' => 'Mobile' ),
                         array( 'phone'      => '415-555',
                                'phone_type' => 'Phone',
                                'is_primary' => 1 ) );
        
        $workEmail = array( 'email' => 'dave.tomberg@gmail.com' );
        $email = array( array( 'email' => 'dave.tomberg@gmail.com' ),
                        array( 'email' => 'dave.tomberg@primary.com', 'is_primary' => 1 ) );
        
        $params = array (
                         'location_type' => 'Work',
                         'is_primary' => 1,
                         'email'         => $email,
                         'phone'         => $phone,
                         'street_address'=> '330 Upper Terrace',
                         'postal_code'   => '94117',
                         'city'          => 'San Francisco',
                         'state_province'=> 'CA',
                         'country'       => 'US'
                         );
        $location =& crm_create_location($contact, $params);
    }
    
}

?>
