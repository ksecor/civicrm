<?php

require_once 'api/v2/EmailHistory.php';

class TestOfEmailHistoryCreateAPIV2 extends CiviUnitTestCase 
{
    function setUp() 
    {
        $this->_contactId = $this->individualCreate();
    }
    
    function tearDown() 
    {
        $this->contactDelete($this->_contactId);
    }

    function testEmailHistoryWithoutContactId( )
    {
        $params = array( 'subject' => 'Test CiviCRM v2 API',
                         'message' => 'test without contact id',
                         'from_name'  => 'anthony_anderson@civicrm.org',
                         'recipient_id_1' => 1,
                         'recipient_name_1' => 'Anthony'
                         );
        $result = civicrm_email_history_add( $params );
        $this->assertEqual( $result['is_error'], 1 );
    }

    function testEmailHistoryWithoutMessage( )
    {
        $params = array( 'subject' => 'Test CiviCRM v2 API',
                         'contact_id' => $this->_contactId,
                         'from_name'  => 'anthony_anderson@civicrm.org',
                         'recipient_id_1' => 1,
                         'recipient_name_1' => 'Aderson'
                         );
        $result = civicrm_email_history_add( $params );
        $this->assertEqual( $result['is_error'], 1 );
    }
    function testEmailHistoryWithoutRecipient( )
    {
        $params = array( 'subject' => 'Test CiviCRM v2 API',
                         'message' => 'test without recipients, should return error',
                         'contact_id' => $this->_contactId,
                         'from_name'  => 'anthony_anderson@civicrm.org'
                         );
                         
        $result = civicrm_email_history_add( $params );
        $this->assertEqual( $result['is_error'], 1 );
    }
    function testEmailHistory( )
    {
        
        $params = array( 'subject' => 'Test CiviCRM v2 API',
                         'message' => 'this is a message ,which add email history',
                         'contact_id' => $this->_contactId,
                         'from_name'  => 'anthony_anderson@civicrm.org',
                         'recipient_id_1' => 1,
                         'recipient_name_1' => 'Anthony Aderson',
                         );

        $result = civicrm_email_history_add( $params );
        $this->assertEqual( $result['is_error'], 0 );
        
    }
}

?>
