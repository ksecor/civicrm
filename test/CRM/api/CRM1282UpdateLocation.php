<?php

require_once 'api/crm.php';

class TestOfCRM1282UpdateLocationAPI extends UnitTestCase 
{
    protected $_individual;
    protected $_location;   
    protected $_createLocation;
    protected $_updateLocation1;
    protected $_updateLocation2;

    function setUp( ) 
    {
    }

    function tearDown( ) 
    {
    }


    function testGetContact() 
    {
        $params = array('contact_id' => 98);
        $returnValues = array('contact_id', 'first_name', 'last_name', 'phone',
                              'postal_code', 'state_province', 'email');
        $this->_individual =& crm_get_contact($params, $returnValues);
    }

    function testGetLocation() 
    {
        $contact = $this->_individual;
        $this->_location = & crm_get_locations(&$contact, $location_types = null);
    }

    function testUpdateLocation() 
    {
        $contact = $this->_individual;
        $location_id = $this->_location[1]->id;
        
        $workPhone = array( 
                           'phone'       => '9833151944',
                           'phone_type'  => 'Phone');

        $workMobile = array(
                            'phone'           => '05446272571',
                            'phone_type'      => 'Mobile',
                            'mobile_provider' => 'Sprint');
        
        $phone = array ($workPhone, $workMobile);

        $workIMFirst = array('name'        => 'Rupam Jaiswal',
                             'provider_id' => '1',
                             'is_primary'  => FALSE);

        $workIMSecond = array('name'       => 'Rupam.Jaiswal',
                             'provider_id' => '3',
                             'is_primary'  => FALSE);
        
        $im = array ($workIMFirst, $workIMSecond);

        $params = array(
                        'supplemental_address_1' => 'Andheri',
                        'street_address'         => 'Linking Road',
                        'location_type'          => 'Main',
                        'city'                   => 'Pune',
                        'phone'                  => $phone,
                        'is_primary'             => 1,
                        'im'                     => $im
                        );  
        
        
        $this->_updateLocation1 = & crm_update_location(&$contact, $location_id, $params);

        
        //adding second location to contact
        $params1 = array(
                        'supplemental_address_1' => 'Andheri',
                        'location_type'          => 'Home',
                        'city'                   => 'pune',
                        'phone'                  => $phone,
                        'is_primary'             => 1,
                        'im'                     => $im
                        );  
        $this->_createLocation = & crm_create_location($contact, $params1);
        $location_id2 = $this->_createLocation->id;


        //modified phone,im for second location

        $workPhone2 = array( 
                           'phone'       => '9822040833',
                           'phone_type'  => 'Phone');

        $workMobile2 = array(
                            'phone'           => '07612272571',
                            'phone_type'      => 'Mobile',
                            'mobile_provider' => 'Sprint');
        
        $phone2 = array ($workPhone2, $workMobile2);

        $workIMFirst2 = array('name'        => 'ritu gautam',
                             'provider_id' => '1',
                             'is_primary'  => FALSE);

        $workIMSecond2 = array('name'       => 'nanda gupta',
                             'provider_id' => '3',
                             'is_primary'  => FALSE);
        
        $im2 = array ($workIMFirst2, $workIMSecond2);

        $params2 = array(
                        'supplemental_address_1' => 'Andheri_updated',
                        'location_type'          => 'Work',
                        'city'                   => 'pune_updated',
                        'phone'                  => $phone2,
                        'is_primary'             => 1,
                        'im'                     => $im2
                        );  
        $this->_updateLocation2 = & crm_update_location(&$contact, $location_id2, $params2);
    }

    function testdeleteLocation() 
    {
        $contact = $this->_individual;
        $location_id = $this->_updateLocation2->id;
        $del = crm_delete_location(&$contact, $location_id);
        $this->assertNull($del);
    }    
}