<?php

require_once 'api/v2/Activity.php';

class TestOfActivityDeleteAPIV2 extends CiviUnitTestCase {
    
    
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }
 
    function testDeleteActivityWithoutId()
    {
        $params = array('activity_name' => 'Meeting');
        $activity =& civicrm_activity_delete($params);
        $this->assertEqual( $activity['is_error'], 1 );
    }

    function testDeleteActivityWithoutName()
    {
        $activity = $this->activityCreate( );
        
        $params = array( 'id' => $createActivity['id'] );
        $result =& civicrm_activity_delete($params);
        $this->assertEqual( $result['is_error'], 1 );

        //deleting contact created for adding activity
        $this->contactDelete( $activity['source_contact_id'] );
        $this->contactDelete( $activity['target_entity_id'] );
    }

    function testDeleteActivity()
    {
        $activity = $this->activityCreate( );

        $params = array(
                        'id' => $activity['id'],
                        'activity_name' => 'Meeting',
                        );

        $result =& civicrm_activity_delete($params);
        $this->assertEqual( $result['is_error'], 0 );

        //deleting contact created for adding activity
        $this->contactDelete( $activity['source_contact_id'] );
        $this->contactDelete( $activity['target_entity_id'] );
    }
    
}

?>
