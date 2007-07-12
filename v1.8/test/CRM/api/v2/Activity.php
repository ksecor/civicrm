<?php

require_once 'api/v2/Activity.php';
require_once 'api/v2/Contact.php';

class TestOfActivityAPI extends UnitTestCase {
    
    protected $_activity    = array();
    protected $_individual  = array();
    
    function setUp() 
    {
        // make sure this is just _41 and _data
    }
    
    function tearDown() 
    {
    }
    
    function testCreateIndividual() 
    {
        $params = array(
                        'first_name'   => 'Apoorva',
                        'last_name'    => 'Mehta',
                        'contact_type' => 'Individual',
                        );
        $contact =& civicrm_contact_add($params);
        $this->assertNotNull( $contact['contact_id'] );
        $this->_individual = $contact;
    }

    function testCreateMeetingActivity()
    {
        $params = array(
                        'source_contact_id' => 101,
                        'target_entity_table' => 'civicrm_contact',
                        'target_entity_id' => $this->_individual['contact_id'],
                        'subject' => 'hello',
                        'scheduled_date_time' => date('Ymd'),
                        'duration_hours' =>30,
                        'duration_minutes' => 20,
                        'location' => 'Pensulvania',
                        'details' => 'a meeting activity',
                        'status' => 'Scheduled',
                        'parent_id' => 1, 
                        'activity_name' =>'Meeting',
                        );
        $activity =& civicrm_activity_create($params);
        $this->assertEqual( $activity['source_contact_id'], 101 );
        $this->assertEqual( $activity['target_entity_table'], 'civicrm_contact' );
        $this->assertEqual( $activity['target_entity_id'], $this->_individual['contact_id'] );
        $this->assertEqual( $activity['subject'], 'hello' );
        $this->assertEqual( $activity['scheduled_date_time'], date('Ymd') );
        $this->assertEqual( $activity['duration_hours'], 30 );
        $this->assertEqual( $activity['duration_minutes'], 20 );
        $this->assertEqual( $activity['location'], 'Pensulvania' );
        $this->assertEqual( $activity['details'], 'a meeting activity' );
        $this->assertEqual( $activity['status'], 'Scheduled' );
        $this->assertEqual( $activity['parent_id'], 1 );
        $this->_activity['meeting'] = $activity;
    }

    function testCreateBadActivityWithoutName()
    {
        $params = array(
                        'source_contact_id' => 101,
                        'target_entity_table' => 'civicrm_contact',
                        'target_entity_id' => $this->_individual['contact_id'],
                        'subject' => 'hello',
                        'scheduled_date_time' => date('Ymd'),
                        'duration_hours' =>30,
                        'duration_minutes' => 20,
                        'location' => 'Pensulvania',
                        'details' => 'normal activity',
                        'status' => 'Scheduled',
                        'parent_id' => 1, 
                        );
        $activity =& civicrm_activity_create($params);
        $this->assertEqual( $activity['is_error'], 1 );
    }

    function testCreateActivity()
    {
        $params = array(
                        'source_contact_id' => 101,
                        'target_entity_table' => 'civicrm_contact',
                        'target_entity_id' => $this->_individual['contact_id'],
                        'subject' => 'hello',
                        'scheduled_date_time' => date('Ymd'),
                        'duration_hours' =>30,
                        'duration_minutes' => 20,
                        'location' => 'Pensulvania',
                        'details' => 'normal activity',
                        'status' => 'Scheduled',
                        'parent_id' => 1, 
                        'activity_name' =>'Activity',
                        );
        $activity =& civicrm_activity_create($params);
        $this->assertEqual( $activity['source_contact_id'], 101 );
        $this->assertEqual( $activity['target_entity_table'], 'civicrm_contact' );
        $this->assertEqual( $activity['target_entity_id'], $this->_individual['contact_id'] );
        $this->assertEqual( $activity['subject'], 'hello' );
        $this->assertEqual( $activity['scheduled_date_time'], date('Ymd') );
        $this->assertEqual( $activity['duration_hours'], 30 );
        $this->assertEqual( $activity['duration_minutes'], 20 );
        $this->assertEqual( $activity['location'], 'Pensulvania' );
        $this->assertEqual( $activity['details'], 'normal activity' );
        $this->assertEqual( $activity['status'], 'Scheduled' );
        $this->assertEqual( $activity['parent_id'], 1 );
        $this->_activity['activity'] = $activity;
    }

    function testGetActivities()
    {
        $activity =& civicrm_activities_get_contact($this->_individual['contact_id']);

        $this->assertEqual( count($activity['meeting']),   1 );
        $this->assertEqual( count($activity['activity']),  1 );
        $this->assertEqual( count($activity['phonecall']), 0 );
    }

    function testUpdateActivity()
    {
        $params = array(
                        'source_contact_id' => 101,
                        'target_entity_table' => 'civicrm_contact',
                        'target_entity_id' => $this->_individual['contact_id'],
                        'subject' => 'hello',
                        'duration_hours' =>30,
                        'duration_minutes' => 20,
                        'location' => 'Pensulvania',
                        'details' => 'a meeting activity',
                        'status' => 'Scheduled',
                        'parent_id' => 1, 
                        'activity_name' =>'Meeting',
                        );

        $dao =& new CRM_Activity_DAO_Meeting( );
        $dao->copyValues( $params );
        if ( $dao->find( true ) ) {
            $this->_meetingID = $dao->id;
        }

        $params = array(
                        'id' => $this->_meetingID,
                        'source_contact_id' => 101,
                        'activity_type_id' => 1,
                        'target_entity_table' => 'civicrm_activity',
                        'target_entity_id' => 80,
                        'subject' => 'Meeting at 9 pm',
                        'duration_hours' => 40,
                        'duration_minutes' => 50,
                        'location' => 'Pensulvania changed',
                        'details' => 'meeting activity changed',
                        'Status' => 'Scheduled',
                        'parent_id' => null,
                        'activity_name'=> 'Meeting',
                        );
        $activity =& civicrm_activity_update($params);
        $this->assertEqual( $activity['id'], $this->_meetingID );
        $this->assertEqual( $activity['source_contact_id'], 101 );
        $this->assertEqual( $activity['target_entity_table'], 'civicrm_activity' );
        $this->assertEqual( $activity['target_entity_id'], 80 );
        $this->assertEqual( $activity['subject'], 'Meeting at 9 pm' );
        $this->assertEqual( $activity['duration_hours'], 40 );
        $this->assertEqual( $activity['duration_minutes'], 50 );
        $this->assertEqual( $activity['location'], 'Pensulvania changed' );
        $this->assertEqual( $activity['details'], 'meeting activity changed' );
        $this->assertEqual( $activity['status'], 'Scheduled' );
        $this->assertEqual( $activity['parent_id'], 'null' );
    }

    function testDeleteActivity()
    {
        $params = array(
                        'id' => $this->_meetingID,
                        'activity_name' => 'Meeting',
                        );
        $activity =& civicrm_activity_delete($params);
    }
    
}

?>
