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

    function testCreateOnlyEntityIdActivityId()
    {
        $params = array('entity_id' => 1, 'activity_id' => 1);
        $history =& crm_create_activity_history($params);
        $this->assertIsA($history, 'CRM_Core_DAO_ActivityHistory');
    }

    function testCreateFull()
    {
        $params = array('entity_id' => 2, 
                        'activity_id' => 2,
                        'entity_table' => 'crm_contact',
                        'activity_type' => 'Phone Call',
                        'module' => 'module1',
                        'callback' => 'callback1',
                        'activity_summary' => 'summary 1',
                        'activity_date' => '20041012',
                        );
        $history =& crm_create_activity_history($params);
        $this->assertIsA($history, 'CRM_Core_DAO_ActivityHistory');
    }
}
?>