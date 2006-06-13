<?php

require_once 'api/crm.php';

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
                        );
        $activityName = 'Phone Call';
        $activity=& crm_create_activity($params, $activityName);
        CRM_Core_Error::debug('Create Activity Details => ',$activity);
        
    }
    function testUpdateActivity()
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
                         );
        $activityName = 'Meeting ';
        $activity=& crm_update_activity($params, $activityName);
        CRM_Core_Error::debug('Update  Activity Details => ',$activity);
        
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
                        );
        $activityName = 'Phone Call';
        $activity=& crm_create_activity($params, $activityName);
        //CRM_Core_Error::debug('Create Activity Details => ',$activity);
        
        $params = array('id' => 10);
        $activityName = 'Phone Call ';
        $activity=& crm_delete_activity($params, $activityName);
        CRM_Core_Error::debug('Record Deleted Successfully !!', $activity);
      
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
                         );
        $activityName = 'Phone Call';
        $activity=& crm_create_activity($params, $activityName);
        
        $contact = 80;
        $activity=& crm_get_contact_activities($contact);
        CRM_Core_Error::debug('Display Activity Details => ',$activity);
    }
}

?>