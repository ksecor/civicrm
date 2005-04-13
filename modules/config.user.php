<?php

global $user_home;
$user_home = '/Users/lobo/svn/crm';

define( 'CRM_SMARTYDIR' , $user_home . DIRECTORY_SEPARATOR . 'packages' . DIRECTORY_SEPARATOR . 'Smarty' . DIRECTORY_SEPARATOR' );
define( 'CRM_HTTPBASE'  , '/lobo/drupal/'                       );
define( 'CRM_MAINMENU'  , '/lobo/drupal/civicrm/'               );
define( 'CRM_DAO_DEBUG' , 0                                     );
define( 'CRM_TEST_DIR'  , $user_home . DIRECTORY_SEPARATOR . 'test'   . DIRECTORY_SEPARATOR' );
define( 'CRM_UPLOAD_DIR', $user_home . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR' );
define( 'CRM_DSN'       , 'mysql://crm:YOUR_PASSWORD@localhost/crm' );

include_once 'config.main.php';

?>
