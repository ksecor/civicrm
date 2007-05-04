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
        $drupalID  = 12345;
        $contactID = crm_uf_get_match_id( $drupalID );
        if ( ! $contactID ) {
            return null;
        }

        $params = array( 'id' => $contactID );
        $properties = array( 'display_name' => 1 );
        $contact = crm_fetch_contact( $params, $properties );
        if ( is_a( $contact, 'CRM_Core_Error' ) ) {
            return null;
        }
        return $contact['display_name'];
    }
}
?>
