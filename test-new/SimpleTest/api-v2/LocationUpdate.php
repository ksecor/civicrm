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
    
    function tearDown( ) 
    {
    }
    
}

?>