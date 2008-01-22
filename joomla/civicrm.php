<?php
  //////////////////////////////////////////////////
  // CiviCRM Front-end Profile - Logic Layer
  //////////////////////////////////////////////////

if( ! defined( '_VALID_MOS' ) && ! defined( '_JEXEC' ) ) {
	die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
}

// PUT ALL YOUR BUSINESS LOGIC CODE HERE

include_once 'civicrm.settings.php';

require_once 'PEAR.php';

require_once 'CRM/Core/Config.php';
require_once 'CRM/Core/DAO.php';
require_once 'CRM/Core/Error.php';
require_once 'CRM/Core/Invoke.php';

civicrm_invoke( );

function civicrm_init( ) {
    $config =& CRM_Core_Config::singleton();
    CRM_Core_DAO::init($config->dsn, $config->daoDebug);

    // this is the front end, so let others know
    $config->userFrameworkFrontend = 1;

    $factoryClass = 'CRM_Contact_DAO_Factory';

    CRM_Core_DAO::setFactory(new $factoryClass());

    // set error handling
    PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, array('CRM_Core_Error', 'handle'));
}


function civicrm_invoke( ) {
    civicrm_init( );

    // add all the values from the itemId param
    // overrride the GET values if conflict
    if ( CRM_Utils_Array::value( 'Itemid', $_GET ) ) {
        global $database;
        $menu = new mosMenu( $database );
        $menu->load( $_GET['Itemid'] );
        $params = new mosParameters( $menu->params );
        $args = array( 'task', 'id', 'gid', 'reset' ); 
        foreach ( $args as $a ) { 
            $val = $params->get( $a, null ); 
            if ( $val !== null ) { 
                $_GET[$a] = $val; 
            } 
        } 
    }
    $task = CRM_Utils_Array::value( 'task', $_GET, '' );
    $args = explode( '/', trim( $task ) );

    // check permission
    if ( ! civicrm_check_permission( $args ) ) {
        echo "You do not have permission to execute this url.";
        return;
    }

    global $my;
    require_once 'CRM/Core/BAO/UFMatch.php';
    CRM_Core_BAO_UFMatch::synchronize( $my, false, 'Joomla', 'Individual' );

    CRM_Core_Invoke::invoke( $args );
}

function civicrm_check_permission( $args ) {
    if ( $args[0] != 'civicrm' ) {
        return false;
    }

    // all profile and file urls are valid
    $arg1 = CRM_Utils_Array::value( 1, $args );
    $validPaths = array( 'profile', 'user', 'dashboard' );
    if ( in_array( $arg1 , $validPaths ) ) {
        return true;
    }

    $config = CRM_Core_Config::singleton( );
    
    // a transaction page is valid
    if ( in_array( 'CiviContribute', $config->enableComponents ) &&
         CRM_Utils_Array::value( 1, $args ) == 'contribute' &&
         CRM_Utils_Array::value( 2, $args ) == 'transact' ) {
        return true;
    }

    // an event registration page is valid
    if ( in_array( 'CiviEvent', $config->enableComponents ) ) {
        if ( CRM_Utils_Array::value( 1, $args ) == 'event' &&
             ( ( CRM_Utils_Array::value( 2, $args ) == 'register' ) || 
               ( CRM_Utils_Array::value( 2, $args ) == 'info' ) ) ) {
            return true;
        }
        // also allow events to be mapped
        if ( CRM_Utils_Array::value( 1, $args ) == 'contact' &&
             CRM_Utils_Array::value( 2, $args ) == 'map'     &&
             CRM_Utils_Array::value( 3, $args ) == 'event'   ) {
            return true;
        }
    }
    
    // allow mailing urls to be processed
    if ( in_array( 'CiviMail', $config->enableComponents ) ) {
        if ( CRM_Utils_Array::value( 1, $args ) == 'mailing' &&
             ( ( in_array( CRM_Utils_Array::value( 2, $args ),
                           array( 'forward', 'unsubscribe', 'resubscribe', 'optout' ) ) ) ) ) {
            return true;
        }
    }
    
    return false;
}

?>
