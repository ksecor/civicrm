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
                
        $this->addTestFile( CIVICRM_TEST_DIR . 'CRM/web/AdminTag.php' );
        $this->addTestFile( CIVICRM_TEST_DIR . 'CRM/web/AdminActivityType.php' );
        $this->addTestFile( CIVICRM_TEST_DIR . 'CRM/web/AdminLocationType.php' );
        $this->addTestFile( CIVICRM_TEST_DIR . 'CRM/web/AdminRelationshipType.php' );
        $this->addTestFile( CIVICRM_TEST_DIR . 'CRM/web/AdminGender.php' );
        $this->addTestFile( CIVICRM_TEST_DIR . 'CRM/web/AdminPrefix.php' );
        $this->addTestFile( CIVICRM_TEST_DIR . 'CRM/web/AdminSuffix.php' );
        $this->addTestFile( CIVICRM_TEST_DIR . 'CRM/web/AdminIMService.php' );
        $this->addTestFile( CIVICRM_TEST_DIR . 'CRM/web/AdminMobileProvider.php' );
        $this->addTestFile( CIVICRM_TEST_DIR . 'CRM/web/AdminCustomData.php' );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/web/NewGroup.php' );
        //$this->addTestFile( CIVICRM_TEST_DIR . 'CRM/web/ManageGroups.php' );
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