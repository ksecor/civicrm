<?php

require_once 'api/v2/Location.php';
require_once 'api/v2/Contact.php';

class TestOfGetLocationAPI extends UnitTestCase 
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
    
    /* Test cases for crm_get_location for Individual contact */ 
    function testCreateContactIndividual()
    {
        $params  = array(
                         'first_name'    => 'Manish',
                         'last_name'     => 'Zope',
                         'location_type' => 'Home',
                         'email'         => 'manish@yahoo.com',
                         'contact_type'  => 'Individual'
                         );
        $contact =& civicrm_contact_add($params);
        
        $this->assertEqual( $contact['is_error'], 0  );
        $this->_individual = $contact;
    }
    
    function testCreateLocationIndividual()
    {
        
        $workPhone = array( 'phone'      => '91-20-276048',
                            'phone_type' => 'Phone'
                            );
        
        $workMobile = array('phone'           => '91-20-9890848585',
                            'phone_type'      => 'Mobile',
                            'mobile_provider' => 'Sprint'
                            );
        
        $workFax = array('phone'      => '91-20-234-657686',
                         'phone_type' => 'Fax',
                         'is_primary' => TRUE
                         );
                
        $phone     = array ($workPhone, $workMobile, $workFax);
        
        $workIMFirst = array('name'        => 'mlzope',
                             'provider_id' => '1',
                             'is_primary'  => FALSE
                             );
        
        $workIMSecond = array('name'       => 'mlzope',
                              'provider_id' => '2',
                              'is_primary'  => FALSE
                              );
        
        $workIMThird = array('name'        => 'mlzope',
                             'provider_id' => '5',
                             'is_primary'  => TRUE
                             );
        
        $im = array ($workIMFirst, $workIMSecond, $workIMThird );
        
        $workEmailFirst = array('email' => 'manish@indiatimes.com');
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
                        'contact_id'             => $this->_individual['contact_id']
                        );
        
        $newLocation =& civicrm_location_add( $params );
        $this->_locationI[$newLocation['id']] = $params['location_type'];
        $this->assertEqual( $newLocation['phone'][3]['phone'], '91-20-234-657686' );
        $this->assertEqual( $newLocation['phone'][1]['phone_type'], 'Phone' );
        $this->assertNull( $newLocation['phone'][2]['mobile_provider_id'], 'Sprint');
        $this->assertEqual( $newLocation['im'][1]['name'], 'mlzope');
        $this->assertEqual($newLocation['im'][2]['provider_id'], 2);
        $this->assertEqual($newLocation['email'][3]['email'], 'manish@sify.com');
        
    }
    
    function testGetLocationIndividualEmptyError()
    {
        $location_types    = array ();
        $contact           = array ('contact_id'    => $this->_individual['contact_id'],
                                    'location_type' => $location_types);
        $newlocation       =& civicrm_location_get( &$contact );
        $this->assertEqual( $newlocation['is_error'], 1  );
        
    }
    
    function testGetLocationIndividual()
    {
        $location_types    = array ('Main');
        $contact           = array ('contact_id'    => $this->_individual['contact_id'],
                                    'location_type' => array ('Main'));//$location_types);
        $newlocation       =& civicrm_location_get( &$contact ); 
        
        foreach ( $newlocation as $loc ) {
            $this->assertEqual($loc['location_type_id'], 3);
            $this->assertEqual($loc['email'][1]['email'], 'manish@indiatimes.com');
            $this->assertEqual($loc['im'][3]['name'], 'mlzope');
        }
    }
    
    //  Test cases for crm_get_location for Household contact
    
    function testCreateContactHousehold() 
    {
        $params  = array('household_name' => 'Zope House',
                         'nick_name'      => 'zope villa',
                         'location_type'  => 'Work',
                         'contact_type'   => 'Household'
                         );
        
        $contact =& civicrm_contact_add( $params );
        
        $this->assertEqual( $contact['is_error'], 0  );
        $this->_household = $contact;
        
    }
    
    function testCreateLocationHousehold()
    {
        $workPhone = array('phone'       => '91-20-276048',
                           'phone_type'  => 'Phone'
                           );
        
        
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
                             'provider_id' => '2',
                             'is_primary'  => FALSE
                             );
        
        $workIMThird  =array('name'        => 'mlzope',
                             'provider_id' => '5',
                             'is_primary'  => TRUE
                             );
        
        
        $im = array ($workIMFirst, $workIMSecond, $workIMThird );
        
        $workEmailFirst  = array('email' => 'manish@indiatimes.com');
        
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
        
        $this->_locationH[$newLocation['id']] = $params['location_type'];
        $this->assertEqual( $newLocation['phone'][3]['phone'], '91-20-234-657686');
        $this->assertEqual( $newLocation['phone'][1]['phone_type'], 'Phone');
        $this->assertNull( $newLocation['phone'][2]['mobile_provider_id'], 'Sprint' );
        $this->assertEqual( $newLocation['im'][1]['name'], 'mlzope');
        $this->assertEqual( $newLocation['im'][2]['provider_id'],2);
        $this->assertEqual( $newLocation['email'][3]['email'], 'manish@sify.com');
        
    }
    
    function testGetLocationHousehold()
    {
        $location_types    = array ('Main');
        $contact           = array ('contact_id'    => $this->_household['contact_id'],
                                    'location_type' => $location_types);
        $newlocation       =& civicrm_location_get( &$contact );    
        
        foreach ($newlocation as $loc) {
            $this->assertEqual($loc['location_type_id'], 3);
        }
    }
    
    /* Test cases for crm_get_location for Organization contact */
    
    function testCreateContactOrganization( ) 
    {
        $params  = array('organization_name' => 'Zope Pvt Ltd', 
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
        $workPhone = array('phone'      => '91-20-276048',
                           'phone_type' => 'Phone'
                           );
        
        
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
                               'provider_id' => '2',
                               'is_primary'  => FALSE
                               );
        
        $workIMThird  = array ('name'        => 'mlzope',
                               'provider_id' => '5',
                               'is_primary'  => TRUE
                               );
        
        $im = array ($workIMFirst, $workIMSecond, $workIMThird );
        
        
        $workEmailFirst  = array ('email' => 'manish@indiatimes.com');
        
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
        
        $this->_locationO[$newLocation['id']] = $params['location_type'];
        $this->assertEqual( $newLocation['phone'][3]['phone'], '91-20-234-657686' );
        $this->assertEqual( $newLocation['phone'][1]['phone_type'], 'Phone' );
        $this->assertNull( $newLocation['phone'][2]['mobile_provider_id'], 'Sprint' );
        $this->assertEqual( $newLocation['im'][1]['name'], 'mlzope' );
        $this->assertEqual( $newLocation['im'][2]['provider_id'], 2 );
        $this->assertEqual( $newLocation['email'][3]['email'], 'manish@sify.com' );
    }
    
    function testGetLocationOrganization()
    {
        $location_types    = array ('Main');
        $contact           = array ('contact_id'    => $this->_organization['contact_id'],
                                    'location_type' => $location_types);
        $newlocation       =& civicrm_location_get( &$contact );    
        
        foreach ($newlocation as $loc) {
            $this->assertEqual( $loc['location_type_id'], 3 );
            
        }
    }
    
    // Deleting the Data creatd for the test cases.
    function testDeleteLocationIndividual()
    {
        foreach ($this->_locationI as $locationType => $locationTypeid) {
            $contact  = array('location_type' => $locationType,
                              'contact_id'    =>  $this->_individual['contact_id']);
            
            $result =& civicrm_location_delete( $contact );
        }
    }
    
    function testDeleteLocationHousehold()
    {
        foreach ($this->_locationH as $locationType => $locationTypeid ) {
            $contact  = array('location_type' => $locationType,
                              'contact_id'    =>  $this->_household['contact_id']);
            
            $result =& civicrm_location_delete( $contact );
        }   
    }
    
    function testDeleteLocationOrganization()
    {
        foreach ($this->_locationO as $locationType => $locationTypeid ) {
            
            $contact  = array('location_type' => $locationType,
                              'contact_id'    =>  $this->_organization['contact_id']);
            
            $result =& civicrm_location_delete( $contact );        
        }
    }
    
    function testDeleteIndividual()
    {
        $contact  = $this->_individual;
        $result =& civicrm_contact_delete( $contact );
    }
    
    function testDeleteHousehold()
    {
        $contact  = $this->_household;
        $result =& civicrm_contact_delete( $contact );
    }
    
    function testDeleteOrganization()
    {
        $contact  = $this->_organization;
        $result =& civicrm_contact_delete( $contact );
    }
}
?>
