<?php

require_once 'api/v2/Location.php';
require_once 'CiviTest/CiviUnitTestCase.php';

class api_v2_LocationUpdateTest extends CiviUnitTestCase 
{
    protected $_contactID;
    
    function get_info( )
    {
        return array(
                     'name'        => 'Location Update',
                     'description' => 'Test all Location Update API methods.',
                     'group'       => 'CiviCRM API Tests',
                     );
    }
    
    function setUp( ) 
    {
        parent::setUp();            
        
        $this->_contactID = $this->organizationCreate( ) ;
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
    
    function tearDown( ) 
    {
        $this->contactDelete( $this->_contactID ) ;        
    }
    
}
