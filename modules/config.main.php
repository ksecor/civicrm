<?php

global $user_home;

$include_path = ini_get('include_path');
$include_path = ".:$user_home:$user_home/packages:$include_path";
ini_set('include_path', $include_path);

define( 'CRM_DSN'                , 'mysql://crm:Mt!Everest@localhost/crm' );

define( 'CRM_TEMPLATE_COMPILEDIR', "$user_home/templates_c" );
define( 'CRM_TEMPLATEDIR'        , "$user_home/templates"   );

if ( ! defined( CRM_HTTPBASE ) ) {
  define( 'CRM_HTTPBASE', '/drupal/' );
}

if ( ! defined( CRM_MAINMENU ) ) {
  define( 'CRM_MAINMENU', '/drupal/' );
}

?>