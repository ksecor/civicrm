<?php

require_once 'config.inc.php';

require_once 'SimpleTest/unit_tester.php';
require_once 'SimpleTest/reporter.php';

if ( !defined( 'TEST' ) ) {
    define( 'TEST', __FILE__ );
}

class UnitTests extends GroupTest {
    
    function __construct( ) {
        parent::__construct( 'Unit Tests for CRM' );
        
        $this->addTestFile( CRM_TEST_DIR . 'CRM/api/CreateContact.php' );
    }

}

if ( TEST == __FILE__ ) {
    $test =& new UnitTests( );

    if ( SimpleReporter::inCli( ) ) {
        exit( $test->run( new TextReporter( ) ) ? 0 : 1 );
    }
    $test->run( new HtmlReporter( ) );
}

?>