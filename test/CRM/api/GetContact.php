<?php

require_once 'api/crm.php';

class TestOfGetContactAPI extends UnitTestCase 
{
    protected $_individual;
    protected $_houseHold;
    protected $_organization;

    function setUp( ) 
    {
    }

    function tearDown( ) 
    {
    }
    
    function testCreateIndividual() 
    {
        $params = array('first_name'    => 'kurund',
                        'last_name'     => 'jalmi', 
                        'location_type'     => 'Main',
                        'email'         => 'kurund@yahoo.com'
                        );
        $contact =& crm_create_contact($params, 'Individual');
        //print_r($contact);
        //$this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        //$this->assertEqual($contact->contact_type, 'Individual');
        //$this->_individual = $contact;
    }


    function testGetContactIndividualByEmailOnly()
    {
        $params = array('email' => 'kurund@yahoo.com');
        $return_properties = array('contact_type', 'display_name', 'sort_name', 'phone', 'phone_type', 'im', 'im_provider');
        $contact =& crm_get_contact($params, $return_properties);
        print_r($contact);

        /*
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->contact_type, 'Individual');
        $this->assertEqual($contact->display_name, 'kurund  jalmi' );
        $this->assertEqual($contact->sort_name, 'jalmi, kurund');
        $this->assertEqual($contact->location[1]->phone[1]->phone, '999999');
        $this->assertEqual($contact->location[1]->phone[1]->phone_type, 'Phone');
        $this->assertEqual($contact->location[1]->im[1]->name, 'kurundssyahoo');
        $this->assertEqual($contact->location[1]->im[1]->provider_id, '3');
        */
    }


    function testDeleteIndividual()
    {
        /*
        $contact = new CRM_Contact_DAO_Contact();
        $contact->id = 102;
        $contact->contact_type = 'Individual';
        */
        $contact = $this->_individual;
        $val =& crm_delete_contact(& $contact);
        $this->assertNull($val);
    }
    




}


?>