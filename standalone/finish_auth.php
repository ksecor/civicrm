<?php

require_once "auth_common.php";

$session =& CRM_Core_Session::singleton( );
$return_to = $session->get('openid_process_url');

$response = $consumer->complete( $return_to );
$new_install = $session->get('new_install');
print "new_install: $new_install<br/>";
if ($new_install) {
    if ($_POST['submit'] == 'Create User') {
        $new_install = 'finish';
    } else {
        $new_install = 'start';
    }
}
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
    //$sreg = $response->extensionResponse( 'sreg', false );
    require_once 'Auth/OpenID/SReg.php';
    $sreg_response = Auth_OpenID_SRegResponse::fromSuccessResponse( $response );
    $sreg = $sreg_response->contents( );
    $email = @$sreg['email'];
    $fullname = @$sreg['fullname'];
    require_once 'user.php';
    require_once 'CRM/Utils/System/Standalone.php';

    if ($new_install == 'start') {
        require_once 'CRM/Core/BAO/UFGroup.php';
        require_once 'CRM/Core/BAO/UFMatch.php';
        require_once 'CRM/Core/Action.php';
        require_once 'CRM/Core/Config.php';
        require_once 'CRM/Core/Session.php';
        # initialize the system by grabbing the config & session singletons
        $config =& CRM_Core_Config::singleton( );
        $session =& CRM_Core_Session::singleton( );

        /* The form still doesn't display even w/ this enabled
        $user_array = $session->get( 'user' );
        $user = new Standalone_User( $openid, $email );
        CRM_Utils_System_Standalone::getUserID( $user );
        $userId = $session->get( 'userID' );
        */
        
        $register = true;
        $reset = false; // Is this right?
        $doNotProcess = false;
        $ctype = 'Individual';
        print "Starting new user registration for userID $userId<br/>";
        $regFormHtml = CRM_Core_BAO_UFGroup::getEditHTML( $userId, '',
                                            CRM_Core_Action::ADD, $register,
                                            $reset, null,
                                            $doNotProcess, $ctype );
        print $regFormHtml;
        exit;
    } elseif ($new_install == 'finish') {
        $user_array = $session->get( 'user' );
        $user = new Standalone_User( $openid, $email );
        CRM_Utils_System_Standalone::getUserID( $user );
        $contactId = $session->get( 'userID' );
        $openId = new CRM_Core_BAO_OpenId( );
        $openId->contact_id = $contact_id;
        $openId->find( true );
        $openId->allowed_to_login = 1;
        $openId->update( );
        // TODO: Can we delete/unset the new_install session variable here?
    } else {
        $user = new Standalone_User( $openid, $email );
    }
    $allow_login = CRM_Utils_System_Standalone::getAllowedToLogin( $user );
    if ( !$allow_login && (!defined('CIVICRM_ALLOW_ALL') || !CIVICRM_ALLOW_ALL ) ) {
        $session->set( 'msg' , 'Login failed.' );	
        $session->set( 'goahead', "no" );
        print "Login failed!<br/>";
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
    print "Got to the end!<br/>";
    header("Location: index.php");
    exit(0);
}

?>