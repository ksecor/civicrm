<?php

require_once 'CRM/Utils/Sunlight.php';

class TestOfSunlightAPI extends CiviUnitTestCase 
{
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }    
   
    function testInfo( ) {
        $result = CRM_Utils_Sunlight::getInfo( null, null, '94117' );
        $this->assertEqual( count( $result ), 5 );
    }

    function testRepresentativeInfo( ) {
        $result = CRM_Utils_Sunlight::getRepresentativeInfo( 'San Francisco', 'CA' );
        $this->assertEqual( count( $result ), 3 );
    }

    function testSenatorInfo( ) {
        $result = CRM_Utils_Sunlight::getSenatorInfo( 'CA' );
        $this->assertEqual( count( $result ), 2 );
    }

}

?>
