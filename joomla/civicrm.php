<?php
  //////////////////////////////////////////////////
  // CiviCRM Front-end Profile - Logic Layer
  //////////////////////////////////////////////////

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

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
        foreach ( $params->_params as $name => $value ) {
            $_GET[$name] = $value;
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
    CRM_Core_BAO_UFMatch::synchronize( $my, false, 'Joomla' );

    CRM_Core_Invoke::invoke( $args );
}

function civicrm_check_permission( $args ) {
    if ( $args[0] != 'civicrm' ) {
        return false;
    }

    // all profile urls are valid
    if ( CRM_Utils_Array::value( 1, $args ) == 'profile' ) {
        return true;
    }

    $config = CRM_Core_Config::singleton( );
    
    // a transaction page is also valid
    if ( in_array( 'CiviContribute', $config->enableComponents ) &&
         CRM_Utils_Array::value( 1, $args ) == 'contribute' &&
         CRM_Utils_Array::value( 2, $args ) == 'transact' ) {
        return true;
    }
    
    return false;
}

?>
