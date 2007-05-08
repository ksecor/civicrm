<?php

require_once 'api/crm.php';

class TestOfCRM1856 extends UnitTestCase 
{
    function testOfCRM1856()
    {
        $params =  array(
                        'domain_id'              => 1,
                        'contribution_type_id'   => 1,
                        'receive_date'           => '2005-10-29 00:00:00',
                        /**
                        'contribution_date_low'  => array( 'Y' => '2005',
                                                           'd' => '28',
                                                           'M' => '10' )
                        **/
                        );
        $contribution = crm_get_contribution($params);
        CRM_Core_Error::debug( 'c', $contribution );
    }
}

?>
