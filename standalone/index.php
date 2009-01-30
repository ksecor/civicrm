<?php
require_once 'bootstrap_common.php';

function invoke() {
    $session  =& CRM_Core_Session::singleton( );
    $config   =& CRM_Core_Config::singleton( );

    // display error if any
    showError( $session );
    
    $urlVar = $config->userFrameworkURLVar;
    if ( !isset( $_GET[$urlVar] ) ) {
        $_GET[$urlVar] = '';
        print '<link rel="Shortcut Icon" type="image/x-icon" href="../i/widget/favicon.png" />';
    }
    
    require_once 'CRM/Core/Invoke.php';

    if ( $session->get('userID') == null || $session->get('userID') == '' ) {
        if ($_GET[$urlVar] == "") {
            require_once "CRM/Core/BAO/UFMatch.php";
            if ( CRM_Core_BAO_UFMatch::isEmptyTable( ) == false ) {
                include('login.html');
            } else {
                $session->set( 'new_install', true );
                include('new_install.html');
            }
            exit(1);
        } else {
            $str = '';
            if ( $session->get('new_install') !== true &&
                 $_GET[$urlVar] !== "civicrm/standalone/register" ) {
                $str = "<a href=\"{$config->userFrameworkBaseURL}\">Login here</a> if you have an account.\n";
            } elseif ($_GET[$urlVar] == "civicrm/standalone/register" && isset($_GET['reset'])) {
                // this is when user first registers with civicrm
                print "<head><style type=\"text/css\"> body {border: 1px #CCC solid;margin: 3em;padding: 1em 1em 1em 2em;} </head>";
            }
            print $str . CRM_Core_Invoke::invoke( explode('/', $_GET[$urlVar] ) );
        }
    } else {
        if ($_GET[$urlVar] == "") {
            print CRM_Core_Invoke::invoke( array("civicrm","dashboard") );
        } else {
            print CRM_Core_Invoke::invoke( explode('/', $_GET[$urlVar] ) );
            print '<link rel="Shortcut Icon" type="image/x-icon" href="../i/widget/favicon.png" />';
        }
    }
}

function showError( &$session ) {
    // display errors if any
    if ( !empty( $error ) ) {
        print "<div class=\"error\">$error</div>\n";
    }
    
    if ( $session->get('msg') ) {
        $msg = $session->get('msg');
        print "<div class=\"msg\">$msg</div>\n";
        $session->set('msg', null);
    }

    if ( $session->get('goahead') == 'no' ) {
        $session->reset();
        print "<a href=\"index.php\">Home Page</a>\n";
        exit();
    }
}

invoke();
?>