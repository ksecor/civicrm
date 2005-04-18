<?php

global $user_home;

$include_path = ini_get('include_path');
$include_path = '.'        . PATH_SEPARATOR .
                $user_home . PATH_SEPARATOR . 
                $user_home . DIRECTORY_SEPARATOR . 'packages' . PATH_SEPARATOR .
                $include_path;
ini_set('include_path', $include_path);

define( 'CRM_TEMPLATE_COMPILEDIR', $user_home . DIRECTORY_SEPARATOR . 'templates_c' );
define( 'CRM_TEMPLATEDIR'        , $user_home . DIRECTORY_SEPARATOR . 'templates'   );

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
if ( function_exists( 'variable_get' ) && ( variable_get('clean_url', '0') != '0' ) ) {
    define( 'CRM_CLEANURL', 1 );
} else {
    define( 'CRM_CLEANURL', 0 );
}

?>