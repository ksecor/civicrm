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
        $returnProperties = array( 'first_name' => 1, 'email' => 1, 'custom_1' => 1, 'custom_3' => 1, 'state_province' => 1, 'country' => 1 );
 
        $values = CRM_Contact_BAO_Query::getQuery( $params, $returnProperties );
        print_r( $values );
    } 
 
} 
 

