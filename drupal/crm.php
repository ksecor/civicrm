<?php
#######################################################
#
# crm.php
#
# defines constants used by the crm module.
#
#######################################################
define( 'CRM_SMARTYDIR'          , '/usr/local/lib/php/Smarty/');
define( 'CRM_DSN'                , 'mysql://nobody:test12345@localhost/crm');
define( 'CRM_DAO_DEBUG_LVL'      , 0 );
define( 'CRM_TEMPLATE_COMPILEDIR', '/home/yvb/svn/crm/templates_c');
define( 'CRM_TEMPLATEDIR'        , '/home/yvb/svn/crm/templates');

# logging related constants.
define('CRM_LOG_LEVEL_PRIORITY', 5);
define('CRM_LOG_FILENAME', '/var/log/crm/debug.log');

# different log levels
define('CRM_LOG_LEVEL_ENTER_EXIT', 0);  // log all entry and exits of functions
define('CRM_LOG_LEVEL_PARAM', 1);       // log all parameters
define('CRM_LOG_LEVEL_L2', 2);          // L2 - for more details
define('CRM_LOG_LEVEL_L3', 3);          // L3 - for a lot more details
define('CRM_LOG_LEVEL_L4', 4);          // L4 - for a lot lot more details
define('CRM_LOG_LEVEL_L5', 5);          // L5 - for a lot lot lot more details
?>
