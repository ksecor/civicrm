<?php

require_once 'api/crm.php';

class TestOfCRM514 extends UnitTestCase 
{
    static $_email = 'username+siteithinkmightspamme_1@gmail.com';

    function setUp() 
    {
    }

    function tearDown() 
    {
    }

    function testGetContactEmail( )
    {
        $params = array('email' => self::$_email );
        $contact =& crm_get_contact($params );
        $this->assertIsA($contact, 'CRM_Core_Error');
    }

}
