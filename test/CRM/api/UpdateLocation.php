<?php

require_once 'api/crm.php';

class TestOfUpdateLocationAPI extends UnitTestCase 
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
    
    function testUpdateLocationIndivdualError()
    {
        $params        = array('city' => 'whatever');
        $location_type = 'Home';
        $newLocation   =& crm_update_location(&$this->_individual, $location_type, $params);
        $this->assertIsA($newLocation, 'CRM_Error');
    }
    
    function testUpdateLocationIndividual()
    {
        $workPhone     = new Phone('91-20-9876543', false, 'Phone'           );
        $workMobile    = new Phone('91-20-989090098988', true, 'Mobile', null);
        $workFax       = new Phone('91-22-123-876543', false, 'Fax'          );
        $phones        = array ($workPhone, $workMobile, $workFax);
        
        $params        = array('phone' => $phones, 
                               'city'  => 'Pune'
                               ); 
        $return_types  = array('location_type', 'state_province', 'phone');
        $location_type = 'Work';
        
        $newLocation   =& crm_update_location(&$this->_individual, $location_type, $params, $return_types);

        $this->assertIsA($newLocation, 'CRM_Contact_DAO_Location'                     );
        $this->assertNull($newLocation->location[2]->state_province                   );
        $this->assertEqual($newLocation->location[2]->phone[1]->phone, '91-20-9876543');
        $this->assertEqual($newLocation->location[2]->phone[3]->phone_type, 'Fax'     );
        $this->assertNull($newLocation->location[2]->phone[2]->mobile_provider_id     );
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
    
    function testUpdateLocationHousehold()
    {
        $workMobile    = new Phone('91-20-989094428380', false, 'Mobile', null);
        $workPhone     = new Phone('91-20-2345678', true, 'Phone'             );
        $workFax       = new Phone('91-20-234-657686', false, 'Fax'           );
        $phones        = array ($workMobile, $workPhone, $workPager);
        $params        = array('phone' => $phones, 
                               'city'  => 'Pune'
                               ); 
        $return_types  = array('location_type', 'state_province', 'phone', 'city');
        $location_type = 'Work';
        
        $newLocation =& crm_update_location(&$this->_individual, $location_type, $params, $return_types);

        $this->assertIsA($newLocation, 'CRM_Contact_DAO_Location'                          );
        $this->assertNull($newLocation->location[2]->state_province_id                     );
        $this->assertEqual($newLocation->location[2]->phone[1]->phone, '91-20-989094428380');
        $this->assertEqual($newLocation->location[2]->phone[3]->phone_type, 'Fax'          );
        $this->assertNull($newLocation->location[2]->phone[1]->mobile_provider_id          );
        $this->assertNull($newLocation->location[2]->city, 'Pune'                          );
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

    function testUpdateLocationOrganization()
    {
        $homeFax     = new Phone('91-22-234-657686', true, 'Fax'            );
        $homePhone   = new Phone('91-22-2345678', false, 'Phone'            );
        $homeMobile  = new Phone('91-22-934572837444', false, 'Mobile', null);
        $phones      = array ($homeFax, $homePhone, $homeMobile);
        
        $params        = array('phone' => $phones, 
                               'city'  => 'Pune'
                               ); 
        $return_types  = array('location_type', 'state_province', 'phone', 'city');
        $location_type = 'Home';
        
        $newLocation =& crm_update_location(&$this->_individual, $location_type, $params, $return_types);
        
        $this->assertIsA($newLocation, 'CRM_Contact_DAO_Location'                        );
        $this->assertNull($newLocation->location[2]->state_province_id                   );
        $this->assertEqual($newLocation->location[2]->phone[1]->phone, '91-22-234-657686');
        $this->assertEqual($newLocation->location[2]->phone[2]->phone_type, 'Phone'      );
        $this->assertNull($newLocation->location[2]->phone[3]->mobile_provider_id        );
        $this->assertNull($newLocation->location[2]->city, 'Pune'                        );
    }
}

?>