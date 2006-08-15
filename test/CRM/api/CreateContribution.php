<?php

require_once 'api/crm.php';

class TestOfCreateContribution extends UnitTestCase {
    
    function testCreateContribution()
    {
        $contribution = array(
                              'domain_id'             => 1,
                              'contact_id'            => 101,
                              'receive_date'          => date('Ymd'),
                              'total_amount'          => 100.00,
                              'contribution_type_id'  => 3,
                              );
        $result = crm_create_contribution($contribution);
        CRM_Core_Error::debug( 'r', $result );
    }

}

?>