<?php

require_once 'CRM/Utils/Sunlight.php';
require_once 'CiviTest/CiviUnitTestCase.php';

class CRM_Utils_SunlightTest extends CiviUnitTestCase 
{

    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }    
   
    function testInfo( ) {
        $this->markTestSkipped( 'Fatal thrown due to wrong answer from Sunlight API' );
        $result = CRM_Utils_Sunlight::getInfo( null, null, '94117' );
        $this->assertEqual( count( $result ), 5 );
    }

    function testRepresentativeInfo( ) {
        $this->markTestSkipped( 'Fatal thrown due to wrong answer from Sunlight API' );
        $result = CRM_Utils_Sunlight::getRepresentativeInfo( 'San Francisco', 'CA' );
        $this->assertEqual( count( $result ), 3 );
    }

    function testSenatorInfo( ) {
        $this->markTestSkipped( 'Fatal thrown due to wrong answer from Sunlight API' );
        $result = CRM_Utils_Sunlight::getSenatorInfo( 'CA' );
        $this->assertEqual( count( $result ), 2 );
    }

}


