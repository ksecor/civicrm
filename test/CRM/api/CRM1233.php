<?php

require_once 'api/crm.php';

class TestOfSearch extends UnitTestCase 
{
    function setUp( ) 
    {
    }

    function tearDown( ) 
    {
    }
    
    function testGroupContact1()
    {
        $params = array ( array( 'group', 'IN', array('5' => 1), 1, 0 ),
                          array( 'contact_type', '=', 'Individual', 1, 0 ),
                          );
        
        $contact =& crm_search($params, null);
        CRM_Core_Error::debug('contact', $contact);
    }

    function testGroupContact2()
    {
        $params = array ( array( 'group', 'IN', array('5' => 1, '6' => 1), 1, 0 ),
                          array( 'contact_type', '=', 'Individual', 1, 0 ),
                          );

        $contact =& crm_search($params, null);
        CRM_Core_Error::debug('contact', $contact);
    }

    function testGroupContact3()
    {
        $params = array ( array( 'group', '=', array('5' => 1), 1, 0 ),
                          array( 'contact_type', '=', 'Individual', 1, 0 ),
                          array( 'group', '=', array('6' => 1), 1, 0 ),
                          array( 'contact_type', '=', 'Individual', 1, 0 ),
                         );

        $contact =& crm_search($params, null);
        CRM_Core_Error::debug('contact', $contact);
    }

    function testOrSearch()
    {
        $params = array ( array( 'sort_name', 'like', '%lobo%', 1, 0 ),
                          array( 'sort_name', 'like', '%adam%', 1, 0 ),
                          );

        $contact =& crm_search($params, null);
        CRM_Core_Error::debug('contact', $contact);
    }
}

?>