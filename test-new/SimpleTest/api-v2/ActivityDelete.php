<?php

require_once 'api/v2/Activity.php';

class TestOfActivityDeleteAPIV2 extends CiviUnitTestCase {
    
    
    function setUp() 
    {
        $this->individualSourceID    = $this->individualCreate( );
        $this->individualTargetID    = $this->individualCreate( );
    }
    
    function tearDown() 
    {
        $this->contactDelete( $this->individualSourceID );
        $this->contactDelete( $this->individualTargetID );
    }
 
    function testDeleteActivityWithoutId()
    {
        $params = array('activity_name' => 'Meeting');
        $activity =& civicrm_activity_delete($params);
        $this->assertEqual( $activity['is_error'], 1 );
    }

    function testDeleteActivityWithoutName()
    {
        $activity = $this->activityCreate( $this->individualSourceID, $this->individualTargetID);
        $params = array( 'id' => $activity['id'] );
        $activity =& civicrm_activity_delete($params);
        $this->assertEqual( $activity['is_error'], 1 );
    }
    function testDeleteActivity()
    {
        $activity = $this->activityCreate( $this->individualSourceID, $this->individualTargetID);
        $params = array(
                        'id' => $activity['id'],
                        'activity_name' => 'Meeting',
                        );
        $activity =& civicrm_activity_delete($params);
        $this->assertEqual( $activity['is_error'], 0 );
    }
    
}

?>
