<?php

require_once 'api/crm.php';

class TestOfUpdateLocationAPI extends UnitTestCase 
{
    protected $_individual;
    protected $_household;
    protected $_organization;
    protected $_locationI = array();
    protected $_locationH = array();
    protected $_locationO = array();
    
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
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        
        $this->_individual = $contact;
    }
    function testCreateLocationIndividual()
    {
        $workPhone = array( 'phone' => '91-20-276048',
                            'phone_type'  => 'Phone');
        
        $workMobile = array('phone' => '91-20-9890848585',
                            'phone_type' => 'Mobile',
                            'mobile_provider' => 'Sprint');
        
        $workFax = array('phone' => '91-20-234-657686',
                         'phone_type' => 'Fax',
                         'is_primary' => TRUE);
        
        $phone     = array ($workPhone, $workMobile, $workFax);
        
        $workIMFirst = array('name' => 'mlzope',
                             'provider_id' => '1',
                             'is_primary' => FALSE);
        
        $workIMSecond = array('name' => 'mlzope',
                             'provider_id' => '3',
                             'is_primary' => FALSE);
        
        $workIMThird = array('name' => 'mlzope',
                             'provider_id' => '5',
                             'is_primary' => TRUE);
        
        $im = array ($workIMFirst, $workIMSecond, $workIMThird );
        
        $workEmailFirst = array('email' => 'manish@5.com');
        
        $workEmailSecond = array('email' => 'manish@hotmail.com');
        
        $workEmailThird = array('email' => 'manish@sify.com');
        
        $email = array($workEmailFirst, $workEmailSecond, $workEmailThird);
        
        $params = array('location_type'          => 'Work',
                        'phone'                  => $phone,
                        'city'                   => 'pune',
                        'country_id'             => 1001,
                        'supplemental_address_1' => 'Andheri',
                        'is_primary'             => 1,
                        'im'                     => $im,
                        'email'                  => $email
                        );
        
        $contact = $this->_individual;
        $newLocation =& crm_create_location($contact, $params);
        $this->assertIsA($newLocation, 'CRM_Core_BAO_Location');
        $this->_locationI['id'] = $newLocation->id;
        $this->assertEqual($newLocation->phone[3]->phone, '91-20-234-657686');
        $this->assertEqual($newLocation->phone[1]->phone_type, 'Phone');
        $this->assertNull($newLocation->phone[2]->mobile_provider_id, 'Sprint');
        $this->assertEqual($newLocation->im[1]->name, 'mlzope');
        $this->assertEqual($newLocation->im[2]->provider_id, 3);
        $this->assertEqual($newLocation->email[3]->email, 'manish@sify.com');
    }
    
    function testUpdateLocationIndividual()
    {
        $workPhone =array('phone' => '02327276048',
                          'phone_type' => 'Phone',
                          'Mobile_Provider' => 'Sprint');
        
        $phones = array ($workPhone);
        
        $workEmailFirst = array('email' => 'Anil@indiatimes.com');
        
        $workEmailSecond = array('email' => 'manish@hotmail.com');
        
        $emails = array($workEmailFirst,$workEmailSecond);
        
        $params = array(
                        'phone'            => $phones,
                        'city'             => 'Mumabi',
                        'email'            => $emails
                        );
        
        $contact = $this->_individual;
        $location =& crm_update_location($contact, $this->_locationI['id'], $params);
        
        $this->assertIsA($location, 'CRM_Core_BAO_Location');
        $this->assertEqual($location->phone[1]->phone, '02327276048');
        $this->assertEqual($location->phone[1]->phone_type, 'Phone');
        $this->assertEqual($location->email[1]->email, 'Anil@indiatimes.com');
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
        //$workPhone  = & new CRM_Core_DAO_Phone();
        $workPhone = array('phone' => '91-20-276048',
                           'phone_type' => 'Phone');
        
        //$workMobile =& new CRM_Core_DAO_Phone();
        $workMobile = array('phone' => '91-20-9890848585',
                            'phone_type' => 'Mobile',
                            'mobile_provider' => 'Sprint');
        
        //$workFax    =& new CRM_Core_DAO_Phone();
        $workFax = array('phone' => '91-20-234-657686',
                        'phone_type' => 'Fax',
                        'is_primary' => TRUE);
        
        $phone     = array ($workPhone, $workMobile, $workFax);
        
        //$workIMFirst  =& new CRM_Core_DAO_IM();
        $workIMFirst = array('name' => 'mlzope',
                            'provider_id' => '1',
                            'is_primary' => FALSE);
        
        //$workIMSecond =& new CRM_Core_DAO_IM();
        $workIMSecond = array('name' => 'mlzope',
                              'provider_id' => '3',
                              'is_primary' => FALSE);
        
        //$workIMThird  =& new CRM_Core_DAO_IM();
        $workIMThird = array('name' => 'mlzope',
                             'provider_id' => '5',
                             'is_primary' => TRUE);
        
        $im = array ($workIMFirst, $workIMSecond, $workIMThird );
        
        //$workEmailFirst  =& new CRM_Core_DAO_Email();
        $workEmailFirst = array( 'email' => 'manish@indiatimes.com');
        
        //$workEmailSecond =& new CRM_Core_DAO_Email();
        $workEmailSecond = array( 'email' => 'manish@hotmail.com');
        
        //$workEmailThird =& new CRM_Core_DAO_Email();
        $workEmailThird = array( 'email' => 'manish@sify.com');
        
        $email = array($workEmailFirst, $workEmailSecond, $workEmailThird);
        
        $params = array('location_type'          => 'Main',
                        'phone'                  => $phone,
                        'city'                   => 'pune',
                        'country_id'             => 1001,
                        'supplemental_address_1' => 'Andheri',
                        'is_primary'             => 1,
                        'im'                     => $im,
                        'email'                  => $email
                        );
        
        $contact = $this->_household;
        $newLocation =& crm_create_location($contact, $params);
        $this->assertIsA($newLocation, 'CRM_Core_BAO_Location');
        $this->_locationH['id'] = $newLocation->id;
        $this->assertEqual($newLocation->phone[3]->phone, '91-20-234-657686');
        $this->assertEqual($newLocation->phone[1]->phone_type, 'Phone');
        //$this->assertEqual($newLocation->phone[2]->mobile_provider_id, 1);
        $this->assertEqual($newLocation->im[1]->name, 'mlzope');
        $this->assertEqual($newLocation->im[2]->provider_id, 3);
        $this->assertEqual($newLocation->email[3]->email, 'manish@sify.com');
    }
    
    function testUpdateLocationHousehold()
    {
        $workPhone = array('phone' => '02327276048',
                           'phone_type' => 'Phone',
                           'mobile_provider' => 'Sprint');
        
        $phones = array ($workPhone);
        
        //$workEmailFirst  =& new CRM_Core_DAO_Email();
        // $workEmailFirst->email = 'Anil@indiatimes.com';
        
        //$workEmailSecond =& new CRM_Core_DAO_Email();
        //$workEmailSecond->email = 'manish@hotmail.com';
        $workEmailFirst = array( 'email' => 'Anil@indiatimes.com');
        $workEmailSecond = array( 'email' => 'manish@hotmail.com');
        $emails = array($workEmailFirst,$workEmailSecond);
        
        $params = array(
                        'phone'            => $phones,
                        'city'             => 'Mumabi',
                        'email'            => $emails
                        );
        
        
        $contact = $this->_household;
        
        $location =& crm_update_location($contact, $this->_locationH['id'], $params);
        $this->assertIsA($location, 'CRM_Core_BAO_Location');
        $this->assertEqual($location->phone[1]->phone, '02327276048');
        $this->assertEqual($location->phone[1]->phone_type, 'Phone');
        $this->assertEqual($location->email[1]->email, 'Anil@indiatimes.com');
    }
    
    //    Test cases for crm_update_location for Organization contact
    
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
        //$workPhone  = & new CRM_Core_DAO_Phone();
        $workPhone = array ('phone' => '91-20-276048',
                            'phone_type'  => 'Phone');
        
        //$workMobile =& new CRM_Core_DAO_Phone();
        $workMobile = array('phone' => '91-20-9890848585',
                            'phone_type' => 'Mobile',
                            'mobile_provider' => 'Sprint');
        
        //$workFax    =& new CRM_Core_DAO_Phone();
        $workFax = array('phone' => '91-20-234-657686',
                         'phone_type' => 'Fax',
                         'is_primary' => TRUE);
        
        $phone     = array ($workPhone, $workMobile, $workFax);
        
        //$workIMFirst  =& new CRM_Core_DAO_IM();
        $workIMFirst = array('name' => 'mlzope',
                             'provider_id' => '1',
                             'is_primary'  => FALSE);
        
        //$workIMSecond =& new CRM_Core_DAO_IM();
        $workIMSecond = array('name' => 'mlzope',
                              'provider_id' => '3',
                              'is_primary'  => FALSE);
        
        //$workIMThird  =& new CRM_Core_DAO_IM();
        $workIMThird = array('name' => 'mlzope',
                             'provider_id' => '5',
                             'is_primary'  => TRUE);
        
        $im = array ($workIMFirst, $workIMSecond, $workIMThird );
        
        //$workEmailFirst  =& new CRM_Core_DAO_Email();
        $workEmailFirst = array('email' => 'manish@indiatimes.com');
        
        //$workEmailSecond =& new CRM_Core_DAO_Email();
        $workEmailSecond = array('email' => 'manish@hotmail.com');
        
        //$workEmailThird =& new CRM_Core_DAO_Email();
        $workEmailThird = array('email' => 'manish@sify.com');
        
        $email = array($workEmailFirst, $workEmailSecond, $workEmailThird);
        
        $params = array('location_type'          => 'Main',
                        'phone'                  => $phone,
                        'city'                   => 'pune',
                        'country_id'             => 1001,
                        'supplemental_address_1' => 'Andheri',
                        'is_primary'             => 1,
                        'im'                     => $im,
                        'email'                  => $email
                        );
        
        $contact = $this->_organization;
        $newLocation =& crm_create_location($contact, $params);
        $this->assertIsA($newLocation, 'CRM_Core_BAO_Location');
        $this->_locationO['id'] = $newLocation->id;
        $this->assertEqual($newLocation->phone[3]->phone, '91-20-234-657686');
        $this->assertEqual($newLocation->phone[1]->phone_type, 'Phone');
        //$this->assertEqual($newLocation->phone[2]->mobile_provider_id, 1);
        $this->assertEqual($newLocation->im[1]->name, 'mlzope');
        $this->assertEqual($newLocation->im[2]->provider_id, 3);
        $this->assertEqual($newLocation->email[3]->email, 'manish@sify.com');
    }
    
    function testUpdateLocationOrganization()
    {
        //$workPhone  = & new CRM_Core_DAO_Phone();
        $workPhone = array('phone' => '02327276048',
                           'phone_type' => 'Phone',
                           'mobile_provider' => 'Sprint');
        
        $phones = array ($workPhone);
        
        //$workEmailFirst  =& new CRM_Core_DAO_Email();
        $workEmailFirst =array('email' => 'Anil@indiatimes.com');
        
        //$workEmailSecond =& new CRM_Core_DAO_Email();
        $workEmailSecond = array('email' => 'manish@hotmail.com');
        
        $emails = array($workEmailFirst,$workEmailSecond);
        
        $params = array(
                        'phone'            => $phones,
                        'city'             => 'Mumabi',
                        'email'            => $emails
                        );
        
        $contact = $this->_organization;
        $location =& crm_update_location($contact, $this->_locationO['id'], $params);
        $this->assertIsA($location, 'CRM_Core_BAO_Location');
        $this->assertEqual($location->phone[1]->phone, '02327276048');
        $this->assertEqual($location->phone[1]->phone_type, 'Phone');
        $this->assertEqual($location->email[1]->email, 'Anil@indiatimes.com');
    }
    
    
     // Deleting the Data creatd for the test cases.
    function testDeleteLocationIndividual()
    {
        foreach ($this->_locationI as $locationType) {
            $contact  = $this->_individual;
            $location_type = $locationType; 
            $result =& crm_delete_location($contact, $location_type);
            $this->assertNull($result);
        }
    }
    
    function testDeleteLocationHousehold()
    {
        foreach ($this->_locationH as $locationType) {
            $contact  = $this->_household;
            $location_type = $locationType; 
            $result =& crm_delete_location($contact, $location_type);
        }
    }
    
    function testDeleteLocationOrganization()
    {
        foreach ($this->_locationO as $locationType) {
            $contact  = $this->_organization;
            $location_type = $locationType; 
            $result =& crm_delete_location($contact, $location_type);
        }
    }
    
    function testDeleteIndividual()
    {
        $contact  = $this->_individual;
        $result =& crm_delete_contact($contact);
        $this->assertNull($result);
    }
    
    function testDeleteHousehold()
    {
        $contact  = $this->_household;
        $result =& crm_delete_contact($contact);
        $this->assertNull($result);
    }

    function testDeleteOrganization()
    {
        $contact  = $this->_organization;
        $result =& crm_delete_contact($contact);
        $this->assertNull($result);
    }
}


