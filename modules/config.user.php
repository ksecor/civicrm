<?php

global $user_home;
$user_home = '/Users/lobo/svn/crm';

define( 'CRM_SMARTYDIR' , $user_home . '/packages/Smarty/'          );
define( 'CRM_HTTPBASE'  , '/lobo/drupal/crm/'                       );
define( 'CRM_MAINMENU'  , '/lobo/drupal/crm/'                       );
define( 'CRM_DAO_DEBUG' , 0                                         );
define( 'CRM_TEST_DIR'  , $user_home . '/test/'                     );
define( 'CRM_UPLOAD_DIR', $user_home . '/upload/'                   );
define( 'CRM_DSN'       , 'mysql://crm:YOUR_PASSWORD@localhost/crm' );

include_once 'config.main.php';

?>
