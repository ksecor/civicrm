<?php

require_once 'api/v2/Location.php';
require_once 'CiviTest/CiviUnitTestCase.php';

class api_v2_LocationDeleteTest extends CiviUnitTestCase 
{

    protected $_contactID;

    function get_info( )
    {
        return array(
                     'name'        => 'Location Delete',
                     'description' => 'Test all Location Delete API methods.',
                     'group'       => 'CiviCRM API Tests',
                     );
    } 
    
    function setUp( )
    { 
        parent::setUp();
            
        $this->_contactID = $this->organizationCreate( ) ;
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
    
    function tearDown() 
    {
        $this->contactDelete( $this->_contactID ) ;
    }
}

