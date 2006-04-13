<?php

require_once 'api/crm.php';

class TestOfCRM785 extends UnitTestCase
{   
    function setUp()
    {
    }
    
    function tearDown()
    {
    }
    
    function testCRM822( )
    {
        $params = array( 'id' => 101 );
        $returnProperties = array( 'phone' => 1,
                                   'email' => 1,
                                   'first_name' => 1,
                                   'last_name' => 1 );
        $contact =& crm_fetch_contact($params, $returnProperties);
        CRM_Core_Error::debug('c', $contact );
    }
    
}
?>