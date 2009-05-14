<?php

require_once 'api/crm.php';

class TestOfCreateProfileContactAPI extends UnitTestCase 
{
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }
    
    function testCreateProfileContact()
    {
        $params = array();

        $params['first_name'] = 'n1111';
        $params['last_name'] = 'n22222222';
        $params['postal_code-1'] = 300005;
        $params['country-1'] = 1228;
 
        $fields = crm_uf_get_profile_fields (1);
//         print_r($fields);
//         print_r($params);
        crm_create_profile_contact( $params, $fields, null );       
    }
}

