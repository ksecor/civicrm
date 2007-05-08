<?php

require_once 'api/v2/EmailHistory.php';

class TestOfEmailHistoryAPIV2 extends UnitTestCase 
{
    function setUp() 
    {
        // make sure this is just _41 and _generated
    }
    
    function tearDown() 
    {
    }
    
    function testEmailHistory( )
    {
        $params = array( 'subject' => 'Test CiviCRM v2 API',
                         'message' => 'This is a long long message, how long can a GET string be, should we also support POST?',
                         'contact_id' => 102,
                         'from_name'  => 'Donald A. Lobo <lobo@yahoo.com>',
                         'recipient_id_1' => 1,
                         'recipient_name_1' => 'Numero Uno',
                         'recipient_id_2' => 2,
                         'recipient_name_2' => 'Numero Dos',
                         'recipient_id_3' => 3,
                         'recipient_name_3' => 'Numero Tres' );
        $result = civicrm_email_history_add( $params );
        $this->assertEqual( $result['is_error'], 0 );
    }
}

?>
