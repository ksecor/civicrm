<?php

require_once 'api/crm.php';

class TestOfGetHistoryAPI extends UnitTestCase {

    function setUp( ) {
    }

    function tearDown( ) {
    }

    function testGetAllHistory()
    {
        CRM_Core_Error::le_method();
        $rows =& crm_get_activity_history(null, null, null, null);
        CRM_Core_Error::ll_method();
    }
}
?>