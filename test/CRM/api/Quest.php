<?php

require_once 'api/crm.php';

require_once 'CRM/Quest/API.php';

class TestOfCRMQuest extends UnitTestCase
{   
    function setUp()
    {
    }

    function tearDown()
    {
    }

    function testCRMQuest( )
    {
        $id = 1;

        CRM_Core_Error::debug( 'info'   , CRM_Quest_API::getApplicationInfo( $id ) );
        CRM_Core_Error::debug( 'contact', CRM_Quest_API::getContactInfo( $id ) );
        CRM_Quest_API::setContactSubType( $id, 'Student' );
    }
}

?>