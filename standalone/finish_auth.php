<?php

require_once "auth_common.php";

$response = $consumer->complete( $_GET );
$session =& CRM_Core_Session::singleton( );
$session->set('goahead', "yes");
if ( $response->status == Auth_OpenID_CANCEL ) {
    // This means authentication was cancelled.
  $session->set('msg', 'Login cancelled.');
  $session->set('goahead', "no");
} else if ( $response->status == Auth_OpenID_FAILURE ) {
  $session->set('msg', "Login failed: " . $response->message);
    $session->set('goahead', "no");
} else if ( $response->status == Auth_OpenID_SUCCESS ) {
    $openid = $response->identity_url;
    $sreg = $response->extensionResponse('sreg');
    $email = @$sreg['email'];
    require_once 'user.php';
    require_once 'CRM/Utils/System/Standalone.php';
    $user = new Standalone_User( $openid, $email );
    require_once 'CRM/Core/Session.php';
    $allow_login = CRM_Utils_System_Standalone::getAllowedToLogin( $user );
    if ( !$allow_login && !CIVICRM_ALLOW_ALL) {
      $session->set( 'msg' , 'Login failed.');	
      $session->set('goahead', "no");
    }
    CRM_Utils_System_Standalone::getUserID( $user );
    $userID = $session->get( 'userID' );
    if ( empty( $userID ) ) {
      $session->set( 'msg' , 'You are not authorized to login.');
	$session->set('goahead', "no");
    }
    header("Location: index.php");
    exit(0);
}

