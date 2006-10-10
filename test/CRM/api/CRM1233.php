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
        $params = array ( array( 'group', 'IN', array('7' => 1), 1, 0 ),
                          array( 'contact_type', '=', 'Organization', 1, 0 ),
                          );
        
        $contact =& civicrm_search($params, $return_properties);
        CRM_Core_Error::debug('contact', $contact);
    }

    function testGroupContact2()
    {
        $params = array ( array( 'group', 'IN', array('7' => 1, '8' => 1), 1, 0 ),
                          array( 'contact_type', '=', 'Organization', 1, 0 ),
                          );

        $contact =& civicrm_search($params, $return_properties);
        CRM_Core_Error::debug('contact', $contact);
    }

    function testGroupContact3()
    {
        $params = array ( array( 'group', '=', array('7' => 1), 1, 0 ),
                          array( 'contact_type', '=', 'Organization', 1, 0 ),
                          array( 'group', '=', array('8' => 1), 1, 0 ),
                          array( 'contact_type', '=', 'Organization', 1, 0 ),
                         );

        $contact =& civicrm_search($params, $return_properties);
        CRM_Core_Error::debug('contact', $contact);
    }
}

?>