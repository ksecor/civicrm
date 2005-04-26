<?php

require_once 'api/crm.php';

class TestOfDeleteLocationAPI extends UnitTestCase 
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

/* Test cases for crm_update_location for Individual contact */ 

    function testCreateIndividual()
    {
        $params  = array('first_name'    => 'Manish',
                         'last_name'     => 'Zope',
                         'location_type' => 'Main',
                         'email'         => 'manish@yahoo.com'
                         );
        $contact =& crm_create_contact($params, 'Individual');
        
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact'                          );
        $this->assertEqual($contact->contact_type, 'Individual'                       );
        $this->assertEqual($contact->contact_type_object->display_name, 'Manish  Zope');
        $this->assertEqual($contact->location[1]->email[1]->email, 'manish@yahoo.com' );
        $this->_individual = $contact;
    }
    
    function testCreateLocationIndividual()
    {
        $workPhone   = new Phone('91-20-2345678', false, 'Phone'           );
        $workMobile  = new Phone('91-20-989090098988', true, 'Mobile', null);
        $workFax     = new Phone('91-20-234-657686', false, 'Fax'          );
        $phones      = array ($workPhone, $workMobile, $workFax);
        
        $params      = array('location_type' => 'Work',
                             'phone'         => $phones,
                             'city'          => 'pune'
                             );
        
        $contact     = $this->_individual;
        $newLocation =& crm_create_location($contact, $params);
        
        $this->assertIsA($newLocation, 'CRM_Contact_DAO_Location'                          );
        $this->assertEqual($newLocation->location[2]->phone[2]->phone, '91-20-989090098988');
        $this->assertEqual($newLocation->location[2]->phone[2]->phone_type, 'mobile'       );
        $this->assertNull($newLocation->location[2]->phone[2]->mobile_provider_id          );
    }

    function testDeleteLocationIndividualError()
    {
        $location_type = array('Home');
        
        $newLocation   = crm_delete_location(&$this->_individual, $location_type);
        $this->assertIsA($newLocation, 'CRM_Error');
    }

    function testDeleteLocationIndividual()
    {
        $location_type = array('Main');
        
        $newLocation   = crm_delete_location(&$this->_individual, $location_type);
        $this->assertNull($newLocation);
    }

/* Test cases for crm_update_location for Household contact */

    function testCreateContactHousehold() 
    {
        $params  = array('household_name' => 'Zope House',
                         'nick_name'      => 'zope villa',
                         'location_type'  => 'Work'
                         );
        $contact =& crm_create_contact($params, 'Household');
        
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact'  );
        $this->assertEqual($contact->contact_type, 'Household');
        $this->_household = $contact;
    }
    
    function testCreateLocationHousehold()
    {
        $homeMobile  = new Phone('91-20-989090098988', false, 'Mobile', null);
        $homePhone   = new Phone('91-20-2345678', true, 'Phone'             );
        $homePager   = new Phone('91-20-234-657686', false, 'Pager'         );
        $phones      = array ($homeMobile, $homePhone, $homePager);
        $params      = array('location_type' => 'Home',
                            'phone'         => $phones,
                            'city'          => 'pune'
                            );
        $contact     = $this->_household;
        $newLocation =& crm_create_location($contact, $params);
        
        $this->assertIsA($newLocation, 'CRM_Contact_DAO_Location'                        );
        $this->assertEqual($newLocation->location[2]->phone[3]->phone, '91-20-234-657686');
        $this->assertEqual($newLocation->location[2]->phone[1]->phone_type, 'mobile'     );
        $this->assertNull($newLocation->location[2]->phone[1]->mobile_provider_id        );
    }
    
    function testDeleteLocationHousehold()
    {
        $location_type = array('Work');
        
        $newLocation   = crm_delete_location(&$this->_household, $location_type);
        $this->assertNull($newLocation);
    }

/* Test cases for crm_update_location for Organization contact */

    function testCreateContactOrganization( ) 
    {
        $params  = array('organization_name' => 'Zope Pvt Ltd', 
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
        $mainMobile  = new Phone('91-20-989090098988', false, 'Mobile', null);
        $mainPager   = new Phone('91-20-234-657686', true, 'Pager'          );
        $mainPhone   = new Phone('91-20-2345678', false, 'Phone'            );
        $phones      = array ($mainMobile, $mainPager, $mainPhone);
        
        $params      = array('location_type' => 'Main',
                             'phone'         => $phones,
                             'city'          => 'pune'
                             );
        $contact     = $this->_organization;
        $newLocation =& crm_create_location($contact, $params);
        
        $this->assertIsA($newLocation, 'CRM_Contact_DAO_Location'                          );
        $this->assertEqual($newLocation->location[2]->phone[1]->phone, '91-20-989090098988');
        $this->assertEqual($newLocation->location[2]->phone[3]->phone_type, 'Phone'        );
        $this->assertNull($newLocation->location[2]->phone[1]->mobile_provider_id          );
    }

    function testDeleteLocationOrganization()
    {
        $location_type = array('Main');
        
        $newLocation   = crm_delete_location(&$this->_organization, $location_type);
        $this->assertNull($newLocation);
    }
    
    function testGetLocationOrganizationError()
    {
        $location_types = array ('Main');
        $contact        = $this->_organization;
        
        $newlocation    =& crm_get_locations(&$contact, $location_types);
        
        $this->assertIsA($newLocation, 'CRM_Error');
    }
?>