<?php
session_start();

// Pull in the settings file & Instantiate the config so that the DB connection will fire up
require_once '../civicrm.config.php';
require_once 'CRM/Core/Config.php';
$config  =& CRM_Core_Config::singleton( );

require_once 'CRM/Core/Session.php';
$session =& CRM_Core_Session::singleton( );

// Check for errors in the session
if ($session->get('error')) {
    print $session->get('error');
    $session->set('error',null);
    $gotError = true;
}
?>