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
    
    function testCRM785( )
    {
        $params = array(
                        'primary_location_id' => 1,
                        'first_name' => 'Michael01',
                        'last_name' => 'Knight01',
                        'prefix' => 'Mr.',
                        'job_title' => 'Chief Slacker',
                        'home_URL' => 'http://www.yahoo.com/',
                        'note' => 'This is a self-note 01',
                        );
        CRM_Core_Error::debug( 'P', $params );
        $contact =& crm_create_contact($params);
        CRM_Core_Error::debug( 'C 01', $contact );
    }
    
    function testAddNoteSimple( )
    {
        $params = array(
                        'primary_location_id' => 1,
                        'first_name' => 'Michael02',
                        'last_name' => 'Knight02',
                        'note' => 'This is a self-note 02',
                        );
        CRM_Core_Error::debug( 'P', $params );
        $contact =& crm_create_contact($params);
        CRM_Core_Error::debug( 'C 02', $contact );
    }
    
    function testAddNoteCompleteLocation( )
    {
        $params = array(
                        'primary_location_id' => 1,
                        'first_name' => 'Michael03',
                        'last_name' => 'Knight03',
                        'prefix' => 'Mr.',
                        'suffix' => 'Jr.',
                        'job_title' => 'Chief Slacker',
                        'home_URL' => 'http://www.yahoo.com/',
                        'location_type' => 'Home',
                        'phone' => '12345678',
                        'email' => 'mkn03@y.net',
                        'im' => 'Hello',
                        'state' => 'OR',
                        'country' => 'US',
                        'note' => 'This is a self-note 03'
                        );
        CRM_Core_Error::debug( 'P', $params );
        $contact =& crm_create_contact($params);
        CRM_Core_Error::debug( 'C 03', $contact );
    }
    
    function testAddNoteNoPrimaryLocationId( )
    {
        $params = array(
                        'first_name' => 'Michael04',
                        'last_name' => 'Knight04',
                        'note' => 'This is a self-note 04',
                        );
        CRM_Core_Error::debug( 'P', $params );
        $contact =& crm_create_contact($params);
        CRM_Core_Error::debug( 'C 02', $contact );
    }
}
?>