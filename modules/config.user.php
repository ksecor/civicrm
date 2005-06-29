<?php

global $user_home;
$user_home = '/Users/lobo/svn/crm';

define( 'CRM_SMARTYDIR'   , $user_home . DIRECTORY_SEPARATOR . 'packages' . DIRECTORY_SEPARATOR . 'Smarty' . DIRECTORY_SEPARATOR );
define( 'CRM_HTTPBASE'    , '/lobo/drupal/'                     );
define( 'CRM_RESOURCEBASE', '/lobo/drupal/modules/civicrm/'     );
define( 'CRM_MAINMENU'    , '/lobo/drupal/civicrm/'               );
define( 'CRM_DAO_DEBUG'   , 0                                     );
define( 'CRM_TEST_DIR'    , $user_home . DIRECTORY_SEPARATOR . 'test'   . DIRECTORY_SEPARATOR );
// the new_link option is super important if u r reusing the same user id across both drupal and civicrm
define( 'CRM_DSN'         , 'mysql://civicrm:YOUR_PASSWORD@localhost/civicrm?new_link=true' );
define( 'CRM_LC_MESSAGES' , 'en_US' );
define( 'CRM_DATEFORMAT_FULL', '%B %E%f, %Y %h:%i %A' );
define( 'CRM_DATEFORMAT_PARTIAL', '%B %Y' );
define( 'CRM_DATEFORMAT_YEAR', '%Y' );
define( 'CRM_DATEFORMAT_QF_DATE', '%b %d %Y' );
define( 'CRM_DATEFORMAT_QF_DATETIME', '%b %d %Y, %I : %M %P' );
define( 'CRM_SMTP_SERVER' , 'smtp.sbcglobal.net' );
define( 'CRM_PHP_VERSION' , 5 );

include_once 'config.main.php';

?>
