<?php

require_once 'api/crm.php';

class TestOfCRM787 extends UnitTestCase
{   
    function setUp()
    {
    }

    function tearDown()
    {
    }

    function testCRM787( )
    {
        $user = array(
                        'first_name' => 'Michael',
                        'last_name' => 'Knight',
                        'prefix' => 'Mr.',
                        'job_title' => 'Chief Slacker',
                        'home_URL' => 'http://www.yahoo.com/',
                        'note' => 'This is a self-note',
                        );

        $contact =& crm_create_contact($user);

        $location = array(
                          'is_primary' => true,
                          'location_type' => 'Main',
                          'email' => 'jedi@example.com',
                          'street_address' => '123 Lincoln Avenue',
                          'supplemental_address_1' => 'Bondi Beach',
                          'supplemental_address_2' => 'Surfing',
                          'phone' => '555-1212',
                          'fax' => 'fax',
                          'mobile' => 'mobile',
                          );

        $home =& new CRM_Core_DAO_Phone();
        $home->phone        = $location['phone'];
        $home->phone_type   = 'Phone';
        $home->is_primary   =  true;
        $mobile  =& new CRM_Core_DAO_Phone();
        $mobile->phone      = $location['mobile'];
        $mobile->phone_type = 'Mobile';
        $fax  =& new CRM_Core_DAO_Phone();
        $fax->phone         = $location['fax'];
        $fax->phone_type    = 'Fax';
        $phones = array($home, $mobile, $fax);
        
        $email =& new CRM_Core_DAO_Email();
        $email->email = $location['email'];
        $email->is_primary = true;
        $emails = array($email);

        $params = array (
                         'is_primary'            => true,
                         'location_type'         => $location['location_type'],
                         'email'                 => $emails,
                         'phone'                 => $phones,
                         'street_address'        => $location['street_address'],
                         'supplemental_address_1'=> $location['supplemental_address_1'],
                         'supplemental_address_2'=> $location['supplemental_address_2'],
                         );
        $location =& crm_create_location($contact, $params);
        $contact =& crm_get_contact( array( 'contact_id' => $contact->id ) );
        CRM_Core_Error::debug( 'l', $contact );
    }

}

?>
