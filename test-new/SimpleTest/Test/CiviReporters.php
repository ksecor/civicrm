<?php
require_once 'SimpleTest/reporter.php';

class CiviHtmlReporter extends HtmlReporter {
    
    function VerboseHtmlReporter() {
        $this->HtmlReporter();
    }

    function paintPass($message) {
        // TBD
    }
}

class CiviTextReporter extends TextReporter {

    function VerboseTextReporter() {
        $this->TextReporter();
    }

    function paintCaseStart($test_name) {
        // echo "STARTING CASE: $test_name \n";
        parent::paintCaseStart($test_name);
    }


    function paintMethodStart($test_name) {
        // echo "STARTING METHOD: $test_name \n";
        parent::paintMethodStart($test_name);
    }

    function paintPass($message) {
        parent::paintPass($message);
        
        $out = "Assertion passed: ";
        $currentTest = $this->getTestList();
        // removing test suite name, not needed
        array_shift( $currentTest );
        // get filename from testcase path
        $out .= array_pop( explode( '/', $currentTest[0] ) );
        // get test method name
        $out .= '->' . array_pop( $currentTest );

        print "$out\n";
    }
}                                                                                


?>