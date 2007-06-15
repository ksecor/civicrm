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

// See if there are things we should output in the HTML <head> section first

print CRM_Core_Invoke::invoke( explode('/', $_GET['q'] ) );

?>
