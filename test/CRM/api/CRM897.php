<?php

require_once 'api/crm.php';

class TestOfCRM897 extends UnitTestCase
{   
    function setUp()
    {
    }
    
    function tearDown()
    {
    }
    
    function testCRM897( )
    {
        $names = _crm_get_pseudo_constant_names( );
        foreach ( $names as $name => $value ) {
            CRM_Core_Error::debug( $name, crm_get_property_values( $name ) );
        }

        CRM_Core_Error::debug( "Does not exist", crm_get_property_values( "Does not exist" ) );
    }
}

?>