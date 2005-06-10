<?php
require_once 'api/crm.php';
require_once 'CRM/Core/DAO/ActivityHistory.php';

class TestOfUpdateHistoryAPI extends UnitTestCase {

    function setUp()
    {
    }

    function tearDown()
    {
    }

    function testNullUpdate()
    {
        $historyDAO = null;
        $param = array();
        $historyDAO =& crm_update_activity_history($historyDAO, $param);
        $this->assertIsA($historyDAO, 'CRM_Core_Error');
    }

    function testUpdateEntityId()
    {
        // create an activity history
        $params = array('entity_id' => 23, 'activity_id' => 112);

        $dao1 =& crm_create_activity_history($params);
        $this->assertIsA($dao1, 'CRM_Core_DAO_ActivityHistory');
        //CRM_Core_Error::debug_var('dao1', $dao1);

        $params = array('id' => $dao1->id, 'activity_type' => 'type1');
        $dao2 =& crm_update_activity_history($dao1, $params);
        //CRM_Core_Error::debug_var('dao2', $dao2);
        
        // do the checks please
        $this->assertIsA($dao2, 'CRM_Core_DAO_ActivityHistory');
        $this->assertEqual($dao2->activity_type, 'type1');        
        $this->assertEqual($dao2->activity_id, 112);        
        $this->assertEqual($dao2->entity_id, 23);        

        $params = array('id' => $dao2->id);        
        $dao3 =& crm_update_activity_history($dao2, $params);
        //CRM_Core_Error::debug_var('dao3', $dao3);

        // do the checks please
        $this->assertIsA($dao3, 'CRM_Core_DAO_ActivityHistory');
        $this->assertEqual($dao3->activity_type, 'type1');        
        $this->assertEqual($dao3->activity_id, 112);        
        $this->assertEqual($dao3->entity_id, 23);        


        $params = array('id' => $dao3->id, 'activity_type' => '');        
        $dao4 =& crm_update_activity_history($dao3, $params);
        //CRM_Core_Error::debug_var('dao4', $dao4);

        // do the checks please
        $this->assertIsA($dao4, 'CRM_Core_DAO_ActivityHistory');
        $this->assertNotEqual($dao4->activity_type, 'type1');        
        $this->assertEqual($dao4->activity_id, 112);        
        $this->assertEqual($dao4->entity_id, 23);        




        $params = array('id' => $dao4->id, 'activity_id' => '');        
        //CRM_Core_Error::debug_var('params', $params);
        $dao5 =& crm_update_activity_history($dao4, $params);
        //CRM_Core_Error::debug_var('dao5', $dao5);

        // do the checks please
        $this->assertIsA($dao5, 'CRM_Core_DAO_ActivityHistory');
        $this->assertNotEqual($dao5->activity_type, 'type1');        
        $this->assertEqual($dao5->activity_id, 112);        
        $this->assertEqual($dao5->entity_id, 23);        


    }

}
?>