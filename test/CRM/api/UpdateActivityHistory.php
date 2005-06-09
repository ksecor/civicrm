<?php
require_once 'api/crm.php';
require_once 'CRM/Core/DAO/ActivityHistory.php';

class TestOfUpdateHistoryAPI extends UnitTestCase {

    function setUp( ) {
    }
    
    function tearDown( ) {
    }

    function testnullObject()
    {
        CRM_Core_Error::le_method();

        $historyDAO = null;
        $param = array();
        //$historyDAO =& crm_update_activity_history($historyDAO);

        $historyDAO =& crm_update_activity_history($historyDAO, $param);

        CRM_Core_Error::debug_var('historyDAO', $historyDAO);

        $this->assertNotA($historyDAO, 'CRM_Core_Error');

        CRM_Core_Error::ll_method();
    }





}
?>