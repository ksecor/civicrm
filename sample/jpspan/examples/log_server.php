<?php
// $Id: log_server.php,v 1.4 2004/11/23 14:10:56 harryf Exp $
/**
* This is a remote script to call from Javascript
*/

//-----------------------------------------------------------------------------------
require_once '../JPSpan.php';
require_once JPSPAN . 'Server/PostOffice.php';
require_once JPSPAN . 'Types.php';

//-----------------------------------------------------------------------------------
class Logger {
    function success($Data) {
        // Ignore LogReader requests
        if ( $Data['requestInfo']['class'] == 'logreader' ) {
            return;
        }
        $message = '['.$Data['gmt'].'] Successful call to: '.
            $Data['requestInfo']['class'].'.'.$Data['requestInfo']['method']."\n";
        error_log($message, 3, dirname(__FILE__)."/data/request.log");
    }
    
    function error($Data) {
        $message = '['.$Data['gmt'].'] Error: '.
            $Data['errorName'].' - '.
                str_replace(array("\n","\r\n"),'',$Data['errorMsg'])."\n";
        error_log($message, 3, dirname(__FILE__)."/data/error.log");
    }
}

// Setup the monitor
define ('JPSPAN_MONITOR', TRUE);
require_once JPSPAN . 'Monitor.php';
$M = & JPSpan_Monitor::instance();
$M->addObserver(new Logger());

//-----------------------------------------------------------------------------------
class SomeClass {

    function sayHello($name) {
        return "Hello $name";
    }
    
    function makePHPError() {
        fopen('/tmp/foo/bar/12325345345','r');
    }
    
    function makeAppError() {
        return new JPSpan_Error(3001,'Example','This is an example error');
    }

}

//-----------------------------------------------------------------------------------
function chopLine($line) {
    if ( strlen($line) > 80 ) {
        return substr($line,0,80)."\n";
    }
    return $line;
}

class LogReader {
    function getRequestTail() {
        return $this->__readLog('./data/request.log');
    }
    
    function getErrorTail() {
        return $this->__readLog('./data/error.log');
    }
    
    function __readLog($fileName) {
        $file = file($fileName);
        if ( count($file) > 10 ) {
            $file = array_slice($file, count($file)-10);
        }
        $tail = array_reverse($file);
        $lines = array_map('chopLine',$tail);
        return implode('',$lines);
    }
}



$S = & new JPSpan_Server_PostOffice();
$S->addHandler(new SomeClass());
$S->addHandler(new LogReader());

//-----------------------------------------------------------------------------------
if (isset($_SERVER['QUERY_STRING']) && strcasecmp($_SERVER['QUERY_STRING'], 'client')==0) {
    define('JPSPAN_INCLUDE_COMPRESS',TRUE);
    $S->displayClient();
    
} else {

    require_once JPSPAN . 'ErrorHandler.php';
    $S->serve();

}
?>
