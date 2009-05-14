<?php

require_once 'api/crm.php';

class TestOfCRM966 extends UnitTestCase
{   
    function setUp()
    {
    }
    
    function tearDown()
    {
    }
    
    function testCRM966( )
    {
        $contactGroups = crm_contact_get_groups( 11 );

        CRM_Core_Error::debug('Groups',$contactGroups);
    }

    function testSmartGroup( ) {
        $params = array( 'contact_id' => 11,
                         'group' => array( '4' => 1 ) );
        $result = crm_contact_search( $params ); 
        CRM_Core_Error::debug( 'r', $result );
    }

}


