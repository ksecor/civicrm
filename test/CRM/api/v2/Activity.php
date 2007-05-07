<?php

require_once 'api/v2/Activity.php';

class TestOfActivityAPI extends UnitTestCase {
    
    function testCreateActivity()
    {
        $params = array('id' => 1,
                        'source_contact_id' => 101,
                        'activity_type_id' => 1,
                        'target_entity_table' => 'civicrm_contact',
                        'target_entity_id' => 80,
                        'subject' => 'hello',
                        'scheduled_date_time' =>'01/01/2006',
                        'duration_hours' =>30,
                        'duration_minutes' => 20,
                        'location' => 'Pensulvania',
                        'details' => '++++++++++',
                        'Status' => 'Scheduled',
                        'parent_id' => 1, 
                        //          'activity_name' =>'Meeting',
                        );
        $activity=& civicrm_create_activity($params);
        $this->assertEqual( $activity['source_contact_id'], 101 );
        
        
    }
    function testUpdateActivity()
    {
        $params = array('id' => 1,
                        'source_contact_id' => 101,
                        'activity_type_id' => 1,
                        'target_entity_table' => 'civicrm_activity',
                        'target_entity_id' => 80,
                        'subject' => 'Meeting at 8 pm',
                        'scheduled_date_time' =>'01/01/2006',
                        'duration_hours' =>30,
                        'duration_minutes' => 20,
                        'location' => 'Pensulvania',
                        'details' => '++++++++++',
                        'Status' => 'Scheduled',
                        'parent_id' => 1,
                        // 'activity_name'=> "Meeting",
                         );
        $activity=& civicrm_update_activity($params);
        $this->assertEqual( $activity['source_contact_id'], 101 );
        
    }
    function testDeleteActivity()
    {
        $params = array('id' => 10,
                        'source_contact_id' => 101,
                        'activity_type_id' => 1,
                        'target_entity_table' => 'civicrm_contact',
                        'target_entity_id' => 80,
                        'subject' => 'hello again',
                        'scheduled_date_time' =>'01/01/2006',
                        'duration_hours' =>30,
                        'duration_minutes' => 20,
                        'location' => 'Pensulvania',
                        'details' => '++++++++++',
                        'Status' => 'Scheduled',
                        'parent_id' => 1, 
                        //   'activity_name'=> "Meeting",
                        );
        $activity=& civicrm_create_activity($params);
        $params = array('id' => 10,
                        'activity_type_id' => 1,
                        );
        $activity=& civicrm_delete_activity($params);
    
      
    }
    
    function testGetActivitie()
    {
        $params = array('id' => 5,
                        'source_contact_id' => 101,
                        'activity_type_id' => 1,
                        'target_entity_table' => 'civicrm_activity',
                        'target_entity_id' => 80,
                        'subject' => 'Meeting at 8 pm',
                        'scheduled_date_time' =>'01/01/2006',
                        'duration_hours' =>30,
                        'duration_minutes' => 20,
                        'location' => 'Pensulvania',
                        'details' => '++++++++++',
                        'Status' => 'Scheduled',
                        'parent_id' => 1    
                        //   'activity_name'=> 'Meeting',
                         );
     
        $activity=& civicrm_create_activity($params);
        
        $contact = 80;
        $activity=& civicrm_get_contact_activities($contact);
     
    }
}

?>
