<?php

global $user_home;
$user_home = '/Users/lobo/svn/crm';

define( 'CRM_SMARTYDIR', '/opt/local/lib/php/Smarty/' );
define( 'CRM_HTTPBASE' , '/lobo/drupal/'              );
define( 'CRM_MAINMENU' , '/lobo/drupal/'              );
define( 'CRM_DAO_DEBUG', 0                            );
define( 'CRM_TEST_DIR' , $user_home . '/test/'        );

include_once 'config.main.php';

?>