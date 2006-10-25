<?php

require_once 'api/crm.php';

require_once '../../tmf/CRM/TMF/API.php';

class TestOfCRMTMF extends UnitTestCase
{   
    function setUp()
{
}

function tearDown()
{
}

function testCRMQuest( )
    {
        $studentId = 1;
        $studentTasks = CRM_TMF_API::getStudentTasks( $studentId );
        CRM_Core_Error::debug( 'studentTasks'   , $studentTasks );
        $nomId = 4;
        $nominations = CRM_TMF_API::getNominations( $nomId );
        CRM_Core_Error::debug( 'nominations'   , $nominations );
    }
}

?>