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
                        'first_name' => 'Michael',
                        'last_name' => 'Knight',
                        'prefix' => 'Mr.',
                        'job_title' => 'Chief Slacker',
                        'home_URL' => 'http://www.yahoo.com/',
                        'note' => 'This is a self-note',
                        );

        CRM_Core_Error::debug( 'c', $params );
        $contact =& crm_create_contact($params);
        CRM_Core_Error::debug( 'c', $contact );
    }
}

?>
