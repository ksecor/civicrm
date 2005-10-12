<?php

require_once 'api/crm.php';

class TestOfCRM272 extends UnitTestCase 
{
    function setUp( ) 
    {
    }

    function tearDown( ) 
    {
    }
    
    function testSearchCountNull( )
    {
        $params = array( );
        $count = crm_contact_search_count( $params );
        echo "$count\n";
    }

    function testSearchCountIndividual( )
    {
        $params = array( 'contact_type' => 'Individual' );
        $count = crm_contact_search_count( $params );
        echo "$count\n";
    }

    function testSearchCountHousehold( )
    {
        $params = array( 'contact_type' => 'Household' );
        $count = crm_contact_search_count( $params );
        echo "$count\n";
    }

    function testSearchCountOrganization( )
    {
        $params = array( 'contact_type' => 'Organization' );
        $count = crm_contact_search_count( $params );
        echo "$count\n";
        $contacts = crm_contact_search( $params );
        print_r( $contacts );
    }

}

?>