<?php

require_once 'api/crm.php';

class TestOfGetContactFlat extends UnitTestCase  
{ 
 
     
    function setUp()  
    { 
    } 
 
    function tearDown()  
    { 
    } 
 
    function testGetContactFlat() 
    { 
        $params = array( 'email' => 'yahoo' );
        $returnProperties = array( 'email' => 1, 'custom_1' => 1, 'custom_3' => 1 );
 
        $query = new CRM_Contact_BAO_Query( $params, $returnProperties );
        print_r( $query->query( ) );
    } 
 
} 
 
?>
