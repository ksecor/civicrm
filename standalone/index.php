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
    //header("Location:login.php");
}

//print "userID: " . $session->get('userID') . "<br/>";
//print "ufName: " . $session->get('ufName') . "<br/>";

if ( $session->get('userID') == null || $session->get('userID') == '' ) {
    if ($_GET[$urlVar] == "") {
        header("Location: login.php");
        exit();
    } else {
        print "<a href=\"{$config->userFrameworkBaseURL}\">Login here</a> if you have an account.\n";
        print CRM_Core_Invoke::invoke( explode('/', $_GET[$urlVar] ) );
    }
} else {
    if ($_GET[$urlVar] == "") {
        print CRM_Core_Invoke::invoke( array("civicrm","dashboard") );
    } else {
        print CRM_Core_Invoke::invoke( explode('/', $_GET[$urlVar] ) );
    }
}
