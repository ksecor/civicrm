<?php

if( ! defined( '_VALID_MOS' ) && ! defined( '_JEXEC' ) ) {
	die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
}

// this has been moved here from install.civicrm.php
// because the 1.5 installer does not run a script at end of install
if ( ! file_exists( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'civicrm.settings.php' ) ) {
	global $database;
    global $mosConfig_absolute_path;
    $path =
        $mosConfig_absolute_path . DIRECTORY_SEPARATOR .
        'administrator'          . DIRECTORY_SEPARATOR .
        'components'             . DIRECTORY_SEPARATOR .
        'com_civicrm'            . DIRECTORY_SEPARATOR ;
    
    // this require actually runs the function needed
    // bad code, but easier to debug on remote machines
    require_once $path . 'configure.php';
}

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

    global $my;
    require_once 'CRM/Core/BAO/UFMatch.php';
    CRM_Core_BAO_UFMatch::synchronize( $my, false, 'Joomla', 'Individual' );

    $args = explode( '/', trim( $_GET['task'] ) );
    CRM_Core_Invoke::invoke( $args );
}

?>
