<?php

global $user_home;

$include_path = ini_get('include_path');
$include_path = '.'        . PATH_SEPARATOR .
                $user_home . PATH_SEPARATOR . 
                $user_home . DIRECTORY_SEPARATOR . 'packages' . PATH_SEPARATOR .
                $include_path;
ini_set('include_path', $include_path);

define( 'CRM_TEMPLATEDIR', $user_home . DIRECTORY_SEPARATOR . 'templates'   );
define( 'CRM_PLUGINSDIR' , $user_home . DIRECTORY_SEPARATOR . 'CRM' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'Smarty' . DIRECTORY_SEPARATOR . 'plugins' );

define( 'CRM_DOMAIN_ID'           , 1       );
define( 'CRM_GETTEXT_CODESET'     , 'utf-8' );
define( 'CRM_GETTEXT_DOMAIN'      , 'civicrm' );
define( 'CRM_GETTEXT_RESOURCE_DIR', $user_home . DIRECTORY_SEPARATOR . 'l10n' );

if ( ! defined(CRM_USERFRAMEWORK) ) {
    define( 'CRM_USERFRAMEWORK', 'Drupal' );
}

if ( ! defined(CRM_HTTPBASE) ) {
  define( 'CRM_HTTPBASE', '/drupal/' );
}

if ( ! defined(CRM_MAINMENU) ) {
  define( 'CRM_MAINMENU', '/drupal/' );
}

if ( ! defined( JPSPAN ) ) {
    define( JPSPAN, $user_home . DIRECTORY_SEPARATOR . packages . DIRECTORY_SEPARATOR . 'JPSpan' . DIRECTORY_SEPARATOR . 'JPSpan' . DIRECTORY_SEPARATOR );
}

// drupal specific code
if ( function_exists( 'variable_get' ) ) {
    if ( variable_get('clean_url', '0') != '0' ) {
        define( 'CRM_CLEANURL', 1 );
    } else {
        define( 'CRM_CLEANURL', 0 );
    }

    $scratch_directory = variable_get( 'file_directory_path', 'files');
    $scratch_directory = $scratch_directory . DIRECTORY_SEPARATOR . 'civicrm';

    $compileDir        = $scratch_directory . DIRECTORY_SEPARATOR . 'templates_c';
    if ( ! is_dir( $compileDir ) ) {
        if ( substr(phpversion(), 0, 1) == 4 ) {
            mkdir( $compileDir, 0777 );
        } else {
            mkdir( $compileDir, 0777, true );
        }
    }
    define( 'CRM_TEMPLATE_COMPILEDIR', $compileDir );

    $uploadDir         = $scratch_directory . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR;
    if ( ! is_dir( $uploadDir ) ) {
        if ( substr(phpversion(), 0, 1) == 4 ) {
            mkdir( $uploadDir, 0777 );
        } else {
            mkdir( $uploadDir, 0777, true );
        }
    }
    define( 'CRM_UPLOAD_DIR'         , $uploadDir );
} else {
    define( 'CRM_CLEANURL', 0 );
    define( 'CRM_TEMPLATE_COMPILEDIR', $user_home . DIRECTORY_SEPARATOR . 'templates_c' );
    define( 'CRM_UPLOAD_DIR'         , $user_home . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR );
}

?>
