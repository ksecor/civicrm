<?php

global $user_home;
$user_home = '/Users/lobo/svn/crm';

define( 'CRM_SMARTYDIR'   , $user_home . DIRECTORY_SEPARATOR . 'packages' . DIRECTORY_SEPARATOR . 'Smarty' . DIRECTORY_SEPARATOR );
define( 'CRM_HTTPBASE'    , '/lobo/mambo/administrator/'  );
define( 'CRM_RESOURCEBASE', '/lobo/mambo/administrator/components/com_civicrm/'       );
define( 'CRM_MAINMENU'    , '/lobo/mambo/administrator/index2.php?option=com_civicrm' );
define( 'CRM_DAO_DEBUG'   , 0                                     );
define( 'CRM_TEST_DIR'    , $user_home . DIRECTORY_SEPARATOR . 'test'   . DIRECTORY_SEPARATOR );
// the new_link option is super important if u r reusing the same user id across both drupal and civicrm
define( 'CRM_DSN'         , 'mysql://civicrm:YOUR_PASSWORD@localhost/civicrm?new_link=true' );

define( 'CRM_USERFRAMEWORK'       , 'Mambo' );
define( 'CRM_USERFRAMEWORK_URLVAR', 'task'  );

define( 'CRM_LC_MESSAGES' , 'en_US' );
define( 'CRM_DATEFORMAT_FULL', '%B %e, %Y' );
define( 'CRM_DATEFORMAT_PARTIAL', '%B %Y' );
define( 'CRM_DATEFORMAT_YEAR', '%Y' );
define( 'CRM_SMTP_SERVER' , 'smtp.sbcglobal.net' );

include_once 'config.main.php';

?>
