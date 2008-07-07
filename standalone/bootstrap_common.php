<?php
// Start the session
session_start();

// Pull in the settings file & Instantiate the config so that the DB connection will fire up
require_once '../civicrm.config.php';
require_once 'CRM/Core/Config.php';
$config =& CRM_Core_Config::singleton( );

if ( ! isset( $civicrm_root ) ) {
    // It seems we need to bootstrap this installation, so redirect there
    header("Location: ../install/index.php?mode=standalone");
    exit(0);
}

// Add the packages to the include path
$include_path = ini_get('include_path');
ini_set('include_path', "$civicrm_root:$civicrm_root/packages:$include_path");

// Check for errors in the session
require_once 'CRM/Core/Session.php';
$session =& CRM_Core_Session::singleton( );
if ($session->get('error')) {
    print $session->get('error');
    $session->set('error',null);
}
?>
