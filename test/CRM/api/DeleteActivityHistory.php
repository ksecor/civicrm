<?php
require_once 'api/crm.php';
require_once 'CRM/Core/DAO/ActivityHistory.php';

class TestOfDeleteHistoryAPI extends UnitTestCase 
{
    function setUp()
    {
    }
    
    function tearDown()
    {
    }
    
    function testCreateIndividual()
    {
        $params = array('first_name' => 'abc1',
                        'last_name' => 'xyz1'
                        );
        $contact =& crm_create_contact($params, 'Individual');
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->contact_type, 'Individual');
        $this->_individual = $contact;   
    }
    
    function testDelete1()
    {
        // create an activity history
        $params = array('entity_id' => $this->_individual->id, 'activity_id' => 111);
        $dao1 =& crm_create_activity_history($params);
        $this->assertIsA($dao1, 'CRM_Core_DAO_ActivityHistory');
        $this->assertEqual($dao1->entity_id, $this->_individual->id);
        $this->assertEqual($dao1->activity_id, 111);        

        // delete the record now
        $rowAffected =& crm_delete_activity_history($dao1);      
        $this->assertEqual($rowAffected, 1);
    }

    function testDelete2()
    {
        // create an activity history
        $params = array('entity_id' => $this->_individual->id, 'activity_id' => 111);
        $dao1 =& crm_create_activity_history($params);
        $this->assertIsA($dao1, 'CRM_Core_DAO_ActivityHistory');
        $this->assertEqual($dao1->entity_id, $this->_individual->id);
        $this->assertEqual($dao1->activity_id, 111);        
        
        // another one
        $params = array('entity_id' => $this->_individual->id, 'activity_id' => 112);
        $dao1 =& crm_create_activity_history($params);
        $this->assertIsA($dao1, 'CRM_Core_DAO_ActivityHistory');
        $this->assertEqual($dao1->entity_id, $this->_individual->id);        
        $this->assertEqual($dao1->activity_id, 112);        
        
        // delete the record now
        $dao1 = new CRM_Core_DAO_ActivityHistory();
        $dao1->entity_id = $this->_individual->id;
        $rowAffected =& crm_delete_activity_history($dao1);  
        $this->assertEqual($rowAffected, 2);
    }
    
    function testDelete3()
    {
        // create an activity history
        $params = array('entity_id' => $this->_individual->id, 'activity_id' => 20);
        $dao1 =& crm_create_activity_history($params);
        $this->assertIsA($dao1, 'CRM_Core_DAO_ActivityHistory');
        $this->assertEqual($dao1->entity_id, $this->_individual->id);        
        $this->assertEqual($dao1->activity_id, 20);        
        
        // another one
        $params = array('entity_id' => $this->_individual->id, 'activity_id' => 113);
        $dao1 =& crm_create_activity_history($params);
        $this->assertIsA($dao1, 'CRM_Core_DAO_ActivityHistory');
        $this->assertEqual($dao1->entity_id, $this->_individual->id);        
        $this->assertEqual($dao1->activity_id, 113);        
        
        // delete the record now
        $dao1 = new CRM_Core_DAO_ActivityHistory();
        $dao1->activity_id = 113;
        $rowAffected =& crm_delete_activity_history($dao1);
        
        $this->assertEqual($rowAffected,1);
    }
    
    function testDeleteIndividual()
    {
        $contact = $this->_individual;
        $val =& crm_delete_contact(& $contact);
        $this->assertNull($val);
    }
}
?>
