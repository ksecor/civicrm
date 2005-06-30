<?php
// $Id: printrresponse.php,v 1.5 2004/11/17 09:27:43 harryf Exp $
/**
* This is a remote script to call from Javascript
*/

// IE's XMLHttpRequest caching...
header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); 
header( "Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" ); 
header( "Cache-Control: no-cache, must-revalidate" ); 
header( "Pragma: no-cache" );

require_once '../JPSpan.php';
require_once JPSPAN . 'Listener.php';

if ( isset($_GET['timeout']) ) {
    sleep($_GET['timeout']);
}

$requestEncodings = array('xml','php');
if ( !isset($_GET['rencoding']) || !in_array($_GET['rencoding'],$requestEncodings) ) {
    $_GET['rencoding'] = 'xml';
}

if ( isset($_GET['error']) ) {
    define ('JPSPAN_ERROR_DEBUG',TRUE);
    require_once JPSPAN . 'ErrorHandler.php';
    switch ($_GET['error']) {
        case 'native':
            fopen('/tmp/foo/bar/12325345345','r');
        break;
        case 'notice':
            trigger_error('Example E_USER_NOTICE');
        break;
        case 'warning':
            trigger_error('Example E_USER_WARNING',E_USER_WARNING);
        break;
        case 'error':
            trigger_error('Example E_USER_ERROR',E_USER_ERROR);
        break;
        case 'exception':
            if ( version_compare(phpversion(), '5', '>=') ) {
                include 'printrresponse_ex4.php';
            } else {
                trigger_error('You are using PHP 4 - exceptions not supported');
            }
        break;
    }
}

/**
* A basic responder
*/
class PrintRResponder {
    function execute($payload) {
        print_r($payload);
    }
}

$L = & new JPSpan_Listener();
$L->encoding = $_GET['rencoding'];
$L->setResponder(new PrintRResponder());
$L->serve();
?>
