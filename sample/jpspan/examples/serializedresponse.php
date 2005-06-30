<?php
// $Id: serializedresponse.php,v 1.3 2004/11/17 09:28:53 harryf Exp $
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
require_once JPSPAN . 'Serializer.php';
require_once JPSPAN . 'Types.php';

$requestEncodings = array('xml','php');
if ( !isset($_GET['rencoding']) || !in_array($_GET['rencoding'],$requestEncodings) ) {
    $_GET['rencoding'] = 'xml';
}

/**
* This class is sent to Javascript
*/
class PHPResponse {}

/**
* Generates a serialized response
*/
class SerializingResponder {
    function execute($payload) {
        if ( !isset($payload->geterror) ) {
            $R = new PHPResponse();
            $R->youSent = $payload;
            $R->someData = array('a','b','c');
            echo JPSpan_Serializer::serialize($R);
        } else {
            if ( $payload->geterror == 'customerror' ) {
                $e = & new JPSpan_Error();
                $e->setError(3001,'MyCustomError','Testing custom error');
            } else {
                $e = & new JPSpan_Error();
                $e->setError(3000,'Error','Test Error');
            }
            echo JPSpan_Serializer::serialize($e);
        }
    }
}

$L = & new JPSpan_Listener();
$L->encoding = $_GET['rencoding'];
$L->setResponder(new SerializingResponder());
$L->serve();
?>
