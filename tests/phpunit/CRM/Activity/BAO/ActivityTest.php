<?php

require_once 'CiviTest/CiviUnitTestCase.php';
require_once 'CiviTest/Contact.php';

class CRM_Activity_BAO_ActivityTest extends CiviUnitTestCase 
{

    function get_info( ) 
    {
        return array(
                     'name'        => 'Activity BAOs',
                     'description' => 'Test all Activity_BAO_Activity methods.',
                     'group'       => 'CiviCRM BAO Tests',
                     );
    }
    
    function setUp( ) 
    {
        parent::setUp();
    }

    /**
     * testcases for create() method
     * create() method Add/Edit activity. 
     */
    function testCreate( )
    {
        $contactId = Contact::createIndividual( );
        
        $params = array( 
                        'source_contact_id'  => $contactId,
                        'subject'            => 'Scheduling Meeting',
                        'activity_type_id'   => 2
                        );
        
        require_once 'CRM/Activity/BAO/Activity.php';
        CRM_Activity_BAO_Activity::create( $params );
        
        $activityId = $this->assertDBNotNull( 'CRM_Activity_DAO_Activity', 'Scheduling Meeting' , 'id', 
                                              'subject', 'Database check for created activity.' );
        
        // Now call create() to modify an existing Activity
        
        $params = array( );
        $params = array(
                        'id'                 => $activityId,
                        'source_contact_id'  => $contactId,
                        'subject'            => 'Scheduling Interview',
                        'activity_type_id'   => 3
                        );

        CRM_Activity_BAO_Activity::create( $params );
        
        $activityTypeId = $this->assertDBNotNull( 'CRM_Activity_DAO_Activity', 'Scheduling Interview',
                                                  'activity_type_id',
                                                  'subject', 'Database check on updated activity record.' );
        $this->assertEquals( $activityTypeId, 3, 'Verify activity type id is 3.');
        
        Contact::delete( $contactId );
        
    }
    
    /**
     * testcase for getContactActivity() method. 
     * getContactActivity() method get activities detail for given target contact id 
     */
    function testGetContactActivity( )
    {
        $contactId = Contact::createIndividual( );
        $params    = array(
                           'first_name'     => 'liz',
                           'last_name'      => 'hurleey',
                           ); 
        $targetContactId = Contact::createIndividual( $params );
        
        $params = array( 
                        'source_contact_id'  => $contactId,
                        'subject'            => 'Scheduling Meeting',
                        'activity_type_id'   => 2,
                        'target_contact_id'  => array( $targetContactId ),
                        'activity_date_time' => date('Ymd'),
                        );
        
        require_once 'CRM/Activity/BAO/Activity.php';
        CRM_Activity_BAO_Activity::create( $params );
        
        $activityId = $this->assertDBNotNull( 'CRM_Activity_DAO_Activity', 'Scheduling Meeting' ,
                                              'id', 
                                              'subject', 'Database check for created activity.' );
        
        $activities = CRM_Activity_BAO_Activity::getContactActivity( $targetContactId );
        
        $this->assertEquals( $activities[$activityId]['subject'], 'Scheduling Meeting', 'Verify activity subject is correct.');
        
        Contact::delete( $contactId );
        Contact::delete( $targetContactId );
    }

    /**
     * testcase for retrieve() method. 
     * retrieve($params, $defaults) method return activity detail for given params
     *                              and set defaults.   
     */
    function testRetrieve ( )
    {
        $contactId = Contact::createIndividual( );
        $params    = array(
                           'first_name'     => 'liz',
                           'last_name'      => 'hurleey',
                           ); 
        $targetContactId = Contact::createIndividual( $params );
        
        $params = array( 
                        'source_contact_id'  => $contactId,
                        'subject'            => 'Scheduling Meeting',
                        'activity_type_id'   => 2,
                        'target_contact_id'  => array( $targetContactId ),
                        'activity_date_time' => date('Ymd'),
                        );
        
        require_once 'CRM/Activity/BAO/Activity.php';
        CRM_Activity_BAO_Activity::create( $params );
        
        $activityId = $this->assertDBNotNull( 'CRM_Activity_DAO_Activity', 'Scheduling Meeting' , 'id', 
                                              'subject', 'Database check for created activity.' );
        
        $activityTargetId = $this->assertDBNotNull( 'CRM_Activity_DAO_ActivityTarget', $targetContactId,
                                                    'id', 'target_contact_id',
                                                    'Database check for created activity target.' );

        $defaults = array();
        $activity = CRM_Activity_BAO_Activity::retrieve( $params, $defaults );
        
        $this->assertEquals( $activity->subject, 'Scheduling Meeting', 'Verify activity subject is correct.');
        $this->assertEquals( $activity->source_contact_id, $contactId, 'Verify source contact id is correct.');
        $this->assertEquals( $activity->activity_type_id, 2, 'Verify activity type id is correct.');
        
        $this->assertEquals( $defaults['subject'], 'Scheduling Meeting', 'Verify activity subject is correct.');
        $this->assertEquals( $defaults['source_contact_id'], $contactId, 'Verify source contact id is correct.');
        $this->assertEquals( $defaults['activity_type_id'], 2, 'Verify activity type id is correct.');
        
        $this->assertEquals( $defaults['target_contact'][1], $targetContactId, 'Verify target contact id is correct.');
        
        Contact::delete( $contactId );
        Contact::delete( $targetContactId );
    }
    
    /**
     * testcase for deleteActivity() method. 
     * deleteActivity($params) method deletes activity for given params.   
     */
    function testDeleteActivity ( )
    {
        $contactId = Contact::createIndividual( );
        $params    = array(
                           'first_name'     => 'liz',
                           'last_name'      => 'hurleey',
                           ); 
        $targetContactId = Contact::createIndividual( $params );
        
        $params = array( 
                        'source_contact_id'  => $contactId,
                        'subject'            => 'Scheduling Meeting',
                        'activity_type_id'   => 2,
                        'target_contact_id'  => array( $targetContactId ),
                        'activity_date_time' => date('Ymd'),
                        );
        
        require_once 'CRM/Activity/BAO/Activity.php';
        CRM_Activity_BAO_Activity::create( $params );
        
        $activityId = $this->assertDBNotNull( 'CRM_Activity_DAO_Activity', 'Scheduling Meeting' , 'id', 
                                              'subject', 'Database check for created activity.' );
        
        $activityTargetId = $this->assertDBNotNull( 'CRM_Activity_DAO_ActivityTarget', $targetContactId,
                                                    'id', 'target_contact_id',
                                                    'Database check for created activity target.' );
        $params = array( 
                        'source_contact_id'  => $contactId,
                        'subject'            => 'Scheduling Meeting',
                        'activity_type_id'   => 2,
                        );
        
        $result = CRM_Activity_BAO_Activity::deleteActivity( $params );
        
        $activityId = $this->assertDBNull( 'CRM_Activity_DAO_Activity', 'Scheduling Meeting' , 'id', 
                                           'subject', 'Database check for deleted activity.' );
        Contact::delete( $contactId );
        Contact::delete( $targetContactId );

    }

    /**
     * testcase for deleteActivityTarget() method. 
     * deleteActivityTarget($activityId) method deletes activity target for given activity id.   
     */
    function testDeleteActivityTarget ( )
    {
        $contactId = Contact::createIndividual( );
        $params    = array(
                           'first_name'     => 'liz',
                           'last_name'      => 'hurleey',
                           ); 
        $targetContactId = Contact::createIndividual( $params );
        
        $params = array( 
                        'source_contact_id'  => $contactId,
                        'subject'            => 'Scheduling Meeting',
                        'activity_type_id'   => 2,
                        'target_contact_id'  => array( $targetContactId ),
                        'activity_date_time' => date('Ymd'),
                        );
        
        require_once 'CRM/Activity/BAO/Activity.php';
        CRM_Activity_BAO_Activity::create( $params );
        
        $activityId = $this->assertDBNotNull( 'CRM_Activity_DAO_Activity', 'Scheduling Meeting' , 'id', 
                                              'subject', 'Database check for created activity.' );
        
        $activityTargetId = $this->assertDBNotNull( 'CRM_Activity_DAO_ActivityTarget', $targetContactId,
                                                    'id', 'target_contact_id', 
                                                    'Database check for created activity target.' );
        
        CRM_Activity_BAO_Activity::deleteActivityTarget( $activityId );
        
        $this->assertDBNull( 'CRM_Activity_DAO_ActivityTarget', $targetContactId, 'id', 
                             'target_contact_id', 'Database check for deleted activity target.' );
        
        Contact::delete( $contactId );
        Contact::delete( $targetContactId );
    }
    
    /**
     * testcase for deleteActivityAssignment() method. 
     * deleteActivityAssignment($activityId) method deletes activity assignment for given activity id.   
     */
    function testDeleteActivityAssignment ( )
    {
        $contactId = Contact::createIndividual( );
        $params    = array(
                           'first_name'     => 'liz',
                           'last_name'      => 'hurleey',
                           ); 
        $assigneeContactId = Contact::createIndividual( $params );
        
        $params = array( 
                        'source_contact_id'  => $contactId,
                        'subject'            => 'Scheduling Meeting',
                        'activity_type_id'   => 2,
                        'assignee_contact_id'=> array( $assigneeContactId ),
                        'activity_date_time' => date('Ymd'),
                        );
        
        require_once 'CRM/Activity/BAO/Activity.php';
        CRM_Activity_BAO_Activity::create( $params );
        
        $activityId = $this->assertDBNotNull( 'CRM_Activity_DAO_Activity', 'Scheduling Meeting' , 'id', 
                                              'subject', 'Database check for created activity.' );
        
        $activityAssignmentId = $this->assertDBNotNull( 'CRM_Activity_DAO_ActivityAssignment', 
                                                        $assigneeContactId, 'id', 'target_contact_id', 
                                                        'Database check for created activity assignment.' );
        
        CRM_Activity_BAO_Activity::deleteActivityAssignment( $activityId );
        
        $this->assertDBNull( 'CRM_Activity_DAO_ActivityAssignment', $assigneeContactId, 'id', 
                             'assignee_contact_id', 'Database check for deleted activity assignment.' );
        
        Contact::delete( $contactId );
        Contact::delete( $assigneeContactId );
    }
    
}
