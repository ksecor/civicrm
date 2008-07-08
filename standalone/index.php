<?php
function invoke() {
    session_start();
    
    // Pull in the settings file & Instantiate the config so that the DB connection will fire up
    require_once '../civicrm.config.php';
    require_once 'CRM/Core/Config.php';
    $config  =& CRM_Core_Config::singleton( );
    
    require_once 'CRM/Core/Session.php';
    $session =& CRM_Core_Session::singleton( );
    
    // display error if any
    showError( $error, $session );

    $urlVar = $config->userFrameworkURLVar;
    if ( !isset( $_GET[$urlVar] ) ) {
        $_GET[$urlVar] = '';
    }
    
    require_once 'CRM/Core/Invoke.php';

    if ( $session->get('userID') == null || $session->get('userID') == '' ) {
        if ($_GET[$urlVar] == "") {
            require_once "CRM/Core/BAO/UFMatch.php";
            if ( CRM_Core_BAO_UFMatch::isEmptyTable( ) ) {
                $session->set( 'new_install', true );
                include('new_install.html');
            } else {
                include('login.html');
            }
            exit(1);
        } else {
            if ( $session->get('new_install') !== true ) {
                print "<a href=\"{$config->userFrameworkBaseURL}\">Login here</a> if you have an account.\n";
            } elseif ($_GET[$urlVar] == "civicrm/standalone/register" && isset($_GET['reset'])) {
                // this is when user first registers with civicrm
                print "<head><style type=\"text/css\"> body {border: 1px #CCC solid;margin: 3em;padding: 1em 1em 1em 2em;} </head>";
            }
            print CRM_Core_Invoke::invoke( explode('/', $_GET[$urlVar] ) );
        }
    } else {
        if ($_GET[$urlVar] == "") {
            print CRM_Core_Invoke::invoke( array("civicrm","dashboard") );
        } else {
            print CRM_Core_Invoke::invoke( explode('/', $_GET[$urlVar] ) );
        }
    }
}

function showError( &$error, &$session ) {
    // Check for errors in the session
    if ($session->get('error')) {
        print $session->get('error');
        $session->set('error',null);
        $gotError = true;
    }
    
    // display errors if any
    if ( !empty( $error ) ) {
        print "<div class=\"error\">$error</div>\n";
        $gotError = true;
    }

    if ( $session->get('msg') ) {
        $msg = $session->get('msg');
        print "<div class=\"msg\">$msg</div>\n";
        $gotError = true;
    }

    if ( isset($gotError) || $session->get('goahead') == 'no' ) {
        exit(0);
    }
}

invoke();
?>