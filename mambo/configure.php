<?php

global $mosConfig_absolute_path;
require_once $mosConfig_absolute_path . DIRECTORY_SEPARATOR . 'configuration.php';

function civicrm_setup( ) {
    global $comPath, $frontPath, $crmPath, $sqlPath, $dsn;
    global $httpBase, $resourceBase, $mainMenu;
    global $httpBaseFE, $mainMenuFE;
    global $compileDir, $uploadDir;

    global $mosConfig_live_site, $mosConfig_absolute_path;
    global $mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db;

    $comPath = $mosConfig_absolute_path . DIRECTORY_SEPARATOR .
        'administrator'          . DIRECTORY_SEPARATOR .
        'components'             . DIRECTORY_SEPARATOR .
        'com_civicrm'            ;
    $crmPath = $comPath . DIRECTORY_SEPARATOR . 'civicrm';
    $pkgPath = $crmPath . DIRECTORY_SEPARATOR . 'packages';
    set_include_path( $comPath . PATH_SEPARATOR .
                      $crmPath . PATH_SEPARATOR .
                      $pkgPath . PATH_SEPARATOR .
                      get_include_path( ) );

    $sqlPath = $crmPath . DIRECTORY_SEPARATOR . 'sql';

    $pieces = parse_url( $mosConfig_live_site );
    $httpBase     = $pieces['path'] . '/administrator/';
    $resourceBase = $httpBase . 'components/com_civicrm/civicrm/';
    $mainMenu     = $httpBase . 'index.php?option=com_civicrm';

    $frontPath = $mosConfig_absolute_path . DIRECTORY_SEPARATOR .
        'components'             . DIRECTORY_SEPARATOR . 
        'com_civicrm'            ;
    $httpBaseFE = $pieces['path'];
    $mainMenuFE = $httpBaseFE . 'index.php?option=com_civicrm';

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

function civicrm_write_file( $name, &$buffer ) {
    $fd  = fopen( $name, "w" );
    if ( ! $fd ) {
        die( "Cannot open $name" );
    }
    fputs( $fd, $buffer );
    fclose( $fd );
}

function civicrm_main( ) {
    global $sqlPath, $comPath, $crmPath, $frontPath;

    civicrm_setup( );

    civicrm_source( $sqlPath . DIRECTORY_SEPARATOR . 'civicrm_40.mysql'     );
    civicrm_source( $sqlPath . DIRECTORY_SEPARATOR . 'civicrm_data.mysql');
    
    // generate backend settings file
    $configFile = $comPath . DIRECTORY_SEPARATOR . 'civicrm.settings.php';
    $string = civicrm_config( false );
    civicrm_write_file( $configFile,
                        $string );

    // generate backend config file
    $string = "
<?php
include_once '$configFile';
?>
";
    $string = trim( $string );
    civicrm_write_file( $crmPath . DIRECTORY_SEPARATOR . 'civicrm.config.php',
                        $string );

    // generate frontend settings file
    $string = civicrm_config( true ); 
    civicrm_write_file( $frontPath . DIRECTORY_SEPARATOR . 'civicrm.settings.php',
                        $string );

}

function civicrm_source( $fileName ) {
    global $dsn, $crmPath;

    require_once 'DB.php';

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

function civicrm_config( $frontend = false ) {
    global $crmPath, $comPath, $httpBase, $resourceBase, $mainMenu, $dsn, $compileDir, $uploadDir, $mysqlPath;
    global $mosConfig_smtphost, $mosConfig_live_site;

    /**
     * make sure we escape the back slashes in the dir names to prevent any
     * issues with windows wehre the dir seperator is a backslash
     */
    $compileDir = addslashes( $compileDir );
    $uploadDir  = addslashes( $uploadDir  );

    if ( $frontend ) {
        $configFile = $comPath . DIRECTORY_SEPARATOR . 'civicrm.settings.php';
        $str = "
<?php 
/** 
 * CiviCRM frontend configuration file. 
 */

define( 'CIVICRM_UF_FRONTEND', 1 );

include_once '$configFile';
                       
?>
";
    } else {
        
        
        $str = "
<?php
/**
 * CiviCRM configuration file.
 */

global \$civicrm_root;

if ( defined( 'CIVICRM_UF_FRONTEND' ) ) {
  define( 'CIVICRM_UF_BASEURL', '$mosConfig_live_site/' );
  define( 'CIVICRM_HTTPBASE'  , '$httpBaseFE'     );
  define( 'CIVICRM_MAINMENU'  , '$mainMenuFE'     );
 } else {
  define( 'CIVICRM_UF_BASEURL', '$mosConfig_live_site/administrator/' );
  define( 'CIVICRM_HTTPBASE'  , '$httpBase'     );
  define( 'CIVICRM_MAINMENU'  , '$mainMenu'     );
}

define( 'CIVICRM_UF'               , 'Mambo' ); 
define( 'CIVICRM_UF_URLVAR'        , 'task'  ); 
define( 'CIVICRM_UF_DSN'           , '$dsn' );
define( 'CIVICRM_UF_USERSTABLENAME', 'jos_users' ); 

\$civicrm_root = '$crmPath';
define( 'CIVICRM_TEMPLATE_COMPILEDIR', '$compileDir' );
define( 'CIVICRM_UPLOADDIR'          , '$uploadDir'  );

define( 'CIVICRM_RESOURCEBASE', '$resourceBase' );

define( 'CIVICRM_MYSQL_VERSION', 4.0 );
define( 'CIVICRM_DSN'         , '$dsn' );
define( 'CIVICRM_MYSQL_PATH', '$mysqlPath' );

define( 'CIVICRM_SMTP_SERVER' , '$mosConfig_smtphost' );

define( 'CIVICRM_PROVINCE_LIMIT' , 'US' ); 
define( 'CIVICRM_COUNTRY_LIMIT'  , 'US' );

define( 'CIVICRM_LC_MESSAGES' , 'en_US' );

define( 'CIVICRM_DATEFORMAT_DATETIME', '%B %E%f, %Y %l:%M %P' ); 
define( 'CIVICRM_DATEFORMAT_FULL', '%B %E%f, %Y' ); 
define( 'CIVICRM_DATEFORMAT_PARTIAL', '%B %Y' ); 
define( 'CIVICRM_DATEFORMAT_YEAR', '%Y' ); 
define( 'CIVICRM_DATEFORMAT_QF_DATE', '%b %d %Y' ); 
define( 'CIVICRM_DATEFORMAT_QF_DATETIME', '%b %d %Y, %I : %M %P' ); 

define('CIVICRM_MAP_PROVIDER','Yahoo');
define('CIVICRM_MAP_API_KEY', '');

// define('CIVICRM_GEOCODE_METHOD', 'CRM_Utils_Geocode_RPC' ); 

define( 'CIVICRM_DOMAIN_ID' , 1 ); 

define( 'CIVICRM_CONTRIBUTE_PAYMENT_PROCESSOR', 'PayPal' );
define( 'CIVICRM_CONTRIBUTE_PAYMENT_CERT_PATH', '' );
define( 'CIVICRM_CONTRIBUTE_PAYMENT_KEY'      , '' );
define( 'CIVICRM_CONTRIBUTE_PAYMENT_PASSWORD' , '' );
define( 'CIVICRM_CONTRIBUTE_PAYMENT_RESPONSE_EMAIL', '\"Please Fix Me\" <fixme@example.com>' );

define( 'CIVICRM_CONTRIBUTE_PAYMENT_TEST_CERT_PATH', '' );
define( 'CIVICRM_CONTRIBUTE_PAYMENT_TEST_KEY'      , '' );
define( 'CIVICRM_CONTRIBUTE_PAYMENT_TEST_PASSWORD' , '' );

define( 'CIVICRM_MONEYFORMAT', '%c %a' );
define( 'CIVICRM_LC_MONETARY', 'en_US' );

define( 'ENABLE_COMPONENTS', 'CiviContribute' );

define( 'CIVICRM_ADDRESS_FORMAT' , ' 
{street_address} 
{supplemental_address_1} 
{supplemental_address_2} 
{city}{, }{state_province}{ }{postal_code} 
{country} 
' );

\$include_path = '.'        . PATH_SEPARATOR .
                \$civicrm_root . PATH_SEPARATOR . 
                \$civicrm_root . DIRECTORY_SEPARATOR . 'packages' . PATH_SEPARATOR .
                get_include_path( );
set_include_path( \$include_path );

define( 'CIVICRM_SMARTYDIR'  , \$civicrm_root . DIRECTORY_SEPARATOR . 'packages' . DIRECTORY_SEPARATOR . 'Smarty' . DIRECTORY_SEPARATOR );
define( 'CIVICRM_TEST_DIR'   , \$civicrm_root . DIRECTORY_SEPARATOR . 'test'   . DIRECTORY_SEPARATOR );
define( 'CIVICRM_DAO_DEBUG'  , 0 );
define( 'CIVICRM_TEMPLATEDIR', \$civicrm_root . DIRECTORY_SEPARATOR . 'templates'   );
define( 'CIVICRM_PLUGINSDIR' , \$civicrm_root . DIRECTORY_SEPARATOR . 'CRM' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'Smarty' . DIRECTORY_SEPARATOR . 'plugins' );

define( 'CIVICRM_GETTEXT_CODESET'    , 'utf-8'   );
define( 'CIVICRM_GETTEXT_DOMAIN'     , 'civicrm' );
define( 'CIVICRM_GETTEXT_RESOURCEDIR', \$civicrm_root . DIRECTORY_SEPARATOR . 'l10n' );

define( 'CIVICRM_CLEANURL', 0 );

?>
";
    }

    $str = trim( $str );
    return $str;
}

civicrm_main( );

?>
