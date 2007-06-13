<?php

require_once 'api/v2/Contact.php';
require_once 'api/v2/Location.php';

class TestOfLocationAddAPIV2 extends CiviUnitTestCase 
{
    protected $_contactID;
    
    function setUp() 
    {
        $this->_contactID = $this->organizationCreate( ) ;
    }
    
    function tearDown() 
    {
        $this->contactDelete( $this->_contactID ) ;
    }    
   
    function testAddLocationEmpty()
    {
        $params = array();        
        $location = & civicrm_location_add($params);
            
        $this->assertEqual( $location['is_error'], 1 );
    }


    function testAddLocationEmptyWithoutContactid()
    {
        $params = array('location_type' => 'Home',
                        'is_primary'    => 1,
                        'name'          => 'Ashbury Terrace'
                        );
        $location = & civicrm_location_add($params);

        $this->assertEqual( $location['is_error'], 1 );
    }


    function testAddLocationEmptyWithoutLocationid()
    {
        $params = array('contact_id'    => $this->_contactID,
                        'is_primary'    => 1,
                        'name'          => 'aaadadf'
                        );
        
        $location = & civicrm_location_add($params);

        $this->assertEqual( $location['is_error'], 1 );
    }

    function testAddLocationOrganizationWithoutAddress()
    {
        $params = array('contact_id'    => $this->_contactID,
                        'location_type' => 'Work',
                        'is_primary'    => 1,
                        'name'          => 'Saint Helier St'
                        );
                
        $location = & civicrm_location_add($params);
        
        $this->assertNotNull($location);
    }
    

    function testAddLocationOrganizationWithBadAddress()
    {

        
        $params = array('contact_id'             => $this->_contactID,
                        'location_type'          => 'Work',
                        'is_primary'             => 1,
                        'name'                   => 'Saint Helier St',
                        'county'                 => 'Marin',
                        'country'                => 'India', 
                        'state_province'         => 'Michigan',
                        'supplemental_address_1' => 'Hallmark Ct', 
                        'supplemental_address_2' => 'Jersey Village'
                        );
        
        $location = & civicrm_location_add($params);

        $this->assertNotNull($location);

    }

    function testAddLocationOrganizationWithAddress()
    {
        $params = array('contact_id'             => $this->_contactID,
                        'location_type'          => 'Work',
                        'is_primary'             => 1,
                        'name'                   => 'Saint Helier St',
                        'county'                 => 'Saginaw County',
                        'country'                => 'United States', 
                        'state_province'         => 'Michigan',
                        'supplemental_address_1' => 'Hallmark Ct', 
                        'supplemental_address_2' => 'Jersey Village'
                        );
        
        $location = & civicrm_location_add($params);
        
        $this->assertNotNull($location);
    }
    
    function testAddLocationOrganizationWithAddressEmail()
    {
        //$workPhone  = & new CRM_Core_DAO_Phone();
        $workPhone = array( 'phone'      => '91-20-276048',
                            'phone_type' => 'Phone'
                            );
        
             
        //$workFax    =& new CRM_Core_DAO_Phone();
        $workFax = array('phone'      => '91-20-234-657686',
                         'phone_type' => 'Fax',
                         'is_primary' => TRUE
                         );
        
        $phone     = array ($workPhone, $workFax);
        
        //$workIMFirst  =& new CRM_Core_DAO_IM();
        $workIMFirst = array('name'        => 'abc',
                             'provider_id' => '1',
                             'is_primary'  => FALSE
                             );
        
        //$workIMSecond =& new CRM_Core_DAO_IM();
        $workIMSecond = array('name'       => 'abc',
                             'provider_id' => '3',
                             'is_primary'  => FALSE
                              );
        
        //$workIMThird  =& new CRM_Core_DAO_IM();
        $workIMThird = array('name'        => 'abc',
                             'provider_id' => '5',
                             'is_primary'  => TRUE
                             );
        
        $im = array ($workIMFirst, $workIMSecond, $workIMThird );
        
        //$workEmailFirst  =& new CRM_Core_DAO_Email();
        $workEmailFirst = array('email' => 'abc@def.com');
        
        //$workEmailSecond =& new CRM_Core_DAO_Email();
        $workEmailSecond = array('email' => 'yash@hotmail.com');
        
        //$workEmailThird =& new CRM_Core_DAO_Email();
        $workEmailThird = array('email' => 'yashi@yahoo.com');
        
        $email = array($workEmailFirst, $workEmailSecond, $workEmailThird);
        
        $params = array('contact_id'             => $this->_contactID,
                        'location_type'          => 'Main',
                        'phone'                  => $phone,
                        'city'                   => 'pune',
                        'country_id'             => 1001,
                        'supplemental_address_1' => 'Andheri',
                        'is_primary'             => 1,
                        'im'                     => $im,
                        'email'                  => $email
                        );
        
        $location = & civicrm_location_add($params); 
       
        $this->assertNotNull($location);
        $this->assertEqual($location['phone'][2]['phone'], '91-20-234-657686');
        $this->assertEqual($location['phone'][1]['phone_type'], 'Phone');
        $this->asSertequal($location['im'][1]['name'], 'abc');
        $this->assertEqual($location['im'][2]['provider_id'], 3);
        $this->assertEqual($location['email'][3]['email'], 'yashi@yahoo.com');
    }
   
}
 
?> 
       