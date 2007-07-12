<?php

require_once 'api/crm.php';

class TestOfGetIMInfo extends UnitTestCase 
{
    function setUp( ) 
    {
    }
    
    function tearDown( ) 
    {
    }

    function testGetIMInfo( )
    {
            
        $params = array('email' => 'lobo@google.com', 'im_provider_id' => 5);
        $props  = array( 'im' => 1);

        list( $contacts, $dontcare ) = crm_contact_search( $params, $props );
        CRM_Core_Error::debug( 'c', $contacts );
    }
    
}

?>
