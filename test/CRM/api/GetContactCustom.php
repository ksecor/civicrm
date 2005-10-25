<?php 
 
require_once 'api/crm.php'; 
 
class TestOfGetContactCustom extends UnitTestCase  
{   
    function setUp( )  
    { 
    } 
   
    function tearDown( )  
    { 
    } 

    function testGetContactCustom( ) {
        $params = array( 'contact_id' => 105 );
        $contact = crm_get_contact( $params );
    }

    function testGetContactSearch( ) {
        $returnProperties = array( 'display_name' => 1,
                                   'email'        => 1 );
        for ( $i = 1; $i <= 7; $i++ ) {
            $returnProperties['custom_' . $i] = 1;
        }
        $params = array( 'id' => 105 );
        $contact = crm_contact_search( $params, $returnProperties );
        CRM_Core_Error::debug( 'c', $contact );
    }

}
