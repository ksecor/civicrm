<?php

require_once '../modules/config.inc.php';

require_once 'SimpleTest/unit_tester.php';
require_once 'SimpleTest/reporter.php';

//require_once 'CRM/Core/Error.php';
require_once 'CRM/Core/Config.php';
require_once 'CRM/Core/I18n.php';

if ( !defined( 'TEST' ) ) {
    define( 'TEST', __FILE__ );
}

class UnitTests extends GroupTest {
    
    function __construct( ) {
        parent::__construct( 'Unit Tests for CRM' );

        // contact api
        //$this->addTestFile( CRM_TEST_DIR . 'CRM/api/CreateContact.php' );
        //$this->addTestFile( CRM_TEST_DIR . 'CRM/api/GetContact.php'    );
        //$this->addTestFile( CRM_TEST_DIR . 'CRM/api/UpdateContact.php' );
        
        // group api
        //$this->addTestFile( CRM_TEST_DIR . 'CRM/api/AddGroupContact.php'    );
        //$this->addTestFile( CRM_TEST_DIR . 'CRM/api/DeleteGroupContact.php'    );
        //$this->addTestFile( CRM_TEST_DIR . 'CRM/api/GetGroups.php'    );
        //$this->addTestFile( CRM_TEST_DIR . 'CRM/api/GetGroupContacts.php'    );
        //$this->addTestFile( CRM_TEST_DIR . 'CRM/api/DeleteContact.php'    );
        //$this->addTestFile( CRM_TEST_DIR . 'CRM/api/getClassProperties.php'    );
        $this->addTestFile( CRM_TEST_DIR . 'CRM/api/SubscribeGroupContacts.php'    );
        $this->addTestFile( CRM_TEST_DIR . 'CRM/api/ConfirmGroupContacts.php'    );
        // location api
        //$this->addTestFile( CRM_TEST_DIR . 'CRM/api/CreateLocation.php'    );
        //$this->addTestFile( CRM_TEST_DIR . 'CRM/api/GetLocation.php'    );
        //$this->addTestFile( CRM_TEST_DIR . 'CRM/api/UpdateLocation.php'    );
        //$this->addTestFile( CRM_TEST_DIR . 'CRM/api/DeleteLocation.php'    );

        // history api
        //$this->addTestFile( CRM_TEST_DIR . 'CRM/api/CreateActivityHistory.php' );
        //$this->addTestFile( CRM_TEST_DIR . 'CRM/api/GetActivityHistory.php' );
        //$this->addTestFile( CRM_TEST_DIR . 'CRM/api/UpdateActivityHistory.php' );
        //$this->addTestFile( CRM_TEST_DIR . 'CRM/api/DeleteActivityHistory.php' );

        // custom group api
        //$this->addTestFile(CRM_TEST_DIR . 'CRM/api/CreateCustomGroup.php');
        
    }

}

function user_access( $str ) {
    return true;
}

if ( TEST == __FILE__ ) {

    $test =& new UnitTests( );
    $config = CRM_Core_Config::singleton();
    CRM_Core_DAO::init($config->dsn, $config->daoDebug);
    $factoryClass = 'CRM_Contact_DAO_Factory';
    CRM_Core_DAO::setFactory(new $factoryClass());

    // set error handling
    PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, array('CRM_Core_Error', 'handle'));
    //PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, array('CRM_Core_Error', 'callback'));


    if (SimpleReporter::inCli()) {
        exit($test->run(new TextReporter()) ? 0 : 1);
    }
    $test->run(new HtmlReporter());
}

?>