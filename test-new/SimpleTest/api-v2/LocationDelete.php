<?php

require_once 'api/v2/Location.php';

class TestOfLocationDeleteAPIV2 extends CiviUnitTestCase 
{
    function setUp( )
    {
    }

    function testEmptyLocationDelete( )
    {
        $location = array( );
        $locationDelete =& civicrm_location_delete( $location );
        $this->assertEqual( $locationDelete['is_error'], 1 );
    }
    
    function testLocationDeleteError( )
    {
        $location = "noID";
        
        $locationDelete =& civicrm_location_delete($location);
        $this->assertEqual( $locationDelete['is_error'], 1 );
        
    }

    function testLocationDeleteWithMissingContactId( )
    {
        $params = array( 'location_type' => 3 );
        $locationDelete =& civicrm_location_delete( $params );
        
        $this->assertEqual( $locationDelete['is_error'], 1 );
        $this->assertNotNull( $locationDelete );
    }
   
    function testLocationDeleteWithMissingLocationTypeId( )
    {
        $contactID = $this->organizationCreate( );
        $params    = array( 'contact_id'    => $contactID );
        $locationDelete =& civicrm_location_delete( $params );

        $this->assertEqual( $locationDelete['is_error'], 1 );
        $this->assertNotNull( $locationDelete );
        $this->contactDelete( $contactID ) ;        
    }


    function testLocationDeleteWithNoMatch( )
    {
        $contactID = $this->organizationCreate( );
        $params    = array(
                           'contact_id'    => $contactID,
                           'location_type' => 10 
                           );
        $locationDelete =& civicrm_location_delete( $params );

        $this->assertEqual( $locationDelete['is_error'], 1 );
        $this->assertNotNull( $locationDelete );
        $this->contactDelete( $contactID ) ;
    }


    function testLocationDelete( )
    {
        $contactID = $this->organizationCreate( );
        $location  = $this->locationAdd( $contactID ); 
        $params = array(
                        'contact_id'    => $contactID,
                        'location_type' => $location['location_type_id']
                        );
        $locationDelete =& civicrm_location_delete( $params );
        
        $this->assertEqual( $locationDelete['is_error'], 0 );
        $this->assertNull( $locationDelete );
        $this->contactDelete( $contactID ) ;
    }
    
    function tearDown() 
    {
    }
}

?>
