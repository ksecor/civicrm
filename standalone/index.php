<?php

require_once 'bootstrap_common.php';

// Get ready to fire it up
require_once 'CRM/Core/Invoke.php';
require_once 'CRM/Core/Session.php';

$session =& CRM_Core_Session::singleton();

if ( !isset( $_GET[CIVICRM_UF_URLVAR] ) ) {
    $_GET[CIVICRM_UF_URLVAR] = '';
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
    include 'login.php';
    exit(0);
}
if ($session->get('goahead') != "no") {
    // If we didn't get any parameters, we should default to the dashboard
    if ($_GET[CIVICRM_UF_URLVAR] == "") {
        print CRM_Core_Invoke::invoke( array("civicrm","dashboard") );
    } else {
        print CRM_Core_Invoke::invoke( explode('/', $_GET[CIVICRM_UF_URLVAR] ) );
    }
} else {
    header("Location:login.php");
    print "One or more errors occurred.";
}

?>
