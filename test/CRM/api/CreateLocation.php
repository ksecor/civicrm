<?php

require_once 'api/crm.php';

class TestOfCreateLocationAPI extends UnitTestCase 
{
    protected $_individual;
    protected $_household;
    protected $_organization;

    function setUp() 
    {
    }

    function tearDown() 
    {
    }

/* Test cases for crm_create_location for Individual contact */ 

    function testCreateIndividual()
    {
        $params = array('first_name'    => 'Manish',
                        'last_name'     => 'Zope',
                        'location_type' => 'Main',
                        'email'         => 'manish@yahoo.com'
                        );
        $contact =& crm_create_contact($params, 'Individual');
        
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->contact_type, 'Individual');
        $this->assertEqual($contact->contact_type_object->display_name, 'Manish  Zope');
        $this->assertEqual($contact->location[1]->email[1]->email, 'manish@yahoo.com');
        $this->_individual = $contact;
        //print_r($this->_individual);
    }

    function testCreateLocationIndividualError()
    {
        $params = array('location_type' => 'Main',
                        'im_provider'   => 'AIM',
                        'im_name'       => 'mlzope'
                        );
        $contact = $this->_individual;
        $newLocation =& crm_create_location($contact, $params);
        
        $this->assertIsA($newLocation, 'CRM_Error');
    }
    
    function testCreateLocationIndividual()
    {
        $workPhone  = new Phone('91-20-2345678', false, 'Phone'           );
        $workMobile = new Phone('91-20-989090098988', true, 'Mobile', null);
        $workFax    = new Phone('91-20-234-657686', false, 'Fax'          );
        $phones     = array ($workPhone, $workMobile, $workFax);
        
        /* following commented code is for adding the IM and Email to the location */
        /*
        $workIMFirst  = new IM('mlzope', true, 'Yahoo'      );
        $workIMSecond = new IM('mlzope', false, 'AIM'       );
        $workIMThird  = new IM('mlzope', false, 'Indiatimes');
        $im = array ($workIMFirst, $workIMSecond, $workIMThird );
        
        $workEmailFirst  = new Email('manish@indiatimes.com', false);
        $workEmailSecond = new Email('manish@hotmail.com', false   );
        $workEmailThird  = new Email('manish@lycos.com', true      );
        $emails = array($workEmailFirst, $workEmailSecond, $workEmailThird);
        */
        $params = array('location_type' => 'Work',
                        'phone'         => $phones,
                        'city'          => 'pune'
                        );
                        /*
                        'im'            => $im,
                        'email'         => $emails
                        );*/
        
        $contact = $this->_individual;
        $newLocation =& crm_create_location($contact, $params);
        
        $this->assertIsA($newLocation, 'CRM_Contact_DAO_Location');
        $this->assertEqual($newLocation->location[2]->phone[2]->phone, '91-20-989090098988');
        $this->assertEqual($newLocation->location[2]->phone[2]->phone_type, 'mobile');
        $this->assertNull($newLocation->location[2]->phone[2]->mobile_provider_id);
        //$this->assertEqual($newLocation->location[2]->im[1]->name, '');
        //$this->assertEqual($newLocation->location[2]->im[1]->provider_id, '1');
        //$this->assertEqual($newLocation->location[2]->email[3]->email, 'manish@lycos.com');
    }

/* Test cases for crm_create_location for Household contact */

    function testCreateHousehold() 
    {
        $params = array('household_name' => 'Zope House',
                        'nick_name'      => 'zope villa',
                        'location_type'  => 'Work'
                        );
        $contact =& crm_create_contact($params, 'Household');
        
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->contact_type, 'Household');
        $this->_household = $contact;
    }
    
    function testCreateLocationHousehold()
    {
        $homeMobile = new Phone('91-20-989090098988', false, 'Mobile', null);
        $homePhone  = new Phone('91-20-2345678', true, 'Phone'             );
        $homePager  = new Phone('91-20-234-657686', false, 'Pager'         );
        $phones     = array ($homeMobile, $homePhone, $homePager);
        
        $params = array('location_type' => 'Home',
                        'phone'         => $phones,
                        'city'          => 'pune'
                        );
        $contact = $this->_household;
        $newLocation =& crm_create_location($contact, $params);
        
        $this->assertIsA($newLocation, 'CRM_Contact_DAO_Location');
        $this->assertEqual($newLocation->location[2]->phone[3]->phone, '91-20-234-657686');
        $this->assertEqual($newLocation->location[2]->phone[1]->phone_type, 'mobile');
        $this->assertNull($newLocation->location[2]->phone[1]->mobile_provider_id);
    }
    
    function testCreateLocationHouseholdError()
    {
        $params = array('location_type' => 'Home',
                        'city'          => 'whatever'
                        );
        $contact = $this->_household;
        $newLocation =& crm_create_location($contact, $params);
        
        $this->assertIsA($newLocation, 'CRM_Error');
    }

/* Test cases for crm_create_location for Organization contact */

    function testCreateOrganization( ) 
    {
        $params = array('organization_name' => 'Zope Pvt Ltd', 
                        'nick_name'         => 'zope companies', 
                        'location_type'     => 'Home'
                        );
        $contact =& crm_create_contact($params, 'Organization');
        
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->contact_type, 'Organization');
        $this->_organization = $contact;
    }
    
    function testCreateLocationOrganization()
    {
        $mainMobile = new Phone('91-20-989090098988', false, 'Mobile', null);
        $mainPager  = new Phone('91-20-234-657686', true, 'Pager'         );
        $mainPhone  = new Phone('91-20-2345678', false, 'Phone'            );
        $phones     = array ($mainMobile, $mainPager, $mainPhone);
        
        $params = array('location_type' => 'Main',
                        'phone'         => $phones,
                        'city'          => 'pune'
                        );
        $contact = $this->_organization;
        $newLocation =& crm_create_location($contact, $params);
        
        $this->assertIsA($newLocation, 'CRM_Contact_DAO_Location');
        $this->assertEqual($newLocation->location[2]->phone[1]->phone, '91-20-989090098988');
        $this->assertEqual($newLocation->location[2]->phone[3]->phone_type, 'Phone');
        $this->assertNull($newLocation->location[2]->phone[1]->mobile_provider_id);
    }
        
    function testCreateLocationOrganization()
    {
        $workMobile = new Phone('91-20-989090098988', false, 'Mobile', null);
        $workPager  = new Phone( '91-20-234-657686', true, 'Pager'         );
        $workPhone  = new Phone('91-20-2345678', false, 'Phone'            );
        
        $phones     = array ($workMobile, $workPager, $workPhone);
        
        $params = array('location_type' => 'Work',
                        'phone'         => $phones,
                        'city'          => 'pune'
                        );
        $contact = $this->_organization;
        $newLocation =& crm_create_location($contact, $params);
        
        $this->assertIsA($newLocation, 'CRM_Contact_DAO_Location');
        $this->assertEqual($newLocation->location[3]->phone[1]->phone, '91-20-989090098988');
        $this->assertEqual($newLocation->location[3]->phone[3]->phone_type, 'Phone');
        $this->assertNull($newLocation->location[3]->phone[1]->mobile_provider_id);
    }
    
    function testCreateLocationOrganizationError()
    {
        $params = array('location_type' => 'Work',
                        'city'          => 'whatever'
                        );
        $contact = $this->_organization;
        $newLocation =& crm_create_location($contact, $params);
        
        $this->assertIsA($newLocation, 'CRM_Error');
    }

}
?>