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
        $params = array( 'id' => 101 );
        $returnProperties = array( 'location' => array( '1' => array (
                                                                         'street_address' => 1,
                                                                         'city'           => 1,
                                                                         'state_province' => 1,
                                                                         'country'        => 1,
                                                                         'phone-Phone'    => 1,
                                                                         'phone-Mobile'   => 1,
                                                                         'phone-1'        => 1,
                                                                         'phone-2'        => 1,
                                                                         'im-1'           => 1,
                                                                         'im-2'           => 1,
                                                                         'email-1'        => 1,
                                                                         'email-2'        => 1,
                                                                         ),
                                                        '2' => array ( 
                                                                         'street_address' => 1, 
                                                                         'city'           => 1, 
                                                                         'state_province' => 1, 
                                                                         'country'        => 1, 
                                                                         'phone-Phone'    => 1,
                                                                         'phone-Mobile'   => 1,
                                                                         'phone-1'        => 1,
                                                                         'phone-2'        => 1,
                                                                         'im-1'           => 1,
                                                                         'im-2'           => 1,
                                                                         'email-1'        => 1,
                                                                         'email-2'        => 1,
                                                                         ) 
                                                        ),
                                   );
 
        $query = CRM_Contact_BAO_Query::apiQuery( $params, $returnProperties );
        print_r( $query );
        echo "\n";
    } 
 
} 
 
?>
