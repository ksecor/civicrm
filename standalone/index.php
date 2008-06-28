<?php

require_once 'bootstrap_common.php';

// Get ready to fire it up
require_once 'CRM/Core/Invoke.php';
require_once 'CRM/Core/Session.php';

$session =& CRM_Core_Session::singleton();

$urlVar = $config->userFrameworkURLVar;

if ( !isset( $_GET[$urlVar] ) ) {
    $_GET[$urlVar] = '';
}

if ( !empty( $error ) ) {
    print "<div class=\"error\">$error</div>\n";
}
if ( !empty( $session->get['msg'] ) ) {
    print "<div class=\"msg\">$msg</div>\n";
}

if ( $session->get('userID') == null || $session->get('userID') == '' ) {
    if ($_GET[$urlVar] == "") {
        header("Location: login.php");
        exit();
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
