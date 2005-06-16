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

define( 'CRM_GETTEXT_CODESET'     , 'utf-8' );
define( 'CRM_GETTEXT_DOMAIN'      , 'civicrm' );
define( 'CRM_GETTEXT_RESOURCE_DIR', $user_home . DIRECTORY_SEPARATOR . 'l10n' );

if ( ! defined( 'CRM_HTTPBASE' ) ) {
  define( 'CRM_HTTPBASE', '/drupal/' );
}

if ( ! defined( 'CRM_MAINMENU' ) ) {
  define( 'CRM_MAINMENU', '/drupal/' );
}

if ( ! defined( 'JPSPAN' ) ) {
    define( 'JPSPAN', $user_home . DIRECTORY_SEPARATOR . 'packages' . DIRECTORY_SEPARATOR . 'JPSpan' . DIRECTORY_SEPARATOR . 'JPSpan' . DIRECTORY_SEPARATOR );
}

define( 'CRM_CLEANURL', 0 );
define( 'CRM_TEMPLATE_COMPILEDIR', '/tmp/civicrm.mambo' );
define( 'CRM_UPLOAD_DIR'         , '/tmp/civicrm.mambo' );

?>
