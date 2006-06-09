<?php

require_once 'api/crm.php';

class TestOfCreateContactAPI extends UnitTestCase 
{
    static $_email = 'username+siteithinkmightspamme_1@gmail.com';

    function setUp() 
    {
    }

    function tearDown() 
    {
    }

    function testCreateContactEmail( )
    {
        $params = array('email' => self::$_email );
        $contact =& crm_create_contact($params, 'Individual');
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
    }

    function testGetContactEmail( )
    {
        $params = array('email' => self::$_email );
        $contact =& crm_get_contact($params, 'Individual');
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
    }

}