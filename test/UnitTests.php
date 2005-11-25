<?php

require_once '../modules/config.inc.php';

require_once 'SimpleTest/unit_tester.php';
require_once 'SimpleTest/reporter.php';

if ( !defined( 'TEST' ) ) {
    define( 'TEST', __FILE__ );
}

class UnitTests extends GroupTest {
    
    function UnitTests( ) {
        $this->GroupTest( 'Unit Tests for CRM' );

        // contact api
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CreateContact.php' );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/GetContact.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/GetContactFlat.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/GetContactCustom.php'    );
        // $this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/Search.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/GetContactHierarchical.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM558.php' );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM562.php' );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM39.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM2474.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM491.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM503.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM514.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM520.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM521.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM522.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CRM531.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/UpdateContact.php' );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/GetContactGroups.php' );

        // group api
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/AddGroupContact.php'    );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/DeleteGroupContact.php'    );
        $this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/GetGroups.php'    );
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
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CreateCustomGroup.php');
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/api/CreateCustomField.php');
        //$this->addTestFile(CIVICRM_TEST_DIR . 'CRM/api/CreateCustomValue.php');
        
        // relationship api
        //$this->addTestFile(CIVICRM_TEST_DIR . 'CRM/api/CreateRelationship.php');
        
        // $this->addTestFile(CIVICRM_TEST_DIR . 'CRM/api/GetRelationship.php');
        //$this->addTestFile(CIVICRM_TEST_DIR . 'CRM/api/DeleteRelationship.php');
        //$this->addTestFile(CIVICRM_TEST_DIR . 'CRM/api/CreateRelationshipType.php');
        //$this->addTestFile(CIVICRM_TEST_DIR . 'CRM/api/GetRelationshipType.php');
        //$this->addTestFile(CIVICRM_TEST_DIR . 'CRM/api/UpdateRelationship.php');
    }

}

function user_access( $str ) {
    return true;
}

function module_list( ) {
    return array( );
}

if ( TEST == __FILE__ ) {

    require_once 'CRM/Core/Config.php';
    $test =& new UnitTests( );

    $config =& CRM_Core_Config::singleton();

    if (SimpleReporter::inCli()) {
        exit($test->run(new TextReporter()) ? 0 : 1);
    }
    $test->run(new HtmlReporter());
}

?>
