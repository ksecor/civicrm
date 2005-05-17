<?php

global $user_home;
$user_home = '/Users/lobo/svn/crm';

define( 'CRM_SMARTYDIR' , $user_home . DIRECTORY_SEPARATOR . 'packages' . DIRECTORY_SEPARATOR . 'Smarty' . DIRECTORY_SEPARATOR );
define( 'CRM_HTTPBASE'    , '/lobo/drupal/'                     );
define( 'CRM_RESOURCEBASE', '/lobo/drupal/modules/civicrm/'     );
define( 'CRM_MAINMENU'  , '/lobo/drupal/civicrm/'               );
define( 'CRM_DAO_DEBUG' , 0                                     );
define( 'CRM_TEST_DIR'  , $user_home . DIRECTORY_SEPARATOR . 'test'   . DIRECTORY_SEPARATOR );
define( 'CRM_DSN'       , 'mysql://civicrm:YOUR_PASSWORD@localhost/civicrm' );
define( 'CRM_LC_MESSAGES', 'en_US' );
define( 'CRM_GETTEXT_CODESET', 'utf-8' );
define( 'CRM_GETTEXT_DOMAIN', 'civicrm' );
define( 'CRM_GETTEXT_RESOURCE_DIR', $user_home . DIRECTORY_SEPARATOR . 'l10n' );

include_once 'config.main.php';

?>
