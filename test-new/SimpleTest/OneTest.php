<?php
require_once '../../civicrm.config.php';

require_once 'SimpleTest/unit_tester.php';
require_once 'SimpleTest/reporter.php';

require_once 'Test/CiviUnitTestCase.php';

require_once 'Test/CiviGroupTest.php';
require_once 'Test/CiviReporters.php';

if ( !defined( 'TEST' ) ) {
    define( 'TEST', __FILE__ );
}

class OneTest extends CiviGroupTest {
    
    function OneTest( ) {
        $this->GroupTest( 'One Test' );
        $this->addTestFile( CIVICRM_TEST_DIR . 'SimpleTest/' . $_GET['q'] . '.php' );
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
    $test =& new OneTest( );

    $config =& CRM_Core_Config::singleton();
 
    if (SimpleReporter::inCli()) {
        $test->run(new CiviTextReporter());
        exit();
    }
    $test->run(new CiviHtmlReporter());
}


