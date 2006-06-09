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
        $params = array('entity_id' => 19, 
                        'activity_id' => 0,
                        'entity_table' => 'civicrm_contact',
                        'activity_type' => 'Donor Solicitation',
                        'module' => '',
                        'callback' => '',
                        'activity_summary' => 'Form letter and follow-up phone call.',
                        'activity_date' => '20060422',
                        'relationship_id' => '151'
                        );
        $history =& crm_create_activity_history($params);
        $this->assertIsA($history, 'CRM_Core_DAO_ActivityHistory');
    }
}
?>