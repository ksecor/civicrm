<?php
// $Id: postoffice_server.php,v 1.4 2004/11/23 14:10:56 harryf Exp $
/**
* This is a remote script to call from Javascript
*/

require_once '../JPSpan.php';
require_once JPSPAN . 'Server/PostOffice.php';

class Math {

    function add($x,$y) {
        return $x + $y;
    }
    
    function subtract($x,$y) {
        return $x - $y;
    }
    
    function divide($x,$y) {
        if ( $y == 0 ) {
            require_once JPSPAN . 'Types.php';
            $Error = & new JPSpan_Error();
            $Error->setError(3000,'ZeroDivisionError','Cannot divide by zero');
            return $Error;
        } else {
            return $x / $y;
        }
    }

}

class Colors {
    function listColors() {
        $colors = array (
            array('name'=>'maroon','r'=>'80','g'=>'00','b'=>'00'),
            array('name'=>'yellow','r'=>'FF','g'=>'FF','b'=>'00'),
            array('name'=>'salmon','r'=>'FA','g'=>'80','b'=>'72'),
        );
        return $colors;
    }
}

$S = & new JPSpan_Server_PostOffice();
// Switch to PHP encoding instead of default XML (example usesr numbers not strings...)
$S->RequestEncoding = 'php';
$S->addHandler(new Math());
$S->addHandler(new Colors());

if (isset($_SERVER['QUERY_STRING']) && strcasecmp($_SERVER['QUERY_STRING'], 'client')==0) {

    // Compress the Javascript
    // define('JPSPAN_INCLUDE_COMPRESS',TRUE);
    $S->displayClient();
    
} else {

    // Include error handler - PHP errors, warnings and notices serialized to JS
    require_once JPSPAN . 'ErrorHandler.php';
    $S->serve();
}
?>
