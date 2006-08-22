<?php
/**
 * CiviCRM Configuration File - v1.5
 */

/**
 * Enable CiviCRM Components:
 *
 * You can choose to enable or hide add-on components which provide additional functionality
 * for your CiviCRM site by listing the component names (separated by commas) below.
 *
 * EXAMPLE: enable the CiviContribute component for collecting and managing online and offline
 * contributions, as well as the CiviMail high capacity broadcast mail component:
 *      define( 'ENABLE_COMPONENTS', 'CiviContribute, CiviMail' );
 *
 */
define( 'ENABLE_COMPONENTS', 'CiviContribute,CiviMember' );

/**
 * Content Management System (CMS) Host:
 *
 * CiviCRM can be hosted in either Drupal or Joomla. Retain the default settings
 * for Drupal.
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
 * code is installed.
 *
 * CIVICRM_TEMPLATE_COMPILEDIR is the file system path where compiled templates are stored.
 * These sub-directories and files are temporary caches and will be recreated automatically
 * if deleted.
 *
 * CIVICRM_UPLOADDIR is the file system path where temporary CiviCRM files - such as
 * import data files - are uploaded.
 *
 * CIVICRM_IMAGE_UPLOADDIR is the file system path where image files are uploaded
 * (e.g. premium product images).
 *
 * CIVICRM_CUSTOM_FILE_UPLOADDIR is the file system path where documents and images which
 * are attachments to contacts records are stored (e.g. contact photos, resumes, contracts, etc.)
 * You define these types of files as "custom fields" with field-type = FILE. Generally these
 * files need to be secure/privacy-protected (CiviCRM provides authenticated users with
 * appropriate permissions to access to these files through indirect URLs). Therefore,
 * this directory SHOULD NOT BE LOCATED UNDER YOUR WEBROOT.
 *
 * IMPORTANT: The COMPILEDIR, UPLOADDIR, IMAGE_UPLOADDIR, and CUSTOM_FILE_UPLOADDIR directories must exist,
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
 * and the CIVICRM_UPLOADDIR would be:
 *      define( 'CIVICRM_UPLOADDIR', '/var/www/htdocs/civicspace/files/civicrm/upload/' );
 *
 * the CIVICRM_IMAGE_UPLOADDIR would be:
 *      define( 'CIVICRM_IMAGE_UPLOADDIR', '/var/www/htdocs/civicspace/files/civicrm/persist/' );
 *
 * the CIVICRM_CUSTOM_FILE_UPLOADDIR could be:
 *      define( 'CIVICRM_CUSTOM_FILE_UPLOADDIR', '/var/crm_docs/' );
 *
 * EXAMPLE - Joomla Installations:
 * If the path to the Joomla home directory is /var/www/htdocs/joomla
 * the $civicrm_root setting would be:
 *      $civicrm_root = '/var/www/htdocs/joomla/administrator/components/com_civicrm/civicrm/';
 *
 * the CIVICRM_TEMPLATE_COMPILEDIR would be:
 *      define( 'CIVICRM_TEMPLATE_COMPILEDIR', '/var/www/htdocs/joomla/media/civicrm/templates_c/' );
 *
 * and the CIVICRM_UPLOADDIR would be:
 *      define( 'CIVICRM_UPLOADDIR', '/var/www/htdocs/joomla/media/civicrm/upload/' );
 *	
 * the CIVICRM_IMAGE_UPLOADDIR would be:
 *      define( 'CIVICRM_IMAGE_UPLOADDIR', '/var/www/htdocs/joomla/media/civicrm/persist/' );
 *
 * the CIVICRM_CUSTOM_FILE_UPLOADDIR could be:
 *      define( 'CIVICRM_CUSTOM_FILE_UPLOADDIR', '/var/crm_docs/' );
 */
global $civicrm_root;
$civicrm_root = '%%crmRoot%%';
define( 'CIVICRM_TEMPLATE_COMPILEDIR', '%%templateCompileDir%%' );
define( 'CIVICRM_UPLOADDIR'          , '%%uploadDir%%'  );
define( 'CIVICRM_IMAGE_UPLOADDIR'    , '%%imageUploadDir%%');
define( 'CIVICRM_CUSTOM_FILE_UPLOADDIR'    , '%%customFileUploadDir%%' );

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
 * CIVICRM_UF_RESOURCEURL - Absolute URL to directory where civicrm.module is located:
 *      define( 'CIVICRM_UF_RESOURCEURL', 'http://www.example.com/civicspace/modules/civicrm/' );
 *
 * CIVICRM_RESOURCEBASE - Relative URL to directory where civicrm.module is located:
 *      define( 'CIVICRM_RESOURCEBASE' , '/civicspace/modules/civicrm/' );
 *
 * CIVICRM_IMAGE_UPLOADURL - Absolute URL to directory where uploaded image files are located:
 *      define( 'CIVICRM_IMAGE_UPLOADURL'    , 'http://www.example.com/civicspace/files/civicrm/persist/' );
 *
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
 * CIVICRM_UF_RESOURCEURL - Absolute URL to directory where CiviCRM componenet is installed:
 * Administration and front-end sites:
 *      define( 'CIVICRM_UF_RESOURCEURL', 'http://www.example.com/joomla/administrator/components/com_civicrm/civicrm/' );
 *
 * CIVICRM_RESOURCEBASE - Relative URL to directory where CiviCRM componenet is installed:
 * Administration and front-end sites:
 *      define( 'CIVICRM_RESOURCEBASE' , '/joomla/administrator/components/com_civicrm/civicrm/' );
 *
 * CIVICRM_IMAGE_UPLOADURL - Absolute URL to directory where uploaded image files are located:
 *      define( 'CIVICRM_IMAGE_UPLOADURL'    , 'http://www.example.com/joomla/media/civicrm/persist/' );
 *
 */
define( 'CIVICRM_UF_BASEURL'      , '%%baseURL%%' );
define( 'CIVICRM_UF_RESOURCEURL'  , '%%resourceURL%%' );
define( 'CIVICRM_RESOURCEBASE'    , '%%resourceBase%%' );
define( 'CIVICRM_IMAGE_UPLOADURL' , '%%imageUploadURL%%' );
define( 'CIVICRM_CUSTOM_FILE_UPLOADURL','%%customFileUploadURL%%' );
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
 * The standard STMP Port is 25, so you should only need to change that value if you find
 * that your SMTP server is running on a non-standard port.
 *
 * If your server requires authentication, set CIVICRM_SMTP_AUTH to true
 * and provide the username and password in CIVICRM_SMTP_USERNAME and
 * CIVICRM_SMTP_PASSWORD.
 *
 * Examples:
 *      define( 'CIVICRM_SMTP_SERVER'  , 'smtp.example.com');
 *      define( 'CIVICRM_SMTP_PORT'    , 25                );
 *      define( 'CIVICRM_SMTP_AUTH'    , true              );
 *      define( 'CIVICRM_SMTP_USERNAME', 'smtp_username'   );
 *      define( 'CIVICRM_SMTP_PASSWORD', 'smtp_password'   );
 */
define( 'CIVICRM_SMTP_SERVER'  , ''    );
define( 'CIVICRM_SMTP_PORT'    , 25    );
define( 'CIVICRM_SMTP_AUTH'    , false );
define( 'CIVICRM_SMTP_USERNAME', ''    );
define( 'CIVICRM_SMTP_PASSWORD', ''    );

/**
 * Country Availability:
 *
 * The CIVICRM_COUNTRY_LIMIT option selects which countries are
 * available to the users of this CiviCRM install. The format of this option
 * is a comma-separated list of country ISO codes (US for United States, PL for
 * Poland, etc.). If you're not sure what is the code for a given country, you
 * can check it in the civicrm_country table and/or the xml/templates/civicrm_country.tpl
 * file. ALL countries are included in country drop-down fields if this setting is empty.
 */
define( 'CIVICRM_COUNTRY_LIMIT' , 'US' );

/**
 * Province Availability:
 *
 * CiviCRM ships with a set of provinces derived from the ISO 3166-2 standard;
 * the civicrm_state_province table includes almost 3800 provinces. The
 * CIVICRM_PROVINCE_LIMIT option selects which countries' provinces are
 * available to the users of this CiviCRM install. The format of this option
 * is a comma-separated list of country ISO codes (US for United States, PL for
 * Poland, etc.). If you're not sure what is the code for a given country, you
 * can check it in the civicrm_country table or the file xml/templates/civicrm_country.tpl
 * file. The default limits the state/province list to the United States.
 */
define( 'CIVICRM_PROVINCE_LIMIT' , 'US' );


/**
 * Default Contact Country
 * 
 * When new contacts are added, the country field can be automatically populated wih
 * a default value. Use the country's ISO code here (US for United States, CA for Canada, etc).
 */
define( 'CIVICRM_DEFAULT_CONTACT_COUNTRY', 'US' );

/**
 * Default Currency
 *
 * For clarity, all of the money amounts in the CiviCRM database must have
 * their currencies specified. If the currency is not specified by the user
 * (in the API call, while importing contributions, etc.) the system uses the
 * below default currency.
 *
 * The currency must be specified by its ISO 4217
 * code: http://en.wikipedia.org/wiki/ISO_4217
 */
define( 'CIVICONTRIBUTE_DEFAULT_CURRENCY' , 'USD' );


/**
 * Localisation:
 *
 * Localisation for CiviCRM's user interface is supported via GNU gettext i18n library.
 * Each locale directory contains the translations for that language in .pot and .po files.
 * To switch to a locale other than the default 'en_US', enter the directory name for the
 * new locale (stored below civicrm/l10n directory). Locale format is 'langageCode_countryCode'
 * (e.g. 'pl_PL' for Polish translation).
 */
define( 'CIVICRM_LC_MESSAGES' , 'en_US' );

/**
 * Address Format:
 *
 * The format of address display and the source for address fields sequence in the edit forms.
 *
 * - Every {...token...} will be replaced with the token's value (keeping anything else inside
 *   the curly braces intact).
 * - If the value of a given token is missing, the whole {...} construct will be dropped.
 * - Any {non-token} construct will be turned into non-token if have tokens on both sides
 *   (i.e., will be dropped if on the beginning or end of any line after tokens are replaced).
 *   If there's no city, "{city}{, }{state_province}{ }{postal_code}" will turn first into
 *   "{, }California{ }12345" and then into "California 12345".
 * - If after token replacements there are consecutive {non-token} constructs, the first
 *   one's contents will replace the whole series. For example, if there's no state_province,
 *   "{city}{, }{state_province}{ }{postal_code}" will first turn into "San Francisco{, }{ }12345"
 *   and then into "San Francisco, 12345".
 */
define( 'CIVICRM_ADDRESS_FORMAT' , '
{street_address}
{supplemental_address_1}
{supplemental_address_2}
{city}{, }{state_province}{ }{postal_code}
{country}
' );

/**
 * Date Formatting:
 *
 * Formats for date display and input fields are configurable. Settings use standard POSIX sequences.
 * Standard U.S. layouts are set by default. Adjust as needed to match your locale/requirements.
 *
 * Refer to CiviCRM Localisation documentation for more info. 
 */ 
define( 'CIVICRM_DATEFORMAT_DATETIME', '%B %E%f, %Y %l:%M %P' );
define( 'CIVICRM_DATEFORMAT_FULL', '%B %E%f, %Y' );
define( 'CIVICRM_DATEFORMAT_PARTIAL', '%B %Y' );
define( 'CIVICRM_DATEFORMAT_YEAR', '%Y' );
define( 'CIVICRM_DATEFORMAT_QF_DATE', '%b %d %Y' );
define( 'CIVICRM_DATEFORMAT_QF_DATETIME', '%b %d %Y, %I : %M %P' );

/**
 * Money Display:
 *
 * Format for monetary values display.
 * %c - currency symbol ('$')
 * %C - currency ISO code ('USD')
 * %a - monetary amount, formatted by properly set LC_MONETARY
 *
 * The final 'look' of the formatted amount depends on the defined
 * CIVICRM_LC_MONETARY locale Locale format is 'langageCode_countryCode'.
 * The specified locale must be supported by the underlying operating system.
 */
define( 'CIVICRM_MONEYFORMAT', '%c %a' );
define( 'CIVICRM_LC_MONETARY', 'en_US' );

/**
 * Mapping:
 *
 * Plug-ins are included for both Google and Yahoo mapping service providers. Choose the
 * provider that has the best coverage for your commonly used locales by setting the Map
 * CIVICRM_MAP_PROVIDER to either 'Google' or 'Yahoo'.
 *
 * For GOOGLE mapping - request an API key for your site here:
 * http://www.google.com/apis/maps/signup.html
 *
 * When prompted for 'My Web Site URL' - enter the url for your CiviCRM menu followed by
 * the path '/contact/search'. Your API Key will be generated and displayed on the next page.
 *
 * EXAMPLE: if your Drupal site url is http://www.example.com/civicspace/ you would enter
 * 'http://www.example.com/civicspace/civicrm/contact/search'
 *
 * For YAHOO mapping - request an Application ID for your site here:
 * http://api.search.yahoo.com/webservices/register_application
 *
 * Enter either your Google API key OR Yahoo Application ID in the CIVICRM_MAP_API_KEY
 * setting below.
 * 
 * IMPORTANT: Yahoo! requires that Contact addresses include latitude
 * and longitude. You can populate these manually, or you must enable one of the
 * automatic Geocode lookup methods described in the next section.
 * Google allows you to send the address rather than lat/long. This feature is enabled
 * by default. If you are using another geocoding service, make sure to disable the
 * CIVICRM_MAP_GEOCODING setting
 *
 */
define('CIVICRM_MAP_PROVIDER'  , '' );
define('CIVICRM_MAP_API_KEY'   , '' );
define('CIVICRM_MAP_GEOCODING' , 1  );

/**
 * Geocode (latitude and longitude) Lookup:
 *
 * CiviCRM can be configured to automatically lookup and insert latitude and longitude for contact
 * addresses. The current version offers three methods for this lookup:
 * - local lookup in the Drupal zipcodes table (requires this table to be installed in the
 *   civicrm_db)
 *      define('CIVICRM_GEOCODE_METHOD', 'CRM_Utils_Geocode_ZipTable' );
 *
 * - remote geocode lookup using Yahoo
 *      define('CIVICRM_GEOCODE_METHOD', 'CRM_Utils_Geocode_Yahoo' );
 *
 * NOTE: Yahoo geocoding service currently requires PHP5+ with SimpleXML enabled.
 * You must request and enter a Yahoo Application ID to use Yahoo's geocode lookup service.
 * Enter this value in the CIVICRM_MAP_API_KEY above if you haven't already done so (the same ID
 * is used for mapping and geocode lookups).
 * 
 * - remote geocode lookup using geocoder.us
 *      define('CIVICRM_GEOCODE_METHOD', 'CRM_Utils_Geocode_RPC' );
 *
 * NOTE: Both remote lookup methods involve moderate to significant network overhead and have
 * usage limits. Yahoo's lookup is a bit faster, but neither method should be enabled when
 * Importing more than a few hundred contacts.
 *
 * You may also create and call your own class using the interface defined at
 * 'CRM_Utils_Geocode_API'.
 */
define('CIVICRM_GEOCODE_METHOD', '' );


/**
 * Automatically Check for New CiviCRM Versions:
 *
 * CiviCRM can be configured to connect periodically to the OpenNGO website and
 * check for the availability of new versions. Set this value to true if you
 * want this install to perform such checks (the info about new versions will
 * be displayed on the main Administer CiviCRM control panel).
 */
define('CIVICRM_VERSION_CHECK', true);

/**
 * Payment Processor Settings:
 *
 * If you are using CiviContribute for Online Contributions, you must obtain a Payment Processor
 * (merchant) account and configure your site and the settings below with that account information.
 * 
 * You should start with a Test Server (e.g. Sandbox) account, and configure both the LIVE and TEST
 * settings below using your test (sandbox) account info. Once you are ready to go live, update
 * the LIVE settings to use your live account info. Consult your Payment Processor's documentation
 * and CiviCRM Payment Processor Configuration documentation (http://objectledge.org/confluence/display/CRM/CiviContribute+Payment+Processor+Configuration)
 * for details on these settings.
 */
define( 'CIVICRM_CONTRIBUTE_PAYMENT_PROCESSOR'     , '' );
// Valid values are 'PayPal' (Website Payments Pro), 'PayPal_Express', and 'Moneris'.

define( 'CIVICRM_CONTRIBUTE_PAYMENT_EXPRESS_BUTTON', 'https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif');
// URL to the button image used for "express" option checkout, e.g. PayPal Express. URL to PayPal US button is provided by default.
// NOTE: If you've enabled SSL for your Contribution page, your button image should be sourced via https as well.

/*
 * TEST Payment Server (Sandbox) Settings:
 * NOTE: Not all settings are used by all payment processors and authentication credential methods.
 * 
 */

// API Username
// PayPal API Signature credential only: API Username value (from your PayPal account - View API Signature screen).
define( 'CIVICRM_CONTRIBUTE_PAYMENT_TEST_USERNAME'      , '' );

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

// API Certificate Path
// PayPal API Certificate credential only: File system path where API Profile files should be created and stored.
define( 'CIVICRM_CONTRIBUTE_PAYMENT_TEST_CERT_PATH'     , '');

// Hostname for "PayPal Express" button submit in test-drive mode. Value for US is provided by default.
// Do not change this value unless you are submitting to a non-US PayPal instance.
define( 'CIVICRM_CONTRIBUTE_PAYMENT_PAYPAL_EXPRESS_TEST_URL', 'www.sandbox.paypal.com');

/*
 * LIVE Payment Server Settings:
 * NOTE: Not all settings are used by all payment processors and authentication credential methods.
 * 
 */

// API Username
// PayPal API Signature credential only: API Username value (from your PayPal account - View API Signature screen).
define( 'CIVICRM_CONTRIBUTE_PAYMENT_USERNAME'      , '' );

// API Password
// PayPal API Signature credential: API Password value (from your PayPal account - View API Signature screen)
// PayPal API Certificate credential: Go to Administer CiviCRM >> Create PayPal API Profile to generate this key value.
// Moneris: API Token value.
define( 'CIVICRM_CONTRIBUTE_PAYMENT_PASSWORD'      , '' );

// API Signature or Key 
// PayPal API Signature credential: Use the API Signature value (from your PayPal account - View API Signature screen).
// PayPal API Certificate credential: Go to Administer CiviCRM >> Create PayPal API Profile to generate this key value.
// Moneris: Use the storeid value.
define( 'CIVICRM_CONTRIBUTE_PAYMENT_KEY'           , '' );

// API Certificate Path
// PayPal API Certificate credential only: File system path where API Profile files should be created and stored.
define( 'CIVICRM_CONTRIBUTE_PAYMENT_CERT_PATH'     , '' );

// Hostname for "PayPal Express" button submit in live mode. Value for US is provided by default.
// Do not change this value unless you are submitting to a non-US PayPal instance.
define( 'CIVICRM_CONTRIBUTE_PAYMENT_PAYPAL_EXPRESS_URL', 'www.paypal.com');

/**
 * Force SSL Redirect for Online Contribution Pages:
 *
 * If your site includes CiviContribute Online Contribution pages, AND you use a payment
 * processor plugin which collects credit card and billing information ON YOUR SITE (any plugin
 * other than PayPal_Express as of now), it is strongly recommended that you create or obtain
 * an SSL certificate and configure your webserver to support SSL connections. Once this is
 * done, you can change this setting to 1. With CIVICRM_ENABLE_SSL set to 1, CiviCRM will
 * automatically redirect requests for online contribution pages to an https (SSL secured) URL.
 * 
 */
define('CIVICRM_ENABLE_SSL', 0 );

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
 * Location Blocks display for Contacts
 *
 * CiviCRM by default shows 2 location blocks when editing a contact, change this number if you
 * want to increase the location blocks
 */
// define( 'CIVICRM_MAX_LOCATION_BLOCKS', 2 );

/**
 * Debugging:
 *
 * Enable CIVICRM_DEBUG (value = 1) when you need to use one of the debug-related tools. These are 
 * triggered via URL parameters - IF CIVICRM_DEBUG is turned on.
 *
 * Debugging tools:
 * Smarty Debug Window - Loads all variables available to the current page template into a pop-up
 * window. To trigger, add '&smartyDebug=1' to any CiviCRM URL query string.
 *
 * Session Reset - Resets all values in your client session. To trigger, add '&sessionReset=2'
 *
 * Directory Cleanup - Empties template cache and/or upload file folders.
 *  To empty template cache (civicrm/templates_c folder), add '&directoryCleanup=1'
 *  To remove temporary upload files (civicrm/upload folder), add '&directoryCleanup=2'
 *  To cleanup both, add '&directoryCleanup=3'
 *
 * Stack Trace -
 *  To display stack trace at top of page, add '&backtrace=1'
 *  If you need a stack trace for a POST result, you can set CIVICRM_BACKTRACE to 1 below
 * (CIVICRM_DEBUG must also be set to 1 for this to work).
 *  
 * WARNING: Do not leave debugging enabled by default as it can be used to expose configuration
 * information to unauthorized browsers.
 */
define( 'CIVICRM_DEBUG',     0 );
define( 'CIVICRM_BACKTRACE', 0 );
 
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
 * Error Handling Customization:
 *
 * You can define your own template for displaying fatal errors by changing the
 * default value for FATAL_ERROR_TEMPLATE. The template file must be located under
 * $civicrm_root/templates. Use a relative path to the file.
 *      define( 'CIVICRM_FATAL_ERROR_TEMPLATE', 'CRM/myFatalError.tpl' );
 * The following smarty variables may be assigned to these template (refer to the default
 * file - CRM/error.tpl - for display example).
 *      $message        - The error message. Always defined.
 *      $code           - An error code. Conditionally defined.
 *      $mysql_code     - A MySQL (DB) error code. Conditionally defined.
 *
 * You can also replace the default fatal error handling function with a custom function:
 *      define( 'CIVICRM_FATAL_ERROR_HANDLER',  'myFatalErrorHandler');
 * The function must be loaded by an enabled module. CiviCRM will pass an array with the
 * errors ($message, $code, $mysql_code) as an argument to the function.
 */
define( 'CIVICRM_FATAL_ERROR_TEMPLATE', 'CRM/error.tpl' );
define( 'CIVICRM_FATAL_ERROR_HANDLER',  '');

/**
 * File encoding conversion:
 *
 * CiviCRM expects the import files to be in the UTF-8 encoding. That said,
 * in most cases (Excel CSV files) the encoding is not UTF-8. In such cases,
 * CiviCRM will recode any non-UTF-8 file to UTF-8 assuming the file's encoding
 * is the one specified below.
 */
define( 'CIVICRM_LEGACY_ENCODING', 'Windows-1252' );

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
