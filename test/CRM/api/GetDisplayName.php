<?php

require_once 'api/crm.php';

class TestOfGetDisplayName extends UnitTestCase 
{
    
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }
    
    function testGetDisplayName( )
    {
        $drupalID  = 1;
        $contactID = crm_uf_get_match_id( $drupalID );
        
        $params = array( 'id' => $contactID );
        $properties = array( 'display_name' => 1 );
        $contact = crm_fetch_contact( $params, $properties );
        CRM_Core_Error::debug( 'displayName', $contact['display_name'] );
    }
}
?>