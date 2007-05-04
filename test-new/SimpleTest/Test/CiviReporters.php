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