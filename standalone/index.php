<?php

require_once 'bootstrap_common.php';

// Get ready to fire it up
require_once 'CRM/Core/Invoke.php';
require_once 'CRM/Core/Session.php';

$session =& CRM_Core_Session::singleton();

//print "userID: " . $session->get('userID') . "<br/>";
//print "ufName: " . $session->get('ufName') . "<br/>";
if ( $session->get('userID') == null || $session->get('userID') == '' ) {
    include 'login.php';
    exit(0);
}

if ( !empty( $error ) ) {
    print "<div class=\"error\">$error</div>\n";
}

if ( !empty( $msg ) ) {
    print "<div class=\"msg\">$msg</div>\n";
}

// If we didn't get any parameters, we should default to the dashboard
if ($_GET[CIVICRM_UF_URLVAR] == "") {
    print CRM_Core_Invoke::invoke( array("civicrm","dashboard") );
} else {
    print CRM_Core_Invoke::invoke( explode('/', $_GET[CIVICRM_UF_URLVAR] ) );
}

?>
