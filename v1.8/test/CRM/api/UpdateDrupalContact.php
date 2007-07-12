<?php

require_once 'api/crm.php';

class TestOfUpdateContactAPI extends UnitTestCase 
{
    protected $_individual;

    function setUp() 
    {
    }

    function tearDown() 
    {
    }
    
    function testUpdateContactIndividual() 
    {
        $params = array( 'email' => 'lobo_foo@yahoo.com' );
        if ($contact = crm_get_contact($params)) { 
            CRM_Core_Error::debug( 'c', $contact );

            $params = array('first_name' => 'Foo',
                            'last_name'  => 'Bar', );
            $contact = crm_update_contact($contact, $params);

            CRM_Core_Error::debug( 'c', $contact );
        }
    }
}

?>
