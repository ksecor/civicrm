<?php

require_once '../modules/config.inc.php';

require_once 'SimpleTest/unit_tester.php';
require_once 'SimpleTest/reporter.php';

require_once 'CRM/Core/Config.php';
require_once 'CRM/Core/I18n.php';

if ( !defined( 'TEST' ) ) {
    define( 'TEST', __FILE__ );
}

class UnitTests extends GroupTest {
    
    function UnitTests( ) {
        $this->GroupTest( 'Unit Tests for CRM' );

        // contact api
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CreateContact.php' );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/GetContact.php'    );
        $this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM369.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM2474.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/UpdateContact.php' );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/GetContactGroups.php' );

        // group api
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/AddGroupContact.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/DeleteGroupContact.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/GetGroups.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/GetGroupContacts.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/DeleteContact.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/getClassProperties.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/SubscribeGroupContacts.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/ConfirmGroupContacts.php'    );
        // location api
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CreateLocation.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/GetLocation.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/UpdateLocation.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/DeleteLocation.php'    );

        // history api
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CreateActivityHistory.php' );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/GetActivityHistory.php' );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/UpdateActivityHistory.php' );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/DeleteActivityHistory.php' );

        // custom group api
        //$this->addTestFile(CIVICRM_TEST_DIR . 'CRM/api/CreateCustomGroup.php');
        
    }

}

function user_access( $str ) {
    return true;
}

if ( TEST == __FILE__ ) {

    $test =& new UnitTests( );

    $config =& CRM_Core_Config::singleton();

    if (SimpleReporter::inCli()) {
        exit($test->run(new TextReporter()) ? 0 : 1);
    }
    $test->run(new HtmlReporter());
}

?>
