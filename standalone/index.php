<?php
require_once 'bootstrap_common.php';

$urlVar = $config->userFrameworkURLVar;
if ( !isset( $_GET[$urlVar] ) ) {
    $_GET[$urlVar] = '';
}

// Display error if any
if ( !empty( $error ) ) {
    print "<div class=\"error\">$error</div>\n";
    $gotError = true;
}
if ( $session->get('msg') ) {
    $msg = $session->get('msg');
    print "<div class=\"msg\">$msg</div>\n";
    $gotError = true;
}
if ( isset($gotError) || $session->get('goahead') == 'no' ) {
    exit(0);
}

// Get ready to fire it up
require_once 'CRM/Core/Invoke.php';
if ( $session->get('userID') == null || $session->get('userID') == '' ) {
    if ($_GET[$urlVar] == "") {
        header("Location: login.php");
        exit(1);
    } else {
        if ( $session->get('new_install') !== true ) {
            print "<a href=\"{$config->userFrameworkBaseURL}\">Login here</a> if you have an account.\n";
        } elseif ($_GET[$urlVar] == "civicrm/standalone/register" && isset($_GET['reset'])) {
            // this is when user first registers with civicrm
            print "<head><style type=\"text/css\"> body {border: 1px #CCC solid;margin: 3em;padding: 1em 1em 1em 2em;} </head>";
        }
        print CRM_Core_Invoke::invoke( explode('/', $_GET[$urlVar] ) );
    }
} else {
    if ($_GET[$urlVar] == "") {
        print CRM_Core_Invoke::invoke( array("civicrm","dashboard") );
    } else {
        print CRM_Core_Invoke::invoke( explode('/', $_GET[$urlVar] ) );
    }
}
?>