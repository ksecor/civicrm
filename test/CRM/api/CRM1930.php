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
    
    function testGroupContactNewFormat()
    {
        $params = array ( array( 'group', 'IN', array('4' => 1), 1, 0 ),
                          array( 'contact_id', '=', '101', 1, 0 ),
                          );
        
        $contact =& crm_search_count($params, null);
        CRM_Core_Error::debug('contact', $contact);
    }

    function testGroupContactOldFormat()
    {
        $params = array ( 'group' => array( '4' => 1 ),
                          'contact_id' => 3 );
        
        $contact =& crm_contact_search_count($params, null);
        CRM_Core_Error::debug('contact', $contact);
    }

}

?>