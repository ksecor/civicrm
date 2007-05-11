<?php

require_once 'api/v2/Location.php';
require_once 'api/v2/Contact.php';

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
                        'first_name'    => 'Maniwwwsh01',
                        'last_name'     => 'Zpe',
                        'location_type' => 'Home',
                        'email'         => 'manggihkk@y.com',
                        'contact_type'  => 'Individual'
                        );
        $contact =& civicrm_contact_add($params);
        $this->assertEqual( $contact['is_error'], 0  );
        $this->_individual = $contact;
    }

  
    function testCreateLocationIndividualNULL()
    {
       
        $params = array ('location_type' => 'Home',
                         'contact_id'    => $this->_individual['contact_id']);
        $location =& civicrm_location_add( $params );
               
        $this->assertNull($location);
    }
          
   function testCreateLocationIndividual01()
    {
        $params = array (
                         'location_type' => 'Work',
                         'city'          => 'pune',
                         'contact_id'    => $this->_individual['contact_id']
                         );
        $location =& civicrm_location_add( $params );
        
        $this->assertIsA($location, 'CRM_Core_BAO_Location');
        $this->_locationI[$location->id] = $params['location_type'];
    }
    
    
   
    function testCreateLocationIndividual02()
    {
        //$workPhone  = & new CRM_Core_DAO_Phone();
        $workPhone = array( 'phone'      => '91-20-276048',
                            'phone_type' => 'Phone'
                            );
        
        //$workMobile =& new CRM_Core_DAO_Phone();
        $workMobile = array('phone'           => '91-20-9890848585',
                            'phone_type'      => 'Mobile',
                            'mobile_provider' => 'Sprint'
                            );
        
        //$workFax    =& new CRM_Core_DAO_Phone();
        $workFax = array('phone'      => '91-20-234-657686',
                         'phone_type' => 'Fax',
                         'is_primary' => TRUE
                         );
        
        $phone     = array ($workPhone, $workMobile, $workFax);
        
        //$workIMFirst  =& new CRM_Core_DAO_IM();
        $workIMFirst = array('name'        => 'mlzope',
                             'provider_id' => '1',
                             'is_primary'  => FALSE
                             );
        
        //$workIMSecond =& new CRM_Core_DAO_IM();
        $workIMSecond = array('name'       => 'mlzope',
                             'provider_id' => '3',
                             'is_primary'  => FALSE
                              );
        
        //$workIMThird  =& new CRM_Core_DAO_IM();
        $workIMThird = array('name'        => 'mlzope',
                             'provider_id' => '5',
                             'is_primary'  => TRUE
                             );
        
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
                        'email'                  => $email,
                        'contact_id'             => $this->_individual['contact_id']
                        );
        
        $newLocation =& civicrm_location_add( $params );
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
    
    function testCreateHousehold() 
    {
        $params = array('household_name' => 'Zope House',
                        'nick_name'      => 'zope villa',
                        'location_type'  => 'Work',
                        'contact_type'   => 'Household'
                        );
        $contact =& civicrm_contact_add( $params );
        
        $this->assertEqual( $contact['is_error'], 0  );
        $this->_household = $contact;
    }
    
    
    
    // Test cases for crm_create_location for Organization contact 
    function testCreateLocationHousehold()
    {
        //$workPhone  = & new CRM_Core_DAO_Phone();
        $workPhone = array('phone'       => '91-20-276048',
                           'phone_type'  => 'Phone'
                           );
        
        // $workMobile =& new CRM_Core_DAO_Phone();
        $workMobile = array('phone'           => '91-20-9890848585',
                            'phone_type'      => 'Mobile',
                            'mobile_provider' => 'Sprint'
                            );
  
        $workFax    = array('phone'      => '91-20-234-657686',
                            'phone_type' => 'Fax',
                            'is_primary' =>' TRUE'
                            );
        
        $phone     = array ($workPhone, $workMobile, $workFax);
        
        $workIMFirst  =array('name'        => 'mlzope',
                             'provider_id' => '1',
                             'is_primary'  => FALSE
                             );
        
        $workIMSecond =array('name'        => 'mlzope',
                             'provider_id' => '3',
                             'is_primary'  => FALSE
                             );
        
        $workIMThird  =array('name'        => 'mlzope',
                             'provider_id' => '5',
                             'is_primary'  => TRUE
                             );
        
        $im = array ($workIMFirst, $workIMSecond, $workIMThird );
        
        $workEmailFirst  = array('email' => 'manish@5.com');
        
        $workEmailSecond = array('email' => 'manish@hotmail.com');
        
        $workEmailThird = array('email' => 'manish@sify.com');
        
        $email = array($workEmailFirst, $workEmailSecond, $workEmailThird);
        
        $params = array('location_type'          => 'Main',
                        'phone'                  => $phone,
                        'city'                   => 'pune',
                        'country_id'             => 1001,
                        'supplemental_address_1' => 'Andheri',
                        'is_primary'             => 1,
                        'im'                     => $im,
                        'email'                  => $email,
                        'contact_id'             => $this->_household['contact_id']
                        );
        
        $newLocation =& civicrm_location_add( $params );
        $this->assertIsA($newLocation, 'CRM_Core_BAO_Location');
        $this->_locationH[$newLocation->id] = $params['location_type'];
        
        $this->assertEqual($newLocation->phone[3]->phone, '91-20-234-657686');
        $this->assertEqual($newLocation->phone[1]->phone_type, 'Phone');
        $this->assertNull($newLocation->phone[2]->mobile_provider_id, 'Sprint');
        $this->assertEqual($newLocation->im[1]->name, 'mlzope');
        $this->assertEqual($newLocation->im[2]->provider_id, 3);
        $this->assertEqual($newLocation->email[3]->email, 'manish@sify.com');
    }
    
    function testCreateOrganization( ) 
    {
        $params = array('organization_name' => 'Zope Pvt Ltd', 
                        'nick_name'         => 'zope companies', 
                        'location_type'     => 'Home',
                        'contact_type'      => 'Organization'
                        );
        $contact =& civicrm_contact_add( $params );
        $this->assertEqual( $contact['is_error'], 0  );
        $this->_organization = $contact;
    }
   
    
    function testCreateLocationOrganization()
    {
        // $workPhone  = & new CRM_Core_DAO_Phone();
        $workPhone = array('phone'      => '91-20-276048',
                           'phone_type' => 'Phone'
                           );
        
        //$workMobile =& new CRM_Core_DAO_Phone();
        $workMobile = array('phone'           => '91-20-9890848585',
                            'phone_type'      => 'Mobile',
                            'mobile_provider' => 'Sprint'
                            );
        
        $workFax    = array('phone'      => '91-20-234-657686',
                            'phone_type' => 'Fax',
                            'is_primary' =>  TRUE
                            );
        
        $phone     = array ($workPhone, $workMobile, $workFax);
        
        $workIMFirst  = array ('name'        => 'mlzope',
                               'provider_id' => '1',
                               'is_primary'  =>  FALSE
                               );
        
        $workIMSecond = array ('name'        => 'mlzope',
                               'provider_id' => '3',
                               'is_primary'  => FALSE
                               );
        
        $workIMThird  = array ('name'        => 'mlzope',
                               'provider_id' => '5',
                               'is_primary'  => TRUE
                               );
        
        $im = array ($workIMFirst, $workIMSecond, $workIMThird );
        
        $workEmailFirst  = array ('email' => 'manish@5.com');
        
        $workEmailSecond = array ('email' => 'manish@hotmail.com');
        
        $workEmailThird = array ('email' => 'manish@sify.com');
        
        $email = array($workEmailFirst, $workEmailSecond, $workEmailThird);
        
        $params = array('location_type'          => 'Main',
                        'phone'                  => $phone,
                        'city'                   => 'pune',
                        'country_id'             => 1001,
                        'supplemental_address_1' => 'Andheri',
                        'is_primary'             => 1,
                        'im'                     => $im,
                        'email'                  => $email,
                        'contact_id'             => $this->_organization['contact_id']
                        );
        
        $newLocation =& civicrm_location_add( $params );
        $this->assertIsA($newLocation, 'CRM_Core_BAO_Location');
        $this->_locationO[$newLocation->id] = $params['location_type'];
        
        $this->assertEqual($newLocation->phone[3]->phone, '91-20-234-657686');
        $this->assertEqual($newLocation->phone[1]->phone_type, 'Phone');
        $this->assertNull($newLocation->phone[2]->mobile_provider_id, 'Sprint');
        $this->assertEqual($newLocation->im[1]->name, 'mlzope');
        $this->assertEqual($newLocation->im[2]->provider_id, 3);
        $this->assertEqual($newLocation->email[3]->email, 'manish@sify.com');
    }
    
    
    // Deleting the Data creatd for the test cases.
    function testDeleteLocationIndividual()
    {
        foreach ($this->_locationI as $locationType) {
            $contact  = $this->_individual;
            $location_type = $locationType; 
            $result =& civicrm_location_delete($contact, $location_type);
        }
    }
      
    function testDeleteLocationHousehold()
    {
        foreach ($this->_locationH as $locationType) {
            $contact  = $this->_household;
            $location_type = $locationType; 
            $result =& civicrm_location_delete($contact, $location_type);
        }
    }
    
    function testDeleteLocationOrganization()
    {
        foreach ($this->_locationO as $locationType) {
            $contact  = $this->_organization;
            $location_type = $locationType; 
            $result =& civicrm_location_delete($contact, $location_type);
        }
    }
    
    function testDeleteIndividual()
    {
       $contact  = $this->_individual;
       $result =& civicrm_contact_delete($contact);
       //  $this->assertNull($result);
    }
     
    function testDeleteHousehold()
    {
        $contact  = $this->_household;
        $result =& civicrm_contact_delete($contact);
        // $this->assertNull($result);
    }
    
    function testDeleteOrganization()
    {
        $contact  = $this->_organization;
        $result =& civicrm_contact_delete($contact);
        //$this->assertNull($result);
        }
}
?>
