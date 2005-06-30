<?php
// $Id: autocomplete_server.php,v 1.5 2004/11/23 14:10:56 harryf Exp $
/**
* This is a remote script to call from Javascript
*/

define ('JPSPAN_ERROR_DEBUG',TRUE);
require_once '../JPSpan.php';
require_once JPSPAN . 'Server/PostOffice.php';

//-----------------------------------------------------------------------------------
class Autocomplete {

    function getWord($fragment='') {
        $words = file('data/countrylist.vars');
        $fraglen = strlen($fragment);
        for ( $i = $fraglen; $i > 0; $i-- ) {
            $matches = preg_grep('/^'.substr($fragment,0,$i).'/i',$words);
            if ( count($matches) > 0 ) {
                return array_shift($matches);
            }
        }
        return '';
    }

}

$S = & new JPSpan_Server_PostOffice();
$S->addHandler(new Autocomplete());

//-----------------------------------------------------------------------------------
// Generates the Javascript client by adding ?client to the server URL
//-----------------------------------------------------------------------------------
if (isset($_SERVER['QUERY_STRING']) && strcasecmp($_SERVER['QUERY_STRING'], 'client')==0) {
    // Compress the Javascript
    // define('JPSPAN_INCLUDE_COMPRESS',TRUE);
    $S->displayClient();
    
//-----------------------------------------------------------------------------------
} else {

    //-----------------------------------------------------------------------------------
    // Some HTTP Basic authentication
    //-----------------------------------------------------------------------------------
    $username = 'admin';
    $password = 'secret';
    
    if ( !isset ( $_SERVER['PHP_AUTH_USER'] ) ) {
        header('HTTP/1.0 401 Unauthorized');
        header('WWW-Authenticate: Basic realm="PHP Secured"');
        exit('This page requires authentication');
    }
    
    if ( !( ($_SERVER['PHP_AUTH_USER']==$username) & ($_SERVER['PHP_AUTH_PW']==$password) ) ) {
        header('HTTP/1.0 401 Unauthorized');
        header('WWW-Authenticate: Basic realm="PHP Secured"');
        exit('Unauthorized!');
    }
    //-----------------------------------------------------------------------------------
    
    // Include error handler - PHP errors, warnings and notices serialized to JS
    require_once JPSPAN . 'ErrorHandler.php';
    $S->serve();

}
?>
