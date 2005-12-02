<?php
require_once 'api/crm.php';
require_once 'CRM/Core/DAO/ActivityHistory.php';

class TestOfDeleteHistoryAPI extends UnitTestCase {

    function setUp()
    {
    }

    function tearDown()
    {
    }

    function testDelete1()
    {
        // create an activity history
        $params = array('entity_id' => 24, 'activity_id' => 111);

        $dao1 =& crm_create_activity_history($params);
        $this->assertIsA($dao1, 'CRM_Core_DAO_ActivityHistory');
        $this->assertEqual($dao1->entity_id, 24);        
        $this->assertEqual($dao1->activity_id, 111);        


        //CRM_Core_Error::debug_var('dao1', $dao1);

        // delete the record now
        $rowAffected =& crm_delete_activity_history($dao1);        
        //$this->assertIsA($dao2, 'CRM_Core_DAO_ActivityHistory');
        $this->assertEqual($rowAffected, 1);
    }

    function testDelete2()
    {
        // create an activity history
        $params = array('entity_id' => 24, 'activity_id' => 111);
        $dao1 =& crm_create_activity_history($params);
        $this->assertIsA($dao1, 'CRM_Core_DAO_ActivityHistory');
        $this->assertEqual($dao1->entity_id, 24);        
        $this->assertEqual($dao1->activity_id, 111);        

        // another one
        $params = array('entity_id' => 24, 'activity_id' => 112);
        $dao1 =& crm_create_activity_history($params);
        $this->assertIsA($dao1, 'CRM_Core_DAO_ActivityHistory');
        $this->assertEqual($dao1->entity_id, 24);        
        $this->assertEqual($dao1->activity_id, 112);        


        // delete the record now
        $dao1 = new CRM_Core_DAO_ActivityHistory();
        $dao1->entity_id = 24;
        $rowAffected =& crm_delete_activity_history($dao1);        
        $this->assertEqual($rowAffected, 2);
    }

    function testDelete3()
    {
        // create an activity history
        $params = array('entity_id' => 5, 'activity_id' => 20);
        $dao1 =& crm_create_activity_history($params);
        $this->assertIsA($dao1, 'CRM_Core_DAO_ActivityHistory');
        $this->assertEqual($dao1->entity_id, 5);        
        $this->assertEqual($dao1->activity_id, 20);        


        // another one
        $params = array('entity_id' => 5, 'activity_id' => 21);
        $dao1 =& crm_create_activity_history($params);
        $this->assertIsA($dao1, 'CRM_Core_DAO_ActivityHistory');
        $this->assertEqual($dao1->entity_id, 5);        
        $this->assertEqual($dao1->activity_id, 21);        

        // delete the record now
        $dao1 = new CRM_Core_DAO_ActivityHistory();
        $dao1->activity_id = 21;
        $rowAffected =& crm_delete_activity_history($dao1);
        
        $this->assertEqual($rowAffected,'1');
    }
}
?>