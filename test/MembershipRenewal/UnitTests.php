<?php
require_once '../../civicrm.config.php';

require_once 'SimpleTest/unit_tester.php';
require_once 'SimpleTest/reporter.php';

if ( !defined( 'TEST' ) ) {
    define( 'TEST', __FILE__ );
}

class UnitTests extends GroupTest {
    
    function UnitTests( ) {
        $this->GroupTest( 'Unit Tests for CRM' );
        
        $this->addTestFile( CIVICRM_TEST_DIR . 'MembershipRenewal/TestCase/TestMembershipRenewal.php' );
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
