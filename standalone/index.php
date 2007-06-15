<?php

// Pull in the settings file
include_once '../civicrm.settings.php';

// Add the packages to the include path
$include_path = ini_get('include_path');
ini_set('include_path', "$civicrm_root:$civicrm_root/packages:$include_path");

// Get ready to fire it up
require_once 'CRM/Core/Invoke.php';
require_once 'CRM/Core/Session.php';
// We have to start the session first as CRM_Core_Session
//  assumes it will be there.
session_start();
$session =& CRM_Core_Session::singleton();
// HACK: This is only here until we implement the login system
$session->set('userID',102);

// If we didn't get any parameters, we should default to the dashboard
if ($_GET[CIVICRM_UF_URLVAR] == "") {
  print CRM_Core_Invoke::invoke( array("civicrm","dashboard") );
} else {
  print CRM_Core_Invoke::invoke( explode('/', $_GET[CIVICRM_UF_URLVAR] ) );
}

?>
