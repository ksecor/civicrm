<?php
require_once "auth_common.php";

$session =& CRM_Core_Session::singleton( );
$return_to = $session->get('openid_process_url');
$response = $consumer->complete( $return_to );
$new_install = $session->get('new_install');
if ( $response->status == Auth_OpenID_CANCEL ) {
    // This means authentication was cancelled.
    $session->set('msg', 'Login cancelled.');
    $session->set('goahead', "no");
} else if ( $response->status == Auth_OpenID_FAILURE ) {
    $session->set('msg', "Login failed: " . $response->message);
    $session->set('goahead', "no");
} else if ( $response->status == Auth_OpenID_SUCCESS ) {
    $session->set('goahead', "yes");
    $openid = $response->identity_url;
    $session->set('openid', $openid);
    require_once 'Auth/OpenID/SReg.php';
    $sreg_response = Auth_OpenID_SRegResponse::fromSuccessResponse( $response );
    $sreg = $sreg_response->contents( );
    $email = @$sreg['email'];
    $fullname = @$sreg['fullname'];
    require_once 'CRM/Utils/System/Standalone.php';
    if ($new_install === true) {
        require_once 'CRM/Core/BAO/UFGroup.php';
        require_once 'CRM/Core/Action.php';
        
        // Redirect to new user registration form
        $urlVar = $config->userFrameworkURLVar;
        header("Location: index.php?$urlVar=civicrm/standalone/register&reset=1");
        exit;
        
        /*
        
        $register = true;
        $reset = false; // Is this right?
        $doNotProcess = false;
        $ctype = 'Individual';
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
        */
    } else {
        require_once 'CRM/Standalone/User.php';
        $user = new CRM_Standalone_User( $openid, $email );
    }
    $allow_login = CRM_Utils_System_Standalone::getAllowedToLogin( $user );
    if ( !$allow_login && (!defined('CIVICRM_ALLOW_ALL') || !CIVICRM_ALLOW_ALL ) ) {
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

