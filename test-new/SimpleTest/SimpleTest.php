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



class SnippetsTests extends CiviGroupTest {
    
    function SnippetsTests() {
        $this->GroupTest( 'Tests for various cases' );
        $this->addTestDirectory( CIVICRM_TEST_DIR . 'SimpleTest/snippets' );        
    }
}


class IssueTests extends CiviGroupTest {
    
    function IssueTests() {
        $this->GroupTest( 'Tests for individual issues' );
        $this->addTestDirectory( CIVICRM_TEST_DIR . 'SimpleTest/issues' );        
    }
}


class ApiV2Tests extends CiviGroupTest {
    
    function ApiV2Tests() {
        $this->GroupTest( 'Unit Tests for API v2' );
        $this->addTestDirectory( CIVICRM_TEST_DIR . 'SimpleTest/api-v2' );        
    }
}


class ApiTests extends CiviGroupTest {
    
    function ApiTests() {
        $this->GroupTest( 'Unit Tests for API' );
        $this->addTestDirectory( CIVICRM_TEST_DIR . 'SimpleTest/api' );
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
//    $test =& new ApiTests( );
    $test2 =& new ApiV2Tests( );
//    $test3 =& new IssueTests( );
//    $test4 =& new SnippetsTests( );

    $config =& CRM_Core_Config::singleton();
 
    if (SimpleReporter::inCli()) {
//        $test->run(new CiviTextReporter());
        $test2->run(new CiviTextReporter());
//        $test3->run(new CiviTextReporter());        
//        $test4->run(new CiviTextReporter());                
        exit();
    }
//    $test->run(new HtmlReporter());
    $test2->run(new HtmlReporter());    
//    $test3->run(new HtmlReporter());    
//    $test4->run(new HtmlReporter());
}

?>
