<?php

require_once 'api/v2/Contact.php';
require_once 'api/v2/Location.php';
require_once 'CiviTest/CiviUnitTestCase.php';

class api_v2_LocationAddTest extends CiviUnitTestCase 
{
    protected $_contactID;

    function get_info( )
    {
        return array(
                     'name'        => 'Location Add',
                     'description' => 'Test all Location Add API methods.',
                     'group'       => 'CiviCRM API Tests',
                     );
    }  

    function setUp() 
    {
        parent::setUp();
    
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
                    
        $this->assertEquals( $location['error_message'], 'Input Parameters empty' );
    }


    function testAddLocationEmptyWithoutContactid()
    {
        $params = array('location_type' => 'Home',
                        'is_primary'    => 1,
                        'name'          => 'Ashbury Terrace'
                        );
        $location = & civicrm_location_add($params);
        
        $this->assertEquals( $location['error_message'], 'Required fields not found for location contact_id' );
    }


    function testAddLocationEmptyWithoutLocationid()
    {
        $params = array('contact_id'    => $this->_contactID,
                        'is_primary'    => 1,
                        'name'          => 'aaadadf'
                        );
        
        $location = & civicrm_location_add($params);
        
        $this->assertEquals( $location['error_message'], 'Required fields not found for location location_type' );
    }

// from v2.0 onward we don't support location add without location blocks.
//     function testAddLocationOrganizationOnlyLocaitonInfo()
//     {
//         $params = array('contact_id'    => $this->_contactID,
//                         'location_type' => 'Work',
//                         'is_primary'    => 1,
//                         'name'          => 'Saint Helier St'
//                         );
//        
//         $location = & civicrm_location_add($params);
//        
//         $match    = array( );
//         $match['address'][0] = array( 'contact_id'             => $this->_contactID,
//                                       'location_type_id'       => 2,
//                                       'is_primary'             => 1 );
//         $this->checkResult( $location['result'], $match );
//     }
    

    function testAddLocationOrganizationWithoutStreetAddress()
    {
        $params = array('contact_id'             => $this->_contactID,
                        'location_type'          => 'Work',
                        'is_primary'             => 1,
                        'name'                   => 'Saint Helier St',
                        'county'                 => 'Marin',
                        'country'                => 'India', 
                        'state_province'         => 'Michigan',
                        'street_address'         => 'B 103, Ram Maruti Road',
                        'supplemental_address_1' => 'Hallmark Ct', 
                        'supplemental_address_2' => 'Jersey Village'
                        );
        
        $location = & civicrm_location_add($params);
        
        $match    = array( );
        $match['address'][0] = array( 'contact_id'             => $this->_contactID,
                                      'location_type_id'       => 2,
                                      'country_id'             => 1101,
                                      'state_province_id'      => 1021,
                                      'street_address'         => 'B 103, Ram Maruti Road',
                                      'supplemental_address_1' => 'Hallmark Ct',
                                      'supplemental_address_2' => 'Jersey Village' );
        
        $this->checkResult( $location['result'], $match );
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
        
        $match    = array( );
        $match['address'][0] = array( 'contact_id'             => $this->_contactID,
                                      'location_type_id'       => 2,
                                      'is_primary'             => 1,
                                      'country_id'             => 1228,
                                      'state_province_id'      => 1021,
                                      'supplemental_address_1' => 'Hallmark Ct',
                                      'supplemental_address_2' => 'Jersey Village' );
        
        $this->checkResult( $location['result'], $match );
    }
    
    function testAddLocationOrganizationWithAddressEmail()
    {
        $workPhone = array( 'phone'         => '91-20-276048',
                            'phone_type_id' => 1,
                            'is_primary'    => 1
                            );
        
        $workFax = array('phone'         => '91-20-234-657686',
                         'phone_type_id' => 3 );
        
        $phone     = array ($workPhone, $workFax);
        
        $workIMFirst = array('name'        => 'Hi',
                             'provider_id' => '1',
                             'is_primary'  => 0
                             );
        
        $workIMSecond = array('name'       => 'Hola',
                             'provider_id' => '3',
                             'is_primary'  => 0
                              );
        
        $workIMThird = array('name'        => 'Welcome',
                             'provider_id' => '5',
                             'is_primary'  => 1
                             );
        
        $im = array ($workIMFirst, $workIMSecond, $workIMThird );
        
        $workEmailFirst  = array( 'email'      => 'abc@def.com',
                                  'on_hold'    => 1);
        
        $workEmailSecond = array( 'email'       => 'yash@hotmail.com',
                                  'is_bulkmail' => 1);
        
        $workEmailThird  = array( 'email'      => 'yashi@yahoo.com');
        
        $email = array($workEmailFirst, $workEmailSecond, $workEmailThird);
        
        $params = array('contact_id'             => $this->_contactID,
                        'location_type'          => 'Main',
                        'phone'                  => $phone,
                        'city'                   => 'San Francisco',
                        'state_province'         => 'California',
                        'country_id'             => 1228,
                        'street_address'         => '123, FC Road', 
                        'supplemental_address_1' => 'Near Wenna Lake',
                        'is_primary'             => 1,
                        'im'                     => $im,
                        'email'                  => $email
                        );
        
        $location = & civicrm_location_add($params); 
        
        $match    = array( );
        $match['address'][0] = array( 'contact_id'             => $this->_contactID,
                                      'location_type_id'       => 3,
                                      'city'                   => 'San Francisco',
                                      'state_province_id'      => 1004,
                                      'country_id'             => 1228,
                                      'street_address'         => '123, FC Road', 
                                      'supplemental_address_1' => 'Near Wenna Lake',
                                      'is_primary'             => 1
                                      );
        
        $match['email'][0] = array( 'is_primary' => 1,
                                    'email'      => 'abc@def.com',
                                    'on_hold'    => 1
                                    );
        $match['email'][1] = array( 'is_primary' => 0,
                                    'email'      => 'yash@hotmail.com',
                                    'is_primary' => 0);
        $match['email'][2] = array( 'contact_id' => $this->_contactID,
                                    'is_primary' => 0,
                                    'email'      => 'yashi@yahoo.com' );
        
        $match['phone'][0] = array( 'is_primary'       => 1,
                                    'phone'            => '91-20-276048',
                                    'phone_type_id'    => 1,
                                    'location_type_id' => 3,
                                    'contact_id'       => $this->_contactID );
        $match['phone'][1] = array( 'is_primary'       => 0,                               
                                    'phone_type_id'    => 3,
                                    'phone'            => '91-20-234-657686' );
        $match['im'][0] = array( 'name'             => 'Hi',
                                 'is_primary'       => 0,
                                 'provider_id'      => 1,
                                 'location_type_id' => 3);
        $match['im'][1] = array( 'name'             => 'Hola',
                                 'is_primary'       => 0,
                                 'provider_id'      => 3);
        $match['im'][2] = array( 'name'             => 'Welcome',
                                 'is_primary'       => 1,
                                 'provider_id'      => 5,
                                 'contact_id'       => $this->_contactID );
        $this->checkResult( $location['result'], $match );
    }
    
    function checkResult( &$result, &$match )
    {
        if ( CRM_Utils_Array::value( 'address', $match ) ) {
            $this->assertDBState( 'CRM_Core_DAO_Address', $result['address'][0], $match['address'][0] );
        }
        
        if ( CRM_Utils_Array::value( 'phone', $match ) ) {
            for( $i = 0; $i < count( $result['phone'] ); $i++){
                $this->assertDBState( 'CRM_Core_DAO_Phone', $result['phone'][$i], $match['phone'][$i] );
            }
        }
        
        if ( CRM_Utils_Array::value( 'email', $match ) ) {
            for( $i=0; $i < count( $result['email'] ); $i++){
                $this->assertDBState( 'CRM_Core_DAO_Email', $result['email'][$i], $match['email'][$i] );
            }
        }
        
        if ( CRM_Utils_Array::value( 'im', $match ) ) {
            for( $i=0; $i<count( $result['im'] ); $i++){
                $this->assertDBState( 'CRM_Core_DAO_IM', $result['im'][$i], $match['im'][$i] );
            }
        }
        
    }
    
}
 