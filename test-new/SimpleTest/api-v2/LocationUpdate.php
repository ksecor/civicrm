<?php

require_once 'api/v2/Location.php';

class TestOfLocationUpdateAPIV2 extends CiviUnitTestCase {
    
    function setup( ) 
    {
    }

    function testLocationUpdateEmpty( ) 
    {
        $params = array( );
        $result = civicrm_location_update( $params );
        $this->assertEqual( $result['is_error'], 1 );
    }


    function testLocationUpdateError( )
    {
        $location = "noID";
        
        $locationUpdate =& civicrm_location_update($location);
        $this->assertEqual( $locationUpdate['is_error'], 1 );
        
    }

    function testLocationUpdateWithMissingContactId( )
    {
        $params = array( 'location_type' => 3 );
        $locationUpdate =& civicrm_location_update( $params );
        
        $this->assertEqual( $locationUpdate['is_error'], 1 );
        $this->assertNotNull( $locationUpdate );
    }
   
    function testLocationUpdateWithMissingLocationTypeId( )
    {
        $contactID = $this->organizationCreate( );
        $params    = array( 'contact_id'    => $contactID );
        $locationUpdate =& civicrm_location_update( $params );

        $this->assertEqual( $locationUpdate['is_error'], 1 );
        $this->assertNotNull( $locationUpdate );
        $this->contactDelete( $contactID ) ;        
    }

    function testLocationUpdate()
    {
        $contactID = $this->organizationCreate( );
        
        $location  = $this->locationAdd( $contactID ); 
        $workPhone =array('phone' => '02327276048',
                          'phone_type' => 'Phone');
        
        $phones = array ($workPhone);
        
        $workEmailFirst = array('email' => 'xyz@indiatimes.com');
        
        $workEmailSecond = array('email' => 'abcdef@hotmail.com');
        
        $emails = array($workEmailFirst,$workEmailSecond);
        
        $params = array(
                        'phone'            => $phones,
                        'city'             => 'Mumbai',
                        'email'            => $emails,
                        'contact_id'       => $contactID,
                        'location_id'      => $location['id']
                        );
        
        
        $locationUpdate =& civicrm_location_update( $params );
        
        $this->assertEqual($locationUpdate['phone'][1]['phone'], '02327276048');
        $this->assertEqual($locationUpdate['phone'][1]['phone_type'], 'Phone');
        $this->assertEqual($locationUpdate['email'][1]['email'], 'xyz@indiatimes.com');
        $this->assertEqual($locationUpdate['email'][2]['email'], 'abcdef@hotmail.com');
    }
    
    function tearDown( ) 
    {
    }
    
}

?>