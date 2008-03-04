<?php

require_once "auth_common.php";
require_once 'CRM/Core/BAO/UFMatch.php';

// Render a default page if we got a submission without an openid
// value.
if (empty($_REQUEST['openid_url'])) {
    // TODO: Error reporting doesn't work correctly yet
    //$session->set( 'error', "Expected an OpenID URL." );
    include 'index.php';
    exit(0);
}

$scheme = 'http';
if (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on') {
    $scheme .= 's';
}

$openid = $_REQUEST['openid_url'];
$server_port = $_SERVER['SERVER_PORT'];
if ($scheme == 'http' && $server_port == 80) {
    $url_port = '';
} elseif ($scheme == 'https' && $server_port == 443) {
    $url_port = '';
} else {
    $url_port = ":$server_port";
}
$process_url = sprintf("$scheme://%s%s%s/finish_auth.php",
                       $_SERVER['SERVER_NAME'], $url_port,
                       dirname($_SERVER['PHP_SELF']));

$trust_root = sprintf("$scheme://%s%s%s",
                      $_SERVER['SERVER_NAME'], $url_port,
                      dirname($_SERVER['PHP_SELF']));

// Begin the OpenID authentication process.
$auth_request = $consumer->begin($openid);

// Handle failure status return values.
if (!$auth_request) {
    $error = "Authentication error.";
    // check for new install, if no, go to index, else goto new-install page
    $contactIds = CRM_Core_BAO_UFMatch::getContactIDs();
    if ( count( $contactIds[0] ) > 0 ) {
        include 'index.php';
    } else {
        header("Location:new_install.php"); 
    }
    exit(0);
}

$auth_request->addExtensionArg('sreg', 'optional', 'email');

// Redirect the user to the OpenID server for authentication.  Store
// the token for this authentication so we can verify the response.

$session =& CRM_Core_Session::singleton( );
$session->set( 'openid_process_url', $process_url );

$redirect_url = $auth_request->redirectURL($trust_root,
                                           $process_url);

header("Location: ".$redirect_url);


