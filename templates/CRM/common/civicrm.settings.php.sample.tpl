<?php
/**
 * CiviCRM Configuration File - v1.6
 */

/**
 * Content Management System (CMS) Host:
 *
 * CiviCRM can be hosted in either Drupal or Joomla.
 * 
 * Settings for Drupal 4.7.x:
 *      define( 'CIVICRM_UF'        , 'Drupal' );
 *      define( 'CIVICRM_UF_VERSION', '4.7' );
 *      define( 'CIVICRM_UF_URLVAR' , 'q'  );
 *
 * For Drupal 4.6.x, same as above except
 *      define( 'CIVICRM_UF_VERSION', '4.6' );
 *
 * Settings for Joomla:
 *      define( 'CIVICRM_UF'        , 'Joomla' );
 *      define( 'CIVICRM_UF_VERSION', '1.0.8' );
 *      define( 'CIVICRM_UF_URLVAR' , 'task'  );
 */
define( 'CIVICRM_UF'               , '%%cms%%'        );
define( 'CIVICRM_UF_VERSION'       , '%%cmsVersion%%' );
define( 'CIVICRM_UF_URLVAR'        , '%%cmsURLVar%%'  );

/**
 * Content Management System (CMS) Datasource:
 *
 * Update this setting with your CMS (Drupal or Joomla) database username, server and DB name.
 * Datasource (DSN) format:
 *      define( 'CIVICRM_UF_DSN', 'mysql://cms_db_username:cms_db_password@db_server/cms_database?new_link=true');
 */
define( 'CIVICRM_UF_DSN'           , 'mysql://%%dbUser%%:%%dbPass%%@%%dbHost%%/%%dbName%%?new_link=true' );

/** 
 * Content Management System (CMS) User Table-name:
 *
 * Update the CIVICRM_UF_USERSTABLENAME if needed to match the name of the table
 * where the CMS user data is stored. Default for Drupal installs is 'users'.
 * If you are using table-prefixing for the users table, you must enter the tablename
 * with the prefix. Default table name for Joomla - 'jos_users'. For Mambo - 'mos_users'.
 */ 
define( 'CIVICRM_UF_USERSTABLENAME', '%%usersTable%%' );

/**
 * File System Paths:
 *
 * $civicrm_root is the file system path on your server where the civicrm
 * code is installed. Use an ABSOLUTE path (not a RELATIVE path) for this setting.
 *
 * CIVICRM_TEMPLATE_COMPILEDIR is the file system path where compiled templates are stored.
 * These sub-directories and files are temporary caches and will be recreated automatically
 * if deleted.
 *
 * IMPORTANT: The COMPILEDIR directory must exist,
 * and your web server must have read/write access to these directories.
 *
 *
 * EXAMPLE - CivicSpace / Drupal:
 * If the path to the CivicSpace or Drupal home directory is /var/www/htdocs/civicspace
 * the $civicrm_root setting would be:
 *      $civicrm_root = '/var/www/htdocs/civicspace/modules/civicrm/';
 *
 * the CIVICRM_TEMPLATE_COMPILEDIR would be:
 *      define( 'CIVICRM_TEMPLATE_COMPILEDIR', '/var/www/htdocs/civicspace/files/civicrm/templates_c/' );
 *
 * EXAMPLE - Joomla Installations:
 * If the path to the Joomla home directory is /var/www/htdocs/joomla
 * the $civicrm_root setting would be:
 *      $civicrm_root = '/var/www/htdocs/joomla/administrator/components/com_civicrm/civicrm/';
 *
 * the CIVICRM_TEMPLATE_COMPILEDIR would be:
 *      define( 'CIVICRM_TEMPLATE_COMPILEDIR', '/var/www/htdocs/joomla/media/civicrm/templates_c/' );
 *
 */
global $civicrm_root;
$civicrm_root = '%%crmRoot%%';
define( 'CIVICRM_TEMPLATE_COMPILEDIR', '%%templateCompileDir%%' );

/**
 * Site URLs:
 *
 * This section defines absolute and relative URLs to access the host CMS (Drupal or Joomla)
 * and CiviCRM resources.
 *
 * IMPORTANT: Trailing slashes should be used on all URL settings.
 *
 * EXAMPLES - Drupal/CivicSpace Installations:
 * If your site's home url is http://www.example.com/civicspace/
 * these variables would be set as below. Modify as needed for your install. 
 *
 * CIVICRM_UF_BASEURL - home URL for your site:
 *      define( 'CIVICRM_UF_BASEURL' , 'http://www.example.com/civicspace/' );
 *
 * EXAMPLES - Joomla Installations:
 * If your site's home url is http://www.example.com/joomla/
 *
 * CIVICRM_UF_BASEURL - home URL for your site:
 * Administration site:
 *      define( 'CIVICRM_UF_BASEURL' , 'http://www.example.com/joomla/administrator/' );
 * Front-end site:
 *      define( 'CIVICRM_UF_BASEURL' , 'http://www.example.com/joomla/' );
 *
 */
define( 'CIVICRM_UF_BASEURL'      , '%%baseURL%%' );

/**
 * CiviCRM Database Settings:
 *
 * Define the version of MySQL you are running. 
 * CiviCRM has been optimized for MySQL 4.1, but will also run on many 4.0.x versions.
 * If you are using a 4.0.x release of MySQL, you MUST change CIVICRM_MYSQL_VERSION to 4.0
 *
 * Define the database URL (CIVICRM_DSN) for the CiviCRM database and the Drupal/Joomla database
 * Database URL format:
 *      define( 'CIVICRM_DSN', 'mysql://crm_db_username:crm_db_password@db_server/crm_database?new_link=true');
 *
 * Drupal and CiviCRM can share the same database, or can be installed into separate databases.
 *
 * EXAMPLE: Drupal and CiviCRM running in the same database...
 *      DB Name = drupal, DB User = drupal
 *      define( 'CIVICRM_DSN'         , 'mysql://drupal:YOUR_PASSWORD@localhost/drupal?new_link=true' );
 *
 * EXAMPLE: Drupal and CiviCRM running in separate databases...
 *      Drupal  DB Name = drupal, DB User = drupal
 *      CiviCRM DB Name = civicrm, CiviCRM DB User = civicrm
 *      define( 'CIVICRM_DSN'         , 'mysql://civicrm:YOUR_PASSWORD@localhost/civicrm?new_link=true' );
 *
 * define( 'CIVICRM_MYSQL_PATH', '/usr/bin/' );
 *
 * This stores the installed path of mysql. You will need to verify and modify this value if you are
 * planning on using CiviCRMs built-in Database Backup utility. If you have shell access, you may be
 * able to query the path by using one of the following commands:
 * $ whereis mysql
 * $ type mysql
 */
// Enter closest dot release to your installed version. 4.0, 4.1, 5.0 are all valid examples. Do NOT
// specify minor revision (second dot) - 4.1.2 is NOT a valid value for this setting. 
define( 'CIVICRM_MYSQL_VERSION', 4.0 );
define( 'CIVICRM_DSN'          , 'mysql://%%dbUser%%:%%dbPass%%@%%dbHost%%/%%dbName%%?new_link=true' );
define( 'CIVICRM_MYSQL_PATH', '/usr/bin/' );

/**
 * SMTP Server:
 *
 * If you are sending emails to contacts using CiviCRM's simple 'Send Email' functionality
 * AND / OR using the CiviMail component, you need to enter the (machine) name for your
 * SMTP Server.
 *
 */
define( 'CIVICRM_SMTP_PASSWORD', ''    );

/**
 * Include county in address block
 * CIVICRM_INCLUDE_COUNTY is set true if you want to include county
 * select box in address block for contact and also in profile
 */
define( 'CIVICRM_INCLUDE_COUNTY' , true );

/** Address Standarization
 *
 * Use this section to enable a postal address standardization plugin.
 * Currently, a plugin for using the US Postal Service address standardization service
 * has been implemented (PROVIDER = 'USPS'). You must register to use the USPS
 * service at http://www.usps.com/webtools/address.htm . When qualified, you will be
 * provided with a USERID and test and live URLs for the service.
 *
 * Other plugins may be added at a later date.
 */
define( 'CIVICRM_ADDRESS_STANDARDIZATION_PROVIDER', '' );
define( 'CIVICRM_ADDRESS_STANDARDIZATION_USERID', '' );
define( 'CIVICRM_ADDRESS_STANDARDIZATION_URL', '' );

/**
 * Payment Processor Settings:
 *
 * If you are using CiviContribute for Online Contributions, you must obtain a Payment Processor
 * (merchant) account and configure your site and the settings below with that account information.
 * 
 * You should start with a Test Server (e.g. Sandbox) account, and configure both the LIVE and TEST
 * settings below using your test (sandbox) account info. Once you are ready to go live, update
 * the LIVE settings to use your live account info. Consult your Payment Processor's documentation
 * and CiviCRM Payment Processor Configuration documentation (http://wiki.civicrm.org/confluence//x/ihk)
 * for details on these settings.
 */

/*
 * TEST Payment Server (Sandbox) Settings:
 * NOTE: Not all settings are used by all payment processors and authentication credential methods.
 * 
 */

// API Password
// PayPal API Signature credential: API Password value (from your PayPal account - View API Signature screen).
// PayPal API Certificate credential: Go to Administer CiviCRM >> Create PayPal API Profile to generate this key value.
// Moneris: API Token value.
define( 'CIVICRM_CONTRIBUTE_PAYMENT_TEST_PASSWORD'      , '' ); 

// API Signature or Key 
// PayPal API Signature credential: Use the API Signature value (from your PayPal account - View API Signature screen).
// PayPal API Certificate credential: Go to Administer CiviCRM >> Create PayPal API Profile to generate this key value.
// Moneris: Use the storeid value.
define( 'CIVICRM_CONTRIBUTE_PAYMENT_TEST_KEY'           , '' ); 

// API Subject
// PayPal API Signature credential only: Use this setting to process transactions in behalf of a 3rd party.
define( 'CIVICRM_CONTRIBUTE_PAYMENT_TEST_SUBJECT'       , '' );

/*
 * LIVE Payment Server Settings:
 * NOTE: Not all settings are used by all payment processors and authentication credential methods.
 * 
 */

// API Password
// PayPal Pro or Express with API Signature credential: API Password value (from your PayPal account - View API Signature screen)
// PayPal Pro or Express with API Certificate credential: Go to Administer CiviCRM >> Create PayPal API Profile to generate this key value.
// Moneris: API Token value.
define( 'CIVICRM_CONTRIBUTE_PAYMENT_PASSWORD'      , '' );

// API Signature or Key 
// PayPal Pro or Express with API Signature credential: Use the API Signature value (from your PayPal account - View API Signature screen).
// PayPal Pro or Express with API Certificate credential: Go to Administer CiviCRM >> Create PayPal API Profile to generate this key value.
// Moneris: Use the storeid value.
define( 'CIVICRM_CONTRIBUTE_PAYMENT_KEY'           , '' );

// API Subject
// PayPal Pro or Express with API Signature credential only: Use this setting to process transactions in behalf of a 3rd party.
define( 'CIVICRM_CONTRIBUTE_PAYMENT_SUBJECT'       , '' );

/**
 * Multi-site Support
 *
 * CiviCRM uses Domain ID keys to allow you to store separate data sets for multiple sites
 * using the same codebase.
 *
 * Refer to the 'Multi-site Support' section of the Installation Guide for more info.
 */
define('CIVICRM_DOMAIN_ID' , 1 );

 
/**
 * Additional CiviMail Settings:
 *
 * CIVICRM_MAILER_SPOOL_PERIOD - Number of seconds between delivery attempts
 * for new outgoing mailings.
 *
 * CIVICRM_VERP_SEPARATOR - Separator character used when CiviMail generates
 * VERP (variable envelope return path) Mail-From addresses. 
 *
 * CIVICRM_MAILER_BATCH_LIMIT - Number of emails sent every CiviMail run (0 - no limit).
 */
define( 'CIVICRM_MAILER_SPOOL_PERIOD', 180);
define( 'CIVICRM_VERP_SEPARATOR', '.' );
define( 'CIVICRM_MAILER_BATCH_LIMIT', 0 );

/**
 * CiviSMS Settings:
 *
 * CiviSMS component is in pre-alpha. Contact the development team if you want to work
 * with this component.
 */
define( 'CIVICRM_SMS_USERNAME'  , 'USERNAME' );
define( 'CIVICRM_SMS_AGGREGATOR', 'CRM_SMS_Protocol_Clickatell' );

/**
 * Joomla! Front-end Component Flag
 * If this configuration file is being used by a Joomla! front-end CiviCRM component
 * instance, this flag is set to 1.
 */
define( 'CIVICRM_UF_FRONTEND', %%frontEnd%% );

/**
 * 
 * Do not change anything below this line. Keep as is
 *
 */

$include_path = '.'        . PATH_SEPARATOR .
                $civicrm_root . PATH_SEPARATOR . 
                $civicrm_root . DIRECTORY_SEPARATOR . 'packages' . PATH_SEPARATOR .
                get_include_path( );
set_include_path( $include_path );

define( 'CIVICRM_SMARTYDIR'  , $civicrm_root . DIRECTORY_SEPARATOR . 'packages' . DIRECTORY_SEPARATOR . 'Smarty' . DIRECTORY_SEPARATOR );
define( 'CIVICRM_TEST_DIR'   , $civicrm_root . DIRECTORY_SEPARATOR . 'test'   . DIRECTORY_SEPARATOR );
define( 'CIVICRM_DAO_DEBUG'  , 0 );
define( 'CIVICRM_TEMPLATEDIR', $civicrm_root . DIRECTORY_SEPARATOR . 'templates'   );
define( 'CIVICRM_PLUGINSDIR' , $civicrm_root . DIRECTORY_SEPARATOR . 'CRM' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'Smarty' . DIRECTORY_SEPARATOR . 'plugins' );

define( 'CIVICRM_GETTEXT_CODESET'    , 'utf-8'   );
define( 'CIVICRM_GETTEXT_DOMAIN'     , 'civicrm' );
define( 'CIVICRM_GETTEXT_RESOURCEDIR', $civicrm_root . DIRECTORY_SEPARATOR . 'l10n' );

if ( function_exists( 'variable_get' ) && variable_get('clean_url', '0') != '0' ) {
    define( 'CIVICRM_CLEANURL', 1 );
} else {
    define( 'CIVICRM_CLEANURL', 0 );
}

// force PHP to auto-detect Mac line endings
ini_set('auto_detect_line_endings', '1');

// make sure the memory_limit is at least 24 MiB
$memLimitString = trim(ini_get('memory_limit'));
$memLimitUnit = strtolower(substr($memLimitString, -1));
$memLimit = (int) $memLimitString;
switch ($memLimitUnit) {
    case 'g': $memLimit *= 1024;
    case 'm': $memLimit *= 1024;
    case 'k': $memLimit *= 1024;
}
if ($memLimit >= 0 and $memLimit < 25165824) {
    ini_set('memory_limit', '24M');
}

?>
