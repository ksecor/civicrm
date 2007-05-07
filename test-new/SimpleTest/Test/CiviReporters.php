<?php
require_once 'SimpleTest/reporter.php';

class CiviHtmlReporter extends HtmlReporter {
    
    function CiviHtmlReporter() {
        $this->HtmlReporter();
    }


    function paintCaseStart($test_name) {
        echo "<p style=\"case\"><b>CASE:</b> $test_name <br/>";
        parent::paintCaseStart($test_name);
    }
        
    function paintCaseEnd($test_name) {
        echo "</p>";
        parent::paintCaseEnd($test_name);
    }    

    function paintMethodStart($test_name) {
        echo "<i>Method</i>: $test_name<br/>";
        parent::paintMethodStart($test_name);
    }
    
    function paintMethodEnd($test_name) {
        echo "<br/>";
        parent::paintMethodEnd($test_name);
    }
    
    function paintPass($message) {
        echo "<br/><code>";
        echo "$message";
        parent::paintPass($message);
        echo "</code>";        
    }
        
    function paintFail($message) {
        echo "<br/><code>";
        parent::paintFail($message);
        echo "</code>";
    }    


}

class CiviTextReporter extends TextReporter {

    // I know, I know. :-p
    private function _line( $c ) {
        $i = 0; while ( $i <= 70 ) { $out .= $c; $i++; }; return "$out\n";    
    }

    function CiviTextReporter() {
        $this->TextReporter();
    }

    function paintHeader($test_name) {
        echo "\n" . $this->_line('=');
        parent::paintHeader($test_name);
        echo $this->_line('=') . "\n";
    }

    function paintFooter($test_name) {
        echo "\n" . $this->_line('=');
        parent::paintFooter($test_name);
        echo $this->_line('=') . "\n";
    }

    function paintCaseStart($test_name) {
        echo "\nSTARTING CASE: $test_name \n";
        parent::paintCaseStart($test_name);
    }
        
    function paintCaseEnd($test_name) {
        parent::paintCaseEnd($test_name);
    }    

    function paintMethodStart($test_name) {
        echo "Method: $test_name ==> Assertions: ";
        parent::paintMethodStart($test_name);
    }
    
    function paintMethodEnd($test_name) {
        echo "\n";
        parent::paintMethodEnd($test_name);
    }
    
    function paintPass($message) {
        echo "ok ";
        parent::paintPass($message);
    }
        
    function paintFail($message) {
        echo "\n\n FAILED!\n" . $this->_line('~');
        parent::paintFail($message);
        echo $this->_line('~');
    }    

}


?>