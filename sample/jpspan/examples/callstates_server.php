<?php
// $Id: callstates_server.php,v 1.3 2004/11/23 14:10:56 harryf Exp $
/**
* This is a remote script to call from Javascript
*/

require_once '../JPSpan.php';
require_once JPSPAN . 'Server/PostOffice.php';

//-----------------------------------------------------------------------------------
class SomeData {

    function getLargeResult() {
        $data = range('a','z');
        $data = implode('',$data)."\n";
        $result = '';
        for($i=0;$i<10000;$i++) {
            $result.=$data;
        }
        return $result;
    }

}

$S = & new JPSpan_Server_PostOffice();
$S->addHandler(new SomeData());

//-----------------------------------------------------------------------------------
if (isset($_SERVER['QUERY_STRING']) && strcasecmp($_SERVER['QUERY_STRING'], 'client')==0) {

    //define('JPSPAN_INCLUDE_COMPRESS',TRUE);
    $S->displayClient();
    
} else {

    require_once JPSPAN . 'ErrorHandler.php';
    $S->serve();

}
?>
