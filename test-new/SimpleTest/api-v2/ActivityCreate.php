<?php

require_once 'api/v2/Activity.php';

class TestOfActivityCreateAPIV2 extends CiviUnitTestCase 
{
    protected $_individualSourceID;
    protected $_individualTargetID;
    
    function setUp() 
    {
        $this->_individualSourceID = $this->individualCreate( );
        
        $contactParams = array( 'first_name'       => 'Julia',
                                'Last_name'        => 'Anderson',
                                'prefix'           => 'Ms',
                                'email'            => 'julia_anderson@civicrm.org',
                                'contact_type'     => 'Individual');
        
        $this->_individualTargetID = $this->individualCreate( $contactParams );
    }
    
    /**
     * check with empty array
     */
    function testActivityCreateEmpty( )
    {
        $params = array( );
        $activity = & civicrm_activity_create($params);
        $this->assertEqual( $activity['is_error'], 1 );
    }

    /**
     * check if required fields are not passed
     */
    function testActivityCreateWithoutRequired( )
    {
        $params = array(
                        'subject'             => 'this case should fail',
                        'scheduled_date_time' => date('Ymd')
                        );
        
        $activity = & civicrm_activity_create($params);
        $this->assertEqual( $activity['is_error'], 1 );
    }

    /**
     * check with incorrect required fields
     */
    function testActivityCreateWithIncorrectData( )
    {
        $params = array(
                        'activity_name'       => 'Breaking Activity',
                        'subject'             => 'this case should fail',
                        'scheduled_date_time' => date('Ymd')
                        );

        $activity = & civicrm_activity_create($params);
        $this->assertEqual( $activity['is_error'], 1 );
    }

    /**
     * check with incorrect required fields
     */
    function testActivityCreateWithIncorrectContactId( )
    {
        $params = array(
                        'activity_name'       => 'Meeting',
                        'source_contact_id'   => 101,
                        'subject'             => 'this case should fail',
                        'scheduled_date_time' => date('Ymd')
                        );

        $activity = & civicrm_activity_create($params);
        $this->assertEqual( $activity['is_error'], 1 );
    }

          
    /**
     * this should create activity
     */
    function testActivityCreate( )
    {
        $params = array(
                        'source_contact_id'   => $this->_individualSourceID,
                        'target_entity_table' => 'civicrm_contact',
                        'target_entity_id'    => $this->_individualTargetID,
                        'subject'             => 'Discussion on Apis for v2',
                        'scheduled_date_time' => date('Ymd'),
                        'duration_hours'      => 30,
                        'duration_minutes'    => 20,
                        'location'            => 'Pensulvania',
                        'details'             => 'a meeting activity',
                        'status'              => 'Scheduled',
                        'activity_name'       => 'Phone Call',
                        );

        $activity = & civicrm_activity_create( $params );
        $this->assertEqual( $activity['is_error'], 0 );
    }

    function testOtherActivityCreate( )
    {
        //create activity type
        //create other activity
    }
    
    function tearDown() 
    {
      $this->contactDelete( $this->_individualSourceID );
      $this->contactDelete( $this->_individualTargetID );
    }
}
 
?> 