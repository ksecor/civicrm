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
                        'activity_name' =>'Meeting',
                        );
        $activity=& civicrm_activity_create($params);
        $this->assertEqual( $activity['source_contact_id'], 101 );
        $this->assertEqual( $activity['subject'], 'hello' );
       
        
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
                        'activity_name'=> 'Meeting',
                         );
        $activity=& civicrm_activity_update($params);
        $this->assertEqual( $activity['source_contact_id'], 101 );
        $this->assertEqual( $activity['subject'],'Meeting at 8 pm' );
      

        
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
                        'activity_name'=> "Meeting",
                        );
        $activity=& civicrm_activity_create($params);
        $params = array('id' => 10,
                        'activity_name' => 'Meeting',
                        );
        $activity=& civicrm_activity_delete($params);
    
      
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
                        'parent_id' => 1 ,   
                        'activity_name'=> 'Meeting',
                         );
     
        $activity=& civicrm_activity_create($params);
        
        $contact = 80;
        $activity=& civicrm_activities_get_contact($contact);
     
    }
}

?>
