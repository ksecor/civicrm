<?php

session_start( );

require_once '../civicrm.config.php';
require_once 'CRM/Core/Config.php';

// build the query
function invoke( ) {
    // intialize the system
    $config =& CRM_Core_Config::singleton( );

    $q = $_GET['q'];
    $args = explode( '/', $q );
    if ( $args[0] != 'help' ) {
        exit( );
    }

    echo "This is the help text. We can have links in it<p>";
}

invoke( );
?>