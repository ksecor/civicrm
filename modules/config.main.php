<?php

global $civicrm_root;

$include_path = ini_get('include_path');
$include_path = '.'        . PATH_SEPARATOR .
                $civicrm_root . PATH_SEPARATOR . 
                $civicrm_root . DIRECTORY_SEPARATOR . 'packages' . PATH_SEPARATOR .
                $include_path;
ini_set('include_path', $include_path);

define( 'CIVICRM_SMARTYDIR'  , $civicrm_root . DIRECTORY_SEPARATOR . 'packages' . DIRECTORY_SEPARATOR . 'Smarty' . DIRECTORY_SEPARATOR );
define( 'CIVICRM_TEST_DIR'   , $civicrm_root . DIRECTORY_SEPARATOR . 'test'   . DIRECTORY_SEPARATOR );
define( 'CIVICRM_DAO_DEBUG'  , 0 );
define( 'CIVICRM_TEMPLATEDIR', $civicrm_root . DIRECTORY_SEPARATOR . 'templates'   );
define( 'CIVICRM_PLUGINSDIR' , $civicrm_root . DIRECTORY_SEPARATOR . 'CRM' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'Smarty' . DIRECTORY_SEPARATOR . 'plugins' );

define( 'CIVICRM_GETTEXT_CODESET'    , 'utf-8'   );
define( 'CIVICRM_GETTEXT_DOMAIN'     , 'civicrm' );
define( 'CIVICRM_GETTEXT_RESOURCEDIR', $civicrm_root . DIRECTORY_SEPARATOR . 'l10n' );

if ( ! defined( 'CIVICRM_USERFRAMEWORK' ) ) {
    define( 'CIVICRM_USERFRAMEWORK', 'Drupal' );
}

if ( ! defined( 'CIVICRM_HTTPBASE' ) ) {
    define( 'CIVICRM_HTTPBASE', '/drupal/' );
}

if ( ! defined( 'CIVICRM_RESOURCEBASE' ) ) {
    define( 'CIVICRM_RESOURCEBASE', CIVICRM_HTTPBASE . 'modules/civicrm/' );
}

if ( ! defined( 'CIVICRM_MAINMENU' ) ) {
    define( 'CIVICRM_MAINMENU', CIVICRM_HTTPBASE . 'civicrm/' );
}

if ( ! defined( 'CIVICRM_DOMAIN_ID' ) ) {
    define( 'CIVICRM_DOMAIN_ID', 1 );
}

if ( ! defined( 'JPSPAN' ) ) {
    define( 'JPSPAN', $civicrm_root . DIRECTORY_SEPARATOR . 'packages' . DIRECTORY_SEPARATOR . 'JPSpan' . DIRECTORY_SEPARATOR );
}

if ( function_exists( 'variable_get' ) && variable_get('clean_url', '0') != '0' ) {
    define( 'CIVICRM_CLEANURL', 1 );
} else {
    define( 'CIVICRM_CLEANURL', 0 );
}

?>
