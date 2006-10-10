<?php

require_once 'CRM/Quest/API.php';

class TestOfCreateTaskStatusAPI extends UnitTestCase {
    
function setUp( ) {
    }
    
function tearDown( ) {
    }
    
function testCreateFull()
{
    $params = array('target_entity_id' => 38617, 
                    'responsible_entity_id' => 38617,
                    'task_id' => 11,
                    'status_id' => 328,
                    );
    $taskStatus =& CRM_Quest_API::createTaskStatus($params);
    $this->assertIsA($taskStatus, 'CRM_Project_DAO_TaskStatus');
}
}
?>