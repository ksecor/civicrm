<?php

require_once 'api/crm.php';

class TestOfGetActivityHistoryAPI extends UnitTestCase 
{
    private $_history1;
    
    function setUp( ) 
    {
    }
    
    function tearDown( ) 
    {
    }
    
    function testCreateActivityHistory()
    {
        $params = array('entity_id'        => 2,
                        'activity_id'      => 2,
                        'entity_table'     => 'civicrm_contact',
                        'activity_type'    => 'Phone Call',
                        'module'           => 'module1',
                        'callback'         => 'callback1',
                        'activity_summary' => 'summary for entity 2 activity 2 Phone Call module 1 callback 1',
                        'activity_date'    => '20051011'
                        );
        $history =& crm_create_activity_history($params);
        $this->assertIsA($history, 'CRM_Core_DAO_ActivityHistory');
    }
    
    function testGetAllHistory()
    {
        $params = array(
                        'entity_table'  => 'civicrm_contact',
                        'entity_id'     => 2,
                        'module'        => 'module1',
                        'activity_type' => 'Phone Call'
                        );
        $sort   = array('activity_date' => 'DESC');
        $offset = 1;
        $numRow = 1;
        $rows =& crm_get_activity_history($params, $offset, $numRow, $sort);
        foreach ($rows as $id => $array) {
            $this->assertEqual($array['id']           , $id              );
            $this->assertEqual($array['entity_id']    , 2                );
            $this->assertEqual($array['activity_type'], 'Phone Call'     );
            $this->assertEqual($array['entity_table'] , 'civicrm_contact');
        }
    }
}
?>