<?php

require_once "auth_common.php";

$response = $consumer->complete( $_GET );
$session =& CRM_Core_Session::singleton( );
$new_install = $session->get('new_install');
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
    $sreg = $response->extensionResponse( 'sreg' );
    $email = @$sreg['email'];
    require_once 'user.php';
    require_once 'CRM/Utils/System/Standalone.php';

    if ($new_install) {
   
        $user_array = $session->get( 'user' );
        $user = new Standalone_User( $openid,
                                     $user_array['email'],
                                     $user_array['firstname'],
                                     $user_array['lastname'] );
        CRM_Utils_System_Standalone::getUserID( $user );
        $contactId = $session->get( 'userID' );
        CRM_Core_BAO_UFMatch::setAllowedToLogin( $contactId, 1 );
    } else {
        $user = new Standalone_User( $openid, $email );
    }    
    $allow_login = CRM_Utils_System_Standalone::getAllowedToLogin( $user );
    if ( !$allow_login && !CIVICRM_ALLOW_ALL ) {
        $session->set( 'msg' , 'Login failed.' );	
        $session->set( 'goahead', "no" );
        header("Location: index.php");
        exit(0);
    }
    if ( empty( $contactId ) ) {
        CRM_Utils_System_Standalone::getUserID( $user );
        $contactId = $session->get( 'userID' );
    }    
    if ( empty( $contactId ) ) {
        $session->set( 'msg' , 'You are not authorized to login.' );
        $session->set( 'goahead', "no" );
    }
    header("Location: index.php");
    exit(0);
}

?>