<?php

global $user_home;

$include_path = ini_get('include_path');
$include_path .= ":$user_home";
ini_set('include_path', $include_path);

define( 'CRM_TEMPLATE_COMPILEDIR', "$user_home/templates_c" );
define( 'CRM_TEMPLATEDIR'        , "$user_home/templates"   );

?>