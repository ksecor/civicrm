<?php

require_once '../modules/config.inc.php';

require_once 'SimpleTest/web_tester.php';
require_once 'SimpleTest/reporter.php';

if ( !defined( 'TEST' ) ) {
    define( 'TEST', __FILE__ );
}

class WebTests extends GroupTest {
    function WebTests()
    {
        $this->GroupTest( 'Web Site Tests for CRM' );
                
        $this->addTestFile( CIVICRM_TEST_DIR . 'CRM/web/AdminDeleteTag.php' );
        $this->addTestFile( CIVICRM_TEST_DIR . 'CRM/web/AdminEditTag.php' );
        $this->addTestFile( CIVICRM_TEST_DIR . 'CRM/web/AdminAddTag.php' );
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
    $test =& new WebTests( );

    $config =& CRM_Core_Config::singleton();

    if (SimpleReporter::inCli()) {
        exit($test->run(new TextReporter()) ? 0 : 1);
    }
    $test->run(new HtmlReporter());
}

?>