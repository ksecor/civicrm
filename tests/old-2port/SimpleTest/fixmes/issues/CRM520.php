<?php

require_once 'api/crm.php';

class TestOfCRM520 extends UnitTestCase 
{
    function setUp() 
    {
    }

    function tearDown() 
    {
    }

    function testCreateContact( )
    {
        $params = array('first_name' => 'lobo', 'last_name' => 'foo', 'custom_2' => 'green' );
        $contact =& crm_create_contact($params, 'Individual' );
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact' );
    }

}
