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
        $params = array( 'first_name' => array( 'op'       => '=',
                                                'value'    => 'Donald',
                                                'grouping' => 1,
                                                'wildcard' => 0 ),
                         'email' => array( 'op'       => 'LIKE',
                                           'value'    => 'hotmail',
                                           'grouping' => 2,
                                           'wildcard' => 1 ),
                         );
                         

        $returnProperties = array( 'first_name' => 1,
                                   'last_name'  => 1,
                                   'sort_name'  => 1,
                                   );

        $contacts = crm_contact_search( $params, $returnProperties );
        CRM_Core_Error::debug( 'c', $contacts );
    }

}

?>