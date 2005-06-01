<?php


global $user_home;
$user_home = '/home/anil/svn/crm/php4/';

define( 'CRM_SMARTYDIR' , $user_home . DIRECTORY_SEPARATOR . 'packages' . DIRECTORY_SEPARATOR . 'Smarty' . DIRECTORY_SEPARATOR );
define( 'CRM_HTTPBASE'    , '/drupal/'                     );
define( 'CRM_RESOURCEBASE', '/drupal/modules/civicrm/'     );
define( 'CRM_MAINMENU'  , '/drupal/civicrm/'               );
define( 'CRM_DAO_DEBUG' , 0                                     );
define( 'CRM_TEST_DIR'  , $user_home . DIRECTORY_SEPARATOR . 'test'   . DIRECTORY_SEPARATOR );
define( 'CRM_DSN'       , 'mysql://civicrm:Mt!Everest@localhost/civicrm' );
define( 'CRM_LC_MESSAGES'         , 'en_US' );

include_once 'config.main.php';

?>
