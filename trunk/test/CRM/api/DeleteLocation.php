<?php

require_once 'api/crm.php';

class TestOfDeleteLocationAPI extends UnitTestCase 
{
    protected $_individual;
    protected $_location;
    
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
        /*
        $workIMFirst  =& new CRM_Core_DAO_IM();
        $workIMFirst->name = 'mlzope';
        $workIMFirst->provider_id    = 'Yahoo';
        $workIMFirst->is_primary    = FALSE;
        
        $workIMSecond =& new CRM_Core_DAO_IM();
        $workIMSecond->name = 'mlzope';
        $workIMSecond->provider_id    = 'AIM';
        $workIMSecond->is_primary    = FALSE;
        
        $workIMThird  =& new CRM_Core_DAO_IM();
        $workIMThird->name = 'mlzope';
        $workIMThird->provider_id    = 'Indiatimes';
        $workIMThird->is_primary    = TRUE;
        
        $im = array ($workIMFirst, $workIMSecond, $workIMThird );
        */
        $workEmailFirst  =& new CRM_Core_DAO_Email();
        $workEmailFirst->email = 'manish@indiatimes.com';
        
        $workEmailSecond =& new CRM_Core_DAO_Email();
        $workEmailSecond->email = 'manish@hotmail.com';
        
        $workEmailThird =& new CRM_Core_DAO_Email();
        $workEmailThird->email = 'manish@sify.com';
        
        $email = array($workEmailFirst, $workEmailSecond, $workEmailThird);
        
        $params = array('location_type'          => 'Work',
                        'phone'                  => $phone,
                        'city'                   => 'pune',
                        'country_id'             => 1001,
                        'supplemental_address_1' => 'Andheri',
                        'is_primary'             => 1,
                        //'im'                     => $im,
                        'email'                  => $email
                        );
        
        $contact = $this->_individual;
        $newLocation =& crm_create_location($contact, $params);
        $this->assertIsA($newLocation, 'CRM_Core_BAO_Location');
        //$this->_location = $params['location_type'];
        $this->_location[$newLocation->id] = $params['location_type'];
        
        $this->assertEqual($newLocation->phone[3]->phone, '91-20-234-657686');
        $this->assertEqual($newLocation->phone[1]->phone_type, 'Phone');
        //$this->assertEqual($newLocation->phone[2]->mobile_provider_id, 1);
        //$this->assertEqual($newLocation->im[1]->name, 'mlzope');
        //$this->assertEqual($newLocation->im[2]->provider_id, 3);
        $this->assertEqual($newLocation->email[3]->email, 'manish@sify.com');
    }
    
    function testDeleteLocationIndividual()
    {
        foreach ($this->_location as  $locationType) {
            $newLocation   =& crm_delete_location($this->_individual, $locationType);
            $this->assertNull($newLocation);
        }
    }
    
    function testDeleteLocationIndividualErrorRepeatLocationType()
    {
        foreach ($this->_location as  $locationType) {
            $newLocation   =& crm_delete_location($this->_individual, $locationType);
            $this->assertNull($newLocation);
        }
    }
    
    function testDeleteContactIndividual()
    {
        $contact  = $this->_individual;
        $result =& crm_delete_contact($contact);
        $this->assertNull($result);
    }
}

?>