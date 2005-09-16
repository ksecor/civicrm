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
 
        CRM_Contact_BAO_Query::query( $params, $returnProperties ); 
    } 
 
} 
 
?>
