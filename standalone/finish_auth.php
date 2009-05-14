<?php
require_once "auth_common.php";

function run() {
    $session  =& CRM_Core_Session::singleton( );
    $config   =& CRM_Core_Config::singleton( );

    $consumer = getConsumer();

    // Complete the authentication process using the server's
    // response.
    $return_to = getReturnTo();
    $response  = $consumer->complete($return_to);

    // Check the response status.
    if ($response->status == Auth_OpenID_CANCEL) {
        // This means the authentication was cancelled.
        $msg = 'Verification cancelled.';
        $session->set('msg', $msg);
        $session->set('goahead', "no");
    } else if ($response->status == Auth_OpenID_FAILURE) {
        // Authentication failed; display the error message.
        $msg = "OpenID authentication failed: " . $response->message;
        $session->set('msg', $msg);
        $session->set('goahead', "no");
    } else if ($response->status == Auth_OpenID_SUCCESS) {
        // This means the authentication succeeded; extract the
        // identity URL and Simple Registration data (if it was
        // returned).

        $openid = $response->getDisplayIdentifier();
        //$esc_identity = escape($openid);
        $session->set('openid' , $openid);
        $session->set('goahead', "yes");

        $sreg_resp = Auth_OpenID_SRegResponse::fromSuccessResponse($response);
        $sreg      = $sreg_resp->contents();
        $sreg['email'] = empty( $sreg['email'] ) ? '' : $sreg['email'];
        if ( $session->get('new_install') == true ) {
            // update group clause
            require_once 'CRM/Core/Transaction.php';
            require_once 'CRM/Contact/BAO/Group.php';
            $groupDAO =& new CRM_Contact_DAO_Group();
            $groupDAO->find( );
            while ( $groupDAO->fetch() ) {
                if ( !isset($transaction) ) {
                    $transaction = new CRM_Core_Transaction( );
                }
                $group =& new CRM_Contact_BAO_Group();
                $group->id = $groupDAO->id;
                $group->find( true );
                $group->buildClause( );
                $group->save( );
            }
            if ( isset($transaction) ) {
                $transaction->commit( );
            }

            // Redirect to new user registration form
            $urlVar = $config->userFrameworkURLVar;
            $config->reset();
            header("Location: index.php?$urlVar=civicrm/standalone/register&reset=1&configReset=1");
            exit;
        } else {
            require_once 'CRM/Standalone/User.php';
            $user = new CRM_Standalone_User( $openid, $sreg['email'] );

            require_once 'CRM/Utils/System/Standalone.php';
            $allow_login = CRM_Utils_System_Standalone::getAllowedToLogin( $user );
            if ( !$allow_login && (!defined('CIVICRM_ALLOW_ALL') || !CIVICRM_ALLOW_ALL ) ) {
                $session->set( 'msg' , 'You are not allowed to login. Login failed. Contact your Administrator.' );	
                $session->set( 'goahead', "no" );
            } else {
                CRM_Utils_System_Standalone::getUserID( $user );
                
                if ( ! $session->get('userID') ) {
                    $session->set( 'msg' , 'You are not authorized to login.' );
                    $session->set( 'goahead', "no" );
                }
            }

            header("Location: index.php");
            exit(0);
        }
    }
    
    displayError("Unknown status returned.");
}

run();
?>