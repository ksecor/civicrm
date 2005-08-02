<?php

require_once '../configuration.php';

function civicrm_setup( ) {
    global $comPath, $crmPath, $sqlPath, $dsn;
    global $httpBase, $resourceBase, $mainMenu;
    global $compileDir, $uploadDir;

    global $mosConfig_live_site, $mosConfig_absolute_path;
    global $mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db;

    $comPath = $mosConfig_absolute_path . DIRECTORY_SEPARATOR .
        'administrator'          . DIRECTORY_SEPARATOR .
        'components'             . DIRECTORY_SEPARATOR .
        'com_civicrm'            ;
    $crmPath = $comPath . DIRECTORY_SEPARATOR . 'civicrm';
    $sqlPath = $crmPath . DIRECTORY_SEPARATOR . 'sql';

    $pieces = parse_url( $mosConfig_live_site );
    $httpBase     = $pieces['path'] . '/administrator/';
    $resourceBase = $httpBase . 'components/com_civicrm/civicrm/';
    $mainMenu     = $httpBase . 'index2.php?option=com_civicrm';

    $scratchDir   = $mosConfig_absolute_path . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'civicrm';
    if ( ! is_dir( $scratchDir ) ) {
        mkdir( $scratchDir, 0777 );
    }
    
    $compileDir        = $scratchDir . DIRECTORY_SEPARATOR . 'templates_c';
    if ( ! is_dir( $compileDir ) ) {
        mkdir( $compileDir, 0777 );
    }
    
    $uploadDir         = $scratchDir . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR;
    if ( ! is_dir( $uploadDir ) ) {
        mkdir( $uploadDir, 0777 );
    }

    $dsn =  'mysql://' . 
        $mosConfig_user     . ':' . 
        $mosConfig_password . '@' .
        $mosConfig_host     . '/' .
        $mosConfig_db       .
        '?new_link=true';

}

function civicrm_main( ) {
    global $sqlPath, $comPath;

    civicrm_setup( );

    civicrm_source( $sqlPath . DIRECTORY_SEPARATOR . 'Contacts.mysql40.sql'  );
    civicrm_source( $sqlPath . DIRECTORY_SEPARATOR . 'FixedData.sql' );

    $configFile = $comPath . DIRECTORY_SEPARATOR . 'config.inc.php';
    $string = civicrm_config( );
    $fd = fopen( $configFile, "w" );
    if ( ! $fd ) {
        die( "Cannot open $configFile" );
    }

    fputs( $fd, $string );
    fclose ( $fd );
}

function civicrm_source( $fileName ) {
    global $dsn, $crmPath;

    ini_set( 'include_path',
             '.:' . $crmPath . ':' .
             $crmPath . DIRECTORY_SEPARATOR . 'packages' );
    require_once 'packages/DB.php';

    $db  =& DB::connect( $dsn );
    if ( PEAR::isError( $db ) ) {
        die( "Cannot open $fileName: " . $db->getMessage( ) );
    }
    
    $string = file_get_contents( $fileName );

    //get rid of comments starting with # and --
    $string = ereg_replace("\n#[^\n]*\n", "\n", $string );
    $string = ereg_replace("\n\-\-[^\n]*\n", "\n", $string );
    
    $queries  = explode( ';', $string );
    foreach ( $queries as $query ) {
        $query = trim( $query );
        if ( ! empty( $query ) ) {
            $res =& $db->query( $query );
            if ( PEAR::isError( $res ) ) {
                die( "Cannot execute $query: " . $res->getMessage( ) );
            }
        }
    }
}

function civicrm_config( ) {
    global $crmPath, $httpBase, $resourceBase, $mainMenu, $dsn, $compileDir, $uploadDir;
    global $mosConfig_smtphost;

    $str = "
<?php
/**
 * CiviCRM configuration file.
 */

global \$civicrm_root;

/**
 * Content Management System (CMS) Host:
 *
 * CiviCRM can be hosted in either Drupal or Mambo.
 *
 * Settings for Mambo:
 *      define( ''CRM_USERFRAMEWORK'       , 'Mambo' );
 *      define( 'CRM_USERFRAMEWORK_URLVAR', 'task'  );
 */
define( 'CRM_USERFRAMEWORK'       , 'Mambo' );
define( 'CRM_USERFRAMEWORK_URLVAR', 'task'  );

/**
 * File system paths for this install:
 *
 * \$civicrm_root is the file system path on your server where the civicrm
 * code is installed.
 *
 * CRM_TEMPLATE_COMPILE_DIR is the file system path where compiled templates are stored.
 *
 * CRM_UPLOAD_DIR is the file system path to which CiviCRM files are uploaded.
 *
 * EXAMPLE - CivicSpace / Drupal:
 * If the path to the CivicSpace or Drupal home directory is /var/www/htdocs/civicspace
 * the \$civicrm_root setting would be:
 *      \$civicrm_root = '/var/www/htdocs/civicspace/modules/civicrm';
 *
 * the CRM_TEMPLATE_COMPILE_DIR would be:
 *      define( 'CRM_TEMPLATE_COMPILE_DIR', '/var/www/htdocs/civicspace/files/civicrm/templates_c' );
 *
 * and the CRM_UPLOAD_DIR would be:
 *      define( 'CRM_UPLOAD_DIR', '/var/www/htdocs/civicspace/files/civicrm/upload' );
 *
 * EXAMPLE - Mambo:
 * If the path to the Mambo home directory is /var/www/htdocs/mambo
 * the \$civicrm_root setting would be:
 *      \$civicrm_root = '/var/www/htdocs/mambo/administrator/components/com_civicrm/civicrm';
 *
 * the CRM_TEMPLATE_COMPILE_DIR would be:
 *      define( 'CRM_TEMPLATE_COMPILE_DIR', '/var/www/htdocs/mambo/media/civicrm/templates_c' );
 *
 * and the CRM_UPLOAD_DIR would be:
 *      define( 'CRM_UPLOAD_DIR', '/var/www/htdocs/mambo/media/civicrm/upload' );
 */
\$civicrm_root = '$crmPath';
define( 'CRM_TEMPLATE_COMPILEDIR', '$compileDir' );
define( 'CRM_UPLOADDIR'          , '$uploadDir'  );

/**
 * Site URLs:
 *
 * This section defines absolute URLs to access Drupal and CiviCRM.
 * IMPORTANT: Trailing slashes are required for CRM_HTTPBASE and CRM_RESOURCEBASE. 
 *
 * EXAMPLE: if your Drupal site url is http://www.example.com/civicspace/
 * these variables would be set as below. Modify as needed for your install.
 */
define( 'CRM_HTTPBASE'    , '$httpBase'     );
define( 'CRM_RESOURCEBASE', '$resourceBase' );
define( 'CRM_MAINMENU'    , '$mainMenu'     );

/**
 * Database settings:
 *
 * Define the version of MySQL you are running. 
 * CiviCRM has been optimized for MySQL 4.1, but will also run on many 4.0.x versions.
 * If you are using a 4.0.x release of MySQL, you MUST change CRM_MYSQL_VERSION to 4.0
 *
 * Define the database URL (CRM_DSN) for the CiviCRM database.
 * Database URL format:
 *      define( 'CRM_DSN', 'mysql://crm_db_username:crm_db_password@db_server/crm_database?new_link=true');
 *
 * Drupal and CiviCRM can share the same database, or can be installed into separate databases.
 *
 * EXAMPLE: Drupal and CiviCRM running in the same database...
 *      DB Name = drupal, DB User = drupal
 *      define( 'CRM_DSN'         , 'mysql://drupal:YOUR_PASSWORD@localhost/drupal?new_link=true' );
 *
 * EXAMPLE: Drupal and CiviCRM running in separate databases...
 *      CiviCRM DB Name = civicrm, CiviCRM DB User = civicrm
 *      define( 'CRM_DSN'         , 'mysql://civicrm:YOUR_PASSWORD@localhost/civicrm?new_link=true' );
 */
define( 'CRM_MYSQL_VERSION', 4.0 );
define( 'CRM_DSN'         , '$dsn' );

/**
 * SMTP Server:
 *
 * If you are sending emails to contacts using CiviCRM's simple 'Send Email' functionality
 * enter your smtp server address here (e.g.'smtp.example.com').
 */
define( 'CRM_SMTP_SERVER' , '$mosConfig_smtphost' );

/**
 * Localisation:
 *
 * Localisation for CiviCRM's user interface is supported via GNU gettext i18n library.
 * Each locale directory contains the translations for that language in .pot and .po files.
 * To switch to a locale other than the default 'en_US', enter the directory name for the
 * new locale (stored below civicrm/l10n directory). Locale format is 'langageCode_countryCode'
 * (e.g. 'pl_PL' for Polish translation).
 */
define( 'CRM_LC_MESSAGES' , 'en_US' );

/**
 * Date formatting:
 *
 * Formats for date display and input fields are configurable. Settings use standard POSIX sequences.
 * Standard U.S. layouts are set by default. Adjust as needed to match your locale/requirements.
 *
 * Refer to CiviCRM Localisation documentation for more info. 
 */ 
define( 'CRM_DATEFORMAT_DATETIME', '%B %E%f, %Y %l:%M %P' );
define( 'CRM_DATEFORMAT_FULL', '%B %E%f, %Y' );
define( 'CRM_DATEFORMAT_PARTIAL', '%B %Y' );
define( 'CRM_DATEFORMAT_YEAR', '%Y' );
define( 'CRM_DATEFORMAT_QF_DATE', '%b %d %Y' );
define( 'CRM_DATEFORMAT_QF_DATETIME', '%b %d %Y, %I : %M %P' );

/**
 * Import - maximum file size:
 *
 * Default maximum Contact Import filesize is 1MB. You may increase this up to a hard-limit of 8MB.
 * However, imports above 1MB will take a 'long time' and are server resource intensive.
 * File size is expressed in bytes (1MB is entered as 1048576).
 */
define( 'CRM_MAX_IMPORT_FILESIZE' , 1048576);

include_once 'config.main.php';

?>
";

    return $str;
}

?>
