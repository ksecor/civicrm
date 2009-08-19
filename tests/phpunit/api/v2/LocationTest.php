<?php

require_once 'api/v2/Contact.php';
require_once 'api/v2/Location.php';
require_once 'CiviTest/CiviUnitTestCase.php';

class api_v2_LocationTest extends CiviUnitTestCase 
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
        $this->_location =& $this->locationAdd($this->_contactId);        
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

    function testEmptyLocationDelete( )
    {
        $location = array( );
        $locationDelete =& civicrm_location_delete( $location );
        $this->assertEquals( $locationDelete['is_error'], 1 );
        $this->assertEquals( $locationDelete['error_message'], '$contact is not valid contact datatype' );
    }
    
    function testLocationDeleteError( )
    {
        $location = "noID";
        
        $locationDelete =& civicrm_location_delete($location);
        $this->assertEquals( $locationDelete['is_error'], 1 );
        $this->assertEquals( $locationDelete['error_message'], 'missing or invalid location' );        
    }

    function testLocationDeleteWithMissingContactId( )
    {
        $params = array( 'location_type' => 3 );
        $locationDelete =& civicrm_location_delete( $params );
        
        $this->assertEquals( $locationDelete['is_error'], 1 );
        $this->assertEquals( $locationDelete['error_message'], '$contact is not valid contact datatype' );        
    }
   
    function testLocationDeleteWithMissingLocationTypeId( )
    {
        $params    = array( 'contact_id'    => $this->_contactID );
        $locationDelete =& civicrm_location_delete( $params );

        $this->assertEquals( $locationDelete['is_error'], 1 );
        $this->assertEquals( $locationDelete['error_message'], 'missing or invalid location' );
    }


    function testLocationDeleteWithNoMatch( )
    {
        $params    = array(
                           'contact_id'    =>  $this->_contactID,
                           'location_type' => 10 
                           );
        $locationDelete =& civicrm_location_delete( $params );

        $this->assertEquals( $locationDelete['is_error'], 1 );
        $this->assertEquals( $locationDelete['error_message'], 'invalid location type' );                
        $this->assertNotNull( $locationDelete );
    }


    function testLocationDelete( )
    {
        $location  = $this->locationAdd(  $this->_contactID ); 
        
        $params = array(
                        'contact_id'    => $this->_contactID,
                        'location_type' => $location['result']['location_type_id']
                        );
        $locationDelete =& civicrm_location_delete( $params );
        
        $this->assertEquals( $locationDelete['is_error'], 0 );
        $this->assertNull( $locationDelete );
    }

    function testGetWithoutProperParams()
    {
        // empty params
        $result =& civicrm_location_get(array());
        $this->assertEquals($result['is_error'], 1);
        // no contact_id
        $result =& civicrm_location_get(array('location_type' => 'Main'));
        $this->assertEquals($result['is_error'], 1);
        // location_type an empty array
        $result =& civicrm_location_get(array('contact_id' => $this->_contactId, 'location_type' => array()));
        $this->assertEquals($result['is_error'], 1);
    }

    function testGetProper()
    {
        $proper = array(
            'country_id'             => 1228,
            'county_id'              => 3,
            'state_province_id'      => 1021,
            'supplemental_address_1' => 'Hallmark Ct',
            'supplemental_address_2' => 'Jersey Village',
        );
        $result = civicrm_location_get(array('contact_id' => $this->_contactId));
        foreach ($result as $location) {
            if ( CRM_Utils_Array::value( 'address', $location ) ) {
                foreach ($proper as $field => $value) {
                    $this->assertEquals($location['address'][$field], $value);
                }
            }
        }
    }

    function testLocationUpdateEmpty( ) 
    {
        $params = array( );
        $result = civicrm_location_update( $params );
        $this->assertEquals( $result['is_error'], 1 );
    }


    function testLocationUpdateError( )
    {
        $location = "noID";
        
        $locationUpdate =& civicrm_location_update($location);
        $this->assertEquals( $locationUpdate['is_error'], 1 );
        $this->assertEquals( $locationUpdate['error_message'], 'missing or invalid location_type_id' );        
        
    }

    function testLocationUpdateWithMissingContactId( )
    {
        $params = array( 'location_type' => 3 );
        $locationUpdate =& civicrm_location_update( $params );
        
        $this->assertEquals( $locationUpdate['is_error'], 1 );
        $this->assertEquals( $locationUpdate['error_message'], '$contact is not valid contact datatype' );        
        $this->assertNotNull( $locationUpdate );
    }
   
    function testLocationUpdateWithMissingLocationTypeId( )
    {
        $params    = array( 'contact_id'    => $this->_contactID );
        $locationUpdate =& civicrm_location_update( $params );

        $this->assertEquals( $locationUpdate['is_error'], 1 );
        $this->assertNotNull( $locationUpdate );
        $this->assertEquals( $locationUpdate['error_message'], 'missing or invalid location_type_id' );        
    }

    function testLocationUpdate()
    {
        $location  = $this->locationAdd( $this->_contactID ); 
       
        $workPhone =array('phone' => '02327276048',
                          'phone_type' => 'Phone');
        
        $phones = array ($workPhone);
        
        $workEmailFirst = array('email' => 'xyz@indiatimes.com');
        
        $workEmailSecond = array('email' => 'abcdef@hotmail.com');
        
        $emails = array($workEmailFirst,$workEmailSecond);
        
        $params = array(
                        'phone'                 => $phones,
                        'city'                  => 'Mumbai',
                        'email'                 => $emails,
                        'contact_id'            => $this->_contactID,
                        'location_type_id'      => $location['result']['location_type_id']
                        );
        
        $locationUpdate =& civicrm_location_update( $params );
        
        $this->assertEquals( $locationUpdate['is_error'], 0 );
    }

    
}
 
