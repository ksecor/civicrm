<?php

require_once 'api/crm.php';

class TestOfGetContactHierachical extends UnitTestCase  
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
        $returnProperties = array( 'location' => array( 'Home' => array (
                                                                         'street_address' => 1,
                                                                         'city'           => 1,
                                                                         'state_province' => 1,
                                                                         'country'        => 1,
                                                                         'phone_1'        => 1,
                                                                         'im_1'           => 1,
                                                                         'email_1'        => 1,
                                                                         ),
                                                        'Work' => array ( 
                                                                         'street_address' => 1, 
                                                                         'city'           => 1, 
                                                                         'state_province' => 1, 
                                                                         'country'        => 1, 
                                                                         'phone_1'        => 1, 
                                                                         'im_1'           => 1, 
                                                                         'email_1'        => 1, 
                                                                         ) 
                                                        ),
                                   'custom_1' => 1, 'custom_3' => 1
                                   );
 
        $query = CRM_Contact_BAO_Query::getQuery( $params, $returnProperties );
        print_r( $query );
    } 
 
} 
 
?>
