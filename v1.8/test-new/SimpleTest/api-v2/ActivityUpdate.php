<?php

require_once 'api/v2/Activity.php';

/**
 * Class contains api test cases for "civicrm_activity_update"
 *
 */
class TestOfActivityUpdateAPIV2 extends CiviUnitTestCase 
{
    protected $_individualSourceId;
    protected $_individualTargetId;
    protected $_activityId;

    function setUp() 
    {
        $activity = $this->activityCreate( );

        $this->_activityId         = $activity['id'];
        $this->_individualSourceId = $activity['source_contact_id'];
        $this->_individualTargetId = $activity['target_entity_id'];
    }
    
    /**
     * check with empty array
     */
    function testActivityUpdateEmpty( )
    {
        $params = array( );
        $result =& civicrm_activity_update($params);
        $this->assertEqual( $result['is_error'], 1 );
    }

    /**
     * check if required fields are not passed
     */
    function testActivityUpdateWithoutRequired( )
    {
        $params = array(
                        'subject'             => 'this case should fail',
                        'scheduled_date_time' => date('Ymd')
                        );
        
        $result =& civicrm_activity_update($params);
        $this->assertEqual( $result['is_error'], 1 );
    }

    /**
     * check with incorrect required fields
     */
    function testActivityUpdateWithIncorrectData( )
    {
        $params = array(
                        'activity_name'       => 'Meeting',
                        'subject'             => 'this case should fail',
                        'scheduled_date_time' => date('Ymd')
                        );

        $result =& civicrm_activity_update($params);
        $this->assertEqual( $result['is_error'], 1 );
    }

    /**
     * check with incorrect required fields
     */
    function testActivityUpdateWithIncorrectId( )
    {
        $params = array( 'id'                  => 'lets break it',
                         'activity_name'       => 'Meeting',
                         'subject'             => 'this case should fail',
                         'scheduled_date_time' => date('Ymd')
                         );

        $result =& civicrm_activity_update($params);
        $this->assertEqual( $result['is_error'], 1 );
    }

    /**
     * check with incorrect required fields
     */
    function testActivityUpdateWithIncorrectContactActivityType( )
    {
        $params = array(
                        'id'                  => $this->_activityId,
                        'activity_name'       => 'Phone Call',
                        'subject'             => 'this case should fail',
                        'scheduled_date_time' => date('Ymd')
                        );

        $result =& civicrm_activity_update($params);
        $this->assertEqual( $result['source_contact_id'], null );
    }

    /**
     * this should create activity
     */
    function testActivityUpdate( )
    {
        $params = array(
                        'id'                  => $this->_activityId,
                        'subject'             => 'Update Discussion on Apis for v2',
                        'scheduled_date_time' => date('Ymd'),
                        'duration_hours'      => 15,
                        'duration_minutes'    => 20,
                        'location'            => '21, Park Avenue',
                        'details'             => 'Lets update Meeting',
                        'status'              => 'Scheduled',
                        'activity_name'       => 'Meeting',
                        );

        $result =& civicrm_activity_update( $params );
        $this->assertEqual( $result['is_error'], 0 );
    }

    /**
     * check activity update with status
     */
    function testActivityUpdateWithStatus( )
    {
        $params = array(
                        'id'                  => $this->_activityId,
                        'source_contact_id'   => $this->_individualSourceId,
                        'target_entity_table' => 'civicrm_contact',
                        'target_entity_id'    => $this->_individualTargetId,
                        'subject'             => 'Hurry update works for other activities',
                        'status'              => 'Completed',
                        'activity_name'       => 'Meeting',
                        );

        $result =& civicrm_activity_update( $params );
        $this->assertEqual( $result['is_error'], 0 );
    }

    /**
     * create activity with custom data 
     * ( fix this once custom * v2 api are ready  )
     */
    function atestActivityUpdateWithCustomData( )
    {
        
    }
    
    function tearDown() 
    {
      $this->contactDelete( $this->_individualSourceId );
      $this->contactDelete( $this->_individualTargetId );
    }
}
 
?> 