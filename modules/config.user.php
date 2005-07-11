<?php

global $user_home;

// this is the path where you have installed the civicrm code
$user_home = '/Users/lobo/htdocs/drupal/modules/civicrm';

// these variables define the absolute urls to access drupal and civicrm
// note that the trailing slash is important
define( 'CRM_HTTPBASE'    , '/lobo/drupal/'                     );
define( 'CRM_RESOURCEBASE', '/lobo/drupal/modules/civicrm/'     );

// the new_link option is super important if u r reusing the same user id across both drupal and civicrm
define( 'CRM_DSN'         , 'mysql://civicrm:YOUR_PASSWORD@localhost/civicrm?new_link=true' );

define( 'CRM_LC_MESSAGES' , 'en_US' );

// this is used for formatting the various date displays. Change this to match
// your locale if different.
define( 'CRM_DATEFORMAT_FULL', '%B %E%f, %Y %h:%i %A' );
define( 'CRM_DATEFORMAT_PARTIAL', '%B %Y' );
define( 'CRM_DATEFORMAT_YEAR', '%Y' );

define( 'CRM_SMTP_SERVER' , 'YOUR SMTP SERVER' );

define( 'CRM_MYSQL_VERSION', 4.1 );

include_once 'config.main.php';

?>
