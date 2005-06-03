<?php

require_once 'api/crm.php';

class TestOfCreateHistoryAPI extends UnitTestCase {

    function setUp( ) {
    }

    function tearDown( ) {
    }

    function testCreateNoParam()
    {
        $params = array();
        $history =& crm_create_activity_history($params);
        $this->assertIsA($history, 'CRM_Core_Error');
    }

    function testCreateOnlyEntityId()
    {
        $params = array('entity_id' => 1);
        $history =& crm_create_activity_history($params);
        $this->assertIsA($history, 'CRM_Core_Error');
    }



}

?>