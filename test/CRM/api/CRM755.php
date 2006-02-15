<?php

require_once 'api/crm.php';

class TestOfCRM755 extends UnitTestCase 
{
    function setUp() 
    {
    }

    function tearDown() 
    {
    }
    
    function testCRM755() 
    {
        $params = array( 'email' => 'lobo_foo@yahoo.com' );
        $returnProperties = array( 'first_name' => 1,
                                   'last_name'  => 1,
                                   'street_address' => 1,
                                   'city' => 1,
                                   'state' => 1,
                                   'postal_code' => 1,
                                   'geo_code_1' => 1,
                                   'geo_code_2' => 1 );

        $contacts = crm_contact_search( $params, $returnProperties );
        CRM_Core_Error::debug( 'c', $contacts );
    }

}

?>