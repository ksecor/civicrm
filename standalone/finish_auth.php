<?php

require_once "auth_common.php";

$response = $consumer->complete( $_GET );

if ( $response->status == Auth_OpenID_CANCEL ) {
    // This means authentication was cancelled.
    $msg = 'Login cancelled.';
} else if ( $response->status == Auth_OpenID_FAILURE ) {
    $msg = "Login failed: " . $response->message;
} else if ( $response->status == Auth_OpenID_SUCCESS ) {
    $openid = $response->identity_url;
    $sreg = $response->extensionResponse('sreg');
    $email = @$sreg['email'];
    $fullname = @$sreg['fullname'];
    $matches = array( );
    preg_match("/^([\w ]+) (\w+)$/", $fullname, $matches);
    $firstname = $matches[1];
    $lastname = $matches[2];
  
    require_once 'user.php';
    require_once 'CRM/Utils/System/Standalone.php';
    $user = new Standalone_User( $openid, $email, $firstname, $lastname );
    require_once 'CRM/Core/Session.php';
    $allow_login = CRM_Utils_System_Standalone::getAllowedToLogin( $user );
    if ( !$allow_login && !CIVICRM_ALLOW_ALL) {
      $msg = 'Login failed.';	
      header( "Location: index.php?bad=true" );
      exit ( 0 );
    }
    $session =& CRM_Core_Session::singleton( );
    print "<pre>";
    print_r($user);
    print "</pre>";
    CRM_Utils_System_Standalone::getUserID( $user );
    $userID = $session->get( 'userID' );
    if ( empty( $userID ) ) {
        $msg = 'You are not authorized to login.';
    }
    header("Location: index.php");
    exit(0);
}

