<?php

require_once 'api/crm.php';

class TestOfCreateLocationAPI extends UnitTestCase 
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
    
    function testCreateIndividual()
    {
        $params = array(
                        'first_name'    => 'Manish01',
                        'last_name'     => 'Zope01',
                        'location_type' => 'Home',
                        'email'         => 'manish@yahoo.com'
                        );
        $contact =& crm_create_contact($params, 'Individual');
        
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->_individual = $contact;
    }
    /*
    function testCreateLocationIndividualNULL()
    {
        $contact = $this->_individual;
        $params = array ('location_type' => 'Home');
        $location =& crm_create_location($contact, $params);
        
        $this->assertNull($location);
    }
    
    function testCreateLocationIndividual01()
    {
        $contact = $this->_individual;
        $params = array (
                         'location_type' => 'Home',
                         'city'          => 'pune',
                         );
        $location =& crm_create_location($contact, $params);
        
        $this->assertIsA($location, 'CRM_Core_BAO_Location');
        $this->_locationI[$location->id] = $params['location_type'];
    }
    */
    /*function testCreateLocationIndividual02()
    {
        $workPhone  = & new CRM_Core_DAO_Phone();
        $workPhone->phone       = '91-20-276048';
        $workPhone->phone_type  = 'Phone';
        
        $workMobile =& new CRM_Core_DAO_Phone();
        $workMobile->phone           = '91-20-9890848585';
        $workMobile->phone_type      = 'Mobile';
        $workMobile->mobile_provider = 'Sprint';
        
        $workFax    =& new CRM_Core_DAO_Phone();
        $workFax->phone         = '91-20-234-657686';
        $workFax->phone_type    = 'Fax';
        $workFax->is_primary    = TRUE;
        
        $phone     = array ($workPhone, $workMobile, $workFax);
        
        $workIMFirst  =& new CRM_Core_DAO_IM();
        $workIMFirst->name = 'mlzope';
        $workIMFirst->provider_id    = '1';
        $workIMFirst->is_primary    = FALSE;
        
        $workIMSecond =& new CRM_Core_DAO_IM();
        $workIMSecond->name = 'mlzope';
        $workIMSecond->provider_id    = '3';
        $workIMSecond->is_primary    = FALSE;
        
        $workIMThird  =& new CRM_Core_DAO_IM();
        $workIMThird->name = 'mlzope';
        $workIMThird->provider_id    = '5';
        $workIMThird->is_primary    = TRUE;
        
        $im = array ($workIMFirst, $workIMSecond, $workIMThird );
        
        $workEmailFirst  =& new CRM_Core_DAO_Email();
        $workEmailFirst->email = 'manish@5.com';
        
        $workEmailSecond =& new CRM_Core_DAO_Email();
        $workEmailSecond->email = 'manish@hotmail.com';
        
        $workEmailThird =& new CRM_Core_DAO_Email();
        $workEmailThird->email = 'manish@sify.com';
        
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
        
        $contact = $this->_individual;
        $newLocation =& crm_create_location($contact, $params);
        $this->assertIsA($newLocation, 'CRM_Core_BAO_Location');
        $this->_locationI[$newLocation->id] = $params['location_type'];
        
        $this->assertEqual($newLocation->phone[3]->phone, '91-20-234-657686');
        $this->assertEqual($newLocation->phone[1]->phone_type, 'Phone');
        $this->assertNull($newLocation->phone[2]->mobile_provider_id, 'Sprint');
        $this->assertEqual($newLocation->im[1]->name, 'mlzope');
        $this->assertEqual($newLocation->im[2]->provider_id, 3);
        $this->assertEqual($newLocation->email[3]->email, 'manish@sify.com');
    }*/
    
    function testCreateLocationIndividual02()
    {
        //$workPhone  = & new CRM_Core_DAO_Phone();
        $workPhone = array( 'phone' => '91-20-276048',
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
        $workEmailFirst = array('email' => 'manish@5.com');
        
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
        
        $contact = $this->_individual;
        $newLocation =& crm_create_location($contact, $params);
        CRM_Core_Error::debug('Location', $newLocation);
        $this->assertIsA($newLocation, 'CRM_Core_BAO_Location');
        $this->_locationI[$newLocation->id] = $params['location_type'];
        
        $this->assertEqual($newLocation->phone[3]->phone, '91-20-234-657686');
        $this->assertEqual($newLocation->phone[1]->phone_type, 'Phone');
        $this->assertNull($newLocation->phone[2]->mobile_provider_id, 'Sprint');
        $this->assertEqual($newLocation->im[1]->name, 'mlzope');
        $this->assertEqual($newLocation->im[2]->provider_id, 3);
        $this->assertEqual($newLocation->email[3]->email, 'manish@sify.com');
    }
    // Test cases for crm_create_location for Household contact 
    /*
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
        $workPhone  = & new CRM_Core_DAO_Phone();
        $workPhone->phone       = '91-20-276048';
        $workPhone->phone_type  = 'Phone';
        
        $workMobile =& new CRM_Core_DAO_Phone();
        $workMobile->phone           = '91-20-9890848585';
        $workMobile->phone_type      = 'Mobile';
        $workMobile->mobile_provider = 'Sprint';
        
        $workFax    =& new CRM_Core_DAO_Phone();
        $workFax->phone         = '91-20-234-657686';
        $workFax->phone_type    = 'Fax';
        $workFax->is_primary    = TRUE;
        
        $phone     = array ($workPhone, $workMobile, $workFax);
        
        $workIMFirst  =& new CRM_Core_DAO_IM();
        $workIMFirst->name = 'mlzope';
        $workIMFirst->provider_id    = '1';
        $workIMFirst->is_primary    = FALSE;
        
        $workIMSecond =& new CRM_Core_DAO_IM();
        $workIMSecond->name = 'mlzope';
        $workIMSecond->provider_id    = '3';
        $workIMSecond->is_primary    = FALSE;
        
        $workIMThird  =& new CRM_Core_DAO_IM();
        $workIMThird->name = 'mlzope';
        $workIMThird->provider_id    = '5';
        $workIMThird->is_primary    = TRUE;
        
        $im = array ($workIMFirst, $workIMSecond, $workIMThird );
        
        $workEmailFirst  =& new CRM_Core_DAO_Email();
        $workEmailFirst->email = 'manish@5.com';
        
        $workEmailSecond =& new CRM_Core_DAO_Email();
        $workEmailSecond->email = 'manish@hotmail.com';
        
        $workEmailThird =& new CRM_Core_DAO_Email();
        $workEmailThird->email = 'manish@sify.com';
        
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
        $this->_locationH[$newLocation->id] = $params['location_type'];
        
        $this->assertEqual($newLocation->phone[3]->phone, '91-20-234-657686');
        $this->assertEqual($newLocation->phone[1]->phone_type, 'Phone');
        $this->assertNull($newLocation->phone[2]->mobile_provider_id, 'Sprint');
        $this->assertEqual($newLocation->im[1]->name, 'mlzope');
        $this->assertEqual($newLocation->im[2]->provider_id, 3);
        $this->assertEqual($newLocation->email[3]->email, 'manish@sify.com');
    }
    
    // Test cases for crm_create_location for Organization contact 

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
        $workPhone  = & new CRM_Core_DAO_Phone();
        $workPhone->phone       = '91-20-276048';
        $workPhone->phone_type  = 'Phone';
        
        $workMobile =& new CRM_Core_DAO_Phone();
        $workMobile->phone           = '91-20-9890848585';
        $workMobile->phone_type      = 'Mobile';
        $workMobile->mobile_provider = 'Sprint';
        
        $workFax    =& new CRM_Core_DAO_Phone();
        $workFax->phone         = '91-20-234-657686';
        $workFax->phone_type    = 'Fax';
        $workFax->is_primary    = TRUE;
        
        $phone     = array ($workPhone, $workMobile, $workFax);
        
        $workIMFirst  =& new CRM_Core_DAO_IM();
        $workIMFirst->name = 'mlzope';
        $workIMFirst->provider_id    = '1';
        $workIMFirst->is_primary    = FALSE;
        
        $workIMSecond =& new CRM_Core_DAO_IM();
        $workIMSecond->name = 'mlzope';
        $workIMSecond->provider_id    = '3';
        $workIMSecond->is_primary    = FALSE;
        
        $workIMThird  =& new CRM_Core_DAO_IM();
        $workIMThird->name = 'mlzope';
        $workIMThird->provider_id    = '5';
        $workIMThird->is_primary    = TRUE;
        
        $im = array ($workIMFirst, $workIMSecond, $workIMThird );
        
        $workEmailFirst  =& new CRM_Core_DAO_Email();
        $workEmailFirst->email = 'manish@5.com';
        
        $workEmailSecond =& new CRM_Core_DAO_Email();
        $workEmailSecond->email = 'manish@hotmail.com';
        
        $workEmailThird =& new CRM_Core_DAO_Email();
        $workEmailThird->email = 'manish@sify.com';
        
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
        $this->_locationO[$newLocation->id] = $params['location_type'];
        
        $this->assertEqual($newLocation->phone[3]->phone, '91-20-234-657686');
        $this->assertEqual($newLocation->phone[1]->phone_type, 'Phone');
        $this->assertNull($newLocation->phone[2]->mobile_provider_id, 'Sprint');
        $this->assertEqual($newLocation->im[1]->name, 'mlzope');
        $this->assertEqual($newLocation->im[2]->provider_id, 3);
        $this->assertEqual($newLocation->email[3]->email, 'manish@sify.com');
    }
    */
    // Deleting the Data creatd for the test cases.
    function testDeleteLocationIndividual()
    {
        foreach ($this->_locationI as $locationType) {
            $contact  = $this->_individual;
            $location_type = $locationType; 
            $result =& crm_delete_location($contact, $location_type);
        }
    }
    /*
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
    */
    function testDeleteIndividual()
    {
        $contact  = $this->_individual;
        $result =& crm_delete_contact($contact);
        $this->assertNull($result);
    }
    /*
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
        }*/
}
?>