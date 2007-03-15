<?php

require_once 'api/crm.php';

class TestOfCRM755 extends UnitTestCase 
{
    function setUp() 
    {
    }

    function tearDown() 
    {
    }
    
    function testCRM755() 
    {
        $params = array( array( 'name'     => 'first_name',
                                'op'       => 'LIKE',
                                'value'    => '%don%',
                                'grouping' => 1,
                                'wildcard' => 0 ),
                         array( 'name'     => 'email-1',
                                'op'       => 'LIKE',
                                'value'    => '%hotmail%',
                                'grouping' => 2,
                                'wildcard' => 0 ),
                         );
                         

        $returnProperties = array( 'first_name' => 1,
                                   'last_name'  => 1,
                                   'sort_name'  => 1,
                                   );

        $config =& CRM_Core_Config::singleton( );
        $config->oldInputStyle = 0;
        $contacts = crm_contact_search( $params, $returnProperties );
        CRM_Core_Error::debug( 'c', $contacts );
    }

}

?>
